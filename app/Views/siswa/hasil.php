  <?= $this->extend('templates/siswa/siswa_template') ?>
  <?= $this->section('content') ?>

  <div class="container py-5">
    <h2 class="mb-4 py-2">Riwayat Ujian</h2>

    <?php if (empty($riwayatUjian)): ?>
      <div class="alert alert-info">
        Anda belum mengikuti ujian apapun.
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($riwayatUjian as $ujian): ?>
          <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div>
                    <h5 class="card-title text-primary mb-1"><?= esc($ujian['nama_ujian']) ?></h5>
                    <small class="text-muted"><?= esc($ujian['nama_jenis']) ?></small>
                  </div>
                  <span class="badge bg-success">Selesai</span>
                </div>

                <div class="mb-3">
                  <small class="text-muted d-block">
                    <i class="bi bi-calendar-check"></i>
                    Selesai: <?= date('d M Y H:i', strtotime($ujian['waktu_selesai'])) ?>
                  </small>
                  <small class="text-muted d-block">
                    <i class="bi bi-clock-history"></i>
                    Durasi Pengerjaan: <?= $ujian['durasi_pengerjaan'] ?>
                  </small>
                  <small class="text-muted d-block">
                    <i class="bi bi-alarm"></i>
                    Durasi Maksimal: <?= $ujian['durasi'] ?>
                  </small>
                </div>

                <p class="card-text small text-muted mb-4"><?= esc($ujian['deskripsi']) ?></p>

                <a href="<?= base_url('siswa/hasil/detail/' . $ujian['peserta_ujian_id']) ?>"
                  class="btn btn-outline-primary">
                  Lihat Detail
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <style>
    .hover-shadow {
      transition: all 0.3s ease;
    }

    .hover-shadow:hover {
      transform: translateY(-5px);
      box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
  </style>

  <?= $this->endSection() ?>