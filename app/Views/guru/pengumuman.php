<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
    <h1 class="mt-4">Pengumuman</h1>
    
    <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahPengumumanModal">
        Tambah Pengumuman
    </button>

    <div class="card mb-4">
        <div class="card-body">
            <table id="pengumumanTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Tanggal Publish</th>
                        <th>Tanggal Berakhir</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($pengumuman as $p): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $p['judul'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['tanggal_publish'])) ?></td>
                        <td><?= $p['tanggal_berakhir'] ? date('d/m/Y H:i', strtotime($p['tanggal_berakhir'])) : '-' ?></td>
                        <td><?= $p['username'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#lihatPengumumanModal<?= $p['pengumuman_id'] ?>">Lihat</button>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPengumumanModal<?= $p['pengumuman_id'] ?>">Edit</button>
                            <a href="<?= base_url('guru/pengumuman/hapus/'.$p['pengumuman_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahPengumumanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/pengumuman/tambah') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="isi_pengumuman" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Publish</label>
                        <input type="datetime-local" name="tanggal_publish" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir</label>
                        <input type="datetime-local" name="tanggal_berakhir" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ada batas waktu</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lihat -->
<?php foreach($pengumuman as $p): ?>
<div class="modal fade" id="lihatPengumumanModal<?= $p['pengumuman_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $p['judul'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    Dipublikasikan: <?= date('d/m/Y H:i', strtotime($p['tanggal_publish'])) ?><br>
                    <?php if($p['tanggal_berakhir']): ?>
                    Berakhir: <?= date('d/m/Y H:i', strtotime($p['tanggal_berakhir'])) ?><br>
                    <?php endif; ?>
                    Oleh: <?= $p['username'] ?>
                </p>
                <div class="mt-3">
                    <?= nl2br($p['isi_pengumuman']) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Edit -->
<?php foreach($pengumuman as $p): ?>
<div class="modal fade" id="editPengumumanModal<?= $p['pengumuman_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/pengumuman/edit/'.$p['pengumuman_id']) ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" value="<?= $p['judul'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="isi_pengumuman" class="form-control" rows="5" required><?= $p['isi_pengumuman'] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Publish</label>
                        <input type="datetime-local" name="tanggal_publish" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($p['tanggal_publish'])) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir</label>
                        <input type="datetime-local" name="tanggal_berakhir" class="form-control" value="<?= $p['tanggal_berakhir'] ? date('Y-m-d\TH:i', strtotime($p['tanggal_berakhir'])) : '' ?>">
                        <small class="text-muted">Kosongkan jika tidak ada batas waktu</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
$(document).ready(function() {
    $('#pengumumanTable').DataTable({
        order: [[2, 'desc']] // Urutkan berdasarkan tanggal publish (kolom ke-3) secara descending
    });
});
</script>

<?= $this->endSection() ?>