<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-5 pt-4"> <!-- Tambahkan margin & padding top -->
  <div class="row">
    <!-- Card Timer -->
    <div class="col-12 mb-4">
      <div class="card bg-light border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <h4 class="mb-0 text-primary"><?= $peserta_ujian['nama_ujian'] ?></h4>
          <div class="d-flex align-items-center">
            <i class="fas fa-clock me-2 text-primary"></i>
            <div class="bg-white px-4 py-2 rounded shadow-sm">
              <span class="fw-bold text-primary" id="timer">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Soal -->
    <div class="col-12">
      <?php foreach ($soal as $index => $s): ?>
        <div class="card mb-4 shadow-sm soal-card" id="soal_<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
          <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Soal <?= $index + 1 ?> dari <?= count($soal) ?></h5>
              <span class="badge bg-primary px-3 py-2">
                Sisa: <?= count($soal) - ($index + 1) ?> soal
              </span>
            </div>
          </div>
          <div class="card-body p-4">
            <!-- Pertanyaan -->
            <div class="mb-4">
              <p class="lead mb-0"><?= $s['pertanyaan'] ?></p>
            </div>

            <!-- Pilihan Jawaban -->
            <div class="options">
              <?php
              $options = [
                'A' => $s['pilihan_a'],
                'B' => $s['pilihan_b'],
                'C' => $s['pilihan_c'],
                'D' => $s['pilihan_d']
              ];

              foreach ($options as $key => $value):
                $isChecked = isset($jawaban_siswa[$s['soal_id']]) && $jawaban_siswa[$s['soal_id']] === $key;
              ?>
                <div class="card mb-3 option-card">
                  <label class="card-body d-flex align-items-center p-3 cursor-pointer <?= $isChecked ? 'bg-light' : '' ?>">
                    <input
                      class="form-check-input me-3"
                      type="radio"
                      name="soal_<?= $s['soal_id'] ?>"
                      value="<?= $key ?>"
                      data-soal-id="<?= $s['soal_id'] ?>"
                      <?= $isChecked ? 'checked' : '' ?>>
                    <span class="option-text"><?= $key ?>. <?= $value ?></span>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted small">
                Pilih jawaban lalu klik 'Selanjutnya'
              </div>
              <div>
                <?php if ($index === count($soal) - 1): ?>
                  <button class="btn btn-danger px-4" onclick="konfirmasiSelesai()">
                    Selesai Ujian
                  </button>
                <?php else: ?>
                  <button class="btn btn-primary px-4" onclick="nextSoal()">
                    Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<style>
  .cursor-pointer {
    cursor: pointer;
  }

  .option-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
  }

  .option-card:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
  }

  .option-card input[type="radio"]:checked+.option-text {
    color: #0d6efd;
    font-weight: 500;
  }

  .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
  }
</style>

