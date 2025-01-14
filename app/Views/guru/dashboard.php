<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4  py-5">
        <div class="col">
            <h2 class="mb-4">Dashboard Guru</h2>
            <div class="row g-4">
                <!-- Jenis Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-primary-subtle mx-auto">
                                <i class="bi bi-journal-text text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title">Jenis Ujian</h5>
                            <p class="card-text">Kelola kategori dan jenis ujian</p>
                            <a href="<?= base_url('guru/jenis-ujian') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Jenis Ujian
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-success-subtle mx-auto">
                                <i class="bi bi-file-earmark-text text-success fs-1"></i>
                            </div>
                            <h5 class="card-title">Ujian</h5>
                            <p class="card-text">Buat dan kelola ujian beserta soal-soalnya</p>
                            <a href="<?= base_url('guru/ujian') ?>" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Ujian
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Jadwal Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-info-subtle mx-auto">
                                <i class="bi bi-calendar-event text-info fs-1"></i>
                            </div>
                            <h5 class="card-title">Jadwal Ujian</h5>
                            <p class="card-text">Atur jadwal pelaksanaan ujian</p>
                            <a href="<?= base_url('guru/jadwal-ujian') ?>" class="btn btn-info">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Jadwal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hasil Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100 mt-4">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-danger-subtle mx-auto">
                                <i class="bi bi-clipboard-data text-danger fs-1"></i>
                            </div>
                            <h5 class="card-title">Hasil Ujian</h5>
                            <p class="card-text">Lihat dan analisis hasil ujian siswa</p>
                            <a href="<?= base_url('guru/hasil-ujian') ?>" class="btn btn-danger">
                                <i class="bi bi-bar-chart me-2"></i>Lihat Hasil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pengumuman Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100 mt-4">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-warning-subtle mx-auto">
                                <i class="bi bi-megaphone text-warning fs-1"></i>
                            </div>
                            <h5 class="card-title">Pengumuman</h5>
                            <p class="card-text">Buat dan kelola pengumuman</p>
                            <a href="<?= base_url('guru/pengumuman') ?>" class="btn btn-warning">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Pengumuman
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>