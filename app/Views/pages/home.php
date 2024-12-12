<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-section">
                <h1 class="display-4 mb-3 title-hero">Selamat Datang di PHY-DA-CAT</h1>
                <p class="lead mb-4">Platform ini menyediakan asesmen diagnosis untuk pembelajaran Fisika, di mana soal-soal akan menyesuaikan dengan kemampuan peserta tes secara otomatis. Asesmen ini dirancang untuk membantu peserta tes dan guru untuk dapat mengetahui tingkat pemahaman pada materi fisika.</p>
                <div class="hero-buttons">
                    <a href="<?= base_url('login') ?>" class="btn btn-primary btn-lg mb-2">Sign In</a>
                    <a href="<?= base_url('register') ?>" class="btn btn-outline-primary btn-lg mb-2">Sign Up</a>
                </div>
            </div>
            <div class="col-md-6 hero-image-section text-center">
                <img src="<?= base_url('assets/images/hero.webp') ?>" alt="PHY-DA-CAT Illustration" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

