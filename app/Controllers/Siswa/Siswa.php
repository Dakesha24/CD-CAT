<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;
use App\Models\JadwalUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\BankSoalModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\DetailJawabanModel;
use App\Models\CatEstimationModel;

class Siswa extends Controller
{
  protected $jadwalUjianModel;
  protected $pesertaUjianModel;
  protected $bankSoalModel;
  protected $siswaModel;
  protected $kelasModel;
  protected $detailJawabanModel;
  protected $catEstimationModel;

  public function __construct()
  {
    $this->jadwalUjianModel = new JadwalUjianModel();
    $this->pesertaUjianModel = new PesertaUjianModel();
    $this->bankSoalModel = new BankSoalModel();
    $this->siswaModel = new SiswaModel();
    $this->kelasModel = new KelasModel();
    $this->detailJawabanModel = new DetailJawabanModel();
    $this->catEstimationModel = new CatEstimationModel();
  }

  public function dashboard()
  {
    return view('siswa/dashboard');
  }

  public function pengumuman()
  {
    return view('siswa/pengumuman');
  }

  public function ujian()
  {
    $userId = session()->get('user_id');

    // Cek apakah data siswa sudah ada
    if (!$this->siswaModel->checkSiswaExists($userId)) {
      session()->setFlashdata('error', 'Silakan lengkapi data profil Anda terlebih dahulu sebelum mengakses ujian.');
      return redirect()->to(base_url('siswa/profil'));
    }

    // Ambil data siswa
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    // Ambil jadwal ujian beserta status peserta jika ada
    $data['jadwal_ujian'] = $this->jadwalUjianModel->getAvailableUjianWithStatus($siswa['siswa_id']);
    return view('siswa/ujian', $data);
  }

  public function hasil()
  {
    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    if (!$siswa) {
      return redirect()->to('siswa/profil')->with('error', 'Lengkapi profil Anda terlebih dahulu');
    }

    // Ambil semua hasil ujian siswa
    $hasil_ujian = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jadwal_ujian.tanggal_mulai, jadwal_ujian.tanggal_selesai, jenis_ujian.nama_ujian')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->where('peserta_ujian.siswa_id', $siswa['siswa_id'])
      ->orderBy('peserta_ujian.created_at', 'DESC')
      ->findAll();

    return view('siswa/hasil', ['hasil_ujian' => $hasil_ujian]);
  }

