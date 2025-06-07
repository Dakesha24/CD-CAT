<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
    <div class="row mb-4 py-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Siswa</h2>
                <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary">
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
                    <form action="<?= base_url('admin/siswa/edit/' . $siswa['user_id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Data Login -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Login</h5>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= old('username', $siswa['username']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email', $siswa['email']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        <?php if ($siswa['status'] == 'active'): ?>
                                            <span class="badge bg-success fs-6">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger fs-6">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Siswa -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Siswa</h5>
                                
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                           value="<?= old('nama_lengkap', $siswa['nama_lengkap']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nomor_peserta" class="form-label">Nomor Peserta *</label>
                                    <input type="text" class="form-control" id="nomor_peserta" name="nomor_peserta" 
                                           value="<?= old('nomor_peserta', $siswa['nomor_peserta']) ?>" required>
                                    <div class="form-text">Nomor unik untuk setiap siswa</div>
                                </div>

                                <div class="mb-3">
                                    <label for="kelas_id" class="form-label">Kelas *</label>
                                    <select class="form-select" id="kelas_id" name="kelas_id" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($kelas as $k): ?>
                                            <option value="<?= $k['kelas_id'] ?>" 
                                                    <?= (old('kelas_id', $siswa['kelas_id']) == $k['kelas_id']) ? 'selected' : '' ?>>
                                                <?= esc($k['nama_kelas'] . ' - ' . $k['tahun_ajaran']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Informasi Tambahan</label>
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">
                                            <strong>Terdaftar:</strong> <?= date('d F Y H:i', strtotime($siswa['created_at'])) ?><br>
                                            <strong>Kelas Saat Ini:</strong> <?= esc($siswa['nama_kelas'] . ' - ' . $siswa['tahun_ajaran']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>