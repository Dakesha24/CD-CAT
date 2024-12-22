<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Profil Siswa</h2>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Siswa</h5>
                </div>
                <div class="card-body">
                    <?php 
                    // Debug untuk melihat data yang dikirim
                    // echo '<pre>'; print_r($siswa); echo '</pre>';
                    // echo '<pre>'; print_r($kelas); echo '</pre>';
                    ?>

                    <form action="<?= base_url('siswa/profil/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="nomor_peserta" class="form-label">Nomor Peserta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomor_peserta" name="nomor_peserta" 
                                   value="<?= old('nomor_peserta', isset($siswa['nomor_peserta']) ? $siswa['nomor_peserta'] : '') ?>" required>
                            <?php if(session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['nomor_peserta'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   value="<?= old('nama_lengkap', isset($siswa['nama_lengkap']) ? $siswa['nama_lengkap'] : '') ?>" required>
                            <?php if(session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['nama_lengkap'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['kelas_id'] ?>" 
                                            <?= old('kelas_id', isset($siswa['kelas_id']) ? $siswa['kelas_id'] : '') == $k['kelas_id'] ? 'selected' : '' ?>>
                                        <?= $k['nama_kelas'] ?> (<?= $k['tahun_ajaran'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['kelas_id'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>