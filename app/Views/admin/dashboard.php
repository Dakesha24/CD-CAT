<?= $this->extend('templates/admin_header') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
  <div class="row">
    <div class="col-md-12 mb-4">
      <h2>Admin Dashboard</h2>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3">
      <!-- Sidebar Menu -->
      <div class="list-group">
        <a href="<?= base_url('admin/dashboard') ?>" class="list-group-item list-group-item-action active">
          Dashboard
        </a>
        <a href="<?= base_url('admin/users') ?>" class="list-group-item list-group-item-action">
          Kelola Pengguna
        </a>
        <a href="<?= base_url('admin/exams') ?>" class="list-group-item list-group-item-action">
          Kelola Ujian
        </a>
        <a href="<?= base_url('admin/feedback') ?>" class="list-group-item list-group-item-action">
          Kritik & Saran
          <?php
          // Jika ada feedback yang belum dibaca
          $unreadCount = 0; // Ganti dengan query yang sesuai
          if ($unreadCount > 0):
          ?>
            <span class="badge bg-danger float-end"><?= $unreadCount ?></span>
          <?php endif; ?>
        </a>
        <a href="<?= base_url('admin/settings') ?>" class="list-group-item list-group-item-action">
          Pengaturan
        </a>
      </div>
    </div>

    <div class="col-md-9">
      <!-- Dashboard Content -->
      <div class="row">
        <div class="col-md-4">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5 class="card-title">Total Pengguna</h5>
              <h2 class="card-text">150</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">Konten Materi</h5>
              <h2 class="card-text">25</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5 class="card-title">Feedback Baru</h5>
              <h2 class="card-text">10</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="card mt-4">
        <div class="card-header">
          Aktivitas Terbaru
        </div>
        <div class="card-body">
          <ul class="list-group">
            <li class="list-group-item">
              <span class="badge bg-primary">Baru</span>
              Pengguna baru mendaftar - John Doe
            </li>
            <li class="list-group-item">
              <span class="badge bg-success">Update</span>
              Konten diperbarui - Materi Kinematika
            </li>
            <li class="list-group-item">
              <span class="badge bg-info">Feedback</span>
              Feedback baru diterima dari user123
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>