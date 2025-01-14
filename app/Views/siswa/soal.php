<?= $this->extend('templates/siswa/siswa_template') ?>

<meta name="robots" content="noindex,nofollow">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <!-- Info Ujian & Timer -->
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0"><?= esc($ujian['nama_ujian']) ?></h4>
              <small class="text-muted"><?= esc($ujian['nama_jenis']) ?></small>
            </div>
            <div class="text-center">
              <h5 class="mb-0">Sisa Waktu</h5>
              <div id="timer" class="h4 mb-0 text-danger">
                <?= floor($sisa_waktu / 3600) ?>:<?= floor(($sisa_waktu % 3600) / 60) ?>:<?= $sisa_waktu % 60 ?>
              </div>
            </div>
            <div class="text-end">
              <h5 class="mb-0">Soal</h5>
              <div class="h4 mb-0"><?= $soal_dijawab +1  ?>/<?= $total_soal ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Soal -->
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-4">Pertanyaan:</h5>
          <p class="lead mb-4"><?= esc($soal['pertanyaan']) ?></p>

          <form action="<?= base_url('siswa/ujian/simpan-jawaban') ?>" method="POST">
            <input type="hidden" name="soal_id" value="<?= $soal['soal_id'] ?>">

            <div class="list-group">
              <?php
              $pilihan = [
                'A' => $soal['pilihan_a'],
                'B' => $soal['pilihan_b'],
                'C' => $soal['pilihan_c'],
                'D' => $soal['pilihan_d']
              ];
              foreach ($pilihan as $key => $value): ?>
                <label class="list-group-item list-group-item-action">
                  <input class="form-check-input me-2"
                    type="radio"
                    name="jawaban"
                    value="<?= $key ?>"
                    required>
                  <?= $key ?>. <?= esc($value) ?>
                </label>
              <?php endforeach; ?>
            </div>

            <div class="text-end mt-4">
              <button type="submit" class="btn btn-primary btn-lg px-5">
                Jawab
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Timer countdown
  let timeLeft = <?= $sisa_waktu ?>;
  const timerElement = document.getElementById('timer');

  const countDown = setInterval(() => {
    timeLeft--;

    const hours = Math.floor(timeLeft / 3600);
    const minutes = Math.floor((timeLeft % 3600) / 60);
    const seconds = timeLeft % 60;

    timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

    if (timeLeft <= 0) {
      clearInterval(countDown);
      // Redirect ke halaman selesai
      window.location.href = '<?= base_url('siswa/ujian/selesai/' . $ujian['jadwal_id']) ?>';
    }
  }, 1000);

  window.onbeforeunload = function() {
    return "Apakah Anda yakin ingin meninggalkan halaman ini?";
  };

  // Nonaktifkan untuk form submit
  document.querySelector('form').onsubmit = function() {
    window.onbeforeunload = null;
  };
</script>
<?= $this->endSection() ?>