<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-5">
  <h2 class="mb-4 py-4">Daftar Hasil Ujian</h2>

  <?php if (empty($daftarUjian)): ?>
    <div class="alert alert-info">
      Belum ada ujian yang telah selesai.
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($daftarUjian as $ujian): ?>
        <div class="col-md-6 mb-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h5 class="card-title text-primary mb-1"><?= esc($ujian['nama_ujian']) ?></h5>
                  <small class="text-muted"><?= esc($ujian['nama_jenis']) ?></small>
                </div>
                <span class="badge bg-info"><?= esc($ujian['nama_kelas']) ?></span>
              </div>

              <p class="card-text"><?= esc($ujian['deskripsi']) ?></p>

              <div class="text-muted small mb-3">
                <div><i class="bi bi-people"></i> <?= $ujian['jumlah_peserta'] ?> siswa telah menyelesaikan</div>
                <div><i class="bi bi-calendar3"></i> <?= date('d M Y H:i', strtotime($ujian['tanggal_mulai'])) ?></div>
              </div>

              <a href="<?= base_url('guru/hasil-ujian/siswa/' . $ujian['jadwal_id']) ?>"
                class="btn btn-outline-primary">
                Lihat Hasil
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>