<script>
  let currentSoal = 0;
  const totalSoal = <?= count($soal) ?>;

  // Update fungsi showSoal
  function showSoal(index) {
    document.querySelectorAll('.soal-card').forEach(card => {
      card.style.display = 'none';
    });
    document.getElementById(`soal_${index}`).style.display = 'block';
    currentSoal = index;

    // Simpan state setiap kali soal berubah
    saveState();

    // Scroll ke atas halaman dengan animasi smooth
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  // Simpan posisi soal saat ini dan jawaban
  function saveState() {
    // Simpan nomor soal saat ini
    sessionStorage.setItem('currentSoal', currentSoal);

    // Simpan jawaban
    const answers = {};
    document.querySelectorAll('.form-check-input').forEach(input => {
      if (input.checked) {
        answers[input.dataset.soalId] = input.value;
      }
    });
    sessionStorage.setItem('examAnswers', JSON.stringify(answers));
  }

  // Update loadState function
  function loadState() {
    // Load current soal
    const savedSoal = sessionStorage.getItem('currentSoal');
    if (savedSoal !== null) {
      showSoal(parseInt(savedSoal));
    }

    // Load saved answers
    const savedAnswers = sessionStorage.getItem('examAnswers');
    if (savedAnswers) {
      const answers = JSON.parse(savedAnswers);
      Object.entries(answers).forEach(([soalId, jawaban]) => {
        const input = document.querySelector(`input[data-soal-id="${soalId}"][value="${jawaban}"]`);
        if (input) {
          input.checked = true;
        }
      });
    }
  }

  function nextSoal() {
    // Cek apakah sudah dijawab
    const currentSoalElement = document.getElementById(`soal_${currentSoal}`);
    const soalId = currentSoalElement.querySelector('.form-check-input').dataset.soalId;
    const answered = Array.from(currentSoalElement.querySelectorAll('.form-check-input')).some(input => input.checked);

    if (!answered) {
      alert('Silakan pilih jawaban terlebih dahulu!');
      return;
    }

    if (currentSoal < totalSoal - 1) {
      showSoal(currentSoal + 1);
    }
  }

  async function saveAnswer(soalId, jawaban) {
    try {
      console.log('Mencoba menyimpan jawaban:', {
        soalId,
        jawaban
      });
      const formData = new FormData();
      formData.append('peserta_ujian_id', '<?= $peserta_ujian['peserta_ujian_id'] ?>');
      formData.append('soal_id', soalId);
      formData.append('jawaban', jawaban);

      const response = await fetch('<?= base_url('siswa/ujian/simpan-jawaban') ?>', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const result = await response.json();
      console.log('Hasil simpan jawaban:', result);
      return result.success;
    } catch (error) {
      console.error('Error saat menyimpan jawaban:', error);
      return false;
    }
  }

  // Function untuk menyimpan semua jawaban sebelum selesai
  async function saveAllAnswers() {
    const answers = JSON.parse(sessionStorage.getItem('examAnswers') || '{}');
    console.log('Jawaban yang akan disimpan:', answers);

    const savePromises = Object.entries(answers).map(([soalId, jawaban]) =>
      saveAnswer(soalId, jawaban)
    );

    try {
      const results = await Promise.all(savePromises);
      console.log('Hasil penyimpanan semua jawaban:', results);
      return results.every(result => result === true);
    } catch (error) {
      console.error('Error saat menyimpan semua jawaban:', error);
      return false;
    }
  }

  async function konfirmasiSelesai() {
    // 1. Dapatkan jawaban terakhir yang dipilih
    const currentSoalElement = document.getElementById(`soal_${currentSoal}`);
    const checkedInput = currentSoalElement.querySelector('.form-check-input:checked');

    if (!checkedInput) {
      alert('Silakan pilih jawaban untuk soal terakhir terlebih dahulu!');
      return;
    }

    if (!confirm('Anda yakin ingin mengakhiri ujian?')) {
      return;
    }

    // Tampilkan loading
    const loadingEl = document.createElement('div');
    loadingEl.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75';
    loadingEl.innerHTML = `
        <div class="card shadow border-0 p-4">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <div>Menyimpan jawaban...</div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingEl);

    try {
      // 2. Simpan jawaban terakhir terlebih dahulu
      const lastSoalId = checkedInput.dataset.soalId;
      const lastJawaban = checkedInput.value;

      console.log('Menyimpan jawaban terakhir:', {
        lastSoalId,
        lastJawaban
      });

      const lastSaveResult = await saveAnswer(lastSoalId, lastJawaban);
      if (!lastSaveResult) {
        throw new Error('Gagal menyimpan jawaban terakhir');
      }

      // 3. Update sessionStorage dengan jawaban terakhir
      let answers = JSON.parse(sessionStorage.getItem('examAnswers') || '{}');
      answers[lastSoalId] = lastJawaban;
      sessionStorage.setItem('examAnswers', JSON.stringify(answers));

      // 4. Simpan semua jawaban sekali lagi untuk memastikan
      const allSaveResult = await saveAllAnswers();
      if (!allSaveResult) {
        throw new Error('Gagal menyimpan beberapa jawaban');
      }

      // 5. Hapus data session setelah berhasil
      sessionStorage.removeItem('examAnswers');
      sessionStorage.removeItem('currentSoal');
      sessionStorage.removeItem('examTimer');

      // 6. Redirect ke halaman selesai
      window.location.href = '<?= base_url("siswa/ujian/selesai/{$peserta_ujian['peserta_ujian_id']}") ?>';
    } catch (error) {
      console.error('Error saat menyelesaikan ujian:', error);
      alert('Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
      document.body.removeChild(loadingEl);
    }
  }

  // Fungsi untuk timer
  function startTimer(durasiMenit) {
    // Cek apakah ada waktu tersimpan di sessionStorage
    let sisaDetik;
    const savedTime = sessionStorage.getItem('examTimer');

    if (savedTime) {
      // Jika ada waktu tersimpan, gunakan itu
      sisaDetik = parseInt(savedTime);
    } else {
      // Jika tidak ada, gunakan durasi penuh
      sisaDetik = durasiMenit * 60;
      sessionStorage.setItem('examTimer', sisaDetik.toString());
    }

    const display = document.querySelector('#timer');
    const interval = setInterval(function() {
      const hours = Math.floor(sisaDetik / 3600);
      const minutes = Math.floor((sisaDetik % 3600) / 60);
      const seconds = sisaDetik % 60;

      display.textContent =
        (hours < 10 ? "0" + hours : hours) + ":" +
        (minutes < 10 ? "0" + minutes : minutes) + ":" +
        (seconds < 10 ? "0" + seconds : seconds);

      // Simpan waktu tersisa ke sessionStorage setiap detik
      sessionStorage.setItem('examTimer', sisaDetik.toString());

      if (--sisaDetik < 0) {
        clearInterval(interval);
        sessionStorage.removeItem('examTimer'); // Hapus timer dari sessionStorage
        alert('Waktu habis!');
        window.location.href = '<?= base_url("siswa/ujian/selesai/{$peserta_ujian['peserta_ujian_id']}") ?>';
      }
    }, 1000);
  }

  // Load saved state when page loads
  window.onload = function() {
    const durasiMenit = <?= $peserta_ujian['durasi_menit'] ?>;
    startTimer(durasiMenit);
    loadState();
  };

  // Tambahkan event listener untuk menghapus timer saat ujian selesai
  document.getElementById('btnSelesai').addEventListener('click', function() {
    if (confirm('Anda yakin ingin mengakhiri ujian?')) {
      sessionStorage.removeItem('examTimer'); // Hapus timer
      window.location.href = '<?= base_url("siswa/ujian/selesai/{$peserta_ujian['peserta_ujian_id']}") ?>';
    }
  });

  // Simpan jawaban dengan animasi
  // Event listener untuk radio buttons
  document.querySelectorAll('.form-check-input').forEach(radio => {
    radio.addEventListener('change', async function() {
      const soalId = this.dataset.soalId;
      const jawaban = this.value;
      const optionCard = this.closest('.option-card');

      // Visual feedback
      optionCard.style.transform = 'scale(0.98)';

      try {
        // Simpan ke session storage
        let answers = JSON.parse(sessionStorage.getItem('examAnswers') || '{}');
        answers[soalId] = jawaban;
        sessionStorage.setItem('examAnswers', JSON.stringify(answers));

        // Simpan ke server
        const saved = await saveAnswer(soalId, jawaban);

        if (saved) {
          optionCard.style.transform = 'scale(1)';
        } else {
          throw new Error('Gagal menyimpan jawaban');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Gagal menyimpan jawaban. Silakan coba lagi.');
        optionCard.style.transform = 'scale(1)';
      }
    });
  });
</script>
<?= $this->endSection() ?>