<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 py-5">
    <div>
      <h2 class="mb-1">Detail Hasil Ujian</h2>
      <p class="text-muted mb-0">
        <?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?>
      </p>
      <?php 
        // Generate kode soal dari tahun dan ID
        $tahunPembuatan = date('Y', strtotime($hasil['tanggal_mulai']));
        $kode_soal = $tahunPembuatan . str_pad($hasil['ujian_id'], 4, '0', STR_PAD_LEFT);
      ?>
      <p class="text-muted mb-0">
        Kode Soal: <strong><?= $kode_soal ?></strong>
      </p>
    </div>
    <div>
      <!-- Tombol Download Hasil -->
      <div class="btn-group me-2">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-download"></i> Download Hasil
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="<?= base_url('guru/hasil-ujian/download-excel-html/' . $hasil['peserta_ujian_id']) ?>">Excel</a></li>
          <li><a class="dropdown-item" href="<?= base_url('guru/hasil-ujian/download-pdf-html/' . $hasil['peserta_ujian_id']) ?>">PDF</a></li>
        </ul>
      </div>
      
      <a href="<?= base_url('guru/hasil-ujian/siswa/' . $hasil['jadwal_id']) ?>"
        class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Info Siswa -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless mb-0">
            <tr>
              <td style="width: 150px">Nama Siswa</td>
              <td>: <?= esc($hasil['nama_lengkap']) ?></td>
            </tr>
            <tr>
              <td>Nomor Peserta</td>
              <td>: <?= esc($hasil['nomor_peserta']) ?></td>
            </tr>
            <tr>
              <td>Kelas</td>
              <td>: <?= esc($hasil['nama_kelas']) ?></td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-borderless mb-0">
            <tr>
              <td style="width: 150px">Waktu Mulai</td>
              <td>: <?= date('d/m/Y H:i', strtotime($hasil['waktu_mulai'])) ?></td>
            </tr>
            <tr>
              <td>Waktu Selesai</td>
              <td>: <?= date('d/m/Y H:i', strtotime($hasil['waktu_selesai'])) ?></td>
            </tr>
            <tr>
              <td>Durasi</td>
              <td>: <?= date('H:i:s', strtotime($hasil['waktu_selesai']) - strtotime($hasil['waktu_mulai'])) ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Hasil Akhir -->
  <?php 
    // Ambil theta terakhir (dari jawaban terakhir)
    $lastTheta = end($detailJawaban)['theta_saat_ini'];
    // Hitung nilai akhir: 50 + 16.6 * theta
    $finalScore = 50 + (16.6 * $lastTheta);
    // Nilai dalam skala 0-100
    $finalGrade = min(100, max(0, round(($finalScore / 100) * 100)));
  ?>
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent">
      <h5 class="card-title mb-0">Hasil Akhir</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td width="200">Theta Akhir (θ)</td>
              <td width="20">:</td>
              <td><strong><?= number_format($lastTheta, 3) ?></strong></td>
            </tr>
            <tr>
              <td>Skor</td>
              <td>:</td>
              <td><strong class="fs-4 text-primary"><?= number_format($finalScore, 1) ?></strong></td>
            </tr>
            <tr>
              <td>Nilai (Skala 0-100)</td>
              <td>:</td>
              <td><strong class="fs-4 text-success"><?= $finalGrade ?></strong></td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td width="200">Total Soal</td>
              <td width="20">:</td>
              <td><strong><?= count($detailJawaban) ?></strong> soal</td>
            </tr>
            <tr>
              <td>Jawaban Benar</td>
              <td>:</td>
              <td>
                <strong><?= array_reduce($detailJawaban, function ($carry, $item) {
                  return $carry + ($item['is_correct'] ? 1 : 0);
                }, 0) ?></strong> soal
              </td>
            </tr>
            <tr>
              <td>Standard Error Akhir</td>
              <td>:</td>
              <td><strong><?= number_format(end($detailJawaban)['se_saat_ini'], 3) ?></strong></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Jawaban -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Detail Jawaban</h5>
      <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#additionalInfoHelp" aria-expanded="false">
        <i class="bi bi-info-circle"></i> Info Kolom
      </button>
    </div>
    
    <div class="collapse" id="additionalInfoHelp">
      <div class="card-body bg-light">
        <h6 class="fw-bold">Penjelasan Kolom:</h6>
        <ul class="small mb-0">
          <li><strong>Pi</strong>: Probabilitas menjawab benar</li>
          <li><strong>Qi</strong>: Probabilitas menjawab salah</li>
          <li><strong>Ii</strong>: Fungsi informasi</li>
          <li><strong>SE</strong>: Standard Error</li>
          <li><strong>ΔSE</strong>: Perubahan Standard Error</li>
          <li><strong>θ</strong>: Theta/Kemampuan setelah menjawab soal</li>
        </ul>
      </div>
    </div>
    
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>ID Soal</th>
            <th>Pertanyaan</th>
            <th>Tingkat Kesulitan</th>
            <th>Jawaban</th>
            <th>Status</th>
            <th>Pi</th>
            <th>Qi</th>
            <th>Ii</th>
            <th>SE</th>
            <th>ΔSE</th>
            <th>θ</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detailJawaban as $i => $jawaban): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= $jawaban['soal_id'] ?></td>
              <td><?= esc($jawaban['pertanyaan']) ?></td>
              <td><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
              <td><?= $jawaban['jawaban_siswa'] ?></td>
              <td>
                <?php if ($jawaban['is_correct']): ?>
                  <span class="badge bg-success">Benar</span>
                <?php else: ?>
                  <span class="badge bg-danger">Salah</span>
                <?php endif; ?>
              </td>
              <td><?= isset($jawaban['pi_saat_ini']) ? number_format($jawaban['pi_saat_ini'], 3) : '-' ?></td>
              <td><?= isset($jawaban['qi_saat_ini']) ? number_format($jawaban['qi_saat_ini'], 3) : '-' ?></td>
              <td><?= isset($jawaban['ii_saat_ini']) ? number_format($jawaban['ii_saat_ini'], 3) : '-' ?></td>
              <td><?= number_format($jawaban['se_saat_ini'], 3) ?></td>
              <td><?= number_format(abs($jawaban['delta_se_saat_ini']), 3) ?></td>
              <td><?= number_format($jawaban['theta_saat_ini'], 3) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Grafik Perkembangan -->
  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
          <h5 class="card-title mb-0">Grafik Theta (θ)</h5>
        </div>
        <div class="card-body">
          <canvas id="thetaChart" height="300"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
          <h5 class="card-title mb-0">Grafik Standard Error (SE)</h5>
        </div>
        <div class="card-body">
          <canvas id="seChart" height="300"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik Informasi -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
          <h5 class="card-title mb-0">Fungsi Informasi Soal</h5>
        </div>
        <div class="card-body">
          <canvas id="infoChart" height="300"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data untuk grafik theta dan SE
  const thetaData = <?= json_encode(array_map(function ($item) {
                      return $item['theta_saat_ini'];
                    }, $detailJawaban)) ?>;

  const seData = <?= json_encode(array_map(function ($item) {
                    return $item['se_saat_ini'];
                  }, $detailJawaban)) ?>;

  const labels = <?= json_encode(array_map(function ($i) {
                    return 'Soal ' . ($i + 1);
                  }, range(0, count($detailJawaban) - 1))) ?>;
  
  // Data untuk grafik Fungsi Informasi
  const infoData = <?= json_encode(array_map(function ($item) {
                      return isset($item['ii_saat_ini']) ? $item['ii_saat_ini'] : null;
                    }, $detailJawaban)) ?>;

  // Grafik Theta
  new Chart(document.getElementById('thetaChart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Theta (θ)',
        data: thetaData,
        borderColor: '#4e73df',
        tension: 0.1,
        fill: false
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Perkembangan Estimasi Kemampuan (θ)'
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          title: {
            display: true,
            text: 'Nilai Theta'
          }
        }
      }
    }
  });

  // Grafik SE
  new Chart(document.getElementById('seChart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Standard Error',
        data: seData,
        borderColor: '#1cc88a',
        tension: 0.1,
        fill: false
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Perkembangan Standard Error'
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          title: {
            display: true,
            text: 'Nilai SE'
          }
        }
      }
    }
  });
  
  // Grafik Fungsi Informasi
  new Chart(document.getElementById('infoChart'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Fungsi Informasi Soal',
        data: infoData,
        backgroundColor: '#36b9cc',
        borderColor: '#2c9faf',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Fungsi Informasi Tiap Soal'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Informasi'
          }
        }
      }
    }
  });
</script>
<?= $this->endSection() ?>