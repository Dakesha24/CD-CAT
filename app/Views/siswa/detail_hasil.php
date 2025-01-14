<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 py-5">
    <h2 class="mb-0">Detail Hasil Ujian</h2>
    <a href="<?= base_url('siswa/hasil') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Ringkasan Ujian -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <h4 class="text-primary mb-1"><?= esc($hasil['nama_ujian']) ?></h4>
          <p class="text-muted mb-3"><?= esc($hasil['nama_jenis']) ?></p>
          <p class="mb-0"><?= esc($hasil['deskripsi']) ?></p>
        </div>
        <div class="col-md-4">
          <div class="border-start ps-4">
            <div class="mb-3">
              <small class="text-muted d-block">Waktu Mulai</small>
              <strong><?= date('d M Y H:i', strtotime($hasil['waktu_mulai'])) ?></strong>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Waktu Selesai</small>
              <strong><?= date('d M Y H:i', strtotime($hasil['waktu_selesai'])) ?></strong>
            </div>
            <div>
              <small class="text-muted d-block">Total Waktu</small>
              <strong><?= date('H:i:s', strtotime($hasil['waktu_selesai']) - strtotime($hasil['waktu_mulai'])) ?></strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistik -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="mb-1"><?= $totalSoal ?></h3>
          <small class="text-muted">Total Soal</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-success mb-1"><?= $jawabanBenar ?></h3>
          <small class="text-muted">Jawaban Benar</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-danger mb-1"><?= $totalSoal - $jawabanBenar ?></h3>
          <small class="text-muted">Jawaban Salah</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-primary mb-1"><?= number_format($jawabanBenar / $totalSoal * 100, 1) ?>%</h3>
          <small class="text-muted">Persentase Benar</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Jawaban -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0">
      <h5 class="mb-0">Detail Jawaban</h5>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>No</th>
            <th>Pertanyaan</th>
            <th>Tingkat Kesulitan</th> <!-- Kolom baru -->
            <th>Jawaban Anda</th>
            <th>Jawaban Benar</th> <!-- Kolom baru -->
            <th>Status</th>
            <th>θ</th>
            <th>SE</th>
            <th>ΔSE</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detailJawaban as $i => $jawaban): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($jawaban['pertanyaan']) ?></td>
              <td>
                <?php
                $tingkatKesulitan = floatval($jawaban['tingkat_kesulitan']);
                if ($tingkatKesulitan <= -2) $badge = 'bg-success';
                elseif ($tingkatKesulitan <= 0) $badge = 'bg-info';
                elseif ($tingkatKesulitan <= 2) $badge = 'bg-warning';
                else $badge = 'bg-danger';
                ?>
                <span class="badge <?= $badge ?>">
                  <?= number_format($tingkatKesulitan, 3) ?>
                </span>
              </td>
              <td><?= $jawaban['jawaban_siswa'] ?></td>
              <td><?= $jawaban['jawaban_benar'] ?></td>
              <td>
                <?php if ($jawaban['is_correct']): ?>
                  <span class="badge bg-success">Benar</span>
                <?php else: ?>
                  <span class="badge bg-danger">Salah</span>
                <?php endif; ?>
              </td>
              <td><?= number_format($jawaban['theta_saat_ini'], 3) ?></td>
              <td><?= number_format($jawaban['se_saat_ini'], 3) ?></td>
              <td><?= number_format($jawaban['delta_se_saat_ini'], 3) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <td colspan="3"><strong>Keterangan Tingkat Kesulitan:</strong></td>
            <td colspan="6">
              <span class="badge bg-success me-2">Sangat Mudah (≤ -2)</span>
              <span class="badge bg-info me-2">Mudah (≤ 0)</span>
              <span class="badge bg-warning me-2">Sulit (≤ 2)</span>
              <span class="badge bg-danger">Sangat Sulit (> 2)</span>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>