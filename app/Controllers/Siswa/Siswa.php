<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;
use App\Models\JadwalUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\SoalUjianModel;
use App\Models\HasilUjianModel;

class Siswa extends Controller
{
  protected $jadwalUjianModel;
  protected $pesertaUjianModel;
  protected $siswaModel;
  protected $kelasModel;
  protected $soalUjianModel;
  protected $hasilUjianModel;

  public function __construct()
  {
    $this->jadwalUjianModel = new JadwalUjianModel();
    $this->pesertaUjianModel = new PesertaUjianModel();
    $this->siswaModel = new SiswaModel();
    $this->kelasModel = new KelasModel();
    $this->soalUjianModel = new SoalUjianModel();
    $this->hasilUjianModel = new HasilUjianModel();
  }

  //dashboard

  public function dashboard()
  {
    return view('siswa/dashboard');
  }

  //pengumuman

  public function pengumuman()
  {
    $pengumumanModel = new \App\Models\PengumumanModel();
    $data['pengumuman'] = $pengumumanModel->getPengumumanWithUser();
    return view('siswa/pengumuman', $data);
  }

  //logic simpan profil/ ubah profil
  public function saveProfil()
  {
    $userId = session()->get('user_id');
    $data = [
      'user_id' => $userId,
      'nomor_peserta' => $this->request->getPost('nomor_peserta'),
      'nama_lengkap' => $this->request->getPost('nama_lengkap'),
      'kelas_id' => $this->request->getPost('kelas_id')
    ];
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

  //Tampilan awal ujian
  public function ujian()
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    //kalo belum isi profil, arakan ke profil
    if (!$siswa) {
      session()->setFlashdata('error', 'Silahkan lengkapi profil Anda terlebih dahulu');
      return redirect()->to(base_url('siswa/profil'));
    }

    //gabungkan data jadwal ujian dengan status peserta
    $jadwalUjian = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi, ujian.durasi, peserta_ujian.status as status_peserta')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('peserta_ujian', 'peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.siswa_id = ' . $siswa['siswa_id'], 'left')
      ->where('jadwal_ujian.kelas_id', $siswa['kelas_id'])
      ->where('jadwal_ujian.tanggal_selesai >=', date('Y-m-d H:i:s'))
      ->where('jadwal_ujian.status !=', 'selesai')
      ->findAll();

    $data = [
      'jadwalUjian' => $jadwalUjian,
      'siswa' => $siswa
    ];

    return view('siswa/ujian', $data);
  }


  //Cek and re check sebelum mulai ujian
  public function mulaiUjian()
  {
    // 1. Debug untuk melihat session user_id
    if (!session()->get('user_id')) {
      session()->setFlashdata('error', 'Silahkan login terlebih dahulu');
      return redirect()->to(base_url('login'));
    }

    // 2. Ambil siswa_id dengan pengecekan
    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    if (!$siswa) {
      session()->setFlashdata('error', 'Data siswa tidak ditemukan. Silahkan lengkapi profil terlebih dahulu');
      return redirect()->to(base_url('siswa/profil'));
    }

    // 3. Ambil data dari form dengan validasi
    $jadwalId = $this->request->getPost('jadwal_id');
    $kodeAkses = $this->request->getPost('kode_akses');

    if (!$jadwalId || !$kodeAkses) {
      session()->setFlashdata('error', 'Data tidak lengkap');
      return redirect()->back();
    }

    // 4. Validasi kode akses
    $jadwal = $this->jadwalUjianModel->find($jadwalId);
    if (!$jadwal || $jadwal['kode_akses'] != $kodeAkses) {
      session()->setFlashdata('error', 'Kode akses ujian tidak valid!');
      return redirect()->back();
    }

    // 5. Cek apakah sudah terdaftar sebagai peserta
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    try {
      if (!$peserta) {
        // 6. Daftarkan sebagai peserta baru dengan pengecekan data
        $dataPeserta = [
          'jadwal_id' => $jadwalId,
          'siswa_id' => $siswa['siswa_id'],
          'status' => 'belum_mulai'
        ];

        // Debug data sebelum insert
        log_message('debug', 'Data peserta yang akan diinsert: ' . print_r($dataPeserta, true));

        $this->pesertaUjianModel->insert($dataPeserta);
      }

      // 7. Redirect ke halaman soal
      return redirect()->to(base_url("siswa/ujian/soal/$jadwalId"));
    } catch (\Exception $e) {
      // 8. Tangkap error jika terjadi masalah
      log_message('error', 'Error saat mendaftarkan peserta: ' . $e->getMessage());
      session()->setFlashdata('error', 'Terjadi kesalahan saat memulai ujian. Silahkan coba lagi.');
      return redirect()->back();
    }
  }

