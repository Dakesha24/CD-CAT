<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
  <div class="row mb-4 py-4">
    <div class="col">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah Siswa</h2>
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
          <form action="<?= base_url('admin/siswa/tambah') ?>" method="post">
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

              <!-- Data Siswa -->
              <div class="col-md-6">
                <h5 class="mb-3">Data Siswa</h5>

                <div class="mb-3">
                  <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                  <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                    value="<?= old('nama_lengkap') ?>" required>
                </div>

                <div class="mb-3">
                  <label for="nomor_peserta" class="form-label">Nomor Peserta *</label>
                  <input type="text" class="form-control" id="nomor_peserta" name="nomor_peserta"
                    value="<?= old('nomor_peserta') ?>" required>
                  <div class="form-text">Nomor unik untuk setiap siswa</div>
                </div>

                <div class="mb-3">
                  <label for="kelas_id" class="form-label">Kelas *</label>
                  <select class="form-select" id="kelas_id" name="kelas_id" required>
                    <option value="">Pilih Kelas</option>
                    <?php foreach ($kelas as $k): ?>
                      <option value="<?= $k['kelas_id'] ?>"
                        <?= (old('kelas_id') == $k['kelas_id']) ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas'] . ' - ' . $k['tahun_ajaran']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
              <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-save me-2"></i>Simpan
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Generate Batch Students -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>Generate Siswa Batch
          </h5>
        </div>
        <div class="card-body">
          <p class="text-muted">Buat beberapa siswa sekaligus dengan nomor peserta otomatis</p>

          <form id="batchForm">
            <div class="row">
              <div class="col-md-4">
                <label for="batch_kelas" class="form-label">Kelas</label>
                <select class="form-select" id="batch_kelas" required>
                  <option value="">Pilih Kelas</option>
                  <?php foreach ($kelas as $k): ?>
                    <option value="<?= $k['kelas_id'] ?>">
                      <?= esc($k['nama_kelas'] . ' - ' . $k['tahun_ajaran']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="batch_jumlah" class="form-label">Jumlah Siswa</label>
                <input type="number" class="form-control" id="batch_jumlah" min="1" max="50" value="10">
              </div>
              <div class="col-md-4">
                <label for="batch_prefix" class="form-label">Prefix No. Peserta</label>
                <input type="text" class="form-control" id="batch_prefix" value="SISWA" maxlength="10">
              </div>
            </div>

            <div class="mt-3">
              <button type="button" class="btn btn-info" onclick="generateBatch()">
                <i class="bi bi-magic me-2"></i>Generate Preview
              </button>
            </div>
          </form>

          <div id="batchPreview" class="mt-4" style="display: none;">
            <h6>Preview Data yang Akan Dibuat:</h6>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Nama Lengkap</th>
                    <th>No. Peserta</th>
                  </tr>
                </thead>
                <tbody id="batchTable">
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-success" onclick="createBatch()">
              <i class="bi bi-check2-all me-2"></i>Buat Semua Siswa
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function generateBatch() {
    const kelas = document.getElementById('batch_kelas').value;
    const jumlah = parseInt(document.getElementById('batch_jumlah').value);
    const prefix = document.getElementById('batch_prefix').value;

    if (!kelas || !jumlah || !prefix) {
      alert('Harap lengkapi semua field');
      return;
    }

    const tableBody = document.getElementById('batchTable');
    tableBody.innerHTML = '';

    for (let i = 1; i <= jumlah; i++) {
      const num = i.toString().padStart(3, '0');
      const username = `${prefix.toLowerCase()}${num}`;
      const email = `${username}@sekolah.com`;
      const nama = `${prefix} ${num}`;
      const noPeserta = `${prefix}${num}`;

      const row = `
            <tr>
                <td>${username}</td>
                <td>${email}</td>
                <td>${nama}</td>
                <td>${noPeserta}</td>
            </tr>
        `;
      tableBody.innerHTML += row;
    }

    document.getElementById('batchPreview').style.display = 'block';
  }

  function createBatch() {
    const kelas = document.getElementById('batch_kelas').value;
    const jumlah = parseInt(document.getElementById('batch_jumlah').value);
    const prefix = document.getElementById('batch_prefix').value;

    if (confirm(`Yakin ingin membuat ${jumlah} siswa sekaligus?`)) {
      // Implementasi AJAX untuk create batch
      // Untuk sementara, redirect ke halaman dengan parameter
      window.location.href = `<?= base_url('admin/siswa/batch') ?>?kelas=${kelas}&jumlah=${jumlah}&prefix=${prefix}`;
    }
  }
</script>

<?= $this->endSection() ?>