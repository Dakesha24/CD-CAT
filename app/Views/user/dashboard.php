<?= $this->extend('templates/user_header') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2>Selamat Datang, <?= session()->get('username') ?>!</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <!-- User Sidebar -->
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action active">
                    Dashboard
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    Latihan Soal
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    Forum Diskusi
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    Profil Saya
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Progress Section -->
            <div class="card">
                <div class="card-header">
                    Progress Pembelajaran
                </div>
                <div class="card-body">
                    <h5>Kinematika</h5>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                    </div>

                    <h5>Dinamika</h5>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">45%</div>
                    </div>

                    <h5>Usaha dan Energi</h5>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30%</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Aktivitas Terbaru
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">Menyelesaikan Latihan Kinematika</li>
                                <li class="list-group-item">Mengikuti Forum Diskusi</li>
                                <li class="list-group-item">Membaca Materi Dinamika</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Pencapaian
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-success me-2">
                                    <i class="bi bi-trophy"></i>
                                </div>
                                <div>Menyelesaikan Bab Kinematika</div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-primary me-2">
                                    <i class="bi bi-star"></i>
                                </div>
                                <div>Nilai Sempurna di Quiz #1</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>