  //menu awal untuk ketika masuk ke soal
  public function soal($jadwalId)
  {
    // Simpan jadwal_id ke session untuk digunakan saat menyimpan jawaban
    session()->set('current_jadwal_id', $jadwalId);


    // 1. Validasi akses dan session
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // 2. Ambil data siswa
    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    if (!$siswa) {
      session()->setFlashdata('error', 'Data siswa tidak ditemukan');
      return redirect()->to(base_url('siswa/profil'));
    }

    // 3. Ambil informasi ujian dan jadwal di awal
    $ujianInfo = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.*, jenis_ujian.nama_jenis')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->first();

    if (!$ujianInfo) {
      session()->setFlashdata('error', 'Data ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 4. Cek status peserta
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    if (!$peserta) {
      session()->setFlashdata('error', 'Anda belum terdaftar sebagai peserta ujian');
      return redirect()->to(base_url('siswa/ujian'));
    }

    if ($peserta['status'] === 'selesai') {
      session()->setFlashdata('error', 'Anda sudah menyelesaikan ujian ini');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 5. Set parameter awal jika baru mulai
    if ($peserta['status'] === 'belum_mulai') {
      // Set waktu mulai
      $waktuMulai = date('Y-m-d H:i:s');

      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => $waktuMulai
      ]);

      $catParams = [
        'theta' => 0,
        'SE' => 1,
        'answered_questions' => [],
        'current_question' => null,
        'total_questions' => 0
      ];
      session()->set('cat_params', $catParams);
    } else {
      $waktuMulai = $peserta['waktu_mulai'];
    }

    // 6. Ambil CAT params dari session dengan validasi
    $catParams = session()->get('cat_params');

    // Jika cat_params belum ada atau null, inisialisasi dengan nilai default
    if (!$catParams) {
      $catParams = [
        'theta' => 0,
        'SE' => 1,
        'answered_questions' => [],
        'current_question' => null,
        'total_questions' => 0
      ];
      session()->set('cat_params', $catParams);
    }

    // 7. Pilih soal berikutnya jika belum ada
    if (!isset($catParams['current_question']) || $catParams['current_question'] === null) {
      // Untuk soal pertama, cari yang paling dekat dengan 0
      $nextQuestion = $this->soalUjianModel
        ->select('*, ABS(tingkat_kesulitan - 0) as distance')  // Hitung jarak dari 0
        ->where('ujian_id', $ujianInfo['id_ujian'])
        ->orderBy('distance', 'ASC')  // Urutkan berdasarkan jarak terdekat dengan 0
        ->first();

      if ($nextQuestion) {
        $catParams['current_question'] = $nextQuestion;
        session()->set('cat_params', $catParams);
      } else {
        session()->setFlashdata('error', 'Tidak ada soal yang tersedia');
        return redirect()->to(base_url('siswa/ujian'));
      }
    }

    // 8. Hitung sisa waktu
    if (!$waktuMulai) {
      // Jika waktu_mulai belum ada, set waktu sekarang
      $waktuMulai = date('Y-m-d H:i:s');
      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'waktu_mulai' => $waktuMulai
      ]);
    }

    // Konversi durasi dari format HH:MM:SS ke detik
    $durasi = explode(':', $ujianInfo['durasi']);
    $durasiDetik = ($durasi[0] * 3600) + ($durasi[1] * 60) + (isset($durasi[2]) ? $durasi[2] : 0);

