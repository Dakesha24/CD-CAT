<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Kelola Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>

<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Ujian</h4>
                    <div>
                        <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-info me-2">
                            <i class="fas fa-calendar"></i> Jadwal Ujian
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <!-- Filter dan Search -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchUjian" placeholder="Cari nama ujian...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterJenis">
                                <option value="">Semua Jenis Ujian</option>
                                <?php 
                                $jenisUnique = array_unique(array_column($ujian, 'nama_jenis'));
                                foreach ($jenisUnique as $jenis): 
                                    if ($jenis): ?>
                                        <option value="<?= esc($jenis) ?>"><?= esc($jenis) ?></option>
                                    <?php endif;
                                endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="resetFilter()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableUjian">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Ujian</th>
                                    <th>Jenis</th>
                                    <th>Durasi</th>
                                    <th>Total Soal</th>
                                    <th>Total Jadwal</th>
                                    <th>Guru Pembuat</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ujian)): ?>
                                    <?php foreach ($ujian as $index => $u): ?>
                                        <tr data-jenis="<?= esc($u['nama_jenis']) ?>">
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= esc($u['nama_ujian']) ?></strong>
                                                <?php if (!empty($u['deskripsi'])): ?>
                                                    <br><small class="text-muted"><?= strlen($u['deskripsi']) > 60 ? substr(esc($u['deskripsi']), 0, 60) . '...' : esc($u['deskripsi']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['nama_jenis']): ?>
                                                    <span class="badge bg-primary"><?= esc($u['nama_jenis']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock text-info"></i>
                                                <?= $u['durasi'] ? date('H:i', strtotime($u['durasi'])) . ' jam' : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?= $u['total_soal'] ?> Soal</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $u['total_jadwal'] ?> Jadwal</span>
                                            </td>
                                            <td>
                                                <?= esc($u['guru_pembuat']) ?: '<em class="text-muted">Tidak diketahui</em>' ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-grid gap-1">
                                                    <a href="<?= base_url('admin/ujian/detail/' . $u['id_ujian']) ?>" 
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Detail & Soal
                                                    </a>
                                                    <a href="<?= base_url('admin/ujian/hapus/' . $u['id_ujian']) ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('PERINGATAN!\n\nMenghapus ujian akan menghapus:\n- Semua soal ujian\n- Semua jadwal ujian\n- Semua hasil siswa\n\nApakah Anda yakin?')">
                                                        <i class="fas fa-trash me-1"></i>Hapus Ujian
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Belum ada ujian yang dibuat.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filter dan Search functionality
document.getElementById('searchUjian').addEventListener('keyup', filterTable);
document.getElementById('filterJenis').addEventListener('change', filterTable);

function filterTable() {
    const searchText = document.getElementById('searchUjian').value.toLowerCase();
    const jenisFilter = document.getElementById('filterJenis').value;
    const rows = document.querySelectorAll('#tableUjian tbody tr');

    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip "no data" row
        
        const namaUjian = row.cells[1].textContent.toLowerCase();
        const jenis = row.getAttribute('data-jenis');
        
        const textMatch = !searchText || namaUjian.includes(searchText);
        const jenisMatch = !jenisFilter || jenis === jenisFilter;
        
        row.style.display = (textMatch && jenisMatch) ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('searchUjian').value = '';
    document.getElementById('filterJenis').value = '';
    filterTable();
}
</script>

<?= $this->endSection() ?>