  public function saveProfil()
  {
    $userId = session()->get('user_id');

    // Debug untuk melihat data yang diterima
    // var_dump($this->request->getPost());
    // die();

    $data = [
      'user_id' => $userId,
      'nomor_peserta' => $this->request->getPost('nomor_peserta'),
      'nama_lengkap' => $this->request->getPost('nama_lengkap'),
      'kelas_id' => $this->request->getPost('kelas_id')
    ];

    // Validasi input
    $rules = [
      'nomor_peserta' => 'required|min_length[5]',
      'nama_lengkap' => 'required|min_length[3]',
      'kelas_id' => 'required|numeric'
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    // Cek apakah update atau insert
    $existingSiswa = $this->siswaModel->where('user_id', $userId)->first();

    try {
      if ($existingSiswa) {
        $this->siswaModel->update($existingSiswa['siswa_id'], $data);
        session()->setFlashdata('success', 'Profil berhasil diperbarui!');
      } else {
        $this->siswaModel->insert($data);
        session()->setFlashdata('success', 'Profil berhasil disimpan!');
      }
      return redirect()->to(base_url('siswa/profil'));
    } catch (\Exception $e) {
      log_message('error', $e->getMessage());
      return redirect()->back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan data.');
    }
  }

  public function profil()
  {
    $userId = session()->get('user_id');
    $data = [
      'siswa' => $this->siswaModel->where('user_id', $userId)->first(),
      'kelas' => $this->kelasModel->findAll(),
      'isNewUser' => !$this->siswaModel->checkSiswaExists($userId)
    ];
    return view('siswa/profil', $data);
  }

  // method-method untuk CAT
  private function hitungProbabilitas($theta, $b, $a = 1, $c = 0)
  {
    $exp = exp($a * ($theta - $b));
    return $c + ((1 - $c) * ($exp / (1 + $exp)));
  }

  private function hitungInformasiSoal($theta, $b, $a = 1, $c = 0)
  {
    $P = $this->hitungProbabilitas($theta, $b, $a, $c);
    $Q = 1 - $P;
    return ($a * $a * $Q * $P) / ($P * (1 - $c));
  }

  private function hitungStandarError($informasi)
  {
    return 1 / sqrt(array_sum($informasi));
  }

  private function pilihSoalCAT($pesertaUjianId, $currentTheta, $jadwalId)
  {
    // Ambil soal yang belum dijawab
    $answeredQuestions = $this->detailJawabanModel
      ->select('soal_id')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->findAll();

    // Jika belum ada soal yang dijawab, array akan kosong
    if (empty($answeredQuestions)) {
      $answeredIds = [0]; // Berikan nilai default agar query tidak error
    } else {
      $answeredIds = array_column($answeredQuestions, 'soal_id');
    }

    // Pilih soal dengan tingkat kesulitan terdekat dengan theta saat ini
    $soal = $this->bankSoalModel
      ->select('bank_soal.*')
      ->join('jadwal_ujian', 'jadwal_ujian.jenis_ujian_id = bank_soal.jenis_ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->whereNotIn('bank_soal.soal_id', $answeredIds)
      ->orderBy("ABS(tingkat_kesulitan - $currentTheta)")
      ->first();

    return $soal;
  }


  public function mulaiUjian()
  {
    $kodeUjian = $this->request->getPost('kode_ujian');
    $jadwalId = $this->request->getPost('jadwal_id');
    $userId = session()->get('user_id');

    // Dapatkan siswa_id berdasarkan user_id
    $siswa = $this->siswaModel->where('user_id', $userId)->first();
    if (!$siswa) {
      return redirect()->back()->with('error', 'Data siswa tidak ditemukan!');
    }

    // Cek apakah siswa sudah memulai ujian ini sebelumnya
    $existingPeserta = $this->pesertaUjianModel
      ->where('siswa_id', $siswa['siswa_id'])
      ->where('jadwal_id', $jadwalId)
      ->where('status !=', 'selesai')
      ->first();

    if ($existingPeserta) {
      return redirect()->to("siswa/ujian/soal/{$existingPeserta['peserta_ujian_id']}");
    }

    // Cek kode ujian
    $jadwal = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, jenis_ujian.is_cat')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->find($jadwalId);

    if (!$jadwal || $jadwal['kode_akses'] !== $kodeUjian) {
      return redirect()->back()->with('error', 'Kode ujian tidak valid!');
    }

    // Buat record peserta ujian
    $dataPeserta = [
      'jadwal_id' => $jadwalId,
      'siswa_id' => $siswa['siswa_id'],
      'status' => 'sedang_mengerjakan',
      'waktu_mulai' => date('Y-m-d H:i:s')
    ];

    $pesertaUjianId = $this->pesertaUjianModel->insert($dataPeserta);

    // Inisialisasi CAT jika jenis ujian adalah CAT
    if ($jadwal['is_cat']) {
      $this->catEstimationModel->insert([
        'peserta_ujian_id' => $pesertaUjianId,
        'theta' => 0,
        'standard_error' => 9.999,
        'previous_se' => 9.999,
        'jumlah_soal' => 0
      ]);
    }

    return redirect()->to("siswa/ujian/soal/$pesertaUjianId");
  }

  public function soal($pesertaUjianId)
  {
    // Inisialisasi default cat_estimation
    $catEstimation = [
      'theta' => 0,
      'standard_error' => 9.999,
      'previous_se' => 9.999,
      'jumlah_soal' => 0,
      'se_target' => 0.3,      // Tambahkan default SE target
      'jumlah_soal_maksimum' => 20  // Tambahkan default jumlah soal maksimum
    ];

    // Ambil data peserta ujian
    $pesertaUjian = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jadwal_ujian.durasi_menit, jadwal_ujian.tanggal_mulai, 
                    jadwal_ujian.tanggal_selesai, jadwal_ujian.jenis_ujian_id, 
                    jenis_ujian.nama_ujian, jenis_ujian.is_cat')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
      ->first();

    if (!$pesertaUjian) {
      return redirect()->to('siswa/ujian')->with('error', 'Sesi ujian tidak ditemukan!');
    }

    if ($pesertaUjian['status'] === 'selesai') {
      return redirect()->to('siswa/hasil')->with('error', 'Ujian sudah selesai!');
    }

    // Hitung sisa waktu
    $waktuMulai = strtotime($pesertaUjian['waktu_mulai']);
    $waktuSekarang = time();
    $waktuBerjalan = $waktuSekarang - $waktuMulai;
    $sisaWaktu = ($pesertaUjian['durasi_menit'] * 60) - $waktuBerjalan;

    if ($sisaWaktu <= 0) {
      $this->pesertaUjianModel->update($pesertaUjianId, [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s')
      ]);
      return redirect()->to('siswa/hasil')->with('error', 'Waktu ujian telah habis!');
    }

    // Tambahkan nilai default untuk se_target dan jumlah_soal_maksimum
    $pesertaUjian['se_target'] = 0.3;  // Sesuaikan dengan kebutuhan
    $pesertaUjian['jumlah_soal_maksimum'] = 20;  // Sesuaikan dengan kebutuhan

    if ($pesertaUjian['is_cat']) {
      // Ambil estimasi kemampuan terkini
      $catEstimationFromDb = $this->catEstimationModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->first();

      if ($catEstimationFromDb) {
        $catEstimation = $catEstimationFromDb;
      }

      // Pilih soal berikutnya berdasarkan CAT
      $soal = $this->pilihSoalCAT($pesertaUjianId, $catEstimation['theta'], $pesertaUjian['jadwal_id']);

      // Jika tidak ada soal yang tersedia
      if (!$soal) {
        // Update status ujian menjadi selesai
        $this->pesertaUjianModel->update($pesertaUjianId, [
          'status' => 'selesai',
          'waktu_selesai' => date('Y-m-d H:i:s')
        ]);
        return redirect()->to('siswa/hasil')->with('success', 'Ujian telah selesai!');
      }

      $data = [
        'peserta_ujian' => $pesertaUjian,
        'soal' => [$soal], // kirim hanya satu soal
        'durasi_menit' => $pesertaUjian['durasi_menit'],
        'cat_estimation' => $catEstimation
      ];
    } else {
      // Logika untuk ujian non-CAT
      $soal = $this->bankSoalModel
        ->where('jenis_ujian_id', $pesertaUjian['jenis_ujian_id'])
        ->findAll();

      // Ambil jawaban siswa
      $jawabanSiswa = $this->detailJawabanModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->findAll();

      // Format jawaban
      $jawabanMap = [];
      foreach ($jawabanSiswa as $jawaban) {
        $jawabanMap[$jawaban['soal_id']] = $jawaban['jawaban_siswa'];
      }

      $data = [
        'peserta_ujian' => $pesertaUjian,
        'soal' => $soal,
        'jawaban_siswa' => $jawabanMap,
        'durasi_menit' => $pesertaUjian['durasi_menit'],
        'cat_estimation' => $catEstimation  // Tambahkan default cat_estimation
      ];
    }

    // Tambahkan log untuk debugging
    log_message('info', 'Data yang dikirim ke view: ' . print_r($data, true));

    return view('siswa/soal_ujian', $data);
  }

  public function simpanJawaban()
  {
    try {
      $pesertaUjianId = $this->request->getPost('peserta_ujian_id');
      $soalId = $this->request->getPost('soal_id');
      $jawaban = $this->request->getPost('jawaban');

      // Validasi input
      if (!$pesertaUjianId || !$soalId || !$jawaban) {
        throw new \Exception('Data tidak lengkap');
      }

      // Tambahkan log untuk debugging
      log_message('debug', 'Input data: ' . json_encode([
        'peserta_ujian_id' => $pesertaUjianId,
        'soal_id' => $soalId,
        'jawaban' => $jawaban
      ]));

      // Cek apakah ujian menggunakan CAT
      $pesertaUjian = $this->pesertaUjianModel
        ->select('jenis_ujian.is_cat')
        ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
        ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->first();

      if (!$pesertaUjian) {
        throw new \Exception('Data ujian tidak ditemukan');
      }

      // Ambil soal dan cek jawaban
      $soal = $this->bankSoalModel->find($soalId);
      if (!$soal) {
        throw new \Exception('Soal tidak ditemukan');
      }

      $isCorrect = ($jawaban === $soal['jawaban_benar']) ? 1 : 0;

      // Simpan jawaban
      $data = [
        'peserta_ujian_id' => $pesertaUjianId,
        'soal_id' => $soalId,
        'jawaban_siswa' => $jawaban,
        'is_correct' => $isCorrect
      ];

      $existingJawaban = $this->detailJawabanModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->where('soal_id', $soalId)
        ->first();

      if ($existingJawaban) {
        $this->detailJawabanModel->update($existingJawaban['jawaban_id'], $data);
      } else {
        $this->detailJawabanModel->insert($data);
      }

      // Update CAT estimation jika menggunakan CAT
      if ($pesertaUjian['is_cat']) {
        $catEstimation = $this->catEstimationModel
          ->where('peserta_ujian_id', $pesertaUjianId)
          ->first();

        if (!$catEstimation) {
          throw new \Exception('Data estimasi CAT tidak ditemukan');
        }

        // Perhitungan CAT
        $P = $this->hitungProbabilitas(
          $catEstimation['theta'],
          $soal['tingkat_kesulitan'],
          $soal['daya_beda'] ?? 1,
          $soal['faktor_tebakan'] ?? 0
        );

        $I = $this->hitungInformasiSoal(
          $catEstimation['theta'],
          $soal['tingkat_kesulitan'],
          $soal['daya_beda'] ?? 1,
          $soal['faktor_tebakan'] ?? 0
        );

        // Hindari division by zero
        if ($I <= 0) {
          $I = 0.0001; // Nilai minimum untuk menghindari division by zero
        }

        $newTheta = $catEstimation['theta'] +
          ($isCorrect ? (1 - $P) : (-$P)) / sqrt($I);

        $newSE = 1 / sqrt($I);

        // Update estimasi
        $this->catEstimationModel->update($catEstimation['estimation_id'], [
          'theta' => $newTheta,
          'previous_se' => $catEstimation['standard_error'],
          'standard_error' => $newSE,
          'jumlah_soal' => $catEstimation['jumlah_soal'] + 1
        ]);

        $data['theta_saat_ini'] = $newTheta;
        $data['se_saat_ini'] = $newSE;

        if ($existingJawaban) {
          $this->detailJawabanModel->update($existingJawaban['jawaban_id'], $data);
        }
      }

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Jawaban berhasil disimpan'
      ]);
    } catch (\Exception $e) {
      log_message('error', 'Error saving answer: ' . $e->getMessage());
      return $this->response->setStatusCode(500)->setJSON([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }


  public function selesaiUjian($pesertaUjianId)
  {
    $pesertaUjian = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jenis_ujian.is_cat')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->find($pesertaUjianId);

    if (!$pesertaUjian) {
      return redirect()->to('siswa/ujian')->with('error', 'Sesi ujian tidak ditemukan!');
    }

    if ($pesertaUjian['is_cat']) {
      // Ambil estimasi kemampuan terakhir untuk CAT
      $catEstimation = $this->catEstimationModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->first();

      // Konversi theta ke skala 0-100
      // Asumsi rentang theta normal adalah -4 hingga 4
      $nilaiAkhir = (($catEstimation['theta'] + 4) / 8) * 100;

      // Pastikan nilai dalam rentang 0-100
      $nilaiAkhir = max(0, min(100, $nilaiAkhir));
    } else {
      // Perhitungan nilai untuk non-CAT (existing logic)
      $totalSoal = $this->detailJawabanModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->countAllResults();

      $jawabanBenar = $this->detailJawabanModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->where('is_correct', true)
        ->countAllResults();

      $nilaiAkhir = $totalSoal > 0 ? ($jawabanBenar / $totalSoal) * 100 : 0;
    }

    // Update status dan nilai
    $this->pesertaUjianModel->update($pesertaUjianId, [
      'status' => 'selesai',
      'waktu_selesai' => date('Y-m-d H:i:s'),
      'nilai_akhir' => $nilaiAkhir
    ]);

    session()->setFlashdata('success', 'Ujian telah selesai! Berikut adalah hasil ujian Anda.');
    return redirect()->to("siswa/hasil/review/$pesertaUjianId");
  }


  public function review($pesertaUjianId)
  {
    $pesertaUjian = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jadwal_ujian.*, jenis_ujian.nama_ujian, jenis_ujian.is_cat')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->first();

    if (!$pesertaUjian) {
      return redirect()->to('siswa/hasil')->with('error', 'Data ujian tidak ditemukan!');
    }

    // Ambil detail jawaban siswa
    $jawabanSiswa = $this->detailJawabanModel
      ->select('detail_jawaban.*, bank_soal.*, detail_jawaban.theta_saat_ini, detail_jawaban.se_saat_ini')
      ->join('bank_soal', 'bank_soal.soal_id = detail_jawaban.soal_id')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->findAll();

    if ($pesertaUjian['is_cat']) {
      // Ambil data estimasi CAT
      $catEstimation = $this->catEstimationModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->first();

      $data = [
        'peserta_ujian' => $pesertaUjian,
        'jawaban_siswa' => $jawabanSiswa,
        'cat_estimation' => $catEstimation
      ];
    } else {
      $data = [
        'peserta_ujian' => $pesertaUjian,
        'jawaban_siswa' => $jawabanSiswa
      ];
    }

    return view('siswa/review_ujian', $data);
  }
}
