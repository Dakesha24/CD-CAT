<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;
use App\Models\JadwalUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\BankSoalModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\DetailJawabanModel;

class Siswa extends Controller
{
  protected $jadwalUjianModel;
  protected $pesertaUjianModel;
  protected $bankSoalModel;
  protected $siswaModel;
  protected $kelasModel;
  protected $detailJawabanModel;

  public function __construct()
  {
    $this->jadwalUjianModel = new JadwalUjianModel();
    $this->pesertaUjianModel = new PesertaUjianModel();
    $this->bankSoalModel = new BankSoalModel();
    $this->siswaModel = new SiswaModel();
    $this->kelasModel = new KelasModel();
    $this->detailJawabanModel = new DetailJawabanModel();
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
      // Jika ditemukan record yang belum selesai, langsung arahkan ke halaman ujian
      return redirect()->to("siswa/ujian/soal/{$existingPeserta['peserta_ujian_id']}");
    }

    // Jika belum ada record, cek kode ujian
    $jadwal = $this->jadwalUjianModel->find($jadwalId);
    if (!$jadwal || $jadwal['kode_akses'] !== $kodeUjian) {
      return redirect()->back()->with('error', 'Kode ujian tidak valid!');
    }

    // Buat record baru jika belum ada
    $dataPeserta = [
      'jadwal_id' => $jadwalId,
      'siswa_id' => $siswa['siswa_id'],
      'status' => 'sedang_mengerjakan',
      'waktu_mulai' => date('Y-m-d H:i:s')
    ];

    $pesertaUjianId = $this->pesertaUjianModel->insert($dataPeserta);
    return redirect()->to("siswa/ujian/soal/$pesertaUjianId");
  }

  public function soal($pesertaUjianId)
  {
    // Perbaiki query untuk mengambil data peserta ujian
    $pesertaUjian = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jadwal_ujian.durasi_menit, jadwal_ujian.tanggal_mulai, 
                   jadwal_ujian.tanggal_selesai, jadwal_ujian.jenis_ujian_id, 
                   jenis_ujian.nama_ujian')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
      ->first();

    // Untuk debug query
    // $db = \Config\Database::connect();
    // echo $db->getLastQuery();
    // die();

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

    // Jika waktu sudah habis
    if ($sisaWaktu <= 0) {
      $this->pesertaUjianModel->update($pesertaUjianId, [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s')
      ]);
      return redirect()->to('siswa/hasil')->with('error', 'Waktu ujian telah habis!');
    }

    // Ambil soal
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
      'durasi_menit' => $pesertaUjian['durasi_menit']
    ];

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

      // Ambil jawaban benar
      $soal = $this->bankSoalModel->find($soalId);
      $isCorrect = ($jawaban === $soal['jawaban_benar']) ? 1 : 0;

      $data = [
        'peserta_ujian_id' => $pesertaUjianId,
        'soal_id' => $soalId,
        'jawaban_siswa' => $jawaban,
        'is_correct' => $isCorrect
      ];

      // Cek existing jawaban
      $existingJawaban = $this->detailJawabanModel
        ->where('peserta_ujian_id', $pesertaUjianId)
        ->where('soal_id', $soalId)
        ->first();

      if ($existingJawaban) {
        $this->detailJawabanModel->update($existingJawaban['jawaban_id'], $data);
      } else {
        $this->detailJawabanModel->insert($data);
      }

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Jawaban berhasil disimpan'
      ]);
    } catch (\Exception $e) {
      log_message('error', 'Error saving answer: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  public function selesaiUjian($pesertaUjianId)
  {
    $pesertaUjian = $this->pesertaUjianModel->find($pesertaUjianId);

    if (!$pesertaUjian) {
      return redirect()->to('siswa/ujian')->with('error', 'Sesi ujian tidak ditemukan!');
    }

    // Hitung total jawaban dan jawaban benar
    $totalSoal = $this->detailJawabanModel
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->countAllResults();

    $jawabanBenar = $this->detailJawabanModel
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->where('is_correct', true)
      ->countAllResults();

    // Hitung nilai (skala 0-100)
    $nilaiAkhir = $totalSoal > 0 ? ($jawabanBenar / $totalSoal) * 100 : 0;

    // Update status dan nilai
    $this->pesertaUjianModel->update($pesertaUjianId, [
      'status' => 'selesai',
      'waktu_selesai' => date('Y-m-d H:i:s'),
      'nilai_akhir' => $nilaiAkhir
    ]);

    // Redirect ke halaman hasil dengan flashdata
    session()->setFlashdata('success', 'Ujian telah selesai! Berikut adalah hasil ujian Anda.');
    return redirect()->to("siswa/hasil/review/$pesertaUjianId");
  }

  public function review($pesertaUjianId)
  {
    // Ambil data peserta ujian dengan detail
    $pesertaUjian = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jadwal_ujian.*, jenis_ujian.nama_ujian')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->first();

    if (!$pesertaUjian) {
      return redirect()->to('siswa/hasil')->with('error', 'Data ujian tidak ditemukan!');
    }

    // Ambil detail jawaban siswa
    $jawabanSiswa = $this->detailJawabanModel
      ->select('detail_jawaban.*, bank_soal.*')
      ->join('bank_soal', 'bank_soal.soal_id = detail_jawaban.soal_id')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->findAll();

    $data = [
      'peserta_ujian' => $pesertaUjian,
      'jawaban_siswa' => $jawabanSiswa
    ];

    return view('siswa/review_ujian', $data);
  }
}
