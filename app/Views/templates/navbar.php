<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">PHY-DA-CAT</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('guide') ?>">Petunjuk Penggunaan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('profile') ?>">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('contact') ?>">Kontak</a>
                </li>
            </ul>
            
            <?php if(session()->get('logged_in')): ?>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        Welcome, <?= session()->get('username') ?>
                    </span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
                </div>
            <?php else: ?>
                <div class="d-flex">
                    <a href="<?= base_url('login') ?>" class="btn btn-outline-primary me-2">Sign In</a>
                    <a href="<?= base_url('register') ?>" class="btn btn-primary">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>