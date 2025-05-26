<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
  <h1 class="mt-4">Kelola Ujian</h1>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUjianModal">
        Tambah Ujian
      </button>
    </div>
    <div class="card-body">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Ujian</th>
            <th>Jenis Ujian</th>
            <th>Deskripsi</th>
            <th>Durasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1;
          foreach ($ujian as $u): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= $u['nama_ujian'] ?></td>
              <td><?php
                  foreach ($jenis_ujian as $ju) {
                    if ($ju['jenis_ujian_id'] == $u['jenis_ujian_id']) {
                      echo $ju['nama_jenis'];
                      break;
                    }
                  }
                  ?></td>
              <td><?= $u['deskripsi'] ?></td>
              <td><?= $u['durasi'] ?></td>
              <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUjianModal<?= $u['id_ujian'] ?>">Edit</button>
                <a href="<?= base_url('guru/ujian/hapus/' . $u['id_ujian']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin?')">Hapus</a>
                <a href="<?= base_url('guru/soal/' . $u['id_ujian']) ?>" class="btn btn-info btn-sm">Kelola Soal</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Ujian -->
<div class="modal fade" id="tambahUjianModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/ujian/tambah') ?>" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Jenis Ujian</label>
            <select name="jenis_ujian_id" class="form-control" required>
              <?php foreach ($jenis_ujian as $ju): ?>
                <option value="<?= $ju['jenis_ujian_id'] ?>"><?= $ju['nama_jenis'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Ujian</label>
            <input type="text" name="nama_ujian" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">SE Awal</label>
            <input type="number" name="se_awal" class="form-control" step="0.0001" value="1.0000" required>
          </div>
          <div class="mb-3">
            <label class="form-label">SE Minimum</label>
            <input type="number" name="se_minimum" class="form-control" step="0.0001" value="0.2500" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Delta SE Minimum</label>
            <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="0.0100" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Durasi (HH:MM:SS)</label>
            <input type="time" name="durasi" class="form-control" step="1" required>
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

<!-- Modal Edit Ujian -->
<?php foreach ($ujian as $u): ?>
  <div class="modal fade" id="editUjianModal<?= $u['id_ujian'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Ujian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('guru/ujian/edit/' . $u['id_ujian']) ?>" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Jenis Ujian</label>
              <select name="jenis_ujian_id" class="form-control" required>
                <?php foreach ($jenis_ujian as $ju): ?>
                  <option value="<?= $ju['jenis_ujian_id'] ?>" <?= $ju['jenis_ujian_id'] == $u['jenis_ujian_id'] ? 'selected' : '' ?>>
                    <?= $ju['nama_jenis'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Ujian</label>
              <input type="text" name="nama_ujian" class="form-control" value="<?= $u['nama_ujian'] ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" required><?= $u['deskripsi'] ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">SE Awal</label>
              <input type="number" name="se_awal" class="form-control" step="0.0001" value="<?= $u['se_awal'] ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">SE Minimum</label>
              <input type="number" name="se_minimum" class="form-control" step="0.0001" value="<?= $u['se_minimum'] ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Delta SE Minimum</label>
              <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="<?= $u['delta_se_minimum'] ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Durasi (HH:MM:SS)</label>
              <input type="time" name="durasi" class="form-control" step="1" value="<?= $u['durasi'] ?>" required>
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
<?php endforeach; ?>

<?= $this->endSection() ?>