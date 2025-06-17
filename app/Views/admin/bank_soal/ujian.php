<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary"><?= esc($bankUjian['nama_ujian']) ?></h2>
      <p class="text-muted mb-2">
        <i class="fas fa-tag me-2"></i><?= esc($bankUjian['nama_jenis']) ?> -
        <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
      </p>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item">
            <a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none">
              <i class="fas fa-home me-1"></i>Dashboard
            </a>
          </li>
          <li class="breadcrumb-item">
            <a href="<?= base_url('admin/bank-soal') ?>" class="text-decoration-none">Bank Soal</a>
          </li>
          <li class="breadcrumb-item">
            <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>" class="text-decoration-none">
              <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
            </a>
          </li>
          <li class="breadcrumb-item">
            <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="text-decoration-none">
              <?= esc($bankUjian['nama_jenis']) ?>
            </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <?= esc($bankUjian['nama_ujian']) ?>
          </li>
        </ol>
      </nav>
    </div>
    <div class="col-auto">
      <div class="d-flex gap-2">
        <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
          <i class="fas fa-plus me-2"></i>Tambah Soal
        </button>
      </div>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Info Bank Ujian -->
  <?php if (!empty($bankUjian['deskripsi'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <i class="fas fa-info-circle me-2"></i><?= esc($bankUjian['deskripsi']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Info Box untuk Panduan CKEditor -->
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Panduan Menulis Soal</h6>
    <p class="mb-2">Gunakan editor yang tersedia untuk format teks yang lebih kaya:</p>
    <ul class="mb-2">
      <li><strong>Format teks:</strong> Bold, italic, underline, warna</li>
      <li><strong>Rumus matematika:</strong> Gunakan superscript (x²) dan subscript (H₂O), atau tombol <kbd>Special Characters</kbd></li>
      <li><strong>Simbol matematika/fisika:</strong> Klik <kbd>Special Characters</kbd> untuk simbol seperti ∫, ∑, π, α, β, γ, δ, ≤, ≥, ±, °, dll</li>
      <li><strong>List:</strong> Bullet points dan numbering</li>
      <li><strong>Tabel:</strong> Untuk data terstruktur</li>
      <li><strong>Upload gambar:</strong> Gunakan field "Foto Soal" di bawah editor</li>
    </ul>
    <div class="mt-2">
      <small class="text-muted"><strong>Tips:</strong> Untuk rumus kompleks, gunakan kombinasi superscript/subscript + special characters. Contoh: E=mc² bisa dibuat dengan mengetik "E=mc" lalu pilih superscript untuk "2"</small>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>

  <?php if (empty($soalList)): ?>
    <!-- Jika belum ada soal -->
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card shadow-sm text-center">
          <div class="card-body py-5">
            <div class="mb-4">
              <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                <i class="fas fa-question-circle fa-3x text-muted"></i>
              </div>
            </div>
            <h5 class="fw-bold text-muted mb-3">Belum Ada Soal</h5>
            <p class="text-muted mb-4">
              Belum ada soal yang dibuat untuk bank ujian
              <strong><?= esc($bankUjian['nama_ujian']) ?></strong>
            </p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
              <i class="fas fa-plus me-2"></i>Tambah Soal Pertama
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- Daftar Soal -->
    <div class="card shadow-sm">
      <div class="card-header bg-light border-0">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="card-title mb-0 fw-bold">
            <i class="fas fa-list me-2 text-primary"></i>Daftar Soal
          </h5>
          <span class="badge bg-primary"><?= count($soalList) ?> soal</span>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th width="5%" class="px-3">No</th>
                <th width="12%">Kode Soal</th>
                <th width="35%">Pertanyaan</th>
                <th width="8%" class="text-center">Jawaban</th>
                <th width="10%" class="text-center">Kesulitan</th>
                <th width="8%" class="text-center">Foto</th>
                <th width="12%" class="text-center">Dibuat</th>
                <th width="22%" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($soalList as $index => $soal): ?>
                <tr>
                  <td class="px-3 fw-semibold"><?= $index + 1 ?></td>
                  <td class="fw-bold text-primary"><?= esc($soal['kode_soal']) ?></td>
                  <td>
                    <div class="text-truncate" style="max-width: 300px;" title="<?= esc(strip_tags($soal['pertanyaan'])) ?>">
                      <?= strip_tags($soal['pertanyaan']) ?>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-primary fs-6"><?= esc($soal['jawaban_benar']) ?></span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-<?= $soal['tingkat_kesulitan'] <= -1 ? 'success' : ($soal['tingkat_kesulitan'] <= 1 ? 'warning' : 'danger') ?>">
                      <?= number_format($soal['tingkat_kesulitan'], 3) ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($soal['foto'])): ?>
                      <i class="fas fa-image text-success" title="Ada foto"></i>
                    <?php else: ?>
                      <i class="fas fa-minus text-muted" title="Tidak ada foto"></i>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <small class="text-muted">
                      <?= date('d/m/Y', strtotime($soal['created_at'])) ?>
                    </small>
                  </td>
                  <td class="text-center">
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="fw-bold btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $soal['soal_id'] ?>" title="Lihat Detail">
                        <i class="fas fa-eye me-1"></i>Detail
                      </button>
                      <button type="button" class="fw-bold btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $soal['soal_id'] ?>" title="Edit">
                        <i class="fas fa-edit me-1"></i>Edit
                      </button>
                      <button type="button" class="fw-bold btn btn-danger" onclick="hapusSoal(<?= $soal['soal_id'] ?>)" title="Hapus">
                        <i class="fas fa-trash me-1"></i>Hapus
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Tambah Soal -->
<div class="modal fade" id="modalTambahSoal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Soal Bank</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/bank-soal/tambah-soal') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="bank_ujian_id" value="<?= $bankUjian['bank_ujian_id'] ?>">
        <div class="modal-body">
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
              <h6 class="mb-0"><i class="fas fa-code text-warning me-2"></i>Kode Soal</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                  <input type="text" name="kode_soal" class="form-control form-control-lg"
                    placeholder="Contoh: MAT001, FIS002" required>
                  <small class="text-muted">Masukkan kode unik untuk soal ini</small>
                </div>
                <div class="col-md-6">
                  <div class="mt-4">
                    <div class="alert alert-warning py-2 mb-0">
                      <small>
                        <strong>Format Kode:</strong><br>
                        • 3-50 karakter<br>
                        • Boleh huruf, angka, dan simbol<br>
                        • Harus unik (tidak boleh sama)
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pertanyaan Section -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
              <h6 class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i>Pertanyaan Soal</h6>
            </div>
            <div class="card-body">
              <div class="mb-2">
                <small class="text-muted">
                  <i class="fas fa-lightbulb me-1"></i>
                  Gunakan toolbar simbol cepat di atas editor atau klik <strong>Special Characters</strong> untuk simbol matematika lengkap
                </small>
              </div>
              <textarea name="pertanyaan" id="pertanyaan_tambah" class="form-control" rows="4" required placeholder="Masukkan pertanyaan soal..."></textarea>

              <div class="mt-3">
                <label class="form-label"><i class="fas fa-image text-secondary me-1"></i>Foto Soal (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                <small class="text-muted">Upload gambar dengan format JPG, JPEG, atau PNG (maks. 2MB). Gunakan toolbar di atas untuk menambah simbol matematika dengan cepat.</small>
              </div>
            </div>
          </div>

          <!-- Pilihan Jawaban Section -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
              <h6 class="mb-0"><i class="fas fa-list text-success me-2"></i>Pilihan Jawaban</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-primary">A.</label>
                  <textarea name="pilihan_a" id="pilihan_a_tambah" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-primary">B.</label>
                  <textarea name="pilihan_b" id="pilihan_b_tambah" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-primary">C.</label>
                  <textarea name="pilihan_c" id="pilihan_c_tambah" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-primary">D.</label>
                  <textarea name="pilihan_d" id="pilihan_d_tambah" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-warning">E. (Opsional)</label>
                  <textarea name="pilihan_e" id="pilihan_e_tambah" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold"><i class="fas fa-check-circle text-success me-1"></i>Jawaban Benar</label>
                  <select name="jawaban_benar" class="form-select form-select-lg" required>
                    <option value="">Pilih Jawaban Benar</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Pengaturan Soal Section -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
              <h6 class="mb-0"><i class="fas fa-cogs text-warning me-2"></i>Pengaturan Soal</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold"><i class="fas fa-balance-scale text-info me-1"></i>Tingkat Kesulitan</label>
                  <div class="input-group">
                    <input type="number" name="tingkat_kesulitan" class="form-control form-control-lg" step="0.001" value="0.000" min="-3" max="3" required>
                    <span class="input-group-text">(-3 hingga +3)</span>
                  </div>
                  <small class="text-muted">Negatif = mudah, Positif = sulit, 0 = sedang</small>
                </div>
                <div class="col-md-6">
                  <div class="mt-4">
                    <div class="alert alert-info py-2 mb-0">
                      <small>
                        <strong>Panduan Tingkat Kesulitan:</strong><br>
                        -3.000 hingga -1.000 = Mudah<br>
                        -0.999 hingga +0.999 = Sedang<br>
                        +1.000 hingga +3.000 = Sulit
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pembahasan Section -->
          <div class="card shadow-sm">
            <div class="card-header bg-light">
              <h6 class="mb-0"><i class="fas fa-lightbulb text-info me-2"></i>Pembahasan (Opsional)</h6>
            </div>
            <div class="card-body">
              <div class="mb-2">
                <small class="text-muted">
                  <i class="fas fa-lightbulb me-1"></i>
                  Gunakan toolbar simbol cepat atau Special Characters untuk rumus matematika
                </small>
              </div>
              <textarea name="pembahasan" id="pembahasan_tambah" class="form-control" rows="4" placeholder="Masukkan pembahasan soal..."></textarea>
              <small class="text-muted mt-2 d-block">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-1"></i>Simpan Soal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Soal -->
<?php foreach ($soalList as $soal): ?>
  <div class="modal fade" id="detailModal<?= $soal['soal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Detail Soal: <?= esc($soal['kode_soal']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Kode Soal:</strong> <span class="badge bg-primary ms-2"><?= esc($soal['kode_soal']) ?></span>
            </div>
            <div class="col-md-4">
              <strong>Jawaban Benar:</strong> <span class="badge bg-success ms-2"><?= $soal['jawaban_benar'] ?></span>
            </div>
            <div class="col-md-4">
              <strong>Tingkat Kesulitan:</strong> <span class="badge bg-info ms-2"><?= number_format($soal['tingkat_kesulitan'], 3) ?></span>
            </div>
          </div>

          <div class="mb-3">
            <strong>Pertanyaan:</strong>
            <div class="p-3 bg-light rounded"><?= $soal['pertanyaan'] ?></div>
          </div>

          <?php if (!empty($soal['foto'])): ?>
            <div class="text-center mb-3">
              <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <strong>Pilihan Jawaban:</strong>
            <div class="mt-2">
              <div class="d-flex mb-2"><span class="badge bg-primary me-2">A</span> <?= $soal['pilihan_a'] ?></div>
              <div class="d-flex mb-2"><span class="badge bg-primary me-2">B</span> <?= $soal['pilihan_b'] ?></div>
              <div class="d-flex mb-2"><span class="badge bg-primary me-2">C</span> <?= $soal['pilihan_c'] ?></div>
              <div class="d-flex mb-2"><span class="badge bg-primary me-2">D</span> <?= $soal['pilihan_d'] ?></div>
              <?php if (!empty($soal['pilihan_e'])): ?>
                <div class="d-flex mb-2"><span class="badge bg-primary me-2">E</span> <?= $soal['pilihan_e'] ?></div>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($soal['pembahasan'])): ?>
            <div class="card bg-light">
              <div class="card-header"><strong>Pembahasan</strong></div>
              <div class="card-body"><?= $soal['pembahasan'] ?></div>
            </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- Modal Edit Soal -->
<?php foreach ($soalList as $soal): ?>
  <div class="modal fade" id="editModal<?= $soal['soal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark border-0">
          <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Soal #<?= $soal['soal_id'] ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('admin/bank-soal/edit-soal/' . $soal['soal_id']) ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <div class="modal-body">
            <div class="card mb-3 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-code text-warning me-2"></i>Kode Soal</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                    <input type="text" name="kode_soal" class="form-control"
                      value="<?= esc($soal['kode_soal']) ?>" required>
                    <small class="text-muted">Kode unik untuk soal ini</small>
                  </div>
                  <div class="col-md-6">
                    <div class="mt-4">
                      <div class="alert alert-warning py-2 mb-0">
                        <small>
                          <strong>Format Kode:</strong><br>
                          • 3-50 karakter<br>
                          • Boleh huruf, angka, dan simbol<br>
                          • Harus unik (tidak boleh sama)
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pertanyaan Section -->
            <div class="card mb-3 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i>Pertanyaan Soal</h6>
              </div>
              <div class="card-body">
                <div class="mb-2">
                  <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    Gunakan toolbar simbol cepat di atas editor atau klik <strong>Special Characters</strong> untuk simbol matematika lengkap
                  </small>
                </div>
                <textarea name="pertanyaan" id="pertanyaan_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="4" required><?= esc($soal['pertanyaan']) ?></textarea>

                <div class="mt-3">
                  <label class="form-label"><i class="fas fa-image text-secondary me-1"></i>Foto Soal</label>
                  <?php if (!empty($soal['foto'])): ?>
                    <div class="mb-2">
                      <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                    <div class="form-check mb-2">
                      <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapusFoto<?= $soal['soal_id'] ?>" value="1">
                      <label class="form-check-label" for="hapusFoto<?= $soal['soal_id'] ?>">
                        Hapus foto yang ada
                      </label>
                    </div>
                  <?php endif; ?>
                  <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                  <small class="text-muted">Upload gambar baru dengan format JPG, JPEG, atau PNG (maks. 2MB)</small>
                </div>
              </div>
            </div>

            <!-- Pilihan Jawaban Section -->
            <div class="card mb-3 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-list text-success me-2"></i>Pilihan Jawaban</h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">A.</label>
                    <textarea name="pilihan_a" id="pilihan_a_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="2" required><?= esc($soal['pilihan_a']) ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">B.</label>
                    <textarea name="pilihan_b" id="pilihan_b_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="2" required><?= esc($soal['pilihan_b']) ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">C.</label>
                    <textarea name="pilihan_c" id="pilihan_c_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="2" required><?= esc($soal['pilihan_c']) ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">D.</label>
                    <textarea name="pilihan_d" id="pilihan_d_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="2" required><?= esc($soal['pilihan_d']) ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-warning">E. (Opsional)</label>
                    <textarea name="pilihan_e" id="pilihan_e_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="2"><?= isset($soal['pilihan_e']) ? esc($soal['pilihan_e']) : '' ?></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-check-circle text-success me-1"></i>Jawaban Benar</label>
                    <select name="jawaban_benar" class="form-select form-select-lg" required>
                      <option value="">Pilih Jawaban Benar</option>
                      <option value="A" <?= $soal['jawaban_benar'] == 'A' ? 'selected' : '' ?>>A</option>
                      <option value="B" <?= $soal['jawaban_benar'] == 'B' ? 'selected' : '' ?>>B</option>
                      <option value="C" <?= $soal['jawaban_benar'] == 'C' ? 'selected' : '' ?>>C</option>
                      <option value="D" <?= $soal['jawaban_benar'] == 'D' ? 'selected' : '' ?>>D</option>
                      <option value="E" <?= $soal['jawaban_benar'] == 'E' ? 'selected' : '' ?>>E</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pengaturan Soal Section -->
            <div class="card mb-3 shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-cogs text-warning me-2"></i>Pengaturan Soal</h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-balance-scale text-info me-1"></i>Tingkat Kesulitan</label>
                    <div class="input-group">
                      <input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="<?= $soal['tingkat_kesulitan'] ?>" min="-3" max="3" required>
                      <span class="input-group-text">(-3 hingga +3)</span>
                    </div>
                    <small class="text-muted">Negatif = mudah, Positif = sulit, 0 = sedang</small>
                  </div>
                  <div class="col-md-6">
                    <div class="mt-4">
                      <div class="alert alert-info py-2 mb-0">
                        <small>
                          <strong>Panduan Tingkat Kesulitan:</strong><br>
                          -3.000 hingga -1.000 = Mudah<br>
                          -0.999 hingga +0.999 = Sedang<br>
                          +1.000 hingga +3.000 = Sulit
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pembahasan Section -->
            <div class="card shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-lightbulb text-info me-2"></i>Pembahasan (Opsional)</h6>
              </div>
              <div class="card-body">
                <div class="mb-2">
                  <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    Gunakan toolbar simbol cepat atau Special Characters untuk rumus matematika
                  </small>
                </div>
                <textarea name="pembahasan" id="pembahasan_edit_<?= $soal['soal_id'] ?>" class="form-control" rows="4"><?= isset($soal['pembahasan']) ? esc($soal['pembahasan']) : '' ?></textarea>
                <small class="text-muted mt-2 d-block">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Batal
            </button>
            <button type="submit" class="btn btn-warning btn-lg">
              <i class="fas fa-save me-1"></i>Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- Load CKEditor 4 -->
<script src="<?= base_url('ckeditor/ckeditor.js') ?>"></script>

<script>
  // Konfigurasi CKEditor (sama persis dengan guru)
  const ckEditorConfig = {
    height: 200,
    toolbar: [{
        name: 'document',
        items: ['Source']
      },
      {
        name: 'clipboard',
        items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
      },
      {
        name: 'editing',
        items: ['Find', 'Replace', '-', 'SelectAll']
      },
      '/',
      {
        name: 'basicstyles',
        items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
      },
      {
        name: 'paragraph',
        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
      },
      {
        name: 'links',
        items: ['Link', 'Unlink']
      },
      '/',
      {
        name: 'styles',
        items: ['Styles', 'Format', 'Font', 'FontSize']
      },
      {
        name: 'colors',
        items: ['TextColor', 'BGColor']
      },
      {
        name: 'tools',
        items: ['Maximize', 'ShowBlocks']
      },
      {
        name: 'insert',
        items: ['Table', 'HorizontalRule', 'SpecialChar', 'MathSymbols']
      }
    ],
    removePlugins: 'image,uploadimage,uploadwidget,uploadfile,filetools,filebrowser,exportpdf',
    versionCheck: false,
    extraPlugins: 'specialchar',
    disallowedContent: 'img[src]',
    allowedContent: {
      'h1 h2 h3 h4 h5 h6 p blockquote li ul ol': true,
      'strong em u s sub sup': true,
      'table thead tbody tr th td': true,
      'a[href]': true,
      'span{color,background-color,font-size,font-family}': true,
      'div{text-align}': true
    },
    specialChars: [
      ['α', 'Alpha'],
      ['β', 'Beta'],
      ['γ', 'Gamma'],
      ['δ', 'Delta'],
      ['ε', 'Epsilon'],
      ['ζ', 'Zeta'],
      ['η', 'Eta'],
      ['θ', 'Theta'],
      ['ι', 'Iota'],
      ['κ', 'Kappa'],
      ['λ', 'Lambda'],
      ['μ', 'Mu'],
      ['ν', 'Nu'],
      ['ξ', 'Xi'],
      ['ο', 'Omicron'],
      ['π', 'Pi'],
      ['ρ', 'Rho'],
      ['σ', 'Sigma'],
      ['τ', 'Tau'],
      ['υ', 'Upsilon'],
      ['φ', 'Phi'],
      ['χ', 'Chi'],
      ['ψ', 'Psi'],
      ['ω', 'Omega'],
      ['Α', 'Alpha (capital)'],
      ['Β', 'Beta (capital)'],
      ['Γ', 'Gamma (capital)'],
      ['Δ', 'Delta (capital)'],
      ['Ε', 'Epsilon (capital)'],
      ['Ζ', 'Zeta (capital)'],
      ['Η', 'Eta (capital)'],
      ['Θ', 'Theta (capital)'],
      ['Ι', 'Iota (capital)'],
      ['Κ', 'Kappa (capital)'],
      ['Λ', 'Lambda (capital)'],
      ['Μ', 'Mu (capital)'],
      ['Ν', 'Nu (capital)'],
      ['Ξ', 'Xi (capital)'],
      ['Ο', 'Omicron (capital)'],
      ['Π', 'Pi (capital)'],
      ['Ρ', 'Rho (capital)'],
      ['Σ', 'Sigma (capital)'],
      ['Τ', 'Tau (capital)'],
      ['Υ', 'Upsilon (capital)'],
      ['Φ', 'Phi (capital)'],
      ['Χ', 'Chi (capital)'],
      ['Ψ', 'Psi (capital)'],
      ['Ω', 'Omega (capital)'],
      ['±', 'Plus-minus'],
      ['∓', 'Minus-plus'],
      ['×', 'Multiplication'],
      ['÷', 'Division'],
      ['∝', 'Proportional'],
      ['∞', 'Infinity'],
      ['∂', 'Partial derivative'],
      ['∇', 'Nabla (gradient)'],
      ['∆', 'Delta (change)'],
      ['∑', 'Summation'],
      ['∏', 'Product'],
      ['∫', 'Integral'],
      ['∮', 'Contour integral'],
      ['∬', 'Double integral'],
      ['∭', 'Triple integral'],
      ['≈', 'Approximately equal'],
      ['≠', 'Not equal'],
      ['≡', 'Identical'],
      ['≤', 'Less than or equal'],
      ['≥', 'Greater than or equal'],
      ['«', 'Much less than'],
      ['»', 'Much greater than'],
      ['∈', 'Element of'],
      ['∉', 'Not element of'],
      ['⊂', 'Subset'],
      ['⊃', 'Superset'],
      ['∪', 'Union'],
      ['∩', 'Intersection'],
      ['∀', 'For all'],
      ['∃', 'There exists'],
      ['∄', 'There does not exist'],
      ['∧', 'Logical and'],
      ['∨', 'Logical or'],
      ['¬', 'Not'],
      ['→', 'Right arrow'],
      ['←', 'Left arrow'],
      ['↑', 'Up arrow'],
      ['↓', 'Down arrow'],
      ['↔', 'Left-right arrow'],
      ['⇒', 'Right double arrow'],
      ['⇐', 'Left double arrow'],
      ['⇔', 'Left-right double arrow'],
      ['↗', 'Northeast arrow'],
      ['↖', 'Northwest arrow'],
      ['↘', 'Southeast arrow'],
      ['↙', 'Southwest arrow'],
      ['°', 'Degree'],
      ['′', 'Prime (minutes/feet)'],
      ['″', 'Double prime (seconds/inches)'],
      ['℃', 'Celsius'],
      ['℉', 'Fahrenheit'],
      ['Å', 'Angstrom'],
      ['ℏ', 'Reduced Planck constant'],
      ['ħ', 'H-bar'],
      ['½', 'One half'],
      ['⅓', 'One third'],
      ['⅔', 'Two thirds'],
      ['¼', 'One quarter'],
      ['¾', 'Three quarters'],
      ['⅕', 'One fifth'],
      ['⅖', 'Two fifths'],
      ['⅗', 'Three fifths'],
      ['⅘', 'Four fifths'],
      ['⅙', 'One sixth'],
      ['⅚', 'Five sixths'],
      ['⅛', 'One eighth'],
      ['⅜', 'Three eighths'],
      ['⅝', 'Five eighths'],
      ['⅞', 'Seven eighths'],
      ['⁰', 'Superscript 0'],
      ['¹', 'Superscript 1'],
      ['²', 'Superscript 2'],
      ['³', 'Superscript 3'],
      ['⁴', 'Superscript 4'],
      ['⁵', 'Superscript 5'],
      ['⁶', 'Superscript 6'],
      ['⁷', 'Superscript 7'],
      ['⁸', 'Superscript 8'],
      ['⁹', 'Superscript 9'],
      ['⁺', 'Superscript plus'],
      ['⁻', 'Superscript minus'],
      ['₀', 'Subscript 0'],
      ['₁', 'Subscript 1'],
      ['₂', 'Subscript 2'],
      ['₃', 'Subscript 3'],
      ['₄', 'Subscript 4'],
      ['₅', 'Subscript 5'],
      ['₆', 'Subscript 6'],
      ['₇', 'Subscript 7'],
      ['₈', 'Subscript 8'],
      ['₉', 'Subscript 9'],
      ['₊', 'Subscript plus'],
      ['₋', 'Subscript minus'],
      ['√', 'Square root'],
      ['∛', 'Cube root'],
      ['∜', 'Fourth root']
    ],
    filebrowserBrowseUrl: '',
    filebrowserUploadUrl: '',
    filebrowserImageBrowseUrl: '',
    filebrowserImageUploadUrl: '',
    contentsCss: [
      'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; }',
      'table { border-collapse: collapse; width: 100%; }',
      'table, th, td { border: 1px solid #ddd; padding: 8px; }'
    ],
    enterMode: CKEDITOR.ENTER_P,
    shiftEnterMode: CKEDITOR.ENTER_BR
  };

  // Konfigurasi khusus untuk pilihan (lebih kecil)
  const ckEditorConfigPilihan = {
    height: 120,
    toolbar: [{
        name: 'basicstyles',
        items: ['Bold', 'Italic', 'Underline', 'Subscript', 'Superscript']
      },
      {
        name: 'colors',
        items: ['TextColor']
      },
      {
        name: 'insert',
        items: ['SpecialChar']
      },
      {
        name: 'tools',
        items: ['Source']
      }
    ],
    removePlugins: 'image,uploadimage,uploadwidget,uploadfile,filetools,filebrowser,exportpdf',
    versionCheck: false,
    extraPlugins: 'specialchar',
    disallowedContent: 'img[src]',
    allowedContent: {
      'strong em u s sub sup': true,
      'span{color,background-color,font-size,font-family}': true
    },
    specialChars: ckEditorConfig.specialChars,
    filebrowserBrowseUrl: '',
    filebrowserUploadUrl: '',
    filebrowserImageBrowseUrl: '',
    filebrowserImageUploadUrl: '',
    contentsCss: [
      'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; }',
      'sub, sup { font-size: 0.75em; }'
    ],
    enterMode: CKEDITOR.ENTER_P,
    shiftEnterMode: CKEDITOR.ENTER_BR
  };

  // Initialize CKEditor untuk modal tambah
  function initializeCKEditorTambah() {
    destroyCKEditorInstances([
      'pertanyaan_tambah',
      'pilihan_a_tambah',
      'pilihan_b_tambah',
      'pilihan_c_tambah',
      'pilihan_d_tambah',
      'pilihan_e_tambah',
      'pembahasan_tambah'
    ]);

    CKEDITOR.replace('pertanyaan_tambah', ckEditorConfig);
    CKEDITOR.replace('pilihan_a_tambah', ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_b_tambah', ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_c_tambah', ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_d_tambah', ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_e_tambah', ckEditorConfigPilihan);
    CKEDITOR.replace('pembahasan_tambah', ckEditorConfig);
  }

  // Initialize CKEditor untuk modal edit
  function initializeCKEditorEdit(soalId) {
    const editorIds = [
      'pertanyaan_edit_' + soalId,
      'pilihan_a_edit_' + soalId,
      'pilihan_b_edit_' + soalId,
      'pilihan_c_edit_' + soalId,
      'pilihan_d_edit_' + soalId,
      'pilihan_e_edit_' + soalId,
      'pembahasan_edit_' + soalId
    ];

    destroyCKEditorInstances(editorIds);

    CKEDITOR.replace('pertanyaan_edit_' + soalId, ckEditorConfig);
    CKEDITOR.replace('pilihan_a_edit_' + soalId, ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_b_edit_' + soalId, ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_c_edit_' + soalId, ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_d_edit_' + soalId, ckEditorConfigPilihan);
    CKEDITOR.replace('pilihan_e_edit_' + soalId, ckEditorConfigPilihan);
    CKEDITOR.replace('pembahasan_edit_' + soalId, ckEditorConfig);
  }

  // Destroy CKEditor instances
  function destroyCKEditorInstances(editorIds) {
    editorIds.forEach(id => {
      if (CKEDITOR.instances[id]) {
        CKEDITOR.instances[id].destroy();
      }
    });
  }

  // Event listeners untuk modal
  document.addEventListener('DOMContentLoaded', function() {
    // Modal tambah soal
    document.getElementById('modalTambahSoal').addEventListener('shown.bs.modal', function() {
      setTimeout(() => {
        initializeCKEditorTambah();
      }, 100);
    });

    document.getElementById('modalTambahSoal').addEventListener('hidden.bs.modal', function() {
      destroyCKEditorInstances([
        'pertanyaan_tambah',
        'pilihan_a_tambah',
        'pilihan_b_tambah',
        'pilihan_c_tambah',
        'pilihan_d_tambah',
        'pilihan_e_tambah',
        'pembahasan_tambah'
      ]);
    });

    // Modal edit soal
    <?php foreach ($soalList as $s): ?>
      const editModal<?= $s['soal_id'] ?> = document.getElementById('editModal<?= $s['soal_id'] ?>');
      if (editModal<?= $s['soal_id'] ?>) {
        editModal<?= $s['soal_id'] ?>.addEventListener('shown.bs.modal', function() {
          setTimeout(() => {
            initializeCKEditorEdit(<?= $s['soal_id'] ?>);
          }, 100);
        });

        editModal<?= $s['soal_id'] ?>.addEventListener('hidden.bs.modal', function() {
          destroyCKEditorInstances([
            'pertanyaan_edit_<?= $s['soal_id'] ?>',
            'pilihan_a_edit_<?= $s['soal_id'] ?>',
            'pilihan_b_edit_<?= $s['soal_id'] ?>',
            'pilihan_c_edit_<?= $s['soal_id'] ?>',
            'pilihan_d_edit_<?= $s['soal_id'] ?>',
            'pilihan_e_edit_<?= $s['soal_id'] ?>',
            'pembahasan_edit_<?= $s['soal_id'] ?>'
          ]);
        });
      }
    <?php endforeach; ?>
  });

  // Update form data before submit
  function updateCKEditorData() {
    for (let instance in CKEDITOR.instances) {
      CKEDITOR.instances[instance].updateElement();
    }
  }

  // Add event listeners to forms
  document.addEventListener('submit', function(e) {
    if (e.target.tagName === 'FORM') {
      updateCKEditorData();
    }
  });

  // Add quick math symbols toolbar
  function addQuickMathSymbols() {
    const quickMathHtml = `
        <div class="quick-math-symbols p-2 bg-light border-bottom">
            <small class="text-muted me-2">Simbol Cepat:</small>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('²')" title="Kuadrat">x²</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('³')" title="Kubik">x³</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('½')" title="Setengah">½</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('π')" title="Pi">π</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('α')" title="Alpha">α</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('β')" title="Beta">β</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('γ')" title="Gamma">γ</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('θ')" title="Theta">θ</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('±')" title="Plus minus">±</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('≤')" title="Kurang sama dengan">≤</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('≥')" title="Lebih sama dengan">≥</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('∫')" title="Integral">∫</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('∑')" title="Sigma">∑</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('√')" title="Akar">√</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('∞')" title="Infinity">∞</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('°')" title="Derajat">°</button>
        </div>
    `;

    document.addEventListener('shown.bs.modal', function(e) {
      if (e.target.id === 'modalTambahSoal' || e.target.id.startsWith('editModal')) {
        const modalBody = e.target.querySelector('.modal-body');
        if (modalBody && !modalBody.querySelector('.quick-math-symbols')) {
          modalBody.insertAdjacentHTML('afterbegin', quickMathHtml);
        }
      }
    });
  }

  // Function to insert symbol into active CKEditor
  function insertSymbol(symbol) {
    for (let instanceName in CKEDITOR.instances) {
      const editor = CKEDITOR.instances[instanceName];
      if (editor.focusManager.hasFocus) {
        editor.insertText(symbol);
        editor.focus();
        return;
      }
    }

    const visibleEditors = [];
    for (let instanceName in CKEDITOR.instances) {
      const editor = CKEDITOR.instances[instanceName];
      if (editor.container && editor.container.isVisible()) {
        visibleEditors.push(editor);
      }
    }

    if (visibleEditors.length > 0) {
      visibleEditors[0].insertText(symbol);
      visibleEditors[0].focus();
      return;
    }

    const firstEditor = Object.keys(CKEDITOR.instances)[0];
    if (firstEditor && CKEDITOR.instances[firstEditor]) {
      CKEDITOR.instances[firstEditor].insertText(symbol);
      CKEDITOR.instances[firstEditor].focus();
    }
  }

  // Initialize quick math symbols
  document.addEventListener('DOMContentLoaded', function() {
    addQuickMathSymbols();

    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      modal.addEventListener('hidden.bs.modal', function() {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.remove();
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
      });
    });
  });

  function hapusSoal(soalId) {
    if (confirm('Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.')) {
      window.location.href = '<?= base_url('admin/bank-soal/hapus-soal/') ?>' + soalId;
    }
  }
</script>

<style>
  /* Custom styles untuk CKEditor (sama dengan guru) */
  .cke_editor {
    margin-bottom: 10px;
  }

  .cke_contents {
    border-radius: 0 0 4px 4px;
  }

  .cke_top {
    border-radius: 4px 4px 0 0;
  }

  .cke_editable {
    font-family: 'Times New Roman', Times, serif;
    line-height: 1.5;
  }

  .cke_button__specialchar {
    background-color: #e3f2fd !important;
  }

  .cke_button__specialchar:hover {
    background-color: #bbdefb !important;
  }

  .math-symbols {
    font-family: 'Times New Roman', 'Symbol', 'Arial Unicode MS', serif;
    font-size: 1.1em;
  }

  .quick-math-symbols {
    border-radius: 4px 4px 0 0;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-top: 1px solid #ddd;
  }

  .quick-math-symbols .btn {
    font-family: 'Times New Roman', Times, serif;
    font-size: 14px;
    padding: 2px 6px;
    line-height: 1.2;
  }

  .quick-math-symbols .btn:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
  }

  /* Enhanced Modal Styling */
  .modal-header.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
  }

  .modal-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
  }

  .modal-footer.bg-light {
    background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%) !important;
  }

  .modal-body .card {
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
  }

  .modal-body .card:hover {
    border-color: #adb5bd;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .modal-body .card-header {
    background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%) !important;
    border-bottom: 1px solid #dee2e6;
  }

  .modal-body .card-header h6 {
    color: #495057;
    font-weight: 600;
  }

  .input-group-text {
    background-color: #e9ecef;
    border-color: #ced4da;
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
  }

  .form-control-lg,
  .form-select-lg {
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
    border-radius: 0.5rem;
  }

  .modal-body .alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
    border-radius: 0.5rem;
  }

  .form-label i {
    width: 16px;
    text-align: center;
  }

  .modal-footer .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
  }

  .modal-footer .btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
  }

  .modal-footer .btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
  }

  .modal-footer .btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    border: none;
    color: #212529;
  }

  .modal-footer .btn-warning:hover {
    background: linear-gradient(135deg, #e0a800 0%, #c7950b 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
  }

  @media (max-width: 768px) {
    .table-responsive {
      font-size: 0.8rem;
    }

    .modal-xl {
      max-width: 95%;
    }

    .cke_toolbar {
      white-space: normal !important;
    }
  }

  .card {
    border: none;
    transition: all 0.3s ease;
  }

  .table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }

  .btn-group-sm .btn {
    --bs-btn-padding-y: 0.25rem;
    --bs-btn-padding-x: 0.75rem;
    --bs-btn-font-size: 0.875rem;
  }

  .breadcrumb {
    background: none;
    padding: 0;
  }

  .breadcrumb-item+.breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
  }

  .breadcrumb-item.active {
    color: #6c757d;
    font-weight: 500;
  }

  .breadcrumb-item a {
    color: #0d6efd;
    font-weight: 500;
  }

  .breadcrumb-item a:hover {
    color: #0b5ed7;
  }

  .modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .alert-info .text-muted {
    color: #0c5460 !important;
  }

  kbd {
    padding: 2px 4px;
    font-size: 87.5%;
    color: #fff;
    background-color: #212529;
    border-radius: 3px;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .25);
  }
</style>

<?= $this->endSection() ?>