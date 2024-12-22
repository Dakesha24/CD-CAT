<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2>Hasil Ujian</h2>
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Daftar Hasil Ujian -->
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0">Riwayat Ujian</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($hasil_ujian)): ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Ujian</th>
                    <th>Tanggal</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Nilai</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 1;
                  foreach ($hasil_ujian as $hasil): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= $hasil['nama_ujian'] ?></td>
                      <td><?= date('d-m-Y', strtotime($hasil['tanggal_mulai'])) ?></td>
                      <td><?= date('H:i', strtotime($hasil['waktu_mulai'])) ?></td>
                      <td><?= date('H:i', strtotime($hasil['waktu_selesai'])) ?></td>
                      <td>
                        <?php if ($hasil['nilai_akhir']): ?>
                          <span class="badge bg-success"><?= number_format($hasil['nilai_akhir'], 2) ?></span>
                        <?php else: ?>
                          <span class="badge bg-warning">Belum ada nilai</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($hasil['status'] == 'selesai'): ?>
                          <span class="badge bg-success">Selesai</span>
                        <?php elseif ($hasil['status'] == 'sedang_mengerjakan'): ?>
                          <span class="badge bg-warning">Sedang Mengerjakan</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Belum Mulai</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($hasil['status'] == 'selesai'): ?>
                          <a href="<?= base_url("siswa/hasil/review/{$hasil['peserta_ujian_id']}") ?>"
                            class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Lihat Pembahasan
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info mb-0">
              Belum ada riwayat ujian.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>