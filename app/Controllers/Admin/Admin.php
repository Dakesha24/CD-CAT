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
        $db = \Config\Database::connect();

        $data['stats'] = [
            'total_guru' => $db->table('guru')->countAllResults(),
            'total_siswa' => $db->table('siswa')->countAllResults(),
            'total_sekolah' => $db->table('sekolah')->countAllResults(),
            'total_kelas' => $db->table('kelas')->countAllResults()
        ];

        return view('admin/dashboard', $data);
    }

    // ===== KELOLA GURU =====

    public function daftarGuru()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil data guru dengan detail sekolah dan jumlah kelas yang diajar
        $data['guru'] = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at,
                 g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, g.sekolah_id,
                 s.nama_sekolah,
                 COUNT(DISTINCT kg.kelas_id) as total_kelas')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->join('sekolah s', 's.sekolah_id = g.sekolah_id', 'left')
            ->join('kelas_guru kg', 'kg.guru_id = g.guru_id', 'left')
            ->where('u.role', 'guru')
            ->groupBy('u.user_id, u.username, u.email, u.status, u.created_at,
                  g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, g.sekolah_id,
                  s.nama_sekolah')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/guru/daftar', $data);
    }

    public function formTambahGuru()
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas dengan info sekolah untuk JavaScript
        $db = \Config\Database::connect();
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

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
            $db = \Config\Database::connect();
            $db->transStart();

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

                $guruId = $this->guruModel->insert($guruData);

                // Handle assignment kelas jika ada
                $kelasIds = $this->request->getPost('kelas_ids');
                if (!empty($kelasIds) && is_array($kelasIds)) {
                    $sekolahId = $this->request->getPost('sekolah_id');

                    foreach ($kelasIds as $kelasId) {
                        // Validasi kelas berada di sekolah yang sama
                        $kelas = $db->table('kelas')
                            ->where('kelas_id', $kelasId)
                            ->where('sekolah_id', $sekolahId)
                            ->get()
                            ->getRowArray();

                        if ($kelas) {
                            // Insert ke tabel kelas_guru
                            $db->table('kelas_guru')->insert([
                                'kelas_id' => $kelasId,
                                'guru_id' => $guruId,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === FALSE) {
                    throw new \Exception('Transaction failed');
                }

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
        $db = \Config\Database::connect();

        // Ambil data guru
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

        if (!$guru) {
            session()->setFlashdata('error', 'Data guru tidak ditemukan');
            return redirect()->to(base_url('admin/guru'));
        }

        // Set default values
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

        // Ambil data sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil kelas yang sudah diajar oleh guru ini
        $data['kelasGuru'] = $db->table('kelas_guru kg')
            ->select('kg.*, k.nama_kelas, k.tahun_ajaran, k.kelas_id')
            ->join('kelas k', 'k.kelas_id = kg.kelas_id')
            ->where('kg.guru_id', $guru['guru_id'])
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil semua kelas untuk JavaScript (untuk assignment baru)
        $data['allKelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $data['guru'] = $guru;

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

            session()->setFlashdata('success', 'Data guru berhasil diperbarui!');
            return redirect()->to(base_url('admin/guru'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Method untuk assign kelas ke guru
    public function assignKelas()
    {
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');

        if (!$guruId || !$kelasId) {
            session()->setFlashdata('error', 'Data tidak lengkap');
            return redirect()->back();
        }

        try {
            $db = \Config\Database::connect();

            // Ambil info guru untuk validasi sekolah
            $guru = $db->table('guru')->where('guru_id', $guruId)->get()->getRowArray();
            if (!$guru) {
                session()->setFlashdata('error', 'Guru tidak ditemukan');
                return redirect()->back();
            }

            // Validasi kelas dari sekolah yang sama
            $kelas = $db->table('kelas')
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $guru['sekolah_id'])
                ->get()
                ->getRowArray();

            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas tidak valid atau tidak berada di sekolah yang sama');
                return redirect()->back();
            }

            // Cek apakah guru sudah mengajar di kelas ini
            $existing = $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->countAllResults();

            if ($existing > 0) {
                session()->setFlashdata('error', 'Guru sudah mengajar di kelas ini');
                return redirect()->back();
            }

            // Insert ke tabel kelas_guru
            $db->table('kelas_guru')->insert([
                'kelas_id' => $kelasId,
                'guru_id' => $guruId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'Kelas berhasil ditambahkan ke guru!');
        } catch (\Exception $e) {
            log_message('error', 'Error assigning kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambahkan kelas.');
        }

        return redirect()->back();
    }

    // Method untuk remove kelas dari guru
    public function removeKelas($guruId, $kelasId)
    {
        try {
            $db = \Config\Database::connect();

            // Hapus dari tabel kelas_guru
            $affected = $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->delete();

            if ($affected > 0) {
                session()->setFlashdata('success', 'Guru berhasil dikeluarkan dari kelas!');
            } else {
                session()->setFlashdata('warning', 'Tidak ada data yang dihapus');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error removing kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengeluarkan guru dari kelas.');
        }

        return redirect()->back();
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
        $db = \Config\Database::connect();

        // Query untuk mengambil data siswa dengan detail sekolah dan kelas
        $data['siswa'] = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at,
                 s.siswa_id, s.nomor_peserta, s.nama_lengkap, s.kelas_id,
                 k.nama_kelas, k.tahun_ajaran,
                 sk.sekolah_id, sk.nama_sekolah')
            ->join('siswa s', 's.user_id = u.user_id', 'left')
            ->join('kelas k', 'k.kelas_id = s.kelas_id', 'left')
            ->join('sekolah sk', 'sk.sekolah_id = k.sekolah_id', 'left')
            ->where('u.role', 'siswa')
            ->orderBy('s.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/siswa/daftar', $data);
    }

    public function formTambahSiswa()
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas dengan info sekolah untuk JavaScript
        $db = \Config\Database::connect();
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

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
            'sekolah_id' => 'required|numeric', // Validasi sekolah
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();

            // Validasi kelas berada di sekolah yang dipilih
            $sekolahId = $this->request->getPost('sekolah_id');
            $kelasId = $this->request->getPost('kelas_id');

            $kelas = $db->table('kelas')
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $sekolahId)
                ->get()
                ->getRowArray();

            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas yang dipilih tidak valid untuk sekolah tersebut.');
                return redirect()->back()->withInput();
            }

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
                    'kelas_id' => $kelasId,
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
                 k.nama_kelas, k.tahun_ajaran, k.sekolah_id,
                 sk.nama_sekolah')
            ->join('siswa s', 's.user_id = u.user_id', 'left')
            ->join('kelas k', 'k.kelas_id = s.kelas_id', 'left')
            ->join('sekolah sk', 'sk.sekolah_id = k.sekolah_id', 'left')
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
            'tahun_ajaran' => '',
            'sekolah_id' => '',
            'nama_sekolah' => ''
        ];

        $siswa = array_merge($defaultFields, $siswa ?: []);

        // Ambil data sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas dengan info sekolah untuk JavaScript
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $data['siswa'] = $siswa;

        return view('admin/siswa/edit', $data);
    }

    public function editSiswa($userId)
    {
        // Debug: Log input data
        log_message('debug', 'Edit siswa called with userId: ' . $userId);
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to(base_url('admin/siswa'));
        }

        log_message('debug', 'Found siswa: ' . json_encode($siswa));

        $rules = [
            'username' => "required|min_length[4]|is_unique[users.username,user_id,{$userId}]",
            'email'    => "required|valid_email|is_unique[users.email,user_id,{$userId}]",
            'nama_lengkap' => 'required|min_length[3]',
            'nomor_peserta' => "required|is_unique[siswa.nomor_peserta,siswa_id,{$siswa['siswa_id']}]",
            'sekolah_id' => 'required|numeric', // Validasi sekolah
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();

            // Validasi kelas berada di sekolah yang dipilih
            $sekolahId = $this->request->getPost('sekolah_id');
            $kelasId = $this->request->getPost('kelas_id');

            $kelas = $db->table('kelas')
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $sekolahId)
                ->get()
                ->getRowArray();

            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas yang dipilih tidak valid untuk sekolah tersebut.');
                return redirect()->back()->withInput();
            }

            // Ambil data input
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $namaLengkap = $this->request->getPost('nama_lengkap');
            $nomorPeserta = $this->request->getPost('nomor_peserta');

            log_message('debug', 'Processing update with data: ' . json_encode([
                'username' => $username,
                'email' => $email,
                'password_provided' => !empty($password),
                'nama_lengkap' => $namaLengkap,
                'nomor_peserta' => $nomorPeserta,
                'kelas_id' => $kelasId
            ]));

            // Update tabel users dengan raw query
            $sqlUser = "UPDATE users SET username = ?, email = ?";
            $paramsUser = [$username, $email];

            if (!empty($password)) {
                $sqlUser .= ", password = ?";
                $paramsUser[] = password_hash($password, PASSWORD_DEFAULT);
                log_message('debug', 'Password will be updated');
            }

            $sqlUser .= " WHERE user_id = ?";
            $paramsUser[] = $userId;

            log_message('debug', 'User SQL: ' . $sqlUser);
            log_message('debug', 'User Params: ' . json_encode($paramsUser));

            $result = $db->query($sqlUser, $paramsUser);
            $userAffectedRows = $db->affectedRows();
            log_message('debug', "User update - Affected rows: {$userAffectedRows}");

            if (!$result) {
                throw new \Exception('User update failed: ' . $db->error()['message']);
            }

            // Update tabel siswa dengan raw query
            $sqlSiswa = "UPDATE siswa SET nama_lengkap = ?, nomor_peserta = ?, kelas_id = ? WHERE user_id = ?";
            $paramsSiswa = [$namaLengkap, $nomorPeserta, $kelasId, $userId];

            log_message('debug', 'Siswa SQL: ' . $sqlSiswa);
            log_message('debug', 'Siswa Params: ' . json_encode($paramsSiswa));

            $result = $db->query($sqlSiswa, $paramsSiswa);
            $siswaAffectedRows = $db->affectedRows();
            log_message('debug', "Siswa update - Affected rows: {$siswaAffectedRows}");

            if (!$result) {
                throw new \Exception('Siswa update failed: ' . $db->error()['message']);
            }

            session()->setFlashdata('success', 'Data siswa berhasil diperbarui!');
            return redirect()->to(base_url('admin/siswa'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating siswa: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        // Ambil data sekolah dengan semua field, jumlah guru, dan jumlah kelas
        $db = \Config\Database::connect();
        $data['sekolah'] = $db->table('sekolah s')
            ->select('s.sekolah_id, s.nama_sekolah, s.alamat, s.telepon, s.email, 
                 COUNT(DISTINCT g.guru_id) as total_guru,
                 COUNT(DISTINCT k.kelas_id) as total_kelas')
            ->join('guru g', 'g.sekolah_id = s.sekolah_id', 'left')
            ->join('kelas k', 'k.sekolah_id = s.sekolah_id', 'left')
            ->groupBy('s.sekolah_id, s.nama_sekolah, s.alamat, s.telepon, s.email')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/sekolah/daftar', $data);
    }

    // Method untuk menampilkan kelas berdasarkan sekolah
    public function daftarKelasBySekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $db = \Config\Database::connect();

        // Ambil data kelas dengan jumlah siswa dan guru
        $kelas = $db->table('kelas k')
            ->select('k.*, 
                 COUNT(DISTINCT s.siswa_id) as total_siswa,
                 COUNT(DISTINCT kg.guru_id) as total_guru')
            ->join('siswa s', 's.kelas_id = k.kelas_id', 'left')
            ->join('kelas_guru kg', 'kg.kelas_id = k.kelas_id', 'left')
            ->where('k.sekolah_id', $sekolahId)
            ->groupBy('k.kelas_id')
            ->orderBy('k.tahun_ajaran', 'DESC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung total guru sekolah
        $sekolah['total_guru'] = $db->table('guru')
            ->where('sekolah_id', $sekolahId)
            ->countAllResults();

        $data = [
            'sekolah' => $sekolah,
            'kelas' => $kelas
        ];

        return view('admin/sekolah/kelas', $data);
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

    //Method untuk form tambah kelas dalam sekolah

    public function formTambahKelasSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $data = [
            'sekolah' => $sekolah,
            'sekolah_id' => $sekolahId
        ];

        return view('admin/sekolah/tambah_kelas', $data);
    }

    // Form edit kelas via sekolah
    public function editKelasSekolah($sekolahId, $kelasId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas || $kelas['sekolah_id'] != $sekolahId) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        }

        $rules = [
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
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
                // sekolah_id tetap sama, tidak berubah
            ];

            $this->kelasModel->update($kelasId, $data);
            session()->setFlashdata('success', 'Data kelas berhasil diperbarui!');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui kelas.');
            return redirect()->back()->withInput();
        }
    }

    // Tambahkan method ini di controller Admin.php (sekitar line 800-an, setelah method formTambahKelasSekolah)

    public function formEditKelasSekolah($sekolahId, $kelasId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas || $kelas['sekolah_id'] != $sekolahId) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        }

        $data = [
            'sekolah' => $sekolah,
            'kelas' => $kelas
        ];

        return view('admin/sekolah/edit_kelas', $data);
    }

    // Hapus kelas via sekolah
    public function hapusKelasSekolah($sekolahId, $kelasId)
    {
        try {
            $sekolahModel = new \App\Models\SekolahModel();
            $sekolah = $sekolahModel->find($sekolahId);

            if (!$sekolah) {
                session()->setFlashdata('error', 'Sekolah tidak ditemukan');
                return redirect()->to(base_url('admin/sekolah'));
            }

            $kelas = $this->kelasModel->find($kelasId);

            if (!$kelas || $kelas['sekolah_id'] != $sekolahId) {
                session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
                return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
            }

            // Cek apakah kelas masih memiliki siswa
            $totalSiswa = $this->siswaModel->where('kelas_id', $kelasId)->countAllResults();

            // Cek apakah kelas masih memiliki guru
            $db = \Config\Database::connect();
            $totalGuru = $db->table('kelas_guru')->where('kelas_id', $kelasId)->countAllResults();

            if ($totalSiswa > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus kelas karena masih memiliki {$totalSiswa} siswa.");
                return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
            }

            if ($totalGuru > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus kelas karena masih memiliki {$totalGuru} guru pengajar.");
                return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
            }

            $this->kelasModel->delete($kelasId);
            session()->setFlashdata('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus kelas.');
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
    }

    // Detail kelas via sekolah (sama seperti detailKelas tapi dengan parameter sekolah)
    public function detailKelasSekolah($sekolahId, $kelasId)
    {
        $db = \Config\Database::connect();

        // Validasi sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        // Ambil detail kelas
        $kelas = $db->table('kelas k')
            ->select('k.*, s.nama_sekolah, s.sekolah_id')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->where('k.kelas_id', $kelasId)
            ->where('k.sekolah_id', $sekolahId)
            ->get()
            ->getRowArray();

        if (!$kelas) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        }

        // Ambil daftar guru yang mengajar di kelas ini (dengan info kelas lain yang diajar)
        $daftarGuru = $db->table('kelas_guru kg')
            ->select('kg.*, g.guru_id, g.nama_lengkap, g.nip, g.mata_pelajaran, 
                 u.user_id, u.username, u.status,
                 GROUP_CONCAT(DISTINCT CASE 
                    WHEN k2.kelas_id != kg.kelas_id THEN k2.nama_kelas 
                    END ORDER BY k2.nama_kelas SEPARATOR ", ") as kelas_lain')
            ->join('guru g', 'g.guru_id = kg.guru_id')
            ->join('users u', 'u.user_id = g.user_id')
            ->join('kelas_guru kg2', 'kg2.guru_id = g.guru_id', 'left')
            ->join('kelas k2', 'k2.kelas_id = kg2.kelas_id', 'left')
            ->where('kg.kelas_id', $kelasId)
            ->groupBy('kg.kelas_guru_id, kg.kelas_id, kg.guru_id, kg.created_at, kg.updated_at, 
                  g.guru_id, g.nama_lengkap, g.nip, g.mata_pelajaran, 
                  u.user_id, u.username, u.status')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil daftar siswa di kelas ini
        $daftarSiswa = $db->table('siswa s')
            ->select('s.*, u.user_id, u.username, u.status')
            ->join('users u', 'u.user_id = s.user_id')
            ->where('s.kelas_id', $kelasId)
            ->orderBy('s.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil daftar guru yang tersedia untuk di-assign (HANYA dari sekolah yang sama)
        $assignedGuruIds = array_column($daftarGuru, 'guru_id');
        $whereNotIn = !empty($assignedGuruIds) ? $assignedGuruIds : [0];

        $availableGuru = $db->table('guru g')
            ->select('g.guru_id, g.nama_lengkap, g.mata_pelajaran,
                 GROUP_CONCAT(DISTINCT k.nama_kelas ORDER BY k.nama_kelas SEPARATOR ", ") as kelas_diajar')
            ->join('users u', 'u.user_id = g.user_id')
            ->join('kelas_guru kg', 'kg.guru_id = g.guru_id', 'left')
            ->join('kelas k', 'k.kelas_id = kg.kelas_id', 'left')
            ->where('g.sekolah_id', $sekolahId) // Filter sekolah
            ->where('u.status', 'active')
            ->whereNotIn('g.guru_id', $whereNotIn)
            ->groupBy('g.guru_id, g.nama_lengkap, g.mata_pelajaran')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'sekolah' => $sekolah,
            'kelas' => $kelas,
            'daftarGuru' => $daftarGuru,
            'daftarSiswa' => $daftarSiswa,
            'availableGuru' => $availableGuru
        ];

        return view('admin/sekolah/detail_kelas', $data);
    }

    public function assignGuruKelasSekolah($sekolahId, $kelasId)
    {
        $guruId = $this->request->getPost('guru_id');

        if (!$guruId) {
            session()->setFlashdata('error', 'Guru harus dipilih');
            return redirect()->back();
        }

        try {
            $db = \Config\Database::connect();

            // Validasi guru dari sekolah yang sama
            $guru = $db->table('guru')->where('guru_id', $guruId)->where('sekolah_id', $sekolahId)->get()->getRowArray();

            if (!$guru) {
                session()->setFlashdata('error', 'Guru tidak ditemukan atau tidak berada di sekolah ini');
                return redirect()->back();
            }

            // Cek apakah guru sudah mengajar di kelas ini
            $existing = $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->countAllResults();

            if ($existing > 0) {
                session()->setFlashdata('error', 'Guru sudah mengajar di kelas ini');
                return redirect()->back();
            }

            // Insert ke tabel kelas_guru
            $db->table('kelas_guru')->insert([
                'kelas_id' => $kelasId,
                'guru_id' => $guruId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'Guru berhasil di-assign ke kelas!');
        } catch (\Exception $e) {
            log_message('error', 'Error assigning guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat assign guru.');
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasId . '/detail'));
    }

    // Remove guru dari kelas via sekolah
    public function removeGuruKelasSekolah($sekolahId, $kelasId, $guruId)
    {
        try {
            $db = \Config\Database::connect();

            $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->delete();

            session()->setFlashdata('success', 'Guru berhasil dikeluarkan dari kelas!');
        } catch (\Exception $e) {
            log_message('error', 'Error removing guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengeluarkan guru.');
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasId . '/detail'));
    }

    // Transfer siswa via sekolah
    public function transferSiswaSekolah($sekolahId, $kelasId, $siswaId)
    {
        $db = \Config\Database::connect();

        // Validasi sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        // Ambil info siswa dan kelas
        $siswa = $db->table('siswa s')
            ->select('s.*, u.username, k.nama_kelas, k.sekolah_id, sk.nama_sekolah')
            ->join('users u', 'u.user_id = s.user_id')
            ->join('kelas k', 'k.kelas_id = s.kelas_id')
            ->join('sekolah sk', 'sk.sekolah_id = k.sekolah_id')
            ->where('s.siswa_id', $siswaId)
            ->where('s.kelas_id', $kelasId)
            ->where('k.sekolah_id', $sekolahId)
            ->get()
            ->getRowArray();

        if (!$siswa) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan atau tidak berada di kelas/sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasId . '/detail'));
        }

        // Ambil daftar kelas lain di sekolah yang sama
        $kelasLain = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, COUNT(s.siswa_id) as jumlah_siswa')
            ->join('siswa s', 's.kelas_id = k.kelas_id', 'left')
            ->where('k.sekolah_id', $sekolahId)
            ->where('k.kelas_id !=', $kelasId)
            ->groupBy('k.kelas_id, k.nama_kelas, k.tahun_ajaran')
            ->orderBy('k.tahun_ajaran', 'DESC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'sekolah' => $sekolah,
            'siswa' => $siswa,
            'kelasAsal' => $kelasId,
            'kelasLain' => $kelasLain
        ];

        return view('admin/sekolah/transfer_siswa', $data);
    }

    // Proses transfer siswa via sekolah
    public function prosesTransferSiswaSekolah()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $sekolahId = $this->request->getPost('sekolah_id');
        $kelasAsalId = $this->request->getPost('kelas_asal_id');
        $kelasTujuanId = $this->request->getPost('kelas_tujuan_id');

        if (!$siswaId || !$sekolahId || !$kelasAsalId || !$kelasTujuanId) {
            session()->setFlashdata('error', 'Data tidak lengkap');
            return redirect()->back();
        }

        try {
            $db = \Config\Database::connect();

            // Validasi kelas tujuan di sekolah yang sama
            $kelasTujuan = $db->table('kelas')->where('kelas_id', $kelasTujuanId)->where('sekolah_id', $sekolahId)->get()->getRowArray();

            if (!$kelasTujuan) {
                session()->setFlashdata('error', 'Kelas tujuan tidak valid');
                return redirect()->back();
            }

            // Ambil info untuk log
            $siswa = $db->table('siswa')->select('nama_lengkap')->where('siswa_id', $siswaId)->get()->getRowArray();
            $kelasAsal = $db->table('kelas')->select('nama_kelas')->where('kelas_id', $kelasAsalId)->get()->getRowArray();

            // Update kelas siswa
            $affected = $db->table('siswa')
                ->where('siswa_id', $siswaId)
                ->update(['kelas_id' => $kelasTujuanId]);

            if ($affected > 0) {
                session()->setFlashdata(
                    'success',
                    "Siswa <strong>{$siswa['nama_lengkap']}</strong> berhasil dipindahkan dari " .
                        "<strong>{$kelasAsal['nama_kelas']}</strong> ke <strong>{$kelasTujuan['nama_kelas']}</strong>."
                );
            } else {
                session()->setFlashdata('warning', 'Tidak ada perubahan yang dilakukan');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error transferring siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memindahkan siswa: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasAsalId . '/detail'));
    }

    // Method untuk tambah kelas dalam sekolah
    public function tambahKelasSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $rules = [
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
                'sekolah_id' => $sekolahId,
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
            ];

            $this->kelasModel->insert($data);
            session()->setFlashdata('success', 'Kelas berhasil ditambahkan!');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah kelas.');
            return redirect()->back()->withInput();
        }
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

    private function hitungDurasiPerSoal($detailJawaban, $waktuMulaiUjian)
    {
        $hasilDenganDurasi = [];
        $waktuSebelumnya = $waktuMulaiUjian;

        foreach ($detailJawaban as $index => $jawaban) {
            $waktuMenjawab = $jawaban['waktu_menjawab'];

            // Hitung durasi dalam detik
            $durasiDetik = strtotime($waktuMenjawab) - strtotime($waktuSebelumnya);

            // Konversi ke menit dan detik
            $menit = floor($durasiDetik / 60);
            $detik = $durasiDetik % 60;

            $jawaban['durasi_pengerjaan_detik'] = $durasiDetik;
            $jawaban['durasi_pengerjaan_format'] = sprintf('%d menit %d detik', $menit, $detik);
            $jawaban['nomor_soal'] = $index + 1;

            $hasilDenganDurasi[] = $jawaban;
            $waktuSebelumnya = $waktuMenjawab;
        }

        return $hasilDenganDurasi;
    }

    public function daftarHasilUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil daftar ujian yang sudah selesai dengan hasil dan informasi waktu
        $data['daftarUjian'] = $db->table('jadwal_ujian ju')
            ->select('ju.jadwal_id, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
                 s.nama_sekolah, g.nama_lengkap as nama_guru, ju.tanggal_mulai, ju.tanggal_selesai,
                 COUNT(DISTINCT pu.peserta_ujian_id) as jumlah_peserta,
                 COUNT(DISTINCT CASE WHEN pu.status = "selesai" THEN pu.peserta_ujian_id END) as peserta_selesai,
                 AVG(TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai))) as rata_rata_durasi_detik,
                 MIN(TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai))) as durasi_tercepat_detik,
                 MAX(TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai))) as durasi_terlama_detik,
                 DATE_FORMAT(ju.tanggal_mulai, "%d/%m/%Y %H:%i") as tanggal_mulai_format,
                 DATE_FORMAT(ju.tanggal_selesai, "%d/%m/%Y %H:%i") as tanggal_selesai_format')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->join('peserta_ujian pu', 'pu.jadwal_id = ju.jadwal_id AND pu.status = "selesai"', 'left')
            ->where('ju.status', 'selesai')
            ->groupBy('ju.jadwal_id, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, g.nama_lengkap, ju.tanggal_mulai, ju.tanggal_selesai')
            ->orderBy('ju.tanggal_selesai', 'DESC')
            ->get()
            ->getResultArray();

        // Format durasi untuk setiap ujian
        foreach ($data['daftarUjian'] as &$ujian) {
            // Format rata-rata durasi
            if ($ujian['rata_rata_durasi_detik']) {
                $jam = floor($ujian['rata_rata_durasi_detik'] / 3600);
                $menit = floor(($ujian['rata_rata_durasi_detik'] % 3600) / 60);
                $detik = $ujian['rata_rata_durasi_detik'] % 60;
                $ujian['rata_rata_durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
            } else {
                $ujian['rata_rata_durasi_format'] = '-';
            }

            // Format durasi tercepat
            if ($ujian['durasi_tercepat_detik']) {
                $jam = floor($ujian['durasi_tercepat_detik'] / 3600);
                $menit = floor(($ujian['durasi_tercepat_detik'] % 3600) / 60);
                $detik = $ujian['durasi_tercepat_detik'] % 60;
                $ujian['durasi_tercepat_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
            } else {
                $ujian['durasi_tercepat_format'] = '-';
            }

            // Format durasi terlama
            if ($ujian['durasi_terlama_detik']) {
                $jam = floor($ujian['durasi_terlama_detik'] / 3600);
                $menit = floor(($ujian['durasi_terlama_detik'] % 3600) / 60);
                $detik = $ujian['durasi_terlama_detik'] % 60;
                $ujian['durasi_terlama_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
            } else {
                $ujian['durasi_terlama_format'] = '-';
            }
        }

        return view('admin/hasil/daftar', $data);
    }


    public function hasilUjianSiswa($jadwalId)
    {
        $db = \Config\Database::connect();

        // Ambil info ujian dengan informasi waktu
        $ujian = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
                 s.nama_sekolah, g.nama_lengkap as nama_guru,
                 DATE_FORMAT(ju.tanggal_mulai, "%d/%m/%Y %H:%i") as tanggal_mulai_format,
                 DATE_FORMAT(ju.tanggal_selesai, "%d/%m/%Y %H:%i") as tanggal_selesai_format')
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

        // Ambil hasil siswa dengan perhitungan nilai dan informasi waktu
        $hasilSiswa = $db->table('peserta_ujian pu')
            ->select('pu.peserta_ujian_id, pu.status, pu.waktu_mulai, pu.waktu_selesai,
                 siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta,
                 u.username,
                 TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai) as durasi_pengerjaan,
                 TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_detik,
                 DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                 DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('users u', 'u.user_id = siswa.user_id', 'left')
            ->where('pu.jadwal_id', $jadwalId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung nilai untuk setiap siswa
        foreach ($hasilSiswa as &$siswa) {
            if ($siswa['status'] === 'selesai') {
                // SIMPLE FIX: Gunakan waktu_menjawab sebagai pengganti hasil_id untuk ordering
                $lastResult = $db->table('hasil_ujian')
                    ->select('theta_saat_ini, se_saat_ini')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->orderBy('waktu_menjawab', 'DESC')
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

                // Format durasi
                if ($siswa['durasi_detik']) {
                    $jam = floor($siswa['durasi_detik'] / 3600);
                    $menit = floor(($siswa['durasi_detik'] % 3600) / 60);
                    $detik = $siswa['durasi_detik'] % 60;
                    $siswa['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);

                    // Hitung rata-rata per soal
                    if ($totalSoal > 0) {
                        $rataRataDetik = $siswa['durasi_detik'] / $totalSoal;
                        $rataRataMenit = floor($rataRataDetik / 60);
                        $rataRataDetikSisa = $rataRataDetik % 60;
                        $siswa['rata_rata_per_soal'] = sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetikSisa);
                    } else {
                        $siswa['rata_rata_per_soal'] = '-';
                    }
                } else {
                    $siswa['durasi_format'] = '-';
                    $siswa['rata_rata_per_soal'] = '-';
                }
            } else {
                $siswa['theta_akhir'] = null;
                $siswa['skor'] = null;
                $siswa['nilai'] = null;
                $siswa['se_akhir'] = null;
                $siswa['jawaban_benar'] = 0;
                $siswa['total_soal'] = 0;
                $siswa['durasi_format'] = '-';
                $siswa['rata_rata_per_soal'] = '-';
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

        // Ambil detail peserta dan ujian dengan informasi waktu
        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, j.nama_jenis, 
                 siswa.nama_lengkap, siswa.nomor_peserta,
                 k.nama_kelas, k.tahun_ajaran, s.nama_sekolah,
                 g.nama_lengkap as nama_guru,
                 TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai) as durasi_total,
                 TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_total_detik,
                 DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                 DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
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

        // FIX: Ambil detail jawaban dengan waktu - hapus alias dan gunakan waktu_menjawab untuk ordering
        $detailJawaban = $db->table('hasil_ujian')
            ->select('hasil_ujian.*, s.pertanyaan, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, 
                 s.jawaban_benar, s.tingkat_kesulitan, s.foto, s.pembahasan,
                 DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian s', 's.soal_id = hasil_ujian.soal_id', 'left')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung durasi per soal
        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);

        // Hitung statistik
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        // Format durasi total
        if ($hasil['durasi_total_detik']) {
            $jam = floor($hasil['durasi_total_detik'] / 3600);
            $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
            $detik = $hasil['durasi_total_detik'] % 60;
            $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
        }

        // Hitung rata-rata waktu per soal
        if ($totalSoal > 0) {
            $rataRataWaktu = $hasil['durasi_total_detik'] / $totalSoal;
            $rataRataMenit = floor($rataRataWaktu / 60);
            $rataRataDetik = $rataRataWaktu % 60;
            $rataRataWaktuFormat = sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik);
        } else {
            $rataRataWaktuFormat = '-';
        }

        // Hitung statistik waktu pengerjaan per soal
        $statistikWaktu = [
            'waktu_tercepat' => 0,
            'waktu_terlama' => 0,
            'rata_rata' => 0
        ];

        if ($totalSoal > 0) {
            $durasiArray = array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik');
            $statistikWaktu = [
                'waktu_tercepat' => min($durasiArray),
                'waktu_terlama' => max($durasiArray),
                'rata_rata' => $rataRataWaktu
            ];
        }

        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'totalSoal' => $totalSoal,
            'jawabanBenar' => $jawabanBenar,
            'rataRataWaktuFormat' => $rataRataWaktuFormat,
            'statistikWaktu' => $statistikWaktu
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

    // ===== KELOLA BANK SOAL =====

    public function bankSoal()
    {
        $db = \Config\Database::connect();

        // Admin bisa akses semua kategori
        $kategoriList = $db->table('bank_ujian')
            ->select('kategori, COUNT(*) as jumlah_bank')
            ->groupBy('kategori')
            ->orderBy('kategori', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil semua jenis ujian untuk dropdown
        $jenisUjianList = $this->jenisUjianModel->findAll();

        $data = [
            'kategoriList' => $kategoriList,
            'jenisUjianList' => $jenisUjianList
        ];

        return view('admin/bank_soal/index', $data);
    }

    public function tambahBankSoal()
    {
        $rules = [
            'kategori' => 'required',
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]',
            'deskripsi' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Cek apakah kombinasi kategori + jenis_ujian + nama_ujian sudah ada
        $db = \Config\Database::connect();
        $existing = $db->table('bank_ujian')
            ->where('kategori', $this->request->getPost('kategori'))
            ->where('jenis_ujian_id', $this->request->getPost('jenis_ujian_id'))
            ->where('nama_ujian', $this->request->getPost('nama_ujian'))
            ->get()->getRowArray();

        if ($existing) {
            session()->setFlashdata('error', 'Bank soal dengan kategori, jenis ujian, dan nama ujian yang sama sudah ada.');
            return redirect()->back()->withInput();
        }

        try {
            $bankUjianData = [
                'kategori' => $this->request->getPost('kategori'),
                'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
                'nama_ujian' => $this->request->getPost('nama_ujian'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $db->table('bank_ujian')->insert($bankUjianData);
            session()->setFlashdata('success', 'Bank soal berhasil ditambahkan!');
            return redirect()->to(base_url('admin/bank-soal'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding bank soal: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah bank soal.');
            return redirect()->back()->withInput();
        }
    }

    public function bankSoalKategori($kategori)
    {
        $db = \Config\Database::connect();

        // Admin bisa akses semua kategori tanpa validasi
        $jenisUjianList = $db->table('bank_ujian')
            ->select('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis, COUNT(*) as jumlah_ujian')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->where('bank_ujian.kategori', $kategori)
            ->groupBy('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis')
            ->orderBy('jenis_ujian.nama_jenis', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'kategori' => $kategori,
            'jenisUjianList' => $jenisUjianList
        ];

        return view('admin/bank_soal/kategori', $data);
    }

    public function bankSoalJenisUjian($kategori, $jenisUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil daftar ujian dalam jenis ujian dan kategori ini
        $ujianList = $db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name, 
                 (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId)
            ->orderBy('bank_ujian.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil info jenis ujian
        $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);

        $data = [
            'kategori' => $kategori,
            'jenisUjian' => $jenisUjian,
            'ujianList' => $ujianList
        ];

        return view('admin/bank_soal/jenis_ujian', $data);
    }

    public function bankSoalUjian($kategori, $jenisUjianId, $bankUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil info bank ujian
        $bankUjian = $db->table('bank_ujian')
            ->select('bank_ujian.*, jenis_ujian.nama_jenis, users.username as creator_name')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.bank_ujian_id', $bankUjianId)
            ->get()
            ->getRowArray();

        if (!$bankUjian) {
            session()->setFlashdata('error', 'Bank ujian tidak ditemukan');
            return redirect()->to(base_url('admin/bank-soal'));
        }

        // Ambil soal-soal dalam bank ujian ini
        $soalList = $db->table('soal_ujian')
            ->select('soal_ujian.*, users.username as creator_name')
            ->join('users', 'users.user_id = soal_ujian.created_by', 'left')
            ->where('bank_ujian_id', $bankUjianId)
            ->where('is_bank_soal', true)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'kategori' => $kategori,
            'bankUjian' => $bankUjian,
            'soalList' => $soalList
        ];

        return view('admin/bank_soal/ujian', $data);
    }

    public function tambahSoalBankUjian()
    {
        $bankUjianId = $this->request->getPost('bank_ujian_id');
        $userId = session()->get('user_id');

        // Admin bisa tambah soal ke bank ujian manapun
        $db = \Config\Database::connect();
        $bankUjian = $db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();

        if (!$bankUjian) {
            return redirect()->back()->with('error', 'Bank ujian tidak ditemukan');
        }

        // Validasi form input
        $rules = [
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        // Ambil data dari form
        $data = [
            'ujian_id' => null,
            'bank_ujian_id' => $bankUjianId,
            'is_bank_soal' => true,
            'created_by' => $userId,
            'kode_soal' => $this->request->getPost('kode_soal'),
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
            $newName = $fotoFile->getRandomName();
            $uploadPath = 'uploads/soal';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fotoFile->move($uploadPath, $newName);
            $data['foto'] = $newName;
        }

        try {
            $this->soalUjianModel->insert($data);
            session()->setFlashdata('success', 'Soal berhasil ditambahkan ke bank ujian!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage());
        }
    }

    public function editSoalBankUjian($soalId)
    {
        // Admin bisa edit soal bank ujian siapa saja
        $soal = $this->soalUjianModel->find($soalId);
        if (!$soal || !$soal['is_bank_soal']) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Validasi form input (sama seperti di guru)
        $rules = [
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        $data = [
            'kode_soal' => $this->request->getPost('kode_soal'),
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

        // Handle foto upload/delete (sama seperti di guru)
        $uploadPath = 'uploads/soal';
        $fotoFile = $this->request->getFile('foto');

        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
            if (!empty($soal['foto'])) {
                $fotoPath = $uploadPath . '/' . $soal['foto'];
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }

            $newName = $fotoFile->getRandomName();
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fotoFile->move($uploadPath, $newName);
            $data['foto'] = $newName;
        }

        if ($this->request->getPost('hapus_foto') == '1' && !empty($soal['foto'])) {
            $fotoPath = $uploadPath . '/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $data['foto'] = null;
        }

        try {
            $this->soalUjianModel->update($soalId, $data);
            session()->setFlashdata('success', 'Soal berhasil diupdate!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

    public function hapusSoalBankUjian($soalId)
    {
        // Admin bisa hapus soal bank ujian siapa saja
        $soal = $this->soalUjianModel->find($soalId);
        if (!$soal || !$soal['is_bank_soal']) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Hapus foto jika ada
        if (!empty($soal['foto'])) {
            $fotoPath = 'uploads/soal/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        try {
            $this->soalUjianModel->delete($soalId);
            session()->setFlashdata('success', 'Soal berhasil dihapus!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus soal.');
        }
    }

    public function hapusBankUjian($bankUjianId)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Cek apakah ada soal di bank ujian ini
            $jumlahSoal = $db->table('soal_ujian')
                ->where('bank_ujian_id', $bankUjianId)
                ->where('is_bank_soal', true)
                ->countAllResults();

            if ($jumlahSoal > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus bank ujian karena masih memiliki {$jumlahSoal} soal. Hapus soal terlebih dahulu.");
                return redirect()->back();
            }

            // Hapus bank ujian
            $db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Bank ujian berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting bank ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus bank ujian.');
        }

        return redirect()->to(base_url('admin/bank-soal'));
    }

    // API Methods untuk AJAX (bisa digunakan untuk modal atau select dinamis)
    public function getKategoriTersedia()
    {
        $db = \Config\Database::connect();

        // Admin bisa akses semua kategori
        $kategori = $db->table('bank_ujian')
            ->select('DISTINCT kategori')
            ->orderBy('kategori', 'ASC')
            ->get()
            ->getResultArray();

        $kategoriList = array_column($kategori, 'kategori');

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $kategoriList
        ]);
    }

    public function getJenisUjianByKategori()
    {
        $kategori = $this->request->getGet('kategori');
        $db = \Config\Database::connect();

        if (!$kategori) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kategori harus dipilih'
            ]);
        }

        // Admin bisa akses semua jenis ujian di kategori manapun
        $jenisUjian = $db->table('bank_ujian')
            ->select('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis, COUNT(*) as jumlah_bank')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->where('bank_ujian.kategori', $kategori)
            ->groupBy('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis')
            ->orderBy('jenis_ujian.nama_jenis', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $jenisUjian
        ]);
    }

    public function getBankUjianByKategoriJenis()
    {
        $kategori = $this->request->getGet('kategori');
        $jenisUjianId = $this->request->getGet('jenis_ujian_id');
        $db = \Config\Database::connect();

        if (!$kategori || !$jenisUjianId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kategori dan jenis ujian harus dipilih'
            ]);
        }

        // Admin bisa akses semua bank ujian
        $bankUjian = $db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name,
                 (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId)
            ->orderBy('bank_ujian.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $bankUjian
        ]);
    }

    public function getSoalBankUjian()
    {
        $bankUjianId = $this->request->getGet('bank_ujian_id');
        $db = \Config\Database::connect();

        if (!$bankUjianId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Bank ujian harus dipilih'
            ]);
        }

        // Validasi bank ujian exists
        $bankUjian = $db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();
        if (!$bankUjian) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Bank ujian tidak ditemukan'
            ]);
        }

        // Admin bisa akses semua soal bank ujian
        $soalList = $this->soalUjianModel
            ->select('soal_ujian.*, soal_ujian.kode_soal')
            ->where('bank_ujian_id', $bankUjianId)
            ->where('is_bank_soal', true)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $soalList,
            'bank_ujian' => $bankUjian
        ]);
    }

    // ===== KELOLA JENIS UJIAN =====

    public function daftarJenisUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil semua jenis ujian dengan informasi lengkap
        $data['jenis_ujian'] = $db->table('jenis_ujian ju')
            ->select('ju.*, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.sekolah_id,
                 g.nama_lengkap as guru_pembuat, u.username as user_pembuat,
                 COUNT(DISTINCT uj.id_ujian) as total_ujian')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('users u', 'u.user_id = ju.created_by', 'left')
            ->join('guru g', 'g.user_id = ju.created_by', 'left')
            ->join('ujian uj', 'uj.jenis_ujian_id = ju.jenis_ujian_id', 'left')
            ->groupBy('ju.jenis_ujian_id, ju.nama_jenis, ju.deskripsi, ju.kelas_id, ju.created_by, ju.created_at, ju.updated_at,
                  k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.sekolah_id, g.nama_lengkap, u.username')
            ->orderBy('ju.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil semua sekolah untuk filter/dropdown
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas untuk dropdown tambah/edit
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.sekolah_id')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/jenis_ujian/daftar', $data);
    }

    public function jenisUjian()
    {
        $db = \Config\Database::connect();

        // Admin bisa melihat semua jenis ujian dari semua guru
        $data['jenis_ujian'] = $db->table('jenis_ujian ju')
            ->select('ju.*, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, u.username as creator_name, g.nama_lengkap as guru_nama')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('users u', 'u.user_id = ju.created_by', 'left')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->orderBy('ju.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil semua kelas untuk dropdown
        $data['semua_kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/jenis_ujian', $data);
    }

    public function tambahJenisUjian()
    {
        $kelasId = $this->request->getPost('kelas_id');
        $userId = session()->get('user_id');

        // Validasi input
        $rules = [
            'nama_jenis' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'required|min_length[10]',
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Validasi kelas exists
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan.');
            return redirect()->back()->withInput();
        }

        try {
            $data = [
                'nama_jenis' => $this->request->getPost('nama_jenis'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'kelas_id' => $kelasId,
                'created_by' => $userId
            ];

            $this->jenisUjianModel->insert($data);
            session()->setFlashdata('success', 'Jenis ujian berhasil ditambahkan!');
            return redirect()->to(base_url('admin/jenis-ujian'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding jenis ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah jenis ujian: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function editJenisUjian($jenisUjianId)
    {
        $userId = session()->get('user_id');

        // Validasi input
        $rules = [
            'nama_jenis' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'required|min_length[10]',
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Cek jenis ujian exists
        $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);
        if (!$jenisUjian) {
            session()->setFlashdata('error', 'Jenis ujian tidak ditemukan.');
            return redirect()->to(base_url('admin/jenis-ujian'));
        }

        $kelasId = $this->request->getPost('kelas_id');

        // Validasi kelas exists
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan.');
            return redirect()->back()->withInput();
        }

        try {
            $data = [
                'nama_jenis' => $this->request->getPost('nama_jenis'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'kelas_id' => $kelasId
            ];

            $this->jenisUjianModel->update($jenisUjianId, $data);
            session()->setFlashdata('success', 'Jenis ujian berhasil diperbarui!');
            return redirect()->to(base_url('admin/jenis-ujian'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating jenis ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui jenis ujian: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function hapusJenisUjian($jenisUjianId)
    {
        try {
            // Cek jenis ujian exists
            $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);
            if (!$jenisUjian) {
                session()->setFlashdata('error', 'Jenis ujian tidak ditemukan.');
                return redirect()->to(base_url('admin/jenis-ujian'));
            }

            // Cek apakah ada ujian yang menggunakan jenis ujian ini
            $db = \Config\Database::connect();
            $ujianTerkait = $db->table('ujian')
                ->where('jenis_ujian_id', $jenisUjianId)
                ->countAllResults();

            if ($ujianTerkait > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus jenis ujian ini karena masih ada {$ujianTerkait} ujian yang menggunakan jenis ujian ini. Harap hapus ujian terkait terlebih dahulu.");
                return redirect()->to(base_url('admin/jenis-ujian'));
            }

            $this->jenisUjianModel->delete($jenisUjianId);
            session()->setFlashdata('success', 'Jenis ujian berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting jenis ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus jenis ujian.');
        }

        return redirect()->to(base_url('admin/jenis-ujian'));
    }

    // API method untuk mendapatkan kelas berdasarkan sekolah (untuk AJAX)
    public function getKelasBySekolah($sekolahId)
    {
        $db = \Config\Database::connect();

        $kelas = $db->table('kelas')
            ->select('kelas_id, nama_kelas, tahun_ajaran')
            ->where('sekolah_id', $sekolahId)
            ->orderBy('tahun_ajaran', 'DESC')
            ->orderBy('nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $kelas
        ]);
    }
}
