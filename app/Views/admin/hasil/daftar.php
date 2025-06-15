<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Hasil Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Daftar Hasil Ujian
                    </h4>
                    <div>
                        <a href="<?= base_url('admin/ujian') ?>" class="btn btn-info me-2">
                            <i class="fas fa-file-alt me-1"></i>Kelola Ujian
                        </a>
                        <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary">
                            <i class="fas fa-calendar me-1"></i>Jadwal Ujian
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

                    <?php if (empty($daftarUjian)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada ujian yang selesai</h5>
                            <p class="text-muted">Hasil ujian akan muncul setelah ada ujian yang telah diselesaikan siswa.</p>
                        </div>
                    <?php else: ?>
                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="searchUjian" placeholder="Cari nama ujian...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterSekolah">
                                    <option value="">Semua Sekolah</option>
                                    <?php
                                    $sekolahUnique = array_unique(array_column($daftarUjian, 'nama_sekolah'));
                                    foreach ($sekolahUnique as $sekolah):
                                        if ($sekolah): ?>
                                            <option value="<?= esc($sekolah) ?>"><?= esc($sekolah) ?></option>
                                    <?php endif;
                                    endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary" onclick="resetFilter()">
                                    <i class="fas fa-redo me-1"></i>Reset
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach ($daftarUjian as $ujian): ?>
                                <div class="col-md-6 mb-4" data-sekolah="<?= esc($ujian['nama_sekolah']) ?>">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title text-primary mb-1"><?= esc($ujian['nama_ujian']) ?></h5>
                                                    <small class="text-muted"><?= esc($ujian['nama_jenis']) ?></small>
                                                </div>
                                                <span class="badge bg-info"><?= esc($ujian['nama_kelas']) ?></span>
                                            </div>

                                            <p class="card-text text-muted small">
                                                <?= strlen($ujian['deskripsi']) > 100 ? substr(esc($ujian['deskripsi']), 0, 100) . '...' : esc($ujian['deskripsi']) ?>
                                            </p>

                                            <!-- Informasi Waktu -->
                                            <div class="mb-3">
                                                <div class="text-muted small">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span><i class="fas fa-calendar me-1"></i>Mulai:</span>
                                                        <span><?= $ujian['tanggal_mulai_format'] ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span><i class="fas fa-calendar-check me-1"></i>Selesai:</span>
                                                        <span><?= $ujian['tanggal_selesai_format'] ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Statistik Peserta -->
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="text-success mb-0"><?= $ujian['peserta_selesai'] ?></h6>
                                                        <small class="text-muted">Selesai</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="text-primary mb-0"><?= $ujian['jumlah_peserta'] ?></h6>
                                                        <small class="text-muted">Total</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="text-info mb-0">
                                                        <?= $ujian['jumlah_peserta'] > 0 ? round(($ujian['peserta_selesai'] / $ujian['jumlah_peserta']) * 100) : 0 ?>%
                                                    </h6>
                                                    <small class="text-muted">Progress</small>
                                                </div>
                                            </div>

                                            <!-- Statistik Waktu -->
                                            <?php if ($ujian['peserta_selesai'] > 0): ?>
                                                <div class="mb-3">
                                                    <div class="text-muted small">
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-clock me-1"></i>Rata-rata:</span>
                                                            <span class="fw-bold"><?= $ujian['rata_rata_durasi_format'] ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-lightning me-1"></i>Tercepat:</span>
                                                            <span><?= $ujian['durasi_tercepat_format'] ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-hourglass me-1"></i>Terlama:</span>
                                                            <span><?= $ujian['durasi_terlama_format'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="text-muted small mb-3">
                                                <div><i class="fas fa-user me-1"></i> <?= esc($ujian['nama_guru']) ?></div>
                                                <div><i class="fas fa-school me-1"></i> <?= esc($ujian['nama_sekolah']) ?></div>
                                            </div>

                                            <a href="<?= base_url('admin/hasil-ujian/siswa/' . $ujian['jadwal_id']) ?>"
                                                class="btn btn-primary">
                                                <i class="fas fa-eye me-1"></i>Lihat Hasil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter functionality
    document.getElementById('searchUjian').addEventListener('keyup', filterCards);
    document.getElementById('filterSekolah').addEventListener('change', filterCards);

    function filterCards() {
        const searchText = document.getElementById('searchUjian').value.toLowerCase();
        const sekolahFilter = document.getElementById('filterSekolah').value;
        const cards = document.querySelectorAll('.col-md-6[data-sekolah]');

        cards.forEach(card => {
            const namaUjian = card.querySelector('.card-title').textContent.toLowerCase();
            const sekolah = card.getAttribute('data-sekolah');

            const textMatch = !searchText || namaUjian.includes(searchText);
            const sekolahMatch = !sekolahFilter || sekolah === sekolahFilter;

            card.style.display = (textMatch && sekolahMatch) ? '' : 'none';
        });
    }

    function resetFilter() {
        document.getElementById('searchUjian').value = '';
        document.getElementById('filterSekolah').value = '';
        filterCards();
    }
</script>

<style>
    .border-end {
        border-right: 1px solid #dee2e6 !important;
    }

    @media (max-width: 768px) {
        .border-end {
            border-right: none !important;
            border-bottom: 1px solid #dee2e6 !important;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .border-end:last-child {
            border-bottom: none !important;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    }
</style>

<?= $this->endSection() ?>