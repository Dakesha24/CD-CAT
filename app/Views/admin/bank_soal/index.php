<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>

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

<!-- Tambahkan ini untuk debug validation errors -->
<?php if (session()->getFlashdata('errors')): ?>
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Validation Errors:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach (session()->getFlashdata('errors') as $error): ?>
        <li><?= $error ?></li>
      <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">Bank Soal</h2>
      <p class="text-muted">Kelola semua bank soal dalam sistem</p>
    </div>
    <div class="col-auto">
      <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBankSoal">
        <i class="fas fa-plus me-2"></i>Tambah Bank Soal Baru
      </button>
    </div>
  </div>

  <!-- Alert Messages -->
  <!-- <?php if (session()->getFlashdata('success')): ?>
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
  <?php endif; ?> -->

  <!-- Kategori Bank Soal -->
  <div class="row g-4">
    <?php if (!empty($kategoriList)): ?>
      <?php foreach ($kategoriList as $kategori): ?>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm hover-shadow">
            <div class="card-body text-center p-4">
              <div class="dropdown text-end">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori['kategori'])) ?>">
                      <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                  </li>
                  <li>
                    <button class="dropdown-item btn-edit-kategori"
                      data-bs-toggle="modal"
                      data-bs-target="#editKategoriModal"
                      data-kategori-name="<?= esc($kategori['kategori']) ?>">
                      <i class="bi bi-pencil me-2"></i>Edit Kategori
                    </button>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="dropdown-item text-danger" href="<?= base_url('admin/bank-soal/hapus-kategori/' . urlencode($kategori['kategori'])) ?>"
                      onclick="return confirm('PERHATIAN! Anda akan menghapus kategori \'<?= esc($kategori['kategori']) ?>\' dan SEMUA bank ujian di dalamnya. Aksi ini tidak dapat dibatalkan. Lanjutkan?')">
                      <i class="bi bi-trash me-2"></i>Hapus Kategori
                    </a>
                  </li>
                </ul>
              </div>
              <div class="mb-3">
                <?php if ($kategori['kategori'] === 'umum'): ?>
                  <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-globe fa-2x text-primary"></i>
                  </div>
                <?php else: ?>
                  <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
                  </div>
                <?php endif; ?>
              </div>
              <h5 class="card-title fw-bold">
                <?= $kategori['kategori'] === 'umum' ? 'Bank Soal Umum' : 'Kelas ' . esc($kategori['kategori']) ?>
              </h5>
              <p class="card-text text-muted mb-3">
                <?= $kategori['jumlah_bank'] ?> bank ujian tersedia
              </p>
              <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori['kategori'])) ?>"
                class="btn <?= $kategori['kategori'] === 'umum' ? 'btn-outline-primary' : 'btn-outline-success' ?>">
                <i class="fas fa-arrow-right me-2"></i>Kelola Kategori
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body text-center p-5">
            <div class="mb-3">
              <i class="fas fa-inbox fa-3x text-muted"></i>
            </div>
            <h5 class="card-title">Belum Ada Bank Soal</h5>
            <p class="card-text text-muted">Mulai dengan menambahkan bank soal pertama</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBankSoal">
              <i class="fas fa-plus me-2"></i>Tambah Bank Soal
            </button>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah Bank Soal -->
<div class="modal fade" id="modalTambahBankSoal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-plus-circle text-primary me-2"></i>Tambah Bank Soal Baru
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/bank-soal/tambah') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label for="kategori" class="form-label fw-semibold">Kategori Bank Soal</label>
              <input type="text" class="form-control" id="kategori" name="kategori"
                placeholder="Contoh: umum, Kelas X IPA 1, dll" required>
              <div class="form-text">Masukkan nama kategori (umum untuk akses semua guru)</div>
            </div>

            <div class="col-12">
              <label for="jenis_ujian_id" class="form-label fw-semibold">Mata Pelajaran</label>
              <select class="form-select" id="jenis_ujian_id" name="jenis_ujian_id" required>
                <option value="">Pilih Mata Pelajaran</option>
                <?php foreach ($jenisUjianList as $jenis): ?>
                  <option value="<?= $jenis['jenis_ujian_id'] ?>"><?= esc($jenis['nama_jenis']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label for="nama_ujian" class="form-label fw-semibold">Nama Bank Ujian</label>
              <input type="text" class="form-control" id="nama_ujian" name="nama_ujian"
                placeholder="Contoh: Ujian Tengah Semester Ganjil 2024" required>
            </div>

            <div class="col-12">
              <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                placeholder="Deskripsi bank soal..." required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="editKategoriModalLabel">
          <i class="fas fa-edit text-primary me-2"></i>Edit Nama Kategori
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/bank-soal/edit-kategori') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="old_kategori_name" id="old_kategori_name">
        <div class="modal-body">
          <div class="mb-3">
            <label for="new_kategori_name" class="form-label">Nama Kategori Baru</label>
            <input type="text" class="form-control" id="new_kategori_name" name="new_kategori_name" required>
            <div class="form-text">Mengubah nama ini akan memperbarui semua bank ujian dalam kategori ini.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
  }

  .card {
    border: none;
    transition: all 0.3s ease;
  }
</style>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk tombol edit
    const editButtons = document.querySelectorAll('.btn-edit-kategori');
    const editModal = new bootstrap.Modal(document.getElementById('editKategoriModal'));
    const oldKategoriInput = document.getElementById('old_kategori_name');
    const newKategoriInput = document.getElementById('new_kategori_name');

    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const kategoriName = this.getAttribute('data-kategori-name');
        oldKategoriInput.value = kategoriName;
        newKategoriInput.value = kategoriName; // Isi dengan nama lama sebagai default
      });
    });

    // Script untuk validasi form tambah bank soal (tetap sama)
    const form = document.querySelector('#modalTambahBankSoal form');
    if (form) {
      form.addEventListener('submit', function(e) {
        const deskripsi = document.getElementById('deskripsi').value;
        if (deskripsi.length < 10) {
          e.preventDefault();
          alert(`Deskripsi minimal 10 karakter. Saat ini: ${deskripsi.length} karakter`);
          document.getElementById('deskripsi').focus();
          return false;
        }
      });
    }
  });
</script>


<?= $this->endSection() ?>