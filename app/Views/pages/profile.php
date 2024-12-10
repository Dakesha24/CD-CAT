<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <h1 class="text-center mb-5">Tim Pengembang PHY-DA-CAT</h1>

    <div class="row justify-content-center">
        <!-- Pengembang -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/api/placeholder/300/300" class="card-img-top" alt="Foto Pengembang">
                <div class="card-body text-center">
                    <h5 class="card-title">Nama Pengembang</h5>
                    <p class="card-text">Web Developer & Researcher</p>
                    <p class="card-text">Universitas Negeri Yogyakarta</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#developerModal">
                        Lihat Deskripsi
                    </button>
                </div>
            </div>
        </div>

        <!-- Pembimbing 1 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/api/placeholder/300/300" class="card-img-top" alt="Foto Pembimbing 1">
                <div class="card-body text-center">
                    <h5 class="card-title">Prof. Dr. Pembimbing Satu</h5>
                    <p class="card-text">Dosen Pembimbing 1</p>
                    <p class="card-text">Universitas Negeri Yogyakarta</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supervisor1Modal">
                        Lihat Deskripsi
                    </button>
                </div>
            </div>
        </div>

        <!-- Pembimbing 2 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/api/placeholder/300/300" class="card-img-top" alt="Foto Pembimbing 2">
                <div class="card-body text-center">
                    <h5 class="card-title">Dr. Pembimbing Dua</h5>
                    <p class="card-text">Dosen Pembimbing 2</p>
                    <p class="card-text">Universitas Negeri Yogyakarta</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supervisor2Modal">
                        Lihat Deskripsi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengembang -->
<div class="modal fade" id="developerModal" tabindex="-1" aria-labelledby="developerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="developerModalLabel">Profil Pengembang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="/api/placeholder/300/300" class="img-fluid rounded" alt="Foto Pengembang">
                    </div>
                    <div class="col-md-8">
                        <h4>Nama Pengembang</h4>
                        <p class="text-muted">Web Developer & Researcher</p>
                        
                        <h5 class="mt-3">Pendidikan</h5>
                        <ul>
                            <li>S2 Pendidikan Fisika - Universitas Negeri Yogyakarta (2022-sekarang)</li>
                            <li>S1 Pendidikan Fisika - Universitas X (2018-2022)</li>
                        </ul>

                        <h5 class="mt-3">Pengalaman</h5>
                        <ul>
                            <li>Web Developer di Company X (2020-2022)</li>
                            <li>Asisten Laboratorium Fisika (2019-2020)</li>
                        </ul>

                        <h5 class="mt-3">Kontak</h5>
                        <p>
                            Email: developer@phydacat.com<br>
                            LinkedIn: linkedin.com/in/developer
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembimbing 1 -->
<div class="modal fade" id="supervisor1Modal" tabindex="-1" aria-labelledby="supervisor1ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supervisor1ModalLabel">Profil Pembimbing 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="/api/placeholder/300/300" class="img-fluid rounded" alt="Foto Pembimbing 1">
                    </div>
                    <div class="col-md-8">
                        <h4>Prof. Dr. Pembimbing Satu</h4>
                        <p class="text-muted">Professor di Jurusan Pendidikan Fisika</p>
                        
                        <h5 class="mt-3">Bidang Keahlian</h5>
                        <ul>
                            <li>Pendidikan Fisika</li>
                            <li>Pengembangan Media Pembelajaran</li>
                            <li>Assessment & Evaluasi Pendidikan</li>
                        </ul>

                        <h5 class="mt-3">Publikasi Terpilih</h5>
                        <ul>
                            <li>Judul Publikasi 1 (2022)</li>
                            <li>Judul Publikasi 2 (2021)</li>
                            <li>Judul Publikasi 3 (2020)</li>
                        </ul>

                        <h5 class="mt-3">Kontak</h5>
                        <p>
                            Email: pembimbing1@uny.ac.id<br>
                            Google Scholar: scholar.google.com/pembimbing1
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembimbing 2 -->
<div class="modal fade" id="supervisor2Modal" tabindex="-1" aria-labelledby="supervisor2ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supervisor2ModalLabel">Profil Pembimbing 2</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="/api/placeholder/300/300" class="img-fluid rounded" alt="Foto Pembimbing 2">
                    </div>
                    <div class="col-md-8">
                        <h4>Dr. Pembimbing Dua</h4>
                        <p class="text-muted">Dosen Jurusan Pendidikan Fisika</p>
                        
                        <h5 class="mt-3">Bidang Keahlian</h5>
                        <ul>
                            <li>Computer Adaptive Testing</li>
                            <li>E-Learning & Educational Technology</li>
                            <li>Physics Education Research</li>
                        </ul>

                        <h5 class="mt-3">Publikasi Terpilih</h5>
                        <ul>
                            <li>Judul Publikasi 1 (2023)</li>
                            <li>Judul Publikasi 2 (2022)</li>
                            <li>Judul Publikasi 3 (2021)</li>
                        </ul>

                        <h5 class="mt-3">Kontak</h5>
                        <p>
                            Email: pembimbing2@uny.ac.id<br>
                            Research Gate: researchgate.net/pembimbing2
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 300px;
    object-fit: cover;
}

.modal-body img {
    max-width: 100%;
    height: auto;
}
</style>
<?= $this->endSection() ?>