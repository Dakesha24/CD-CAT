<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\UjianModel;
use App\Models\SoalUjianModel;
use App\Models\JenisUjianModel;
use App\Models\JadwalUjianModel;
use App\Models\HasilUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\PengumumanModel;

class Admin extends Controller
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $ujianModel;
    protected $soalUjianModel;
    protected $jenisUjianModel;
    protected $jadwalUjianModel;
    protected $hasilUjianModel;
    protected $pesertaUjianModel;
    protected $pengumumanModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->ujianModel = new UjianModel();
        $this->soalUjianModel = new SoalUjianModel();
        $this->jenisUjianModel = new JenisUjianModel();
        $this->jadwalUjianModel = new JadwalUjianModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->pesertaUjianModel = new PesertaUjianModel();
        $this->pengumumanModel = new PengumumanModel();
    }

    public function dashboard()
    {
        $data['stats'] = $this->userModel->getDashboardStats();
        return view('admin/dashboard', $data);
    }

    // ===== KELOLA GURU =====

    public function daftarGuru()
    {
        $data['guru'] = $this->userModel->getGuruWithDetails();
        return view('admin/guru/daftar', $data);
    }

    public function formTambahGuru()
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        return view('admin/guru/tambah', $data);
    }

    public function tambahGuru()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'nama_lengkap' => 'required|min_length[3]',
            'nip' => 'permit_empty|is_unique[guru.nip]',
            'mata_pelajaran' => 'required',
            'sekolah_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Insert ke tabel users
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'guru',
                'status' => 'active'
            ];

            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // Insert ke tabel guru
                $guruData = [
                    'user_id' => $userId,
                    'sekolah_id' => $this->request->getPost('sekolah_id'),
                    'nip' => $this->request->getPost('nip') ?: null,
                    'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                    'mata_pelajaran' => $this->request->getPost('mata_pelajaran')
                ];

                $this->guruModel->insert($guruData);

                session()->setFlashdata('success', 'Guru berhasil ditambahkan!');
                return redirect()->to(base_url('admin/guru'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error adding guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah guru.');
            return redirect()->back()->withInput();
        }
    }

    public function formEditGuru($userId)
    {
        // Gunakan Database service langsung
        $db = \Config\Database::connect();

        $guru = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at, 
                     g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, g.sekolah_id,
                     s.nama_sekolah')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->join('sekolah s', 's.sekolah_id = g.sekolah_id', 'left')
            ->where('u.user_id', $userId)
            ->where('u.role', 'guru')
            ->get()
            ->getRowArray();

        // Debug: tampilkan data untuk troubleshooting
        log_message('debug', 'Guru data: ' . print_r($guru, true));

        if (!$guru) {
            session()->setFlashdata('error', 'Data guru tidak ditemukan');
            return redirect()->to(base_url('admin/guru'));
        }

        // Pastikan semua field ada dengan nilai default
        $defaultFields = [
            'user_id' => '',
            'username' => '',
            'email' => '',
            'status' => 'active',
            'guru_id' => '',
            'sekolah_id' => '',
            'nip' => '',
            'nama_lengkap' => '',
            'mata_pelajaran' => '',
            'nama_sekolah' => ''
        ];

        $guru = array_merge($defaultFields, $guru ?: []);

        $sekolahModel = new \App\Models\SekolahModel();
        $data['guru'] = $guru;
        $data['sekolah'] = $sekolahModel->findAll();

        return view('admin/guru/edit', $data);
    }

    public function editGuru($userId)
    {
        // Validasi input
        $rules = [
            'username' => "required|min_length[4]",
            'email'    => "required|valid_email",
            'nama_lengkap' => 'required|min_length[3]',
            'mata_pelajaran' => 'required',
            'sekolah_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();

            // Ambil data input
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $namaLengkap = $this->request->getPost('nama_lengkap');
            $nip = $this->request->getPost('nip');
            $mataPelajaran = $this->request->getPost('mata_pelajaran');
            $sekolahId = $this->request->getPost('sekolah_id');

            // Update tabel users dengan raw query
            $sqlUser = "UPDATE users SET username = ?, email = ?";
            $paramsUser = [$username, $email];

            if (!empty($password)) {
                $sqlUser .= ", password = ?";
                $paramsUser[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sqlUser .= " WHERE user_id = ?";
            $paramsUser[] = $userId;

            $db->query($sqlUser, $paramsUser);

            // Update tabel guru dengan raw query
            $sqlGuru = "UPDATE guru SET nama_lengkap = ?, nip = ?, mata_pelajaran = ?, sekolah_id = ? WHERE user_id = ?";
            $paramsGuru = [$namaLengkap, $nip, $mataPelajaran, $sekolahId, $userId];

            $db->query($sqlGuru, $paramsGuru);

            // Cek apakah ada baris yang terpengaruh
            $affectedRows = $db->affectedRows();

            log_message('debug', "Update guru - Affected rows: {$affectedRows}");
            log_message('debug', "Update data: " . json_encode([
                'username' => $username,
                'email' => $email,
                'nama_lengkap' => $namaLengkap,
                'nip' => $nip,
                'mata_pelajaran' => $mataPelajaran,
                'sekolah_id' => $sekolahId,
                'user_id' => $userId
            ]));

            session()->setFlashdata('success', 'Data guru berhasil diperbarui!');
            return redirect()->to(base_url('admin/guru'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function hapusGuru($userId)
    {
        try {
            $this->userModel->softDelete($userId);
            session()->setFlashdata('success', 'Guru berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menonaktifkan guru.');
        }

        return redirect()->to(base_url('admin/guru'));
    }

    public function restoreGuru($userId)
    {
        try {
            $this->userModel->restore($userId);
            session()->setFlashdata('success', 'Guru berhasil diaktifkan kembali!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengaktifkan guru.');
        }

        return redirect()->to(base_url('admin/guru'));
    }

    // ===== KELOLA SISWA =====

    public function daftarSiswa()
    {
        $data['siswa'] = $this->userModel->getSiswaWithDetails();
        return view('admin/siswa/daftar', $data);
    }

    public function formTambahSiswa()
    {
        $data['kelas'] = $this->kelasModel->findAll();
        return view('admin/siswa/tambah', $data);
    }

    public function tambahSiswa()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'nama_lengkap' => 'required|min_length[3]',
            'nomor_peserta' => 'required|is_unique[siswa.nomor_peserta]',
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Insert ke tabel users
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'siswa',
                'status' => 'active'
            ];

            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // Insert ke tabel siswa
                $siswaData = [
                    'user_id' => $userId,
                    'kelas_id' => $this->request->getPost('kelas_id'),
                    'nomor_peserta' => $this->request->getPost('nomor_peserta'),
                    'nama_lengkap' => $this->request->getPost('nama_lengkap')
                ];

                $this->siswaModel->insert($siswaData);

                session()->setFlashdata('success', 'Siswa berhasil ditambahkan!');
                return redirect()->to(base_url('admin/siswa'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error adding siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah siswa.');
            return redirect()->back()->withInput();
        }
    }

    public function formEditSiswa($userId)
    {
        $db = \Config\Database::connect();

        $siswa = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at, 
                     s.siswa_id, s.nomor_peserta, s.nama_lengkap, s.kelas_id,
                     k.nama_kelas, k.tahun_ajaran')
            ->join('siswa s', 's.user_id = u.user_id', 'left')
            ->join('kelas k', 'k.kelas_id = s.kelas_id', 'left')
            ->where('u.user_id', $userId)
            ->where('u.role', 'siswa')
            ->get()
            ->getRowArray();

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to(base_url('admin/siswa'));
        }

        // Set default values
        $defaultFields = [
            'user_id' => '',
            'username' => '',
            'email' => '',
            'status' => 'active',
            'siswa_id' => '',
            'kelas_id' => '',
            'nomor_peserta' => '',
            'nama_lengkap' => '',
            'nama_kelas' => '',
            'tahun_ajaran' => ''
        ];

        $siswa = array_merge($defaultFields, $siswa ?: []);

        $data['siswa'] = $siswa;
        $data['kelas'] = $this->kelasModel->findAll();

        return view('admin/siswa/edit', $data);
    }

    public function editSiswa($userId)
    {
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to(base_url('admin/siswa'));
        }

        $rules = [
            'username' => "required|min_length[4]|is_unique[users.username,user_id,{$userId}]",
            'email'    => "required|valid_email|is_unique[users.email,user_id,{$userId}]",
            'nama_lengkap' => 'required|min_length[3]',
            'nomor_peserta' => "required|is_unique[siswa.nomor_peserta,siswa_id,{$siswa['siswa_id']}]",
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Update tabel users
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email')
            ];

            // Update password jika diisi
            if ($this->request->getPost('password')) {
                $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            $this->userModel->update($userId, $userData);

            // Update tabel siswa
            $siswaData = [
                'kelas_id' => $this->request->getPost('kelas_id'),
                'nomor_peserta' => $this->request->getPost('nomor_peserta'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap')
            ];

            $this->siswaModel->update($siswa['siswa_id'], $siswaData);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Data siswa berhasil diperbarui!');
            return redirect()->to(base_url('admin/siswa'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui data siswa.');
            return redirect()->back()->withInput();
        }
    }

    public function hapusSiswa($userId)
    {
        try {
            $this->userModel->softDelete($userId);
            session()->setFlashdata('success', 'Siswa berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menonaktifkan siswa.');
        }

        return redirect()->to(base_url('admin/siswa'));
    }

    public function restoreSiswa($userId)
    {
        try {
            $this->userModel->restore($userId);
            session()->setFlashdata('success', 'Siswa berhasil diaktifkan kembali!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengaktifkan siswa.');
        }

        return redirect()->to(base_url('admin/siswa'));
    }

    // Method untuk batch create siswa
    public function batchCreateSiswa()
    {
        $kelasId = $this->request->getGet('kelas');
        $jumlah = (int)$this->request->getGet('jumlah');
        $prefix = $this->request->getGet('prefix');

        if (!$kelasId || !$jumlah || !$prefix || $jumlah > 50) {
            session()->setFlashdata('error', 'Parameter tidak valid');
            return redirect()->to(base_url('admin/siswa/tambah'));
        }

        try {
            $berhasil = 0;
            $gagal = 0;
            $errors = [];

            for ($i = 1; $i <= $jumlah; $i++) {
                $num = str_pad($i, 3, '0', STR_PAD_LEFT);
                $username = strtolower($prefix) . $num;
                $email = $username . '@sekolah.com';
                $nama = $prefix . ' ' . $num;
                $noPeserta = $prefix . $num;
                $password = 'password123'; // Default password

                // Cek apakah username sudah ada
                if ($this->userModel->where('username', $username)->first()) {
                    $gagal++;
                    $errors[] = "Username {$username} sudah digunakan";
                    continue;
                }

                // Cek apakah nomor peserta sudah ada
                if ($this->siswaModel->where('nomor_peserta', $noPeserta)->first()) {
                    $gagal++;
                    $errors[] = "Nomor peserta {$noPeserta} sudah digunakan";
                    continue;
                }

                // Insert user
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'siswa',
                    'status' => 'active'
                ];

                $userId = $this->userModel->insert($userData);

                if ($userId) {
                    // Insert siswa
                    $siswaData = [
                        'user_id' => $userId,
                        'kelas_id' => $kelasId,
                        'nomor_peserta' => $noPeserta,
                        'nama_lengkap' => $nama
                    ];

                    if ($this->siswaModel->insert($siswaData)) {
                        $berhasil++;
                    } else {
                        $gagal++;
                        $this->userModel->delete($userId); // Rollback user jika siswa gagal
                    }
                } else {
                    $gagal++;
                }
            }

            $message = "Batch create selesai. Berhasil: {$berhasil}, Gagal: {$gagal}";

            if ($gagal > 0) {
                $message .= "\nError: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
                }
                session()->setFlashdata('warning', $message);
            } else {
                session()->setFlashdata('success', $message);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error batch create siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat batch create siswa');
        }

        return redirect()->to(base_url('admin/siswa'));
    }

    // ===== KELOLA SEKOLAH =====

    public function daftarSekolah()
    {
        // Ambil data sekolah dengan semua field dan jumlah guru
        $db = \Config\Database::connect();
        $data['sekolah'] = $db->table('sekolah s')
            ->select('s.sekolah_id, s.nama_sekolah, s.alamat, s.telepon, s.email, COUNT(g.guru_id) as total_guru')
            ->join('guru g', 'g.sekolah_id = s.sekolah_id', 'left')
            ->groupBy('s.sekolah_id, s.nama_sekolah, s.alamat, s.telepon, s.email')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/sekolah/daftar', $data);
    }

    public function formTambahSekolah()
    {
        return view('admin/sekolah/tambah');
    }

    public function tambahSekolah()
    {
        $rules = [
            'nama_sekolah' => 'required|min_length[3]',
            'alamat' => 'permit_empty',
            'telepon' => 'permit_empty|min_length[10]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $sekolahModel = new \App\Models\SekolahModel();
            $data = [
                'nama_sekolah' => $this->request->getPost('nama_sekolah'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email')
            ];

            $sekolahModel->insert($data);
            session()->setFlashdata('success', 'Sekolah berhasil ditambahkan!');
            return redirect()->to(base_url('admin/sekolah'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding sekolah: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah sekolah.');
            return redirect()->back()->withInput();
        }
    }

    public function formEditSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Data sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $data['sekolah'] = $sekolah;
        return view('admin/sekolah/edit', $data);
    }

    public function editSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Data sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $rules = [
            'nama_sekolah' => 'required|min_length[3]',
            'alamat' => 'permit_empty',
            'telepon' => 'permit_empty|min_length[10]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'nama_sekolah' => $this->request->getPost('nama_sekolah'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email')
            ];

            $sekolahModel->update($sekolahId, $data);
            session()->setFlashdata('success', 'Data sekolah berhasil diperbarui!');
            return redirect()->to(base_url('admin/sekolah'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating sekolah: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui sekolah.');
            return redirect()->back()->withInput();
        }
    }

    public function hapusSekolah($sekolahId)
    {
        try {
            $sekolahModel = new \App\Models\SekolahModel();

            // Cek apakah sekolah masih memiliki guru
            $totalGuru = $this->guruModel->where('sekolah_id', $sekolahId)->countAllResults();

            if ($totalGuru > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus sekolah karena masih memiliki {$totalGuru} guru.");
                return redirect()->to(base_url('admin/sekolah'));
            }

            $sekolahModel->delete($sekolahId);
            session()->setFlashdata('success', 'Sekolah berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting sekolah: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus sekolah.');
        }

        return redirect()->to(base_url('admin/sekolah'));
    }

    // ===== KELOLA KELAS =====

    public function daftarKelas()
    {
        // Ambil data kelas dengan jumlah siswa
        $db = \Config\Database::connect();
        $data['kelas'] = $db->table('kelas k')
            ->select('k.*, COUNT(s.siswa_id) as total_siswa')
            ->join('siswa s', 's.kelas_id = k.kelas_id', 'left')
            ->groupBy('k.kelas_id')
            ->orderBy('k.tahun_ajaran', 'DESC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/kelas/daftar', $data);
    }

    public function formTambahKelas()
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();
        return view('admin/kelas/tambah', $data);
    }

    public function tambahKelas()
    {
        $rules = [
            'sekolah_id' => 'required|numeric',
            'nama_kelas' => 'required|min_length[2]',
            'tahun_ajaran' => 'required|regex_match[/^\d{4}\/\d{4}$/]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'sekolah_id' => $this->request->getPost('sekolah_id'),
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
            ];

            $this->kelasModel->insert($data);
            session()->setFlashdata('success', 'Kelas berhasil ditambahkan!');
            return redirect()->to(base_url('admin/kelas'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah kelas.');
            return redirect()->back()->withInput();
        }
    }

    public function formEditKelas($kelasId)
    {
        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas) {
            session()->setFlashdata('error', 'Data kelas tidak ditemukan');
            return redirect()->to(base_url('admin/kelas'));
        }

        $sekolahModel = new \App\Models\SekolahModel();
        $data['kelas'] = $kelas;
        $data['sekolah'] = $sekolahModel->findAll();

        return view('admin/kelas/edit', $data);
    }

    public function editKelas($kelasId)
    {
        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas) {
            session()->setFlashdata('error', 'Data kelas tidak ditemukan');
            return redirect()->to(base_url('admin/kelas'));
        }

        $rules = [
            'sekolah_id' => 'required|numeric',
            'nama_kelas' => 'required|min_length[2]',
            'tahun_ajaran' => 'required|regex_match[/^\d{4}\/\d{4}$/]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'sekolah_id' => $this->request->getPost('sekolah_id'),
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
            ];

            $this->kelasModel->update($kelasId, $data);
            session()->setFlashdata('success', 'Data kelas berhasil diperbarui!');
            return redirect()->to(base_url('admin/kelas'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui kelas.');
            return redirect()->back()->withInput();
        }
    }

    public function hapusKelas($kelasId)
    {
        try {
            // Cek apakah kelas masih memiliki siswa
            $totalSiswa = $this->siswaModel->where('kelas_id', $kelasId)->countAllResults();

            if ($totalSiswa > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus kelas karena masih memiliki {$totalSiswa} siswa.");
                return redirect()->to(base_url('admin/kelas'));
            }

            $this->kelasModel->delete($kelasId);
            session()->setFlashdata('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus kelas.');
        }

        return redirect()->to(base_url('admin/kelas'));
    }

    // ===== KELOLA UJIAN =====

    public function daftarUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil data ujian dengan informasi lengkap
        $data['ujian'] = $db->table('ujian u')
            ->select('u.id_ujian, u.nama_ujian, u.deskripsi, u.durasi, u.created_at,
                     j.nama_jenis,
                     COUNT(DISTINCT s.soal_id) as total_soal,
                     COUNT(DISTINCT ja.jadwal_id) as total_jadwal,
                     g.nama_lengkap as guru_pembuat')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('soal_ujian s', 's.ujian_id = u.id_ujian', 'left')
            ->join('jadwal_ujian ja', 'ja.ujian_id = u.id_ujian', 'left')
            ->join('guru g', 'g.guru_id = ja.guru_id', 'left')
            ->groupBy('u.id_ujian, u.nama_ujian, u.deskripsi, u.durasi, u.created_at, j.nama_jenis, g.nama_lengkap')
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/ujian/daftar', $data);
    }

    public function detailUjian($ujianId)
    {
        $db = \Config\Database::connect();

        // Ambil detail ujian
        $ujian = $db->table('ujian u')
            ->select('u.*, j.nama_jenis')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->where('u.id_ujian', $ujianId)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            session()->setFlashdata('error', 'Ujian tidak ditemukan');
            return redirect()->to(base_url('admin/ujian'));
        }

        // Ambil soal-soal ujian
        $soal = $db->table('soal_ujian')
            ->where('ujian_id', $ujianId)
            ->orderBy('soal_id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'ujian' => $ujian,
            'soal' => $soal
        ];

        return view('admin/ujian/detail', $data);
    }

    public function hapusUjian($ujianId)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Cek apakah ujian memiliki jadwal yang sedang berlangsung
            $jadwalAktif = $db->table('jadwal_ujian')
                ->where('ujian_id', $ujianId)
                ->where('status !=', 'selesai')
                ->countAllResults();

            if ($jadwalAktif > 0) {
                session()->setFlashdata('error', 'Tidak dapat menghapus ujian karena masih memiliki jadwal yang aktif.');
                return redirect()->to(base_url('admin/ujian'));
            }

            // Hapus hasil ujian terlebih dahulu (jika ada)
            $db->query("DELETE hu FROM hasil_ujian hu 
                       INNER JOIN peserta_ujian pu ON hu.peserta_ujian_id = pu.peserta_ujian_id
                       INNER JOIN jadwal_ujian ju ON pu.jadwal_id = ju.jadwal_id
                       WHERE ju.ujian_id = ?", [$ujianId]);

            // Hapus peserta ujian
            $db->query("DELETE pu FROM peserta_ujian pu 
                       INNER JOIN jadwal_ujian ju ON pu.jadwal_id = ju.jadwal_id
                       WHERE ju.ujian_id = ?", [$ujianId]);

            // Hapus jadwal ujian
            $db->table('jadwal_ujian')->where('ujian_id', $ujianId)->delete();

            // Hapus soal ujian
            $db->table('soal_ujian')->where('ujian_id', $ujianId)->delete();

            // Hapus ujian
            $db->table('ujian')->where('id_ujian', $ujianId)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Ujian beserta semua data terkait berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus ujian: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/ujian'));
    }

    public function hapusSoal($soalId)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Cek apakah soal sudah dijawab siswa
            $sudahDijawab = $db->table('hasil_ujian')->where('soal_id', $soalId)->countAllResults();

            if ($sudahDijawab > 0) {
                session()->setFlashdata('error', 'Tidak dapat menghapus soal yang sudah dijawab siswa');
                return redirect()->back();
            }

            // Hapus soal
            $db->table('soal_ujian')->where('soal_id', $soalId)->delete();

            $db->transComplete();

            session()->setFlashdata('success', 'Soal berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting soal: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus soal: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function detailSoal($soalId)
    {
        $db = \Config\Database::connect();

        $soal = $db->table('soal_ujian s')
            ->select('s.*, u.nama_ujian, j.nama_jenis')
            ->join('ujian u', 'u.id_ujian = s.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->where('s.soal_id', $soalId)
            ->get()
            ->getRowArray();

        if (!$soal) {
            session()->setFlashdata('error', 'Soal tidak ditemukan');
            return redirect()->to(base_url('admin/ujian'));
        }

        $data['soal'] = $soal;
        return view('admin/ujian/detail_soal', $data);
    }

    // ===== KELOLA JADWAL UJIAN =====

    public function daftarJadwal()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil data jadwal ujian dengan informasi lengkap
        $data['jadwal'] = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, g.nama_lengkap as nama_guru, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah,
                     COUNT(DISTINCT pu.peserta_ujian_id) as total_peserta')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('peserta_ujian pu', 'pu.jadwal_id = ju.jadwal_id', 'left')
            ->groupBy('ju.jadwal_id, u.nama_ujian, g.nama_lengkap, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah')
            ->orderBy('ju.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/jadwal/daftar', $data);
    }

    public function detailJadwal($jadwalId)
    {
        $db = \Config\Database::connect();

        // Ambil detail jadwal
        $jadwal = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, u.deskripsi, u.durasi, g.nama_lengkap as nama_guru, g.mata_pelajaran,
                     k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.alamat')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->where('ju.jadwal_id', $jadwalId)
            ->get()
            ->getRowArray();

        if (!$jadwal) {
            session()->setFlashdata('error', 'Jadwal tidak ditemukan');
            return redirect()->to(base_url('admin/jadwal'));
        }

        // Ambil peserta ujian
        $peserta = $db->table('peserta_ujian pu')
            ->select('pu.*, siswa.nama_lengkap, siswa.nomor_peserta, u.username')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('users u', 'u.user_id = siswa.user_id', 'left')
            ->where('pu.jadwal_id', $jadwalId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'jadwal' => $jadwal,
            'peserta' => $peserta
        ];

        return view('admin/jadwal/detail', $data);
    }

    public function hapusJadwal($jadwalId)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Cek status jadwal
            $jadwal = $db->table('jadwal_ujian')->where('jadwal_id', $jadwalId)->get()->getRowArray();

            if (!$jadwal) {
                session()->setFlashdata('error', 'Jadwal tidak ditemukan');
                return redirect()->back();
            }

            if ($jadwal['status'] === 'sedang_berlangsung') {
                session()->setFlashdata('error', 'Tidak dapat menghapus jadwal yang sedang berlangsung');
                return redirect()->back();
            }

            // Hapus hasil ujian
            $db->query("DELETE hu FROM hasil_ujian hu 
                       INNER JOIN peserta_ujian pu ON hu.peserta_ujian_id = pu.peserta_ujian_id
                       WHERE pu.jadwal_id = ?", [$jadwalId]);

            // Hapus peserta ujian
            $db->table('peserta_ujian')->where('jadwal_id', $jadwalId)->delete();

            // Hapus jadwal
            $db->table('jadwal_ujian')->where('jadwal_id', $jadwalId)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Jadwal ujian berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting jadwal: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus jadwal: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/jadwal'));
    }

    // ===== KELOLA HASIL UJIAN =====

    public function daftarHasilUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil daftar ujian yang sudah selesai dengan hasil
        $data['daftarUjian'] = $db->table('jadwal_ujian ju')
            ->select('ju.jadwal_id, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
                     s.nama_sekolah, g.nama_lengkap as nama_guru, ju.tanggal_mulai, ju.tanggal_selesai,
                     COUNT(DISTINCT pu.peserta_ujian_id) as jumlah_peserta,
                     COUNT(DISTINCT CASE WHEN pu.status = "selesai" THEN pu.peserta_ujian_id END) as peserta_selesai')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->join('peserta_ujian pu', 'pu.jadwal_id = ju.jadwal_id', 'left')
            ->where('ju.status', 'selesai')
            ->groupBy('ju.jadwal_id, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, g.nama_lengkap, ju.tanggal_mulai, ju.tanggal_selesai')
            ->orderBy('ju.tanggal_selesai', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/hasil/daftar', $data);
    }

    public function hasilUjianSiswa($jadwalId)
    {
        $db = \Config\Database::connect();

        // Ambil info ujian
        $ujian = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
                     s.nama_sekolah, g.nama_lengkap as nama_guru')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->where('ju.jadwal_id', $jadwalId)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            session()->setFlashdata('error', 'Jadwal ujian tidak ditemukan');
            return redirect()->to(base_url('admin/hasil-ujian'));
        }

        // Ambil hasil siswa dengan perhitungan nilai
        $hasilSiswa = $db->table('peserta_ujian pu')
            ->select('pu.peserta_ujian_id, pu.status, pu.waktu_mulai, pu.waktu_selesai,
                     siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta,
                     u.username')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('users u', 'u.user_id = siswa.user_id', 'left')
            ->where('pu.jadwal_id', $jadwalId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung nilai untuk setiap siswa
        foreach ($hasilSiswa as &$siswa) {
            if ($siswa['status'] === 'selesai') {
                // Ambil theta terakhir
                $lastResult = $db->table('hasil_ujian hu')
                    ->select('hu.theta_saat_ini, hu.se_saat_ini')
                    ->where('hu.peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->orderBy('hu.jawaban_id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                if ($lastResult) {
                    $theta = $lastResult['theta_saat_ini'];
                    $finalScore = 50 + (16.6 * $theta);
                    $finalGrade = min(100, max(0, round(($finalScore / 100) * 100)));

                    $siswa['theta_akhir'] = $theta;
                    $siswa['skor'] = $finalScore;
                    $siswa['nilai'] = $finalGrade;
                    $siswa['se_akhir'] = $lastResult['se_saat_ini'];
                } else {
                    $siswa['theta_akhir'] = null;
                    $siswa['skor'] = 0;
                    $siswa['nilai'] = 0;
                    $siswa['se_akhir'] = null;
                }

                // Hitung jumlah jawaban benar
                $jawabanBenar = $db->table('hasil_ujian')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->where('is_correct', 1)
                    ->countAllResults();

                $totalSoal = $db->table('hasil_ujian')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->countAllResults();

                $siswa['jawaban_benar'] = $jawabanBenar;
                $siswa['total_soal'] = $totalSoal;
            } else {
                $siswa['theta_akhir'] = null;
                $siswa['skor'] = null;
                $siswa['nilai'] = null;
                $siswa['se_akhir'] = null;
                $siswa['jawaban_benar'] = 0;
                $siswa['total_soal'] = 0;
            }
        }

        $data = [
            'ujian' => $ujian,
            'hasilSiswa' => $hasilSiswa
        ];

        return view('admin/hasil/siswa', $data);
    }

    public function detailHasilSiswa($pesertaUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil detail peserta dan ujian
        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, j.nama_jenis, 
                     siswa.nama_lengkap, siswa.nomor_peserta,
                     k.nama_kelas, k.tahun_ajaran, s.nama_sekolah,
                     g.nama_lengkap as nama_guru')
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id', 'left')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->where('pu.peserta_ujian_id', $pesertaUjianId)
            ->get()
            ->getRowArray();

        if (!$hasil) {
            session()->setFlashdata('error', 'Data hasil ujian tidak ditemukan');
            return redirect()->to(base_url('admin/hasil-ujian'));
        }

        // Ambil detail jawaban
        $detailJawaban = $db->table('hasil_ujian hu')
            ->select('hu.*, s.pertanyaan, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, 
                     s.jawaban_benar, s.tingkat_kesulitan, s.foto, s.pembahasan')
            ->join('soal_ujian s', 's.soal_id = hu.soal_id', 'left')
            ->where('hu.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hu.jawaban_id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawaban
        ];

        return view('admin/hasil/detail', $data);
    }

    public function hapusHasilSiswa($pesertaUjianId)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Ambil info peserta untuk redirect
            $peserta = $db->table('peserta_ujian')->where('peserta_ujian_id', $pesertaUjianId)->get()->getRowArray();

            if (!$peserta) {
                session()->setFlashdata('error', 'Data peserta tidak ditemukan');
                return redirect()->back();
            }

            // Hapus hasil ujian
            $db->table('hasil_ujian')->where('peserta_ujian_id', $pesertaUjianId)->delete();

            // Reset status peserta
            $db->table('peserta_ujian')
                ->where('peserta_ujian_id', $pesertaUjianId)
                ->update([
                    'status' => 'belum_mulai',
                    'waktu_mulai' => null,
                    'waktu_selesai' => null
                ]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Hasil ujian siswa berhasil dihapus dan direset!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting hasil siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus hasil ujian: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/hasil-ujian/siswa/' . $peserta['jadwal_id']));
    }

    // ===== KELOLA PENGUMUMAN =====

    public function daftarPengumuman()
    {
        $db = \Config\Database::connect();

        // Ambil semua pengumuman dengan info pembuat
        $data['pengumuman'] = $db->table('pengumuman p')
            ->select('p.*, u.username as pembuat')
            ->join('users u', 'u.user_id = p.created_by', 'left')
            ->orderBy('p.tanggal_publish', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/pengumuman/daftar', $data);
    }

    public function formTambahPengumuman()
    {
        return view('admin/pengumuman/tambah');
    }

    public function tambahPengumuman()
    {
        $rules = [
            'judul' => 'required|min_length[5]|max_length[200]',
            'isi_pengumuman' => 'required|min_length[10]',
            // HAPUS validasi tanggal_berakhir dari rules
            // 'tanggal_berakhir' => 'permit_empty|valid_date[Y-m-d H:i]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $pengumumanModel = new \App\Models\PengumumanModel();

            // Handle tanggal_berakhir secara manual
            $tanggalBerakhir = $this->request->getPost('tanggal_berakhir');

            // Validasi manual untuk tanggal berakhir jika diisi
            if (!empty($tanggalBerakhir)) {
                // Konversi dari format datetime-local ke format database
                $tanggalBerakhir = date('Y-m-d H:i:s', strtotime($tanggalBerakhir));

                // Validasi apakah tanggal valid
                if ($tanggalBerakhir === false || $tanggalBerakhir === '1970-01-01 00:00:00') {
                    session()->setFlashdata('error', 'Format tanggal berakhir tidak valid.');
                    return redirect()->back()->withInput();
                }

                // Validasi apakah tanggal berakhir tidak lebih awal dari sekarang
                if (strtotime($tanggalBerakhir) <= time()) {
                    session()->setFlashdata('error', 'Tanggal berakhir harus lebih dari waktu sekarang.');
                    return redirect()->back()->withInput();
                }
            } else {
                $tanggalBerakhir = null;
            }

            $data = [
                'judul' => $this->request->getPost('judul'),
                'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
                'tanggal_publish' => date('Y-m-d H:i:s'),
                'tanggal_berakhir' => $tanggalBerakhir,
                'created_by' => session()->get('user_id')
            ];

            $pengumumanModel->insert($data);
            session()->setFlashdata('success', 'Pengumuman berhasil ditambahkan!');
            return redirect()->to(base_url('admin/pengumuman'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding pengumuman: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah pengumuman: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function formEditPengumuman($pengumumanId)
    {
        $pengumumanModel = new \App\Models\PengumumanModel();
        $pengumuman = $pengumumanModel->find($pengumumanId);

        if (!$pengumuman) {
            session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
            return redirect()->to(base_url('admin/pengumuman'));
        }

        $data['pengumuman'] = $pengumuman;
        return view('admin/pengumuman/edit', $data);
    }

    public function editPengumuman($pengumumanId)
    {
        $pengumumanModel = new \App\Models\PengumumanModel();
        $pengumuman = $pengumumanModel->find($pengumumanId);

        if (!$pengumuman) {
            session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
            return redirect()->to(base_url('admin/pengumuman'));
        }

        $rules = [
            'judul' => 'required|min_length[5]|max_length[200]',
            'isi_pengumuman' => 'required|min_length[10]',
            // HAPUS validasi tanggal_berakhir dari rules
            // 'tanggal_berakhir' => 'permit_empty|valid_date[Y-m-d H:i]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Handle tanggal_berakhir secara manual
            $tanggalBerakhir = $this->request->getPost('tanggal_berakhir');

            // Validasi manual untuk tanggal berakhir jika diisi
            if (!empty($tanggalBerakhir)) {
                // Konversi dari format datetime-local ke format database
                $tanggalBerakhir = date('Y-m-d H:i:s', strtotime($tanggalBerakhir));

                // Validasi apakah tanggal valid
                if ($tanggalBerakhir === false || $tanggalBerakhir === '1970-01-01 00:00:00') {
                    session()->setFlashdata('error', 'Format tanggal berakhir tidak valid.');
                    return redirect()->back()->withInput();
                }
            } else {
                $tanggalBerakhir = null;
            }

            $data = [
                'judul' => $this->request->getPost('judul'),
                'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
                'tanggal_berakhir' => $tanggalBerakhir
            ];

            $pengumumanModel->update($pengumumanId, $data);
            session()->setFlashdata('success', 'Pengumuman berhasil diperbarui!');
            return redirect()->to(base_url('admin/pengumuman'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating pengumuman: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui pengumuman: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function hapusPengumuman($pengumumanId)
    {
        try {
            $pengumumanModel = new \App\Models\PengumumanModel();
            $pengumuman = $pengumumanModel->find($pengumumanId);

            if (!$pengumuman) {
                session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
                return redirect()->to(base_url('admin/pengumuman'));
            }

            $pengumumanModel->delete($pengumumanId);
            session()->setFlashdata('success', 'Pengumuman berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting pengumuman: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus pengumuman.');
        }

        return redirect()->to(base_url('admin/pengumuman'));
    }

    public function detailPengumuman($pengumumanId)
    {
        $db = \Config\Database::connect();

        $pengumuman = $db->table('pengumuman p')
            ->select('p.*, u.username as pembuat')
            ->join('users u', 'u.user_id = p.created_by', 'left')
            ->where('p.pengumuman_id', $pengumumanId)
            ->get()
            ->getRowArray();

        if (!$pengumuman) {
            session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
            return redirect()->to(base_url('admin/pengumuman'));
        }

        $data['pengumuman'] = $pengumuman;
        return view('admin/pengumuman/detail', $data);
    }

    // Method untuk toggle status aktif/nonaktif pengumuman (opsional)
    public function toggleStatusPengumuman($pengumumanId)
    {
        try {
            $pengumumanModel = new \App\Models\PengumumanModel();
            $pengumuman = $pengumumanModel->find($pengumumanId);

            if (!$pengumuman) {
                session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
                return redirect()->to(base_url('admin/pengumuman'));
            }

            // Toggle berdasarkan tanggal berakhir
            $newStatus = $pengumuman['tanggal_berakhir'] ? null : date('Y-m-d H:i:s');

            $pengumumanModel->update($pengumumanId, ['tanggal_berakhir' => $newStatus]);

            $statusText = $newStatus ? 'dinonaktifkan' : 'diaktifkan';
            session()->setFlashdata('success', "Pengumuman berhasil {$statusText}!");
        } catch (\Exception $e) {
            log_message('error', 'Error toggling pengumuman status: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengubah status pengumuman.');
        }

        return redirect()->to(base_url('admin/pengumuman'));
    }
}