    // Hitung sisa waktu
    $waktuMulaiTimestamp = strtotime($waktuMulai);
    $waktuSelesai = $waktuMulaiTimestamp + $durasiDetik;
    $sisaWaktu = $waktuSelesai - time();

    // Jika waktu sudah habis, arahkan ke halaman selesai
    if ($sisaWaktu <= 0) {
      // Update status peserta
      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s')
      ]);

      // Hapus session CAT
      session()->remove('cat_params');

      return redirect()->to(base_url("siswa/ujian/selesai/{$jadwalId}"));
    }


    // 9. Siapkan data untuk view
    $data = [
      'ujian' => $ujianInfo,
      'soal' => $catParams['current_question'],
      'sisa_waktu' => $sisaWaktu,
      'total_soal' => 'Adaptif',
      'soal_dijawab' => count($catParams['answered_questions'])
    ];

    // 10. Tampilkan view
    return view('siswa/soal', $data);
  }

  public function simpanJawaban()
  {
    // Debug untuk melihat input
    log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));

    // 1. Validasi input
    $soalId = $this->request->getPost('soal_id');
    $jawaban = $this->request->getPost('jawaban');

    if (!$soalId || !$jawaban) {
      session()->setFlashdata('error', 'Data jawaban tidak lengkap');
      return redirect()->back();
    }

    // 2. Ambil data soal dengan validasi
    $soal = $this->soalUjianModel->find($soalId);
    if (!$soal) {
      session()->setFlashdata('error', 'Soal tidak ditemukan');
      return redirect()->back();
    }

    // Ambil jadwal_id dari URL (yang disimpan dalam session saat akses soal)
    $current_jadwal_id = session()->get('current_jadwal_id');
    if (!$current_jadwal_id) {
      session()->setFlashdata('error', 'Data jadwal ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 2.1 Ambil info ujian dengan jadwal_id yang benar
    $ujianInfo = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.*')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->where('jadwal_ujian.jadwal_id', $current_jadwal_id)
      ->first();

    if (!$ujianInfo) {
      session()->setFlashdata('error', 'Data ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 3. Ambil CAT params dari session dengan validasi
    $catParams = session()->get('cat_params');
    if (!$catParams) {
      session()->setFlashdata('error', 'Parameter ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 4. Cek jawaban
    $isBenar = ($jawaban === $soal['jawaban_benar']);

    try {
      // 5. Hitung parameter CAT
      $theta = $catParams['theta'];
      $b = $soal['tingkat_kesulitan'];

      // 6. Hitung probabilitas
      $e = 2.71828;
      $Pi = pow($e, ($theta - $b)) / (1 + pow($e, ($theta - $b)));
      $Qi = 1 - $Pi;

      // 7. Hitung fungsi informasi
      $totalIi = 0;
      foreach ($catParams['answered_questions'] as $answeredSoalId) {
        $answeredSoal = $this->soalUjianModel->find($answeredSoalId);
        $bi = $answeredSoal['tingkat_kesulitan'];

        // Hitung Pi dan Qi untuk setiap soal yang sudah dijawab
        $Pi_answered = pow($e, ($theta - $bi)) / (1 + pow($e, ($theta - $bi)));
        $Qi_answered = 1 - $Pi_answered;

        // Tambahkan ke total informasi
        $totalIi += ($Pi_answered * $Qi_answered);
      }

      // Tambahkan informasi soal saat ini
      $totalIi += ($Pi * $Qi);

      // 8. Hitung SE baru
      $SE_old = $catParams['SE'];
      $SE_new = 1 / sqrt($totalIi);
      $delta_SE = $SE_old - $SE_new;

      // Debug info
      log_message('debug', 'Total Information: ' . $totalIi);
      log_message('debug', 'SE_new: ' . $SE_new);
      log_message('debug', 'Delta SE: ' . $delta_SE);

      // 9. Pilih soal berikutnya berdasarkan jawaban
      if ($isBenar) {
        $theta = $b;
        //didapat dari tetha = bi + 1/D.alpha ln(0,5(1 + akar (1 + 8c)))
        //karena logistic 1 PL, maka aplha = 1, D=1.7, c=0
        //ln(1) = 0
        //maka tetha = bi

        // Jika benar, cari soal lebih sulit
        $nextQuestion = $this->soalUjianModel
          ->select('*, ABS(tingkat_kesulitan - ' . ($b + 0.01) . ') as distance')
          ->where('ujian_id', $soal['ujian_id'])
          ->where('tingkat_kesulitan >', $b);

        if (!empty($catParams['answered_questions'])) {
          $nextQuestion->whereNotIn('soal_id', $catParams['answered_questions']);
        }

        $nextQuestion = $nextQuestion->orderBy('tingkat_kesulitan', 'ASC')
          ->first();
      } else {
        // Jika salah, update theta dan cari soal lebih mudah
        $theta = $b;
        $nextQuestion = $this->soalUjianModel
          ->select('*, ABS(tingkat_kesulitan - ' . ($b - 0.01) . ') as distance')
          ->where('ujian_id', $soal['ujian_id'])
          ->where('tingkat_kesulitan <', $b);

        if (!empty($catParams['answered_questions'])) {
          $nextQuestion->whereNotIn('soal_id', $catParams['answered_questions']);
        }

        $nextQuestion = $nextQuestion->orderBy('tingkat_kesulitan', 'DESC')
          ->first();
      }

      // Debug info sebelum update
      log_message('debug', 'Total Questions before: ' . $catParams['total_questions']);
      log_message('debug', 'Maksimal Soal: ' . $ujianInfo['maksimal_soal_tampil']);

      // Update CAT parameters
      $catParams['theta'] = $theta;
      $catParams['SE'] = $SE_new;
      if (!in_array($soalId, $catParams['answered_questions'])) {
        $catParams['answered_questions'][] = $soalId;
      }
      $catParams['current_question'] = $nextQuestion;
      $catParams['total_questions'] = count($catParams['answered_questions']);

      // Debug info setelah update
      log_message('debug', 'Total Questions after: ' . $catParams['total_questions']);

      // Cek kondisi berhenti dengan lebih ketat
      $shouldStop = false;

      // 1. Cek maksimal soal
      // if ($catParams['total_questions'] >= (int)$ujianInfo['maksimal_soal_tampil']) {
      //   log_message('debug', 'Stopping: Reached max questions');
      //   $shouldStop = true;
      // }


      // 2. Cek SE target
      if ($SE_new < (float)$ujianInfo['se_minimum']) {
        log_message('debug', 'Stopping: SE below minimum');
        $shouldStop = true;
      }

      // 3. Cek Delta SE
      else if (abs($delta_SE) < (float)$ujianInfo['delta_se_minimum']) {
        log_message('debug', 'Stopping: Delta SE below minimum');
        $shouldStop = true;
      }

      // 4. Cek waktu
      else if (!$nextQuestion) {
        log_message('debug', 'Stopping: No more questions');
        $shouldStop = true;
      }

      // Update session dengan parameter terbaru
      session()->set('cat_params', $catParams);

      try {
        // Ambil peserta_ujian_id
        $siswaId = $this->siswaModel->where('user_id', session()->get('user_id'))->first()['siswa_id'];
        $peserta = $this->pesertaUjianModel
          ->where('jadwal_id', $ujianInfo['jadwal_id'])
          ->where('siswa_id', $siswaId)
          ->first();

        // Hitung ulang probabilitas dan fungsi informasi berdasarkan theta terbaru
        $e = 2.71828;
        $updated_Pi = pow($e, ($theta - $b)) / (1 + pow($e, ($theta - $b)));
        $updated_Qi = 1 - $updated_Pi;
        $updated_Ii = $updated_Pi * $updated_Qi;

        // Simpan hasil jawaban ke tabel hasil_ujian
        $dataHasil = [
          'peserta_ujian_id' => $peserta['peserta_ujian_id'],
          'soal_id' => $soalId,
          'jawaban_siswa' => $jawaban,
          'is_correct' => $isBenar,
          'theta_saat_ini' => $theta,
          'pi_saat_ini' => $updated_Pi,
          'qi_saat_ini' => $updated_Qi,
          'ii_saat_ini' => $updated_Ii,
          'se_saat_ini' => $SE_new,
          'delta_se_saat_ini' => $delta_SE
        ];

        // Debug info
        log_message('debug', 'Saving hasil ujian: ' . print_r($dataHasil, true));

        $this->hasilUjianModel->insert($dataHasil);
      } catch (\Exception $e) {
        log_message('error', 'Error saving hasil ujian: ' . $e->getMessage());
      }

      if ($shouldStop) {
        // Update status peserta menjadi selesai
        $siswaId = $this->siswaModel->where('user_id', session()->get('user_id'))->first()['siswa_id'];

        $peserta = $this->pesertaUjianModel
          ->where('jadwal_id', $ujianInfo['jadwal_id'])
          ->where('siswa_id', $siswaId)
          ->first();

        if ($peserta) {
          $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s')
          ]);
        }

        return redirect()->to(base_url("siswa/ujian/selesai/{$ujianInfo['jadwal_id']}"));
      }

      // Lanjut ke soal berikutnya
      return redirect()->back();
    } catch (\Exception $e) {
      log_message('error', 'Error saat memproses jawaban: ' . $e->getMessage());
      session()->setFlashdata('error', 'Terjadi kesalahan saat memproses jawaban');
      return redirect()->back();
    }
  }

  public function selesaiUjian($jadwalId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // 1. Ambil data peserta
    $siswaId = $this->siswaModel->where('user_id', session()->get('user_id'))->first()['siswa_id'];
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswaId)
      ->first();

    if (!$peserta) {
      session()->setFlashdata('error', 'Data peserta tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 2. Update status peserta menjadi selesai
    $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
      'status' => 'selesai',
      'waktu_selesai' => date('Y-m-d H:i:s')
    ]);

    // 3. Ambil informasi ujian untuk ditampilkan
    $ujianInfo = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->first();

    // 4. Hitung nilai akhir dari CAT params
    $catParams = session()->get('cat_params');
    $nilaiAkhir = $catParams ? $catParams['theta'] : 0;

    // 5. Clear session CAT
    session()->remove('cat_params');

    $data = [
      'ujian' => $ujianInfo,
      'peserta' => $peserta,
      'nilai_akhir' => $nilaiAkhir,
      'total_soal' => count($catParams['answered_questions'])
    ];

    return view('siswa/selesai_ujian', $data);
  }

  public function hasil()
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    // Tambahkan kolom durasi dari tabel ujian
    $riwayatUjian = $this->pesertaUjianModel
      ->select('
            peserta_ujian.*, 
            jadwal_ujian.*, 
            ujian.nama_ujian, 
            ujian.deskripsi, 
            ujian.durasi,
            jenis_ujian.nama_jenis,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_pengerjaan
        ')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.siswa_id', $siswa['siswa_id'])
      ->where('peserta_ujian.status', 'selesai')
      ->orderBy('peserta_ujian.waktu_selesai', 'DESC')
      ->findAll();

    $data = [
      'riwayatUjian' => $riwayatUjian
    ];

    return view('siswa/hasil', $data);
  }

  public function detailHasil($pesertaUjianId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // Ambil detail hasil ujian
    $hasil = $this->pesertaUjianModel
      ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, jenis_ujian.nama_jenis')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
      ->first();

    // Ambil detail jawaban
    $detailJawaban = $this->hasilUjianModel
      ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.jawaban_benar, soal_ujian.tingkat_kesulitan')
      ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
      ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
      ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
      ->findAll();

    $data = [
      'hasil' => $hasil,
      'detailJawaban' => $detailJawaban,
      'totalSoal' => count($detailJawaban),
      'jawabanBenar' => array_reduce($detailJawaban, function ($carry, $item) {
        return $carry + ($item['is_correct'] ? 1 : 0);
      }, 0)
    ];

    return view('siswa/detail_hasil', $data);
  }
}
