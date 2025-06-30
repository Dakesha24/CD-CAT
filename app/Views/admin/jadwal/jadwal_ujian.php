<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary">Jadwal Ujian</h2>
            <p class="text-muted">Atur jadwal pelaksanaan ujian untuk kelas yang Anda ajar</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Jadwal
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Daftar Jadwal Ujian -->
    <div class="row g-4">
        <?php if (!empty($jadwal)): ?>
            <?php foreach ($jadwal as $j): ?>
                <?php
                $now = new DateTime();
                $startDate = new DateTime($j['tanggal_mulai']);
                $endDate = new DateTime($j['tanggal_selesai']);

                $statusColor = 'secondary';
                $statusText = str_replace('_', ' ', $j['status']);
                if ($j['status'] == 'sedang_berlangsung') {
                    $statusColor = 'success';
                } elseif ($j['status'] == 'selesai') {
                    $statusColor = 'dark';
                }
                ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="bi bi-calendar-event text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-<?= $statusColor ?> small">
                                            <?= ucwords($statusText) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $j['jadwal_id'] ?>">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="<?= base_url('admin/jadwal-ujian/hapus/' . $j['jadwal_id']) ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-2"><?= esc($j['nama_ujian']) ?></h5>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-code-square me-1"></i>Kode: <?= esc($j['kode_ujian']) ?>
                                </p>
                                <p class="card-text text-muted small mb-3">
                                    <i class="bi bi-building me-1"></i><?= esc($j['nama_sekolah']) ?>
                                    <br><i class="bi bi-people me-1"></i>Kelas <?= esc($j['nama_kelas']) ?>
                                    <br><i class="bi bi-person-check me-1"></i>Pengawas: <?= esc($j['nama_lengkap']) ?>
                                </p>

                                <div class="mb-3">
                                    <div class="row g-2 text-center">
                                        <div class="col-12">
                                            <div class="bg-light rounded p-2">
                                                <div class="fw-bold text-dark small"><?= date('d/m/Y H:i', strtotime($j['tanggal_mulai'])) ?></div>
                                                <small class="text-muted">Mulai</small>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="bg-light rounded p-2">
                                                <div class="fw-bold text-dark small"><?= date('d/m/Y H:i', strtotime($j['tanggal_selesai'])) ?></div>
                                                <small class="text-muted">Selesai</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Kode Akses:</span>
                                        <span class="badge bg-light text-dark"><?= esc($j['kode_akses']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $j['jadwal_id'] ?>">
                                    <i class="bi bi-pencil me-2"></i>Edit Jadwal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Belum ada jadwal ujian</h5>
                        <p class="text-muted">Tambahkan jadwal ujian pertama untuk kelas yang Anda ajar</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Jadwal Ujian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/jadwal-ujian/tambah') ?>" method="post" id="formTambahJadwal">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Step 1: Pilih Sekolah -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekolah <span class="text-danger">*</span></label>
                            <select id="sekolah-select-tambah" name="sekolah_id" class="form-select" required>
                                <option value="">Pilih Sekolah Terlebih Dahulu</option>
                                <?php if (!empty($sekolah)): ?>
                                    <?php foreach ($sekolah as $s): ?>
                                        <option value="<?= $s['sekolah_id'] ?>"><?= esc($s['nama_sekolah']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Step 2: Pilih Kelas -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select id="kelas-select-tambah" name="kelas_id" class="form-select" required disabled>
                                <option value="">Pilih Sekolah Terlebih Dahulu</option>
                            </select>
                            <div class="form-text">Pilih kelas yang akan mengikuti ujian</div>
                        </div>

                        <!-- Step 3: Pilih Ujian -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ujian <span class="text-danger">*</span></label>
                            <select id="ujian-select-tambah" name="ujian_id" class="form-select" required disabled>
                                <option value="">Pilih Kelas Terlebih Dahulu</option>
                            </select>
                            <div class="form-text">Menampilkan ujian umum dan ujian khusus untuk kelas yang dipilih</div>
                        </div>

                        <!-- Step 4: Pilih Guru Pengawas -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Guru Pengawas <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">Pilih Guru Pengawas</option>
                                <?php if (!empty($guru)): ?>
                                    <?php foreach ($guru as $g): ?>
                                        <option value="<?= $g['guru_id'] ?>">
                                            <?= esc($g['nama_lengkap']) ?> - <?= esc($g['mata_pelajaran']) ?>
                                            <br><small class="text-muted"><?= esc($g['nama_sekolah']) ?></small>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Pilih guru yang akan mengawas ujian</div>
                        </div>

                        <!-- Informasi Waktu -->
                        <div class="col-12">
                            <hr class="my-2">
                        </div>
                        <div class="col-12">
                            <h6 class="fw-semibold text-primary mb-3">
                                <i class="bi bi-clock me-2"></i>Pengaturan Waktu
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal & Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal & Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
                        </div>

                        <!-- Kode Akses -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Kode Akses <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="kode_akses" class="form-control" placeholder="Masukkan kode akses ujian" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="generateKodeAkses()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Generate
                                </button>
                            </div>
                            <div class="form-text">Kode yang akan digunakan siswa untuk mengakses ujian</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($jadwal)): ?>
    <?php foreach ($jadwal as $j): ?>
        <div class="modal fade" id="editJadwalModal<?= $j['jadwal_id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil text-warning me-2"></i>Edit Jadwal Ujian
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('admin/jadwal-ujian/edit/' . $j['jadwal_id']) ?>" method="post">
                        <div class="modal-body">
                            <!-- Info Jadwal yang Sedang Diedit -->
                            <div class="alert alert-info mb-4">
                                <h6 class="alert-heading mb-2">
                                    <i class="bi bi-info-circle me-2"></i>Informasi Jadwal
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Ujian:</strong> <?= esc($j['nama_ujian']) ?></p>
                                        <p class="mb-1"><strong>Kelas:</strong> <?= esc($j['nama_kelas']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Sekolah:</strong> <?= esc($j['nama_sekolah']) ?></p>
                                        <p class="mb-0"><strong>Status:</strong>
                                            <span class="badge bg-<?= $j['status'] == 'sedang_berlangsung' ? 'success' : ($j['status'] == 'selesai' ? 'dark' : 'secondary') ?>">
                                                <?= ucwords(str_replace('_', ' ', $j['status'])) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sekolah <span class="text-danger">*</span></label>
                                    <select class="form-select sekolah-select-edit" name="sekolah_id" data-jadwal-id="<?= $j['jadwal_id'] ?>" required>
                                        <option value="">Pilih Sekolah</option>
                                        <?php if (!empty($sekolah)): ?>
                                            <?php foreach ($sekolah as $s): ?>
                                                <option value="<?= $s['sekolah_id'] ?>"
                                                    <?= (isset($j['sekolah_id']) && $j['sekolah_id'] == $s['sekolah_id']) ? 'selected' : '' ?>>
                                                    <?= esc($s['nama_sekolah']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-select kelas-select-edit" name="kelas_id" data-jadwal-id="<?= $j['jadwal_id'] ?>" required>
                                        <option value="<?= $j['kelas_id'] ?>" selected>
                                            <?= esc($j['nama_kelas']) ?> (<?= esc($j['nama_sekolah']) ?>)
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ujian <span class="text-danger">*</span></label>
                                    <select class="form-select ujian-select-edit" name="ujian_id" data-jadwal-id="<?= $j['jadwal_id'] ?>" required>
                                        <option value="<?= $j['ujian_id'] ?>" selected>
                                            <?= esc($j['nama_ujian']) ?> (<?= esc($j['kode_ujian']) ?>)
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Guru Pengawas <span class="text-danger">*</span></label>
                                    <select name="guru_id" class="form-select" required>
                                        <option value="">Pilih Guru Pengawas</option>
                                        <?php if (!empty($guru)): ?>
                                            <?php foreach ($guru as $g): ?>
                                                <option value="<?= $g['guru_id'] ?>" <?= ($g['guru_id'] == $j['guru_id']) ? 'selected' : '' ?>>
                                                    <?= esc($g['nama_lengkap']) ?> - <?= esc($g['mata_pelajaran']) ?>
                                                    (<?= esc($g['nama_sekolah']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal & Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($j['tanggal_mulai'])) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal & Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_selesai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($j['tanggal_selesai'])) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kode Akses <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_akses" class="form-control" value="<?= esc($j['kode_akses']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="belum_mulai" <?= ($j['status'] == 'belum_mulai') ? 'selected' : '' ?>>Belum Mulai</option>
                                        <option value="sedang_berlangsung" <?= ($j['status'] == 'sedang_berlangsung') ? 'selected' : '' ?>>Sedang Berlangsung</option>
                                        <option value="selesai" <?= ($j['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-2"></i>Update Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<style>
    .hover-card {
        transition: all 0.3s ease;
        border: none;
        min-height: 420px;
    }

    .hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .hover-card .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .badge {
        font-size: 0.75rem;
    }

    .card-title {
        line-height: 1.3;
        min-height: 2.6em;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-text {
        min-height: 1.5em;
    }

    /* Style for form steps */
    .form-label .text-danger {
        font-size: 0.8em;
    }

    .alert-info {
        background-color: #e7f3ff;
        border-color: #b3d9ff;
        color: #004085;
    }

    /* Disabled select styling */
    .form-select:disabled {
        background-color: #f8f9fa;
        opacity: 0.65;
    }

    /* Loading state for selects */
    .form-select.loading {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 1v6l4-4-4 4 4 4-4-4v6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
    }

    /* Better spacing for form sections */
    .modal-body hr {
        margin: 1.5rem 0;
        opacity: 0.3;
    }

    /* Input group button styling */
    .input-group .btn-outline-secondary {
        border-color: #ced4da;
    }

    .input-group .btn-outline-secondary:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }

    /* Better dropdown option styling */
    .form-select option {
        padding: 0.5rem 0.75rem;
    }

    /* Card status badges */
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .badge.bg-success {
        background-color: #198754 !important;
    }

    .badge.bg-dark {
        background-color: #212529 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .hover-card {
            min-height: auto;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .card-body.p-4 {
            padding: 1rem !important;
        }
    }

    /* Animation for dropdown changes */
    .form-select {
        transition: all 0.2s ease-in-out;
    }

    /* Error states */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Success states */
    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Custom tooltip for form hints */
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Section headers in modal */
    .modal-body h6 {
        color: #0d6efd;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handler untuk Modal Tambah
        const sekolahSelectTambah = document.getElementById('sekolah-select-tambah');
        const kelasSelectTambah = document.getElementById('kelas-select-tambah');
        const ujianSelectTambah = document.getElementById('ujian-select-tambah');

        if (sekolahSelectTambah && kelasSelectTambah) {
            sekolahSelectTambah.addEventListener('change', function() {
                handleSekolahChangeTambah(this, kelasSelectTambah, ujianSelectTambah);
            });
        }

        if (kelasSelectTambah && ujianSelectTambah) {
            kelasSelectTambah.addEventListener('change', function() {
                handleKelasChangeTambah(this, ujianSelectTambah);
            });
        }

        // Handler untuk Modal Edit
        const sekolahSelectsEdit = document.querySelectorAll('.sekolah-select-edit');
        sekolahSelectsEdit.forEach(function(sekolahSelect) {
            const jadwalId = sekolahSelect.dataset.jadwalId;
            const kelasSelect = document.querySelector(`.kelas-select-edit[data-jadwal-id="${jadwalId}"]`);
            const ujianSelect = document.querySelector(`.ujian-select-edit[data-jadwal-id="${jadwalId}"]`);

            if (kelasSelect) {
                sekolahSelect.addEventListener('change', function() {
                    handleSekolahChangeEdit(this, kelasSelect, ujianSelect);
                });
            }

            if (kelasSelect && ujianSelect) {
                kelasSelect.addEventListener('change', function() {
                    handleKelasChangeEdit(this, ujianSelect);
                });
            }
        });

        function handleSekolahChangeTambah(sekolahSelect, kelasSelect, ujianSelect) {
            const sekolahId = sekolahSelect.value;

            // Reset dropdown kelas dan ujian
            kelasSelect.innerHTML = '<option value="">Memuat kelas...</option>';
            kelasSelect.disabled = true;
            ujianSelect.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
            ujianSelect.disabled = true;

            if (!sekolahId) {
                kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
                return;
            }

            // Fetch kelas berdasarkan sekolah
            fetch(`<?= base_url('admin/api/kelas-by-sekolah/') ?>${sekolahId}`)
                .then(response => response.json())
                .then(responseData => {
                    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

                    if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                        responseData.data.forEach(kelas => {
                            const option = document.createElement('option');
                            option.value = kelas.kelas_id;
                            option.textContent = kelas.nama_kelas;
                            kelasSelect.appendChild(option);
                        });
                    }

                    kelasSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching kelas:', error);
                    kelasSelect.innerHTML = '<option value="">Gagal memuat kelas</option>';
                    kelasSelect.disabled = false;
                });
        }

        function handleKelasChangeTambah(kelasSelect, ujianSelect) {
            const kelasId = kelasSelect.value;

            ujianSelect.innerHTML = '<option value="">Memuat ujian...</option>';
            ujianSelect.disabled = true;

            if (!kelasId) {
                ujianSelect.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
                return;
            }

            // Fetch ujian berdasarkan kelas
            fetch(`<?= base_url('admin/api/ujian-by-kelas/') ?>${kelasId}`)
                .then(response => response.json())
                .then(responseData => {
                    ujianSelect.innerHTML = '<option value="">Pilih Ujian</option>';

                    if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                        responseData.data.forEach(ujian => {
                            const option = document.createElement('option');
                            option.value = ujian.id_ujian;
                            option.textContent = `${ujian.nama_ujian} (${ujian.kode_ujian})`;
                            if (!ujian.kelas_id) {
                                option.textContent += ' - Umum';
                            }
                            ujianSelect.appendChild(option);
                        });
                    }

                    ujianSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching ujian:', error);
                    ujianSelect.innerHTML = '<option value="">Gagal memuat ujian</option>';
                    ujianSelect.disabled = false;
                });
        }

        function handleSekolahChangeEdit(sekolahSelect, kelasSelect, ujianSelect) {
            // Similar logic for edit modal
            const sekolahId = sekolahSelect.value;

            kelasSelect.innerHTML = '<option value="">Memuat kelas...</option>';
            kelasSelect.disabled = true;
            ujianSelect.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
            ujianSelect.disabled = true;

            if (!sekolahId) {
                kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
                return;
            }

            fetch(`<?= base_url('admin/api/kelas-by-sekolah/') ?>${sekolahId}`)
                .then(response => response.json())
                .then(responseData => {
                    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

                    if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                        responseData.data.forEach(kelas => {
                            const option = document.createElement('option');
                            option.value = kelas.kelas_id;
                            option.textContent = kelas.nama_kelas;
                            kelasSelect.appendChild(option);
                        });
                    }

                    kelasSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching kelas:', error);
                    kelasSelect.innerHTML = '<option value="">Gagal memuat kelas</option>';
                    kelasSelect.disabled = false;
                });
        }

        function handleKelasChangeEdit(kelasSelect, ujianSelect) {
            const kelasId = kelasSelect.value;

            ujianSelect.innerHTML = '<option value="">Memuat ujian...</option>';
            ujianSelect.disabled = true;

            if (!kelasId) {
                ujianSelect.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
                return;
            }

            fetch(`<?= base_url('admin/api/ujian-by-kelas/') ?>${kelasId}`)
                .then(response => response.json())
                .then(responseData => {
                    ujianSelect.innerHTML = '<option value="">Pilih Ujian</option>';

                    if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                        responseData.data.forEach(ujian => {
                            const option = document.createElement('option');
                            option.value = ujian.id_ujian;
                            option.textContent = `${ujian.nama_ujian} (${ujian.kode_ujian})`;
                            if (!ujian.kelas_id) {
                                option.textContent += ' - Umum';
                            }
                            ujianSelect.appendChild(option);
                        });
                    }

                    ujianSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching ujian:', error);
                    ujianSelect.innerHTML = '<option value="">Gagal memuat ujian</option>';
                    ujianSelect.disabled = false;
                });
        }

        // Reset form when modal is closed
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                const form = modal.querySelector('form');
                if (form && form.id === 'formTambahJadwal') {
                    form.reset();

                    // Reset dropdowns
                    const kelasSelect = form.querySelector('#kelas-select-tambah');
                    const ujianSelect = form.querySelector('#ujian-select-tambah');

                    if (kelasSelect) {
                        kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
                        kelasSelect.disabled = true;
                    }

                    if (ujianSelect) {
                        ujianSelect.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
                        ujianSelect.disabled = true;
                    }
                }
            });
        });
    });

    // Function to generate random access code
    function generateKodeAkses() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 8; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.querySelector('input[name="kode_akses"]').value = result;
    }
</script>

<?= $this->endSection() ?>