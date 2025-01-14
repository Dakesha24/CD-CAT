<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
  <h1 class="mt-4">Manajemen Jenis Ujian</h1>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Tombol Tambah -->
  <div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
      <i class="fas fa-plus"></i> Tambah Jenis Ujian
    </button>
  </div>

  <!-- Tabel Jenis Ujian -->
  <div class="card mb-4">
    <div class="card-body">
      <table id="jenisUjianTable" class="table table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Jenis</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($jenis_ujian as $key => $jenis): ?>
            <tr>
              <td><?= $key + 1 ?></td>
              <td><?= esc($jenis['nama_jenis']) ?></td>
              <td><?= esc($jenis['deskripsi']) ?></td>
              <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                  data-bs-target="#editModal<?= $jenis['jenis_ujian_id'] ?>">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <a href="<?= base_url('guru/jenis-ujian/hapus/' . $jenis['jenis_ujian_id']) ?>"
                  class="btn btn-sm btn-danger"
                  onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                  <i class="fas fa-trash"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jenis Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/jenis-ujian/tambah') ?>" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Jenis</label>
            <input type="text" class="form-control" name="nama_jenis" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
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

<!-- Modal Edit -->
<?php foreach ($jenis_ujian as $jenis): ?>
  <div class="modal fade" id="editModal<?= $jenis['jenis_ujian_id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Jenis Ujian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('guru/jenis-ujian/edit/' . $jenis['jenis_ujian_id']) ?>" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Jenis</label>
              <input type="text" class="form-control" name="nama_jenis"
                value="<?= esc($jenis['nama_jenis']) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" rows="3" required><?= esc($jenis['deskripsi']) ?></textarea>
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

<!-- DataTables -->
<script>
  $(document).ready(function() {
    $('#jenisUjianTable').DataTable();
  });
</script>

<?= $this->endSection() ?>