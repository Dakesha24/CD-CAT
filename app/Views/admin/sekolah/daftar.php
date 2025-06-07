<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
    <div class="row mb-4 py-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Kelola Sekolah</h2>
                <a href="<?= base_url('admin/sekolah/tambah') ?>" class="btn btn-info">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Sekolah
                </a>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabel Sekolah -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Sekolah</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th>Total Guru</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sekolah)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data sekolah</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sekolah as $index => $s): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><strong><?= esc($s['nama_sekolah']) ?></strong></td>
                                            <td><?= esc($s['alamat'] ?: '-') ?></td>
                                            <td><?= esc($s['telepon'] ?: '-') ?></td>
                                            <td><?= esc($s['email'] ?: '-') ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $s['total_guru'] ?> Guru</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/sekolah/edit/' . $s['sekolah_id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <?php if ($s['total_guru'] == 0): ?>
                                                        <a href="<?= base_url('admin/sekolah/hapus/' . $s['sekolah_id']) ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Hapus"
                                                           onclick="return confirm('Yakin ingin menghapus sekolah ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-outline-secondary" 
                                                                title="Tidak dapat dihapus karena masih memiliki guru" disabled>
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>