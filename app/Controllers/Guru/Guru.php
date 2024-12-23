<?php

namespace App\Controllers\Guru;

use CodeIgniter\Controller;
use App\Models\BankSoalModel;
use App\Models\JenisUjianModel;

class Guru extends Controller
{
    protected $bankSoalModel;
    protected $jenisUjianModel;

    public function __construct()
    {
        $this->bankSoalModel = new BankSoalModel();
        $this->jenisUjianModel = new JenisUjianModel();
    }

    public function dashboard()
    {
        return view('guru/dashboard');
    }

    public function ujianAktif()
    {
        return view('guru/ujian_aktif');
    }

    public function bankSoal()
    {
        // Get the logged in guru_id (adjust according to your authentication system)
        $guru_id = session()->get('guru_id');

        $data = [
            'title' => 'Bank Soal',
            'jenis_ujian' => $this->jenisUjianModel->findAll(),
            'soal' => $this->bankSoalModel->getSoalByGuru($guru_id) // Get soal for this guru only
        ];

        return view('guru/bank_soal', $data);
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
            'tingkat_kesulitan' => 'required|integer|between[1,5]'
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $data = [
            'guru_id' => session()->get('user_id'), // Sesuaikan dengan sistem auth Anda
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan')
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
        return view('guru/jadwal_ujian');
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
