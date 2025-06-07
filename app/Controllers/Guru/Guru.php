<?php

namespace App\Controllers\Guru;

use CodeIgniter\Controller;
use App\Models\JenisUjianModel;
use App\Models\UjianModel;
use App\Models\SoalUjianModel;
use App\Models\KelasModel;
use App\Models\JadwalUjianModel;
use App\Models\GuruModel;
use App\Models\PengumumanModel;
use App\Models\HasilUjianModel;
use App\Models\PesertaUjianModel;

class Guru extends Controller
{
    protected $jenisUjianModel;
    protected $ujianModel;
    protected $soalUjianModel;
    protected $jadwalUjianModel;
    protected $kelasModel;
    protected $guruModel;
    protected $pengumumanModel;
    protected $hasilUjianModel;
    protected $pesertaUjianModel;
    protected $db;

    public function __construct()
    {
        $this->jenisUjianModel = new JenisUjianModel();
        $this->ujianModel = new UjianModel();
        $this->soalUjianModel = new SoalUjianModel();
        $this->jadwalUjianModel = new JadwalUjianModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->pengumumanModel = new PengumumanModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->pesertaUjianModel = new PesertaUjianModel();
        $this->db = \Config\Database::connect();
    }

    public function dashboard()
    {
        return view('guru/dashboard');
    }

    public function ujian()
    {
        $data['ujian'] = $this->ujianModel->findAll();
        $data['jenis_ujian'] = $this->jenisUjianModel->findAll();
        return view('guru/ujian', $data);
    }

