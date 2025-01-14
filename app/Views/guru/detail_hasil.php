<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 py-5">
    <div>
      <h2 class="mb-1">Detail Hasil Ujian</h2>
      <p class="text-muted mb-0">
        <?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?>
      </p>
    </div>
    <a href="<?= base_url('guru/hasil-ujian/siswa/' . $hasil['jadwal_id']) ?>"
      class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
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

  <!-- Detail Jawaban -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent">
      <h5 class="card-title mb-0">Detail Jawaban</h5>
    </div>
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>Pertanyaan</th>
            <th>Tingkat Kesulitan</th>
            <th>Jawaban</th>
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
              <td><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
              <td><?= $jawaban['jawaban_siswa'] ?></td>
              <td>
                <?php if ($jawaban['is_correct']): ?>
                  <span class="badge bg-success">Benar</span>
                <?php else: ?>
                  <span class="badge bg-danger">Salah</span>
                <?php endif; ?>
              </td>
              <td><?= number_format($jawaban['theta_saat_ini'], 3) ?></td>
              <td><?= number_format($jawaban['se_saat_ini'], 3) ?></td>
              <td><?= number_format(abs($jawaban['delta_se_saat_ini']), 3) ?></td>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data untuk grafik
  const thetaData = <?= json_encode(array_map(function ($item) {
                      return $item['theta_saat_ini'];
                    }, $detailJawaban)) ?>;

  const seData = <?= json_encode(array_map(function ($item) {
                    return $item['se_saat_ini'];
                  }, $detailJawaban)) ?>;

  const labels = <?= json_encode(array_map(function ($i) {
                    return 'Soal ' . ($i + 1);
                  }, range(0, count($detailJawaban) - 1))) ?>;

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
</script>
<?= $this->endSection() ?>