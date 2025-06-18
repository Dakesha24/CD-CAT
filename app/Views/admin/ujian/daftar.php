<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Kelola Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>

<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Ujian</h4>
                    <div>
                        <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-info me-2">
                            <i class="fas fa-calendar"></i> Jadwal Ujian
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
                            <input type="text" class="form-control" id="searchUjian" placeholder="Cari nama ujian...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterJenis">
                                <option value="">Semua Mata Pelajaran</option>
                                <?php
                                $jenisUnique = array_unique(array_filter(array_column($ujian, 'nama_jenis')));
                                foreach ($jenisUnique as $jenis): ?>
                                    <option value="<?= esc($jenis) ?>"><?= esc($jenis) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterSekolah">
                                <option value="">Semua Sekolah</option>
                                <?php
                                $sekolahUnique = array_unique(array_filter(array_column($ujian, 'nama_sekolah')));
                                foreach ($sekolahUnique as $sekolah): ?>
                                    <option value="<?= esc($sekolah) ?>"><?= esc($sekolah) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterKelas">
                                <option value="">Semua Kelas</option>
                                <?php
                                $kelasUnique = array_unique(array_filter(array_column($ujian, 'nama_kelas')));
                                foreach ($kelasUnique as $kelas): ?>
                                    <option value="<?= esc($kelas) ?>"><?= esc($kelas) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="resetFilter()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableUjian">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Ujian</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Sekolah</th>
                                    <th>Kelas</th>
                                    <th>Durasi</th>
                                    <th>Total Soal</th>
                                    <th>Total Jadwal</th>
                                    <th>Guru Pembuat</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ujian)): ?>
                                    <?php foreach ($ujian as $index => $u): ?>
                                        <tr data-jenis="<?= esc($u['nama_jenis']) ?>" 
                                            data-sekolah="<?= esc($u['nama_sekolah']) ?>"
                                            data-kelas="<?= esc($u['nama_kelas']) ?>">
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= esc($u['nama_ujian']) ?></strong>
                                                <?php if (!empty($u['deskripsi'])): ?>
                                                    <br><small class="text-muted"><?= strlen($u['deskripsi']) > 50 ? substr(esc($u['deskripsi']), 0, 50) . '...' : esc($u['deskripsi']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['nama_jenis']): ?>
                                                    <span class="badge bg-primary"><?= esc($u['nama_jenis']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['nama_sekolah']): ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-school text-success me-1"></i>
                                                        <span class="text-truncate" title="<?= esc($u['nama_sekolah']) ?>">
                                                            <?= strlen($u['nama_sekolah']) > 20 ? substr(esc($u['nama_sekolah']), 0, 20) . '...' : esc($u['nama_sekolah']) ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted"><em>Tidak ada</em></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['nama_kelas']): ?>
                                                    <div class="text-center">
                                                        <span class="badge bg-warning text-dark"><?= esc($u['nama_kelas']) ?></span>
                                                        <?php if ($u['tahun_ajaran']): ?>
                                                            <br><small class="text-muted"><?= esc($u['tahun_ajaran']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted"><em>Tidak ada</em></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock text-info"></i>
                                                <?= $u['durasi'] ? date('H:i', strtotime($u['durasi'])) . ' jam' : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?= $u['total_soal'] ?> Soal</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $u['total_jadwal'] ?> Jadwal</span>
                                            </td>
                                            <td>
                                                <?= esc($u['guru_pembuat']) ?: '<em class="text-muted">Tidak diketahui</em>' ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-grid gap-1">
                                                    <a href="<?= base_url('admin/ujian/detail/' . $u['id_ujian']) ?>"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Detail & Soal
                                                    </a>
                                                    <a href="<?= base_url('admin/ujian/hapus/' . $u['id_ujian']) ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('PERINGATAN!\n\nMenghapus ujian akan menghapus:\n- Semua soal ujian\n- Semua jadwal ujian\n- Semua hasil siswa\n\nApakah Anda yakin?')">
                                                        <i class="fas fa-trash me-1"></i>Hapus Ujian
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Belum ada ujian yang dibuat.</p>
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
    document.getElementById('searchUjian').addEventListener('keyup', filterTable);
    document.getElementById('filterJenis').addEventListener('change', filterTable);
    document.getElementById('filterSekolah').addEventListener('change', filterTable);
    document.getElementById('filterKelas').addEventListener('change', filterTable);

    function filterTable() {
        const searchText = document.getElementById('searchUjian').value.toLowerCase();
        const jenisFilter = document.getElementById('filterJenis').value;
        const sekolahFilter = document.getElementById('filterSekolah').value;
        const kelasFilter = document.getElementById('filterKelas').value;
        const rows = document.querySelectorAll('#tableUjian tbody tr');

        rows.forEach(row => {
            if (row.cells.length === 1) return; // Skip "no data" row

            const namaUjian = row.cells[1].textContent.toLowerCase();
            const jenis = row.getAttribute('data-jenis');
            const sekolah = row.getAttribute('data-sekolah');
            const kelas = row.getAttribute('data-kelas');

            const textMatch = !searchText || namaUjian.includes(searchText);
            const jenisMatch = !jenisFilter || jenis === jenisFilter;
            const sekolahMatch = !sekolahFilter || sekolah === sekolahFilter;
            const kelasMatch = !kelasFilter || kelas === kelasFilter;

            row.style.display = (textMatch && jenisMatch && sekolahMatch && kelasMatch) ? '' : 'none';
        });
    }

    function resetFilter() {
        document.getElementById('searchUjian').value = '';
        document.getElementById('filterJenis').value = '';
        document.getElementById('filterSekolah').value = '';
        document.getElementById('filterKelas').value = '';
        filterTable();
    }
</script>

<?= $this->endSection() ?>