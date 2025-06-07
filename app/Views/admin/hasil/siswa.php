<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Hasil Ujian Siswa<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Info Ujian -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Hasil Ujian: <?= esc($ujian['nama_ujian']) ?>
                    </h4>
                    <a href="<?= base_url('admin/hasil-ujian') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
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

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Jenis Ujian:</strong></td>
                                    <td><?= esc($ujian['nama_jenis']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas:</strong></td>
                                    <td><?= esc($ujian['nama_kelas']) ?> - <?= esc($ujian['tahun_ajaran']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Sekolah:</strong></td>
                                    <td><?= esc($ujian['nama_sekolah']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Guru:</strong></td>
                                    <td><?= esc($ujian['nama_guru']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td><?= date('d F Y', strtotime($ujian['tanggal_mulai'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kode Akses:</strong></td>
                                    <td><code><?= esc($ujian['kode_akses']) ?></code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($ujian['deskripsi'])): ?>
                        <div class="mt-3">
                            <strong>Deskripsi:</strong>
                            <p class="text-muted mb-0"><?= nl2br(esc($ujian['deskripsi'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hasil Siswa -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Hasil Siswa (<?= count($hasilSiswa) ?>)
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="exportHasil()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="printHasil()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama/nomor peserta...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="selesai">Selesai</option>
                                <option value="sedang_mengerjakan">Sedang Mengerjakan</option>
                                <option value="belum_mulai">Belum Mulai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="resetFilter()">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4><?= count(array_filter($hasilSiswa, fn($s) => $s['status'] === 'selesai')) ?></h4>
                                    <p class="mb-0">Selesai</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4><?= count(array_filter($hasilSiswa, fn($s) => $s['status'] === 'sedang_mengerjakan')) ?></h4>
                                    <p class="mb-0">Mengerjakan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h4><?= count(array_filter($hasilSiswa, fn($s) => $s['status'] === 'belum_mulai')) ?></h4>
                                    <p class="mb-0">Belum Mulai</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <?php 
                                    $nilaiSelesai = array_filter($hasilSiswa, fn($s) => $s['status'] === 'selesai' && $s['nilai'] !== null);
                                    $rataRata = count($nilaiSelesai) > 0 ? array_sum(array_column($nilaiSelesai, 'nilai')) / count($nilaiSelesai) : 0;
                                    ?>
                                    <h4><?= round($rataRata, 1) ?></h4>
                                    <p class="mb-0">Rata-rata</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Hasil -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableHasil">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Peserta</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Nilai</th>
                                    <th>Skor</th>
                                    <th>Theta (θ)</th>
                                    <th>Benar/Total</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasilSiswa as $index => $siswa): ?>
                                    <tr data-status="<?= $siswa['status'] ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= esc($siswa['nomor_peserta']) ?></strong></td>
                                        <td><?= esc($siswa['nama_lengkap']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($siswa['status']) {
                                                case 'selesai':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Selesai';
                                                    break;
                                                case 'sedang_mengerjakan':
                                                    $statusClass = 'bg-primary';
                                                    $statusText = 'Mengerjakan';
                                                    break;
                                                case 'belum_mulai':
                                                    $statusClass = 'bg-warning text-dark';
                                                    $statusText = 'Belum Mulai';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai' && $siswa['nilai'] !== null): ?>
                                                <strong class="fs-6 text-success"><?= $siswa['nilai'] ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai' && $siswa['skor'] !== null): ?>
                                                <?= number_format($siswa['skor'], 1) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai' && $siswa['theta_akhir'] !== null): ?>
                                                <?= number_format($siswa['theta_akhir'], 3) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai'): ?>
                                                <span class="badge bg-info">
                                                    <?= $siswa['jawaban_benar'] ?>/<?= $siswa['total_soal'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['waktu_mulai'] && $siswa['waktu_selesai']): ?>
                                                <?php
                                                $durasi = strtotime($siswa['waktu_selesai']) - strtotime($siswa['waktu_mulai']);
                                                $jam = floor($durasi / 3600);
                                                $menit = floor(($durasi % 3600) / 60);
                                                ?>
                                                <small class="text-muted"><?= sprintf('%02d:%02d', $jam, $menit) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-grid gap-1">
                                                <?php if ($siswa['status'] === 'selesai'): ?>
                                                    <a href="<?= base_url('admin/hasil-ujian/detail/' . $siswa['peserta_ujian_id']) ?>" 
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                    <a href="<?= base_url('admin/hasil-ujian/hapus/' . $siswa['peserta_ujian_id']) ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus hasil ujian siswa ini?\n\nSiswa akan direset ke status belum mulai.')">
                                                        <i class="fas fa-trash me-1"></i>Hapus
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-hourglass-half me-1"></i>Belum Selesai
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('searchSiswa').addEventListener('keyup', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);

function filterTable() {
    const searchText = document.getElementById('searchSiswa').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#tableHasil tbody tr');

    rows.forEach(row => {
        const nama = row.cells[2].textContent.toLowerCase();
        const nomor = row.cells[1].textContent.toLowerCase();
        const status = row.getAttribute('data-status');
        
        const textMatch = !searchText || nama.includes(searchText) || nomor.includes(searchText);
        const statusMatch = !statusFilter || status === statusFilter;
        
        row.style.display = (textMatch && statusMatch) ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('searchSiswa').value = '';
    document.getElementById('filterStatus').value = '';
    filterTable();
}

function exportHasil() {
    const namaUjian = '<?= addslashes($ujian['nama_ujian']) ?>';
    const namaKelas = '<?= addslashes($ujian['nama_kelas']) ?>';
    
    // Buat CSV content
    let csvContent = "No,Nomor Peserta,Nama Siswa,Status,Nilai,Skor,Theta,Benar/Total,Durasi\n";
    
    const rows = document.querySelectorAll('#tableHasil tbody tr');
    rows.forEach((row, index) => {
        if (row.style.display !== 'none') {
            const cells = row.querySelectorAll('td');
            const rowData = [];
            // Skip kolom aksi (index 9)
            for (let i = 0; i < cells.length - 1; i++) {
                rowData.push('"' + cells[i].textContent.trim().replace(/"/g, '""') + '"');
            }
            csvContent += rowData.join(',') + '\n';
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `hasil_${namaUjian.replace(/[^a-zA-Z0-9]/g, '_')}_${namaKelas.replace(/[^a-zA-Z0-9]/g, '_')}.csv`;
    link.click();
}

function printHasil() {
    const printWindow = window.open('', '_blank');
    const ujianInfo = `
        <div style="text-align: center; margin-bottom: 20px;">
            <h2>Hasil Ujian</h2>
            <h3><?= esc($ujian['nama_ujian']) ?></h3>
            <p>Kelas: <?= esc($ujian['nama_kelas']) ?> - <?= esc($ujian['nama_sekolah']) ?></p>
            <p>Guru: <?= esc($ujian['nama_guru']) ?></p>
            <p>Tanggal: <?= date('d F Y', strtotime($ujian['tanggal_mulai'])) ?></p>
        </div>
    `;
    
    const tableContent = document.getElementById('tableHasil').outerHTML;
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Hasil Ujian</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .badge { padding: 2px 4px; border-radius: 3px; font-size: 10px; }
                    .bg-success { background-color: #d4edda; color: #155724; }
                    .bg-primary { background-color: #cce7ff; color: #004085; }
                    .bg-warning { background-color: #fff3cd; color: #856404; }
                    .bg-info { background-color: #d1ecf1; color: #0c5460; }
                    .d-grid { display: none; }
                </style>
            </head>
            <body>
                ${ujianInfo}
                ${tableContent}
                <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
                    Dicetak pada: ${new Date().toLocaleString('id-ID')}
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}
</script>

<?= $this->endSection() ?>