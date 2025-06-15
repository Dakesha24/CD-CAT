<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<div class="container">
  <div class="row mb-4 py-5">
    <div class="col">
      <h2 class="mb-4">Dashboard Admin</h2>

      <!-- Statistics Cards -->
      <div class="row g-4 mb-5">
        <div class="col-md-3">
          <div class="card bg-primary text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4><?= $stats['total_guru'] ?? 0 ?></h4>
                  <p class="mb-0">Total Guru</p>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-person-workspace fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card bg-success text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4><?= $stats['total_siswa'] ?? 0 ?></h4>
                  <p class="mb-0">Total Siswa</p>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-people fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card bg-info text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4><?= $stats['total_sekolah'] ?? 0 ?></h4>
                  <p class="mb-0">Total Sekolah</p>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-building fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card bg-warning text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4><?= $stats['total_kelas'] ?? 0 ?></h4>
                  <p class="mb-0">Total Kelas</p>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-door-open fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Menu Cards -->
      <div class="row g-4">
        <!-- Kelola Guru Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-primary-subtle mx-auto mb-3">
                <i class="bi bi-person-workspace text-primary fs-1"></i>
              </div>
              <h5 class="card-title">Kelola Guru</h5>
              <p class="card-text">Tambah, edit, dan kelola data guru dalam sistem</p>
              <a href="<?= base_url('admin/guru') ?>" class="btn btn-primary">
                <i class="bi bi-person-gear me-2"></i>Kelola Guru
              </a>
            </div>
          </div>
        </div>

        <!-- Kelola Siswa Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-success-subtle mx-auto mb-3">
                <i class="bi bi-people text-success fs-1"></i>
              </div>
              <h5 class="card-title">Kelola Siswa</h5>
              <p class="card-text">Tambah, edit, dan kelola data siswa dalam sistem</p>
              <a href="<?= base_url('admin/siswa') ?>" class="btn btn-success">
                <i class="bi bi-person-plus me-2"></i>Kelola Siswa
              </a>
            </div>
          </div>
        </div>

        <!-- Kelola Sekolah & Kelas Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-info-subtle mx-auto mb-3">
                <i class="bi bi-building text-info fs-1"></i>
              </div>
              <h5 class="card-title">Kelola Sekolah & Kelas</h5>
              <p class="card-text">Kelola sekolah, kelas, assign guru, dan transfer siswa</p>
              <a href="<?= base_url('admin/sekolah') ?>" class="btn btn-info">
                <i class="bi bi-building-gear me-2"></i>Kelola Sekolah & Kelas
              </a>
            </div>
          </div>
        </div>

        <!-- Kelola Ujian Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-danger-subtle mx-auto mb-3">
                <i class="bi bi-file-earmark-text text-danger fs-1"></i>
              </div>
              <h5 class="card-title">Kelola Ujian</h5>
              <p class="card-text">Monitor dan kelola ujian yang telah dibuat guru</p>
              <a href="<?= base_url('admin/ujian') ?>" class="btn btn-danger">
                <i class="bi bi-clipboard-check me-2"></i>Kelola Ujian
              </a>
            </div>
          </div>
        </div>

        <!-- Jadwal Ujian Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-secondary-subtle mx-auto mb-3">
                <i class="bi bi-calendar-check text-secondary fs-1"></i>
              </div>
              <h5 class="card-title">Jadwal Ujian</h5>
              <p class="card-text">Monitor jadwal ujian dan peserta ujian</p>
              <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary">
                <i class="bi bi-calendar-week me-2"></i>Jadwal Ujian
              </a>
            </div>
          </div>
        </div>

        <!-- Hasil Ujian Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-success-subtle mx-auto mb-3">
                <i class="bi bi-bar-chart text-success fs-1"></i>
              </div>
              <h5 class="card-title">Hasil Ujian</h5>
              <p class="card-text">Monitor dan analisis hasil ujian siswa</p>
              <a href="<?= base_url('admin/hasil-ujian') ?>" class="btn btn-success">
                <i class="bi bi-graph-up me-2"></i>Hasil Ujian
              </a>
            </div>
          </div>
        </div>

        <!-- Kelola Pengumuman Card -->
        <div class="col-md-4 col-lg-3">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-dark-subtle mx-auto mb-3">
                <i class="bi bi-megaphone text-dark fs-1"></i>
              </div>
              <h5 class="card-title">Kelola Pengumuman</h5>
              <p class="card-text">Tambah, edit, dan kelola pengumuman sistem</p>
              <a href="<?= base_url('admin/pengumuman') ?>" class="btn btn-dark">
                <i class="bi bi-plus-circle me-2"></i>Kelola Pengumuman
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
  .menu-card {
    transition: transform 0.2s;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }

  .icon-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card.bg-primary,
  .card.bg-success,
  .card.bg-warning,
  .card.bg-info {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .border-end {
    border-right: 1px solid #dee2e6 !important;
  }

  @media (max-width: 768px) {
    .border-end {
      border-right: none !important;
      border-bottom: 1px solid #dee2e6 !important;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
    }

    .border-end:last-child {
      border-bottom: none !important;
      margin-bottom: 0;
      padding-bottom: 0;
    }
  }
</style>

<?= $this->endSection() ?>