    public function tambahUjian()
    {
        $data = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            //'maksimal_soal_tampil' => $this->request->getPost('maksimal_soal_tampil'),
            'durasi' => $this->request->getPost('durasi')
        ];
        $this->ujianModel->insert($data);
        return redirect()->to('guru/ujian')->with('success', 'Ujian berhasil ditambahkan');
    }

    public function editUjian($id)
    {
        $data = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            //'maksimal_soal_tampil' => $this->request->getPost('maksimal_soal_tampil'),
            'durasi' => $this->request->getPost('durasi')
        ];
        $this->ujianModel->update($id, $data);
        return redirect()->to('guru/ujian')->with('success', 'Ujian berhasil diupdate');
    }

    public function hapusUjian($id)
    {
        $soalTerkait = $this->soalUjianModel->where('ujian_id', $id)->countAllResults();

        if ($soalTerkait > 0) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Tidak dapat menghapus ujian ini karena masih ada ' . $soalTerkait . ' soal yang terkait. Harap hapus soal-soal ujian terlebih dahulu.');
        }

        try {
            $this->ujianModel->delete($id);
            return redirect()->to('guru/ujian')
                ->with('success', 'Ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Terjadi kesalahan saat menghapus ujian');
        }
    }

    //kelola soal

    public function kelolaSoal($ujian_id)
    {
        $data['ujian'] = $this->ujianModel->find($ujian_id);
        $data['soal'] = $this->soalUjianModel->where('ujian_id', $ujian_id)->findAll();
        return view('guru/kelola_soal', $data);
    }

    public function tambahSoal()
    {
        // Validasi form input
        $rules = [
            'ujian_id' => 'required|numeric',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'pilihan_e' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        // Jika validasi gagal, kembalikan pesan error
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Ambil data dari form
        $data = [
            'ujian_id' => $this->request->getPost('ujian_id'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // Upload foto jika ada
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
            // Generate nama file unik
            $newName = $fotoFile->getRandomName();

            // Lokasi penyimpanan yang benar (relatif terhadap public/)
            $uploadPath = 'uploads/soal';

            // Buat direktori upload jika belum ada
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Pindahkan ke direktori upload
            $fotoFile->move($uploadPath, $newName);

            // Simpan nama file ke database
            $data['foto'] = $newName;
        }

        // Simpan data soal ke database
        try {
            $this->soalUjianModel->insert($data);
            return redirect()->to('guru/soal/' . $data['ujian_id'])->with('success', 'Soal berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan soal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal.');
        }
    }

    public function editSoal($id)
    {
        // Validasi form input
        $rules = [
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'pilihan_e' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        // Jika validasi gagal, kembalikan pesan error
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Ambil data soal yang akan diedit
        $soal = $this->soalUjianModel->find($id);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Ambil data dari form
        $data = [
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // Lokasi penyimpanan yang benar (relatif terhadap public/)
        $uploadPath = 'uploads/soal';

        // Upload foto jika ada
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
            // Hapus foto lama jika ada
            if (!empty($soal['foto'])) {
                $fotoPath = $uploadPath . '/' . $soal['foto'];
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }

            // Generate nama file unik
            $newName = $fotoFile->getRandomName();

            // Buat direktori upload jika belum ada
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Pindahkan ke direktori upload
            $fotoFile->move($uploadPath, $newName);

            // Simpan nama file ke database
            $data['foto'] = $newName;
        }

        // Checkbox untuk menghapus foto
        if ($this->request->getPost('hapus_foto') == '1' && !empty($soal['foto'])) {
            // Hapus file fisik
            $fotoPath = $uploadPath . '/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }

            // Set nilai foto menjadi null di database
            $data['foto'] = null;
        }

        // Update data soal di database
        try {
            $this->soalUjianModel->update($id, $data);
            $ujian_id = $this->request->getPost('ujian_id');
            return redirect()->to('guru/soal/' . $ujian_id)->with('success', 'Soal berhasil diupdate');
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate soal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal.');
        }
    }

    public function hapusSoal($id, $ujian_id)
    {
        $this->soalUjianModel->delete($id);
        return redirect()->to('guru/soal/' . $ujian_id)->with('success', 'Soal berhasil dihapus');
    }


    //jadwal

    public function jadwalUjian()
    {
        $data['jadwal'] = $this->jadwalUjianModel->getJadwalWithRelations();

        // Daftar ujian untuk modal tambah: hanya ujian yang belum memiliki jadwal
        // $data['ujian_tambah'] = $this->ujianModel
        //     ->select('ujian.*')
        //     ->join('jadwal_ujian', 'jadwal_ujian.ujian_id = ujian.id_ujian', 'left')
        //     ->where('jadwal_ujian.jadwal_id IS NULL')
        //     ->findAll();

        // Daftar ujian untuk modal tambah: menampilkan semua ujian
        $data['ujian_tambah'] = $this->ujianModel->findAll();


        // Daftar ujian untuk modal edit: semua ujian
        $data['ujian_edit'] = $this->ujianModel->findAll();

        $data['kelas'] = $this->kelasModel->findAll();
        $data['guru'] = $this->guruModel->findAll();

        return view('guru/jadwal_ujian', $data);
    }


    public function tambahJadwal()
    {
        $ujian_id = $this->request->getPost('ujian_id');
        $kelas_id = $this->request->getPost('kelas_id');

        // Cek apakah kombinasi ujian_id dan kelas_id sudah ada
        $existing = $this->jadwalUjianModel
            ->where('ujian_id', $ujian_id)
            ->where('kelas_id', $kelas_id)
            ->first();

        if ($existing) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Jadwal ujian untuk kelas ini sudah ada. Pilih kelas lain atau ujian lain.');
        }

        $data = [
            'ujian_id' => $ujian_id,
            'kelas_id' => $kelas_id,
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
            'status' => 'belum_mulai'
        ];

        $this->jadwalUjianModel->insert($data);
        return redirect()->to('guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil ditambahkan');
    }

    public function editJadwal($id)
    {
        $ujian_id = $this->request->getPost('ujian_id');
        $kelas_id = $this->request->getPost('kelas_id');

        // Cek apakah kombinasi ujian_id dan kelas_id sudah ada, kecuali untuk jadwal yang sedang diedit
        $existing = $this->jadwalUjianModel
            ->where('ujian_id', $ujian_id)
            ->where('kelas_id', $kelas_id)
            ->where('jadwal_id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Jadwal ujian untuk kelas ini sudah ada. Pilih kelas lain atau ujian lain.');
        }

        $data = [
            'ujian_id' => $ujian_id,
            'kelas_id' => $kelas_id,
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
            'status' => $this->request->getPost('status')
        ];

        $this->jadwalUjianModel->update($id, $data);
        return redirect()->to('guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil diupdate');
    }

    public function hapusJadwal($id)
    {
        $this->jadwalUjianModel->delete($id);
        return redirect()->to('guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil dihapus');
    }


    //hasil ujian

    public function hasilUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Ambil daftar ujian yang sudah pernah dijadwalkan oleh guru ini
        $daftarUjian = $this->jadwalUjianModel
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi, jenis_ujian.nama_jenis, kelas.nama_kelas,
                 (SELECT COUNT(*) FROM peserta_ujian WHERE peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.status = "selesai") as jumlah_peserta')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->where('jadwal_ujian.guru_id', $guru['guru_id'])
            ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
            ->findAll();

        return view('guru/hasil_ujian', ['daftarUjian' => $daftarUjian]);
    }

    public function daftarSiswa($jadwalId)
    {
        // Ambil data ujian
        $ujian = $this->jadwalUjianModel
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi, jenis_ujian.nama_jenis, kelas.nama_kelas')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->where('jadwal_ujian.jadwal_id', $jadwalId)
            ->first();

        // Ambil daftar siswa yang sudah selesai
        $daftarSiswa = $this->pesertaUjianModel
            ->select('peserta_ujian.*, siswa.nama_lengkap, siswa.nomor_peserta,
                 (SELECT COUNT(*) FROM hasil_ujian WHERE hasil_ujian.peserta_ujian_id = peserta_ujian.peserta_ujian_id) as jumlah_soal,
                 (SELECT COUNT(*) FROM hasil_ujian WHERE hasil_ujian.peserta_ujian_id = peserta_ujian.peserta_ujian_id AND hasil_ujian.is_correct = 1) as jawaban_benar')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->where('peserta_ujian.jadwal_id', $jadwalId)
            ->where('peserta_ujian.status', 'selesai')
            ->orderBy('peserta_ujian.waktu_selesai', 'ASC')
            ->findAll();

        return view('guru/daftar_siswa', [
            'ujian' => $ujian,
            'daftarSiswa' => $daftarSiswa
        ]);
    }

    public function detailHasil($pesertaUjianId)
    {
        // Ambil detail hasil ujian
        $hasil = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, jenis_ujian.nama_jenis, 
                 siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        // Ambil detail jawaban
        $detailJawaban = $this->hasilUjianModel
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.jawaban_benar, 
                 soal_ujian.tingkat_kesulitan')
            ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->findAll();

        return view('guru/detail_hasil', [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawaban
        ]);
    }

    //pengumuman

    public function pengumuman()
    {
        $data['pengumuman'] = $this->pengumumanModel->getPengumumanWithUser();
        return view('guru/pengumuman', $data);
    }

    public function tambahPengumuman()
    {
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
            'tanggal_publish' => $this->request->getPost('tanggal_publish'),
            'tanggal_berakhir' => $this->request->getPost('tanggal_berakhir'),
            'created_by' => session()->get('user_id')
        ];
        $this->pengumumanModel->insert($data);
        return redirect()->to('guru/pengumuman')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function editPengumuman($id)
    {
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
            'tanggal_publish' => $this->request->getPost('tanggal_publish'),
            'tanggal_berakhir' => $this->request->getPost('tanggal_berakhir')
        ];
        $this->pengumumanModel->update($id, $data);
        return redirect()->to('guru/pengumuman')->with('success', 'Pengumuman berhasil diupdate');
    }

    public function hapusPengumuman($id)
    {
        $this->pengumumanModel->delete($id);
        return redirect()->to('guru/pengumuman')->with('success', 'Pengumuman berhasil dihapus');
    }


    public function profil()
    {
        $userId = session()->get('user_id');

        // Ambil data guru dengan join ke users dan sekolah
        $guru = $this->guruModel
            ->select('guru.*, users.username, users.email, sekolah.nama_sekolah')
            ->join('users', 'users.user_id = guru.user_id')
            ->join('sekolah', 'sekolah.sekolah_id = guru.sekolah_id')
            ->where('guru.user_id', $userId)
            ->first();

        $data = [
            'guru' => $guru,
            'validation' => \Config\Services::validation()
        ];

        return view('guru/profil', $data);
    }

    public function saveProfil()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi input
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'nip' => 'required|min_length[5]',
            'mata_pelajaran' => 'required',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Update data users
        $this->db->table('users')->where('user_id', $userId)->update([
            'email' => $this->request->getPost('email')
        ]);

        // Update data guru
        $dataGuru = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip' => $this->request->getPost('nip'),
            'mata_pelajaran' => $this->request->getPost('mata_pelajaran')
        ];

        try {
            $this->guruModel->update($guru['guru_id'], $dataGuru);
            session()->setFlashdata('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan data.');
            log_message('error', $e->getMessage());
        }

        return redirect()->to(base_url('guru/profil'));
    }


    public function jenisUjian()
    {
        $data['jenis_ujian'] = $this->jenisUjianModel->findAll();
        return view('guru/jenis_ujian', $data);
    }

    public function tambahJenisUjian()
    {
        $data = [
            'nama_jenis' => $this->request->getPost('nama_jenis'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];
        $this->jenisUjianModel->insert($data);
        return redirect()->to('guru/jenis-ujian')->with('success', 'Jenis ujian berhasil ditambahkan');
    }

    public function editJenisUjian($id)
    {
        $data = [
            'nama_jenis' => $this->request->getPost('nama_jenis'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];
        $this->jenisUjianModel->update($id, $data);
        return redirect()->to('guru/jenis-ujian')->with('success', 'Jenis ujian berhasil diupdate');
    }

    public function hapusJenisUjian($id)
    {
        // Cek apakah ada ujian yang menggunakan jenis ujian ini
        $ujianTerkait = $this->db->table('ujian')
            ->where('jenis_ujian_id', $id)
            ->countAllResults();

        if ($ujianTerkait > 0) {
            // Jika ada ujian terkait, kirim pesan error
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Tidak dapat menghapus jenis ujian ini karena masih ada ' . $ujianTerkait . ' ujian yang menggunakan jenis ujian ini. Harap hapus ujian terkait terlebih dahulu.');
        }

        try {
            // Jika tidak ada ujian terkait, lakukan penghapusan
            $this->jenisUjianModel->delete($id);
            return redirect()->to('guru/jenis-ujian')
                ->with('success', 'Jenis ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Terjadi kesalahan saat menghapus jenis ujian');
        }
    }

    //method download hasil
    public function downloadExcelHTML($pesertaUjianId)
    {
        // Ambil detail hasil ujian
        $hasil = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, jenis_ujian.nama_jenis, 
             siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        // Ambil detail jawaban
        $detailJawaban = $this->hasilUjianModel
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.jawaban_benar, 
             soal_ujian.tingkat_kesulitan')
            ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->findAll();

        // Hitung nilai akhir
        $lastTheta = end($detailJawaban)['theta_saat_ini'];
        $finalScore = 50 + (16.6 * $lastTheta);

        // Hitung jumlah jawaban benar
        $jawabanBenar = array_reduce($detailJawaban, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        // Data untuk grafik
        $thetaData = json_encode(array_map(function ($item) {
            return $item['theta_saat_ini'];
        }, $detailJawaban));

        $seData = json_encode(array_map(function ($item) {
            return $item['se_saat_ini'];
        }, $detailJawaban));

        $labels = json_encode(array_map(function ($i) {
            return 'Soal ' . ($i + 1);
        }, range(0, count($detailJawaban) - 1)));

        // Data untuk view
        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawaban,
            'lastTheta' => $lastTheta,
            'finalScore' => $finalScore,
            'jawabanBenar' => $jawabanBenar,
            'thetaData' => $thetaData,
            'seData' => $seData,
            'labels' => $labels,
            'isExcel' => true
        ];

        // Nama file
        $filename = 'hasil_ujian_' . $hasil['nomor_peserta'] . '_' . date('dmY') . '.xls';

        // Set header untuk Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Load view dan keluarkan sebagai Excel
        echo view('guru/hasil_ujian_excel', $data);
        exit;
    }

    public function downloadPDFHTML($pesertaUjianId)
    {
        // Ambil detail hasil ujian
        $hasil = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, jenis_ujian.nama_jenis, 
             siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        // Ambil detail jawaban
        $detailJawaban = $this->hasilUjianModel
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.jawaban_benar, 
             soal_ujian.tingkat_kesulitan')
            ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->findAll();

        // Hitung nilai akhir
        $lastTheta = end($detailJawaban)['theta_saat_ini'];
        $finalScore = 50 + (16.6 * $lastTheta);

        // Hitung jumlah jawaban benar
        $jawabanBenar = array_reduce($detailJawaban, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        // Data untuk grafik
        $thetaData = json_encode(array_map(function ($item) {
            return $item['theta_saat_ini'];
        }, $detailJawaban));

        $seData = json_encode(array_map(function ($item) {
            return $item['se_saat_ini'];
        }, $detailJawaban));

        $labels = json_encode(array_map(function ($i) {
            return 'Soal ' . ($i + 1);
        }, range(0, count($detailJawaban) - 1)));

        // Data untuk view
        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawaban,
            'lastTheta' => $lastTheta,
            'finalScore' => $finalScore,
            'jawabanBenar' => $jawabanBenar,
            'thetaData' => $thetaData,
            'seData' => $seData,
            'labels' => $labels,
            'isPDF' => true
        ];

        // Load view ke variabel HTML
        $html = view('guru/hasil_ujian_pdf', $data);

        // Print mode (untuk bisa disimpan sebagai PDF dari browser)
        // Set header untuk browser
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="hasil_ujian_' . $hasil['nomor_peserta'] . '.html"');

        // Output HTML
        echo $html;
        exit;
    }
    
}
