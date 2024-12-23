<?php

namespace App\Controllers\Guru;

use CodeIgniter\Controller;
use App\Models\BankSoalModel;
use App\Models\JenisUjianModel;
use App\Models\GuruModel;
use App\Models\JadwalUjianModel;
use App\Models\KelasModel;

class Guru extends Controller
{
    protected $bankSoalModel;
    protected $jenisUjianModel;
    protected $guruModel;
    protected $jadwalUjianModel;
    protected $kelasModel;


    public function __construct()
    {
        $this->bankSoalModel = new BankSoalModel();
        $this->jenisUjianModel = new JenisUjianModel();
        $this->guruModel = new GuruModel();
        $this->jadwalUjianModel = new JadwalUjianModel();
        $this->kelasModel = new KelasModel();
    }

    public function dashboard()
    {
        $ujian_today = $this->jadwalUjianModel->getUjianToday($this->guruModel->id_saya());
        $total_bankSoal = $this->bankSoalModel->getSoalByGuru($this->guruModel->id_saya());
        $total_bankSoal = count($total_bankSoal);
        $UpcomingUjian = $this->jadwalUjianModel->getUpcomingUjian($this->guruModel->id_saya());
        $data = [
            'ujian_today' => $ujian_today,
            'siswa' => '2',
            'soal' => $total_bankSoal,
            'upcoming_ujian' => $UpcomingUjian
        ];
        // return view('guru/dashboard');
        return view('guru/dashboard', $data);
    }

    public function ujianAktif()
    {
        return view('guru/ujian_aktif');
    }

    public function bankSoal()
    {
        $data = [
            'title' => 'Bank Soal',
            'jenis_ujian' => $this->jenisUjianModel->findAll(),
        ];

        return view('guru/bank_soal', $data);
    }

    public function formTambahSoal()
    {
        $guru_id = $this->guruModel->id_saya();
        $data = [
            'title' => 'Tambah Soal',
            'soal' => $this->bankSoalModel->getSoalByGuru($guru_id) // Get soal for this guru only

        ];

        return view('guru/daftar_soal', $data);
    }

    public function tambahSoal()
    {
        if (!$this->validate([
            'jenis_ujian_id' => 'required',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D]',
            // 'tingkat_kesulitan' => 'required|integer|between[1,5]'
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $data = [
            'guru_id' => $this->guruModel->id_saya(),
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan_b' => NULL,
            'discrimination' => NULL,
            'difficulty' => NULL,
            'guessing' => NULL,
            'daya_beda' => NULL,
        ];
        if ($this->bankSoalModel->insert($data)) {
            return redirect()->to('/guru/bank-soal')->with('success', 'Soal berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan soal');
    }

    public function deleteSoal($id)
    {
        if ($this->request->isAJAX()) {
            if ($this->bankSoalModel->delete($id)) {
                return $this->response->setJSON(['success' => true]);
            }
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setStatusCode(404);
    }

    public function jadwalUjian()
    {
        $data = [
            'title' => 'Jadwal Ujian',
            'ujian' => $this->jadwalUjianModel->getJadwalUjianByGuru($this->guruModel->id_saya())
        ];
        return view('guru/jadwal_ujian', $data);
    }

    public function jadwalUjianTambah()
    {
        $data = [
            'title' => 'Tambah Jadwal Ujian',
            'ujian' => $this->jenisUjianModel->findAll(),
            'jenis_ujian' => $this->jenisUjianModel->findAll(),
            'kelas' => $this->kelasModel->getKelas(),

        ];
        return view('guru/tambah_jadwal_ujian', $data);
    }

    public function jadwalUjianTambahProses()
    {
        if (!$this->validate([
            'jenis_ujian_id' => 'required',
            'kelas_id' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'durasi_menit' => 'required|integer'
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $data = [
            'guru_id' => $this->guruModel->id_saya(),
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'status' => 'belum_dimulai'
        ];

        if ($this->jadwalUjianModel->insert($data)) {
            return redirect()->to('/guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal ujian');
    }

    public function hasilUjian()
    {
        return view('guru/hasil_ujian');
    }

    public function profil()
    {
        return view('guru/profil');
    }
}
