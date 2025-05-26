<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4  py-5">
        <div class="col">
            <h2 class="mb-4">Dashboard Admin</h2>
            <div class="row g-4">
                <!-- Jenis Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-primary-subtle mx-auto">
                                <i class="bi bi-journal-text text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title">#</h5>
                            <p class="card-text">Text</p>
                            <a href="<?= base_url('guru/jenis-ujian') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Jenis Ujian
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>