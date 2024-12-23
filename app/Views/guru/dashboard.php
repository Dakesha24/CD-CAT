<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Guru</h2>

    <div class="row g-4">
        <!-- Ujian Aktif Card -->
        <div class="col-md-6 col-lg-3">
            <a href="<?= base_url('guru/ujian-aktif') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #e3f2fd;">
                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                        <h5 class="card-title mb-3">Ujian Aktif</h5>
                        <p class="card-text text-muted">Monitoring siswa yang sedang ujian</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Bank Soal Card -->
        <div class="col-md-6 col-lg-3">
            <a href="<?= base_url('/guru/daftar_soal') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #e8f5e9;">
                            <i class="bi bi-journal-text fs-1 text-success"></i>
                        </div>
                        <h5 class="card-title mb-3">Bank Soal</h5>
                        <p class="card-text text-muted">Kelola soal-soal ujian</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Jadwal Ujian Card -->
        <div class="col-md-6 col-lg-3">
            <a href="<?= base_url('guru/jadwal-ujian') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #fff3e0;">
                            <i class="bi bi-calendar-event fs-1 text-warning"></i>
                        </div>
                        <h5 class="card-title mb-3">Jadwal Ujian</h5>
                        <p class="card-text text-muted">Atur jadwal pelaksanaan ujian</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Hasil Ujian Card -->
        <div class="col-md-6 col-lg-3">
            <a href="<?= base_url('guru/hasil-ujian') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #e0f7fa;">
                            <i class="bi bi-clipboard-data fs-1 text-info"></i>
                        </div>
                        <h5 class="card-title mb-3">Hasil Ujian</h5>
                        <p class="card-text text-muted">Lihat hasil ujian siswa</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="row mt-5 g-4">
        <div class="col-12">
            <h4 class="mb-4">Statistik Cepat</h4>
        </div>
        <!-- Ujian Hari Ini -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Ujian Hari Ini</h6>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check fs-1 text-primary me-3"></i>
                        <div>
                            <h3 class="mb-0"><?= $ujian_today ?></h3>
                            <small class="text-muted">Ujian aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Siswa Sedang Ujian -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Siswa Sedang Ujian</h6>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-video3 fs-1 text-success me-3"></i>
                        <div>
                            <h3 class="mb-0"><?= $siswa ?></h3>
                            <small class="text-muted">Siswa aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Bank Soal -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Total Bank Soal</h6>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-journals fs-1 text-info me-3"></i>
                        <div>
                            <h3 class="mb-0"><?= $soal ?></h3>
                            <small class="text-muted">Soal tersedia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Ujian Mendatang -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Jadwal Ujian Mendatang</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($upcoming_ujian)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada jadwal ujian mendatang</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($upcoming_ujian as $ujian) : ?>
                                        <tr>
                                            <td>
                                                <?= $ujian['nama_ujian']
                                                ?>
                                            </td>
                                            <td>
                                                <?= $ujian['nama_kelas']
                                                ?>
                                            </td>
                                            <td>
                                                <?= date('d-m-Y', strtotime($ujian['tanggal_mulai']))
                                                ?>
                                            </td>
                                            <td>
                                                <?= date('H:i', strtotime($ujian['tanggal_mulai'])) . ' - ' . date('H:i', strtotime($ujian['tanggal_selesai'])) // Format waktu 
                                                ?>
                                            </td>
                                            <td>
                                                <?= ucfirst($ujian['status'])
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>