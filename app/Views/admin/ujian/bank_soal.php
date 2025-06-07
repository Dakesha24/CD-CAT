<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Bank Soal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-database"></i> Bank Soal
                    </h4>
                    <a href="<?= base_url('admin/ujian') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Ujian
                    </a>
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
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchSoal" placeholder="Cari pertanyaan...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterUjian">
                                <option value="">Semua Ujian</option>
                                <?php 
                                $ujianUnique = [];
                                foreach ($soal as $s) {
                                    if (!in_array($s['nama_ujian'], $ujianUnique)) {
                                        $ujianUnique[] = $s['nama_ujian'];
                                    }
                                }
                                foreach ($ujianUnique as $ujian): 
                                    if ($ujian): ?>
                                        <option value="<?= esc($ujian) ?>"><?= esc($ujian) ?></option>
                                    <?php endif;
                                endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterJawaban">
                                <option value="">Semua Jawaban</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilter()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4><?= count($soal) ?></h4>
                                    <p class="mb-0">Total Soal</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4><?= count(array_unique(array_column($soal, 'nama_ujian'))) ?></h4>
                                    <p class="mb-0">Total Ujian</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4><?= count(array_unique(array_filter(array_column($soal, 'nama_guru')))) ?></h4>
                                    <p class="mb-0">Guru Pembuat</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4><?= count(array_unique(array_column($soal, 'nama_jenis'))) ?></h4>
                                    <p class="mb-0">Jenis Ujian</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableSoal">
                            <thead class="table-primary">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Pertanyaan</th>
                                    <th width="150">Ujian</th>
                                    <th width="120">Jenis Ujian</th>
                                    <th width="100">Jawaban</th>
                                    <th width="100">Kesulitan</th>
                                    <th width="150">Guru Pembuat</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($soal)): ?>
                                    <?php foreach ($soal as $index => $s): ?>
                                        <tr data-ujian="<?= esc($s['nama_ujian']) ?>" data-jawaban="<?= $s['jawaban_benar'] ?>">
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <div class="question-preview">
                                                    <?= strlen($s['pertanyaan']) > 120 ? substr(esc($s['pertanyaan']), 0, 120) . '...' : esc($s['pertanyaan']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?= esc($s['nama_ujian']) ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($s['nama_jenis']): ?>
                                                    <span class="badge bg-primary"><?= esc($s['nama_jenis']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success fs-6"><?= $s['jawaban_benar'] ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $kesulitan = (float)$s['tingkat_kesulitan'];
                                                $badgeClass = 'bg-secondary';
                                                if ($kesulitan >= 0.8) $badgeClass = 'bg-danger';
                                                elseif ($kesulitan >= 0.5) $badgeClass = 'bg-warning';
                                                elseif ($kesulitan >= 0.2) $badgeClass = 'bg-info';
                                                else $badgeClass = 'bg-success';
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= number_format($kesulitan, 2) ?></span>
                                            </td>
                                            <td>
                                                <?= esc($s['nama_guru']) ?: '<em class="text-muted">Tidak diketahui</em>' ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/bank-soal/detail/' . $s['soal_id']) ?>" 
                                                       class="btn btn-info btn-sm" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('admin/soal/hapus/' . $s['soal_id']) ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')"
                                                       title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Belum ada soal dalam bank soal.</p>
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

<style>
.question-preview {
    max-height: 3em;
    overflow: hidden;
    line-height: 1.5em;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<script>
// Filter dan Search functionality
document.getElementById('searchSoal').addEventListener('keyup', filterTable);
document.getElementById('filterUjian').addEventListener('change', filterTable);
document.getElementById('filterJawaban').addEventListener('change', filterTable);

function filterTable() {
    const searchText = document.getElementById('searchSoal').value.toLowerCase();
    const ujianFilter = document.getElementById('filterUjian').value;
    const jawabanFilter = document.getElementById('filterJawaban').value;
    const rows = document.querySelectorAll('#tableSoal tbody tr');

    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip "no data" row
        
        const pertanyaan = row.cells[1].textContent.toLowerCase();
        const ujian = row.getAttribute('data-ujian');
        const jawaban = row.getAttribute('data-jawaban');
        
        const textMatch = !searchText || pertanyaan.includes(searchText);
        const ujianMatch = !ujianFilter || ujian === ujianFilter;
        const jawabanMatch = !jawabanFilter || jawaban === jawabanFilter;
        
        row.style.display = (textMatch && ujianMatch && jawabanMatch) ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('searchSoal').value = '';
    document.getElementById('filterUjian').value = '';
    document.getElementById('filterJawaban').value = '';
    filterTable();
}
</script>

<?= $this->endSection() ?>