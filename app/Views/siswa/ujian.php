<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2>Daftar Ujian</h2>
    </div>
  </div>

  <!-- Ujian yang Tersedia -->
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0">Ujian yang Tersedia</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($jadwal_ujian)): ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Nama Ujian</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($jadwal_ujian as $ujian): ?>
                    <tr>
                      <td><?= $ujian['nama_ujian'] ?></td>
                      <td><?= date('d-m-Y', strtotime($ujian['tanggal_mulai'])) ?></td>
                      <td><?= date('H:i', strtotime($ujian['tanggal_mulai'])) ?> - <?= date('H:i', strtotime($ujian['tanggal_selesai'])) ?></td>
                      <td><?= $ujian['durasi_menit'] ?> menit</td>
                      <td>
                        <?php if (isset($ujian['peserta_status'])): ?>
                          <?php if ($ujian['peserta_status'] == 'sedang_mengerjakan'): ?>
                            <span class="badge bg-warning">Sedang Dikerjakan</span>
                          <?php elseif ($ujian['peserta_status'] == 'selesai'): ?>
                            <span class="badge bg-success">Selesai</span>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="badge bg-info">Belum Dimulai</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (isset($ujian['peserta_ujian_id']) && $ujian['peserta_status'] == 'sedang_mengerjakan'): ?>
                          <!-- Jika sudah ada sesi yang berjalan -->
                          <a href="<?= base_url("siswa/ujian/soal/{$ujian['peserta_ujian_id']}") ?>" class="btn btn-warning btn-sm">
                            Lanjutkan Ujian
                          </a>
                        <?php elseif (!isset($ujian['peserta_status']) && $ujian['status'] == 'sedang_berlangsung'): ?>
                          <!-- Jika belum mulai dan jadwal masih berlangsung -->
                          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKodeUjian" data-ujian-id="<?= $ujian['jadwal_id'] ?>">
                            Mulai Ujian
                          </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info mb-0">
              Belum ada ujian yang tersedia saat ini.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Kode Ujian -->
<div class="modal fade" id="modalKodeUjian" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Masukkan Kode Ujian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('siswa/ujian/mulai') ?>" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="kodeUjian" class="form-label">Kode Ujian</label>
            <input type="text" class="form-control" id="kodeUjian" name="kode_ujian" required>
            <input type="hidden" name="jadwal_id" id="jadwalId">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Mulai Ujian</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Script untuk mengisi jadwal_id ke modal
  document.addEventListener('DOMContentLoaded', function() {
    var modalKodeUjian = document.getElementById('modalKodeUjian');
    modalKodeUjian.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget;
      var ujianId = button.getAttribute('data-ujian-id');
      var jadwalIdInput = modalKodeUjian.querySelector('#jadwalId');
      jadwalIdInput.value = ujianId;
    });
  });
</script>
<?= $this->endSection() ?>