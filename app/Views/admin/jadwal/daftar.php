<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Kelola Jadwal Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Daftar Jadwal Ujian</h4>
          <div>
            <a href="<?= base_url('admin/ujian') ?>" class="btn btn-info me-2">
              <i class="fas fa-file-alt"></i> Kelola Ujian
            </a>
          </div>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('success') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <!-- Filter dan Search -->
          <div class="row mb-3">
            <div class="col-md-3">
              <input type="text" class="form-control" id="searchJadwal" placeholder="Cari ujian/guru...">
            </div>
            <div class="col-md-2">
              <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="belum_mulai">Belum Mulai</option>
                <option value="sedang_berlangsung">Berlangsung</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="filterSekolah">
                <option value="">Semua Sekolah</option>
                <?php
                $sekolahUnique = array_unique(array_column($jadwal, 'nama_sekolah'));
                foreach ($sekolahUnique as $sekolah):
                  if ($sekolah): ?>
                    <option value="<?= esc($sekolah) ?>"><?= esc($sekolah) ?></option>
                <?php endif;
                endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <button class="btn btn-outline-secondary" onclick="resetFilter()">
                <i class="fas fa-redo"></i> Reset
              </button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover" id="tableJadwal">
              <thead class="table-primary">
                <tr>
                  <th>No</th>
                  <th>Ujian</th>
                  <th>Guru</th>
                  <th>Kelas & Sekolah</th>
                  <th>Jadwal</th>
                  <th>Status</th>
                  <th>Peserta</th>
                  <th>Kode Akses</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($jadwal)): ?>
                  <?php foreach ($jadwal as $index => $j): ?>
                    <tr data-status="<?= $j['status'] ?>" data-sekolah="<?= esc($j['nama_sekolah']) ?>">
                      <td><?= $index + 1 ?></td>
                      <td>
                        <strong><?= esc($j['nama_ujian']) ?></strong>
                      </td>
                      <td>
                        <?= esc($j['nama_guru']) ?: '<em class="text-muted">Tidak diketahui</em>' ?>
                      </td>
                      <td>
                        <strong><?= esc($j['nama_kelas']) ?></strong><br>
                        <small class="text-muted">
                          <?= esc($j['nama_sekolah']) ?><br>
                          TA: <?= esc($j['tahun_ajaran']) ?>
                        </small>
                      </td>
                      <td>
                        <strong>Mulai:</strong><br>
                        <small><?= date('d/m/Y H:i', strtotime($j['tanggal_mulai'])) ?></small><br>
                        <strong>Selesai:</strong><br>
                        <small><?= date('d/m/Y H:i', strtotime($j['tanggal_selesai'])) ?></small>
                      </td>
                      <td>
                        <?php
                        $statusClass = '';
                        $statusText = '';
                        switch ($j['status']) {
                          case 'belum_mulai':
                            $statusClass = 'bg-warning text-dark';
                            $statusText = 'Belum Mulai';
                            break;
                          case 'sedang_berlangsung':
                            $statusClass = 'bg-success';
                            $statusText = 'Berlangsung';
                            break;
                          case 'selesai':
                            $statusClass = 'bg-secondary';
                            $statusText = 'Selesai';
                            break;
                          default:
                            $statusClass = 'bg-light text-dark';
                            $statusText = $j['status'];
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                      </td>
                      <td>
                        <span class="badge bg-info"><?= $j['total_peserta'] ?> Peserta</span>
                      </td>
                      <td>
                        <code class="bg-light p-1 rounded"><?= esc($j['kode_akses']) ?></code>
                      </td>
                      <td>
                        <div class="d-grid gap-1">
                          <a href="<?= base_url('admin/jadwal/detail/' . $j['jadwal_id']) ?>"
                            class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i>Detail
                          </a>
                          <?php if ($j['status'] !== 'sedang_berlangsung'): ?>
                            <a href="<?= base_url('admin/jadwal/hapus/' . $j['jadwal_id']) ?>"
                              class="btn btn-danger btn-sm"
                              onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ujian ini?\n\nSemua data peserta dan hasil ujian akan ikut terhapus.')">
                              <i class="fas fa-trash me-1"></i>Hapus
                            </a>
                          <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>
                              <i class="fas fa-lock me-1"></i>Terkunci
                            </button>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="9" class="text-center">
                      <div class="py-4">
                        <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada jadwal ujian yang dibuat.</p>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Filter dan Search functionality
  document.getElementById('searchJadwal').addEventListener('keyup', filterTable);
  document.getElementById('filterStatus').addEventListener('change', filterTable);
  document.getElementById('filterSekolah').addEventListener('change', filterTable);

  function filterTable() {
    const searchText = document.getElementById('searchJadwal').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const sekolahFilter = document.getElementById('filterSekolah').value;
    const rows = document.querySelectorAll('#tableJadwal tbody tr');

    rows.forEach(row => {
      if (row.cells.length === 1) return; // Skip "no data" row

      const namaUjian = row.cells[1].textContent.toLowerCase();
      const namaGuru = row.cells[2].textContent.toLowerCase();
      const status = row.getAttribute('data-status');
      const sekolah = row.getAttribute('data-sekolah');

      const textMatch = !searchText || namaUjian.includes(searchText) || namaGuru.includes(searchText);
      const statusMatch = !statusFilter || status === statusFilter;
      const sekolahMatch = !sekolahFilter || sekolah === sekolahFilter;

      row.style.display = (textMatch && statusMatch && sekolahMatch) ? '' : 'none';
    });
  }

  function resetFilter() {
    document.getElementById('searchJadwal').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterSekolah').value = '';
    filterTable();
  }
</script>

<?= $this->endSection() ?>