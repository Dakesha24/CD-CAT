<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="hero-section">
    <div class="container">
        <h1 class="display-4">Selamat Datang di PHY-DA-CAT</h1>
        <p class="lead">Asesmen diagnosis tingkat kognitif di materi Fisika yang menyesuaikan kemampuan peserta tes.</p>
        <?php if(!session()->get('logged_in')): ?>
            <div class="mt-4">
                <a href="<?= base_url('login') ?>" class="btn btn-primary btn-lg me-3">Sign In</a>
                <a href="<?= base_url('register') ?>" class="btn btn-outline-primary btn-lg">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pembelajaran Interaktif</h5>
                    <p class="card-text">Pelajari fisika dengan cara yang menyenangkan melalui simulasi interaktif.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Latihan Soal</h5>
                    <p class="card-text">Tingkatkan pemahaman Anda dengan berbagai latihan soal yang tersedia.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Forum Diskusi</h5>
                    <p class="card-text">Diskusikan materi dengan sesama pengguna dan dapatkan bantuan dari ahli.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>