<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Detail Soal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-question-circle"></i> Detail Soal #<?= $soal['soal_id'] ?>
                    </h4>
                    <div>
                        <a href="<?= base_url('admin/ujian/detail/' . $soal['ujian_id']) ?>" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Ujian
                        </a>
                        <a href="<?= base_url('admin/soal/hapus/' . $soal['soal_id']) ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                            <i class="fas fa-trash me-1"></i>Hapus Soal
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Info Ujian -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-file-alt me-1"></i>Ujian:</strong> 
                                <?= esc($soal['nama_ujian']) ?><br>
                                <strong><i class="fas fa-tag me-1"></i>Jenis:</strong> 
                                <?= esc($soal['nama_jenis']) ?: '-' ?>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-chart-bar me-1"></i>Tingkat Kesulitan:</strong> 
                                <?php 
                                $kesulitan = (float)$soal['tingkat_kesulitan'];
                                $badgeClass = 'bg-secondary';
                                $levelText = 'Tidak diketahui';
                                
                                if ($kesulitan >= 0.8) {
                                    $badgeClass = 'bg-danger';
                                    $levelText = 'Sangat Sulit';
                                } elseif ($kesulitan >= 0.5) {
                                    $badgeClass = 'bg-warning text-dark';
                                    $levelText = 'Sulit';
                                } elseif ($kesulitan >= 0.2) {
                                    $badgeClass = 'bg-info';
                                    $levelText = 'Sedang';
                                } else {
                                    $badgeClass = 'bg-success';
                                    $levelText = 'Mudah';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= number_format($kesulitan, 4) ?> (<?= $levelText ?>)</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <!-- Pertanyaan -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="question-text mb-3">
                                        <?= nl2br(esc($soal['pertanyaan'])) ?>
                                    </div>
                                    
                                    <?php if (!empty($soal['foto'])): ?>
                                        <div class="text-center">
                                            <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" 
                                                 class="img-fluid rounded border shadow" 
                                                 alt="Gambar Soal"
                                                 style="max-width: 100%; max-height: 400px;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal"
                                                 style="cursor: pointer;">
                                            <p class="text-muted mt-2 small">
                                                <i class="fas fa-search-plus"></i> Klik gambar untuk memperbesar
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Pilihan Jawaban -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Pilihan Jawaban</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Pilihan A -->
                                            <div class="option-item mb-3 p-3 rounded <?= $soal['jawaban_benar'] === 'A' ? 'bg-success text-white border border-success' : 'bg-light border' ?>">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge <?= $soal['jawaban_benar'] === 'A' ? 'bg-white text-success' : 'bg-primary' ?> me-3 fs-6">A</span>
                                                    <div class="flex-grow-1">
                                                        <?= nl2br(esc($soal['pilihan_a'])) ?>
                                                        <?php if ($soal['jawaban_benar'] === 'A'): ?>
                                                            <i class="fas fa-check-circle ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pilihan B -->
                                            <div class="option-item mb-3 p-3 rounded <?= $soal['jawaban_benar'] === 'B' ? 'bg-success text-white border border-success' : 'bg-light border' ?>">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge <?= $soal['jawaban_benar'] === 'B' ? 'bg-white text-success' : 'bg-primary' ?> me-3 fs-6">B</span>
                                                    <div class="flex-grow-1">
                                                        <?= nl2br(esc($soal['pilihan_b'])) ?>
                                                        <?php if ($soal['jawaban_benar'] === 'B'): ?>
                                                            <i class="fas fa-check-circle ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Pilihan C -->
                                            <div class="option-item mb-3 p-3 rounded <?= $soal['jawaban_benar'] === 'C' ? 'bg-success text-white border border-success' : 'bg-light border' ?>">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge <?= $soal['jawaban_benar'] === 'C' ? 'bg-white text-success' : 'bg-primary' ?> me-3 fs-6">C</span>
                                                    <div class="flex-grow-1">
                                                        <?= nl2br(esc($soal['pilihan_c'])) ?>
                                                        <?php if ($soal['jawaban_benar'] === 'C'): ?>
                                                            <i class="fas fa-check-circle ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pilihan D -->
                                            <div class="option-item mb-3 p-3 rounded <?= $soal['jawaban_benar'] === 'D' ? 'bg-success text-white border border-success' : 'bg-light border' ?>">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge <?= $soal['jawaban_benar'] === 'D' ? 'bg-white text-success' : 'bg-primary' ?> me-3 fs-6">D</span>
                                                    <div class="flex-grow-1">
                                                        <?= nl2br(esc($soal['pilihan_d'])) ?>
                                                        <?php if ($soal['jawaban_benar'] === 'D'): ?>
                                                            <i class="fas fa-check-circle ms-2"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jawaban Benar Highlight -->
                                    <div class="alert alert-success mt-4">
                                        <h6><i class="fas fa-check-circle me-2"></i>Jawaban Benar: <strong><?= $soal['jawaban_benar'] ?></strong></h6>
                                    </div>
                                </div>
                            </div>

                            <!-- Pembahasan -->
                            <?php if (!empty($soal['pembahasan'])): ?>
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Pembahasan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="pembahasan-content">
                                            <?= nl2br(esc($soal['pembahasan'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="card">
                                    <div class="card-body text-center text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h6>Belum Ada Pembahasan</h6>
                                        <p class="mb-0">Guru belum menambahkan pembahasan untuk soal ini.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk memperbesar gambar -->
<?php if (!empty($soal['foto'])): ?>
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gambar Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" 
                     class="img-fluid" 
                     alt="Gambar Soal">
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.question-text {
    font-size: 1.1em;
    line-height: 1.6;
    color: #333;
}

.option-item {
    transition: all 0.2s;
    border: 1px solid #dee2e6;
}

.option-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.pembahasan-content {
    font-size: 1.05em;
    line-height: 1.7;
    color: #444;
}

.img-fluid {
    cursor: pointer;
    transition: transform 0.2s;
}

.img-fluid:hover {
    transform: scale(1.02);
}
</style>

<?= $this->endSection() ?>