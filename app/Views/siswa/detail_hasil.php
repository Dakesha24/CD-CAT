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
          <p class="text-muted mb-1"><?= esc($hasil['nama_jenis']) ?></p>
          <!-- TAMBAHAN: Tampilkan kode ujian -->
          <p class="text-muted mb-3"><small><i class="bi bi-hash"></i> <?= esc($hasil['kode_ujian']) ?></small></p>
          <p class="mb-0"><?= esc($hasil['deskripsi']) ?></p>
        </div>
        <div class="col-md-4">
          <div class="border-start ps-4">
            <div class="mb-3">
              <small class="text-muted d-block">Waktu Mulai</small>
              <strong><?= $hasil['waktu_mulai_format'] ?></strong>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Waktu Selesai</small>
              <strong><?= $hasil['waktu_selesai_format'] ?></strong>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Total Waktu Pengerjaan</small>
              <strong><?= $hasil['durasi_total_format'] ?></strong>
            </div>
            <div>
              <small class="text-muted d-block">Rata-rata per Soal</small>
              <strong><?= $rataRataWaktuFormat ?></strong>
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
          <h3 class="text-primary mb-1"><?= $skor ?></h3>
          <small class="text-muted">Skor Ujian</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Tombol Unduh Laporan -->
  <div class="mb-4">
    <a href="<?= base_url('siswa/hasil/unduh/' . $hasil['peserta_ujian_id']) ?>" class="btn btn-primary" target="_blank">
      <i class="bi bi-download"></i> Unduh Laporan Hasil Ujian
    </a>
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
            <th>Kode Soal</th>
            <th>Pertanyaan</th>
            <th>Jawaban Anda</th>
            <th>Jawaban Benar</th>
            <th>Status</th>
            <th>Waktu Jawab</th>
            <th>Durasi</th>
            <th>Pembahasan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detailJawaban as $i => $jawaban): ?>
            <tr>
              <td><?= $jawaban['nomor_soal'] ?></td>
              <!-- TAMBAHAN: Tampilkan kode soal -->
              <td><small class="text-muted"><?= esc($jawaban['kode_soal']) ?></small></td>
              <td><?= esc($jawaban['pertanyaan']) ?></td>
              <td><?= $jawaban['jawaban_siswa'] ?></td>
              <td><?= $jawaban['jawaban_benar'] ?></td>
              <td>
                <?php if ($jawaban['is_correct']): ?>
                  <span class="badge bg-success">Benar</span>
                <?php else: ?>
                  <span class="badge bg-danger">Salah</span>
                <?php endif; ?>
              </td>
              <td>
                <small class="text-muted"><?= $jawaban['waktu_menjawab_format'] ?></small>
              </td>
              <td>
                <small class="fw-bold"><?= $jawaban['durasi_pengerjaan_format'] ?></small>
              </td>
              <td>
                <?php if (isset($jawaban['pembahasan']) && !empty($jawaban['pembahasan'])): ?>
                  <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pembahasanModal<?= $i ?>">
                    Lihat Pembahasan
                  </button>

                  <!-- Modal Pembahasan -->
                  <div class="modal fade" id="pembahasanModal<?= $i ?>" tabindex="-1" aria-labelledby="pembahasanModalLabel<?= $i ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="pembahasanModalLabel<?= $i ?>">Pembahasan Soal #<?= $jawaban['nomor_soal'] ?> (<?= esc($jawaban['kode_soal']) ?>)</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <?= $jawaban['pembahasan'] ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <span class="text-muted">Tidak tersedia</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>