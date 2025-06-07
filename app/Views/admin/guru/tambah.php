<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
    <div class="row mb-4 py-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Tambah Guru</h2>
                <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->get('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->get('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('admin/guru/tambah') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Data Login -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Login</h5>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= old('username') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Minimal 6 karakter</div>
                                </div>
                            </div>

                            <!-- Data Guru -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Guru</h5>
                                
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                           value="<?= old('nama_lengkap') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control" id="nip" name="nip" 
                                           value="<?= old('nip') ?>">
                                    <div class="form-text">Opsional</div>
                                </div>

                                <div class="mb-3">
                                    <label for="mata_pelajaran" class="form-label">Mata Pelajaran *</label>
                                    <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" 
                                           value="<?= old('mata_pelajaran') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sekolah_id" class="form-label">Sekolah *</label>
                                    <select class="form-select" id="sekolah_id" name="sekolah_id" required>
                                        <option value="">Pilih Sekolah</option>
                                        <?php foreach ($sekolah as $s): ?>
                                            <option value="<?= $s['sekolah_id'] ?>" 
                                                    <?= (old('sekolah_id') == $s['sekolah_id']) ? 'selected' : '' ?>>
                                                <?= esc($s['nama_sekolah']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>