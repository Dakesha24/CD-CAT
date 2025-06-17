<?= $this->extend('templates/guru/guru_template') ?>

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
                    <a href="<?= base_url('guru/hasil-ujian') ?>" class="btn btn-secondary">
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
                                    <td><strong>Kode Ujian:</strong></td>
                                    <td><code><?= esc($ujian['kode_ujian']) ?></code></td>
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
                                    <td><strong>Jadwal Ujian:</strong></td>
                                    <td><?= $ujian['tanggal_mulai_format'] ?> - <?= $ujian['tanggal_selesai_format'] ?></td>
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
                                    $skorSelesai = array_filter($hasilSiswa, fn($s) => $s['status'] === 'selesai' && $s['skor'] !== null);
                                    $rataRata = count($skorSelesai) > 0 ? array_sum(array_column($skorSelesai, 'skor')) / count($skorSelesai) : 0;
                                    ?>
                                    <h4><?= round($rataRata, 1) ?></h4>
                                    <p class="mb-0">Rata-rata Skor</p>
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
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Waktu Mulai</th>
                                    <th>Waktu Selesai</th>
                                    <th>Durasi Total</th>
                                    <th>Rata-rata/Soal</th>
                                    <th>Skor</th>
                                    <th>Benar/Total</th>
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
                                            <?php if ($siswa['waktu_mulai']): ?>
                                                <small class="text-muted"><?= $siswa['waktu_mulai_format'] ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['waktu_selesai']): ?>
                                                <small class="text-muted"><?= $siswa['waktu_selesai_format'] ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai' && $siswa['durasi_format']): ?>
                                                <span class="fw-bold"><?= $siswa['durasi_format'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai' && $siswa['rata_rata_per_soal']): ?>
                                                <small class="text-info"><?= $siswa['rata_rata_per_soal'] ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($siswa['status'] === 'selesai' && $siswa['skor'] !== null): ?>
                                                <strong class="fs-6 text-success"><?= number_format($siswa['skor'], 1) ?></strong>
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
                                            <div class="d-grid gap-1">
                                                <?php if ($siswa['status'] === 'selesai'): ?>
                                                    <!-- Tombol untuk siswa yang sudah selesai -->
                                                    <a href="<?= base_url('guru/hasil-ujian/detail/' . $siswa['peserta_ujian_id']) ?>"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                    <a href="<?= base_url('guru/hasil-ujian/download-excel-html/' . $siswa['peserta_ujian_id']) ?>"
                                                        class="btn btn-success btn-sm">
                                                        <i class="fas fa-file-excel me-1"></i>Excel
                                                    </a>
                                                    <a href="<?= base_url('guru/hasil-ujian/download-pdf-html/' . $siswa['peserta_ujian_id']) ?>"
                                                        class="btn btn-danger btn-sm" target="_blank">
                                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="confirmDelete(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'selesai')"
                                                        title="Hapus Hasil Ujian">
                                                        <i class="fas fa-trash me-1"></i>Hapus
                                                    </button>

                                                <?php elseif ($siswa['status'] === 'sedang_mengerjakan'): ?>
                                                    <!-- Tombol untuk siswa yang sedang mengerjakan -->
                                                    <span class="badge bg-primary mb-1">
                                                        <i class="fas fa-clock me-1"></i>Sedang Mengerjakan
                                                    </span>
                                                    <button type="button"
                                                        class="btn btn-warning btn-sm"
                                                        onclick="confirmReset(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'sedang_mengerjakan')"
                                                        title="Reset ke Belum Mulai">
                                                        <i class="fas fa-redo me-1"></i>Reset
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="confirmDelete(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'sedang_mengerjakan')"
                                                        title="Hapus Peserta Ujian">
                                                        <i class="fas fa-trash me-1"></i>Hapus
                                                    </button>

                                                <?php else: // belum_mulai 
                                                ?>
                                                    <!-- Tombol untuk siswa yang belum mulai -->
                                                    <span class="badge bg-warning text-dark mb-1">
                                                        <i class="fas fa-hourglass-half me-1"></i>Belum Mulai
                                                    </span>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="confirmDelete(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'belum_mulai')"
                                                        title="Hapus Peserta Ujian">
                                                        <i class="fas fa-trash me-1"></i>Hapus
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
        let csvContent = "No,Nomor Peserta,Nama Siswa,Status,Waktu Mulai,Waktu Selesai,Durasi Total,Rata-rata per Soal,Skor,Benar/Total\n";

        const rows = document.querySelectorAll('#tableHasil tbody tr');
        rows.forEach((row, index) => {
            if (row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                const rowData = [];
                // Skip kolom aksi (index 10)
                for (let i = 0; i < cells.length - 1; i++) {
                    rowData.push('"' + cells[i].textContent.trim().replace(/"/g, '""') + '"');
                }
                csvContent += rowData.join(',') + '\n';
            }
        });

        // Download CSV
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
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
            <p>Jadwal: <?= $ujian['tanggal_mulai_format'] ?> - <?= $ujian['tanggal_selesai_format'] ?></p>
        </div>
    `;

        const tableContent = document.getElementById('tableHasil').outerHTML;

        printWindow.document.write(`
        <html>
            <head>
                <title>Hasil Ujian</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
                    th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .badge { padding: 2px 4px; border-radius: 3px; font-size: 8px; }
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

    // Function untuk konfirmasi hapus hasil ujian
    function confirmDelete(pesertaUjianId, namaSiswa, status) {
        let title, warning, description;

        if (status === 'selesai') {
            title = 'Konfirmasi Hapus Hasil Ujian';
            warning = 'Semua data jawaban dan hasil ujian akan dihapus permanen';
            description = 'Apakah Anda yakin ingin menghapus hasil ujian untuk:';
        } else if (status === 'sedang_mengerjakan') {
            title = 'Konfirmasi Hapus Peserta yang Sedang Mengerjakan';
            warning = 'Peserta akan dihapus dari ujian dan progress yang sudah dikerjakan akan hilang';
            description = 'Apakah Anda yakin ingin menghapus peserta yang sedang mengerjakan:';
        } else {
            title = 'Konfirmasi Hapus Peserta Ujian';
            warning = 'Peserta akan dihapus dari daftar ujian';
            description = 'Apakah Anda yakin ingin menghapus peserta ujian:';
        }

        const modalHtml = `
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            ${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                        </div>
                        <p>${description}</p>
                        <div class="card bg-light">
                            <div class="card-body">
                                <strong>${namaSiswa}</strong><br>
                                <small class="text-muted">${warning}</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteHasil(${pesertaUjianId})">
                            <i class="fas fa-trash me-1"></i>Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Remove existing modal
        const existingModal = document.getElementById('deleteModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }




    // TAMBAHAN: Function untuk konfirmasi reset status ujian
    function confirmReset(pesertaUjianId, namaSiswa, status) {
        let description, warning;

        if (status === 'sedang_mengerjakan') {
            description = 'Apakah Anda yakin ingin reset ujian untuk siswa yang sedang mengerjakan:';
            warning = 'Progress ujian yang sudah dikerjakan akan hilang dan siswa dapat mengulang dari awal';
        } else {
            description = 'Apakah Anda yakin ingin reset status ujian untuk:';
            warning = 'Status akan dikembalikan ke "belum_mulai"';
        }

        const modalHtml = `
        <div class="modal fade" id="resetModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-redo text-warning me-2"></i>
                            Konfirmasi Reset Status Ujian
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Info:</strong> ${warning}
                        </div>
                        <p>${description}</p>
                        <div class="card bg-light">
                            <div class="card-body">
                                <strong>${namaSiswa}</strong><br>
                                <small class="text-muted">Siswa dapat mengulang ujian dari awal</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-warning" onclick="resetStatus(${pesertaUjianId})">
                            <i class="fas fa-redo me-1"></i>Ya, Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Remove existing modal
        const existingModal = document.getElementById('resetModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('resetModal'));
        modal.show();
    }

    // TAMBAHAN: Function untuk execute hapus
    function deleteHasil(pesertaUjianId) {
        // Show loading
        const deleteBtn = document.querySelector('#deleteModal .btn-danger');
        const originalText = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
        deleteBtn.disabled = true;

        // Redirect ke controller
        window.location.href = `<?= base_url('guru/hasil-ujian/hapus/') ?>${pesertaUjianId}`;
    }

    // TAMBAHAN: Function untuk execute reset
    function resetStatus(pesertaUjianId) {
        // Show loading
        const resetBtn = document.querySelector('#resetModal .btn-warning');
        const originalText = resetBtn.innerHTML;
        resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Reset...';
        resetBtn.disabled = true;

        // Redirect ke controller
        window.location.href = `<?= base_url('guru/hasil-ujian/reset/') ?>${pesertaUjianId}`;
    }
</script>

<?= $this->endSection() ?>