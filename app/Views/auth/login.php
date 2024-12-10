<!-- auth/login.php -->
<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Sign In</h3>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('login') ?>" method="post">
                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Don't have an account? <a href="<?= base_url('register') ?>">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
