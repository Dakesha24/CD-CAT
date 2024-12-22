<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-5 pt-4">
    <!-- Header Result -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1"><?= $peserta_ujian['nama_ujian'] ?></h4>
                    <p class="text-muted mb-0">
                        Selesai pada: <?= date('d M Y, H:i', strtotime($peserta_ujian['waktu_selesai'])) ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <h2 class="mb-0 <?= $peserta_ujian['nilai_akhir'] >= 70 ? 'text-success' : 'text-danger' ?>">
                        Nilai: <?= number_format($peserta_ujian['nilai_akhir'], 1) ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Jawaban -->
    <div class="row">
        <div class="col-12">
            <?php foreach ($jawaban_siswa as $index => $jawaban): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Soal <?= $index + 1 ?></h5>
                        <?php if ($jawaban['is_correct']): ?>
                            <span class="badge bg-success px-3 py-2">Benar</span>
                        <?php else: ?>
                            <span class="badge bg-danger px-3 py-2">Salah</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pertanyaan -->
                    <p class="lead mb-4"><?= $jawaban['pertanyaan'] ?></p>

                    <!-- Pilihan Jawaban -->
                    <div class="options">
                        <?php
                        $options = [
                            'A' => $jawaban['pilihan_a'],
                            'B' => $jawaban['pilihan_b'],
                            'C' => $jawaban['pilihan_c'],
                            'D' => $jawaban['pilihan_d']
                        ];
                        
                        foreach ($options as $key => $value):
                            $isJawabanSiswa = ($jawaban['jawaban_siswa'] === $key);
                            $isJawabanBenar = ($jawaban['jawaban_benar'] === $key);
                        ?>
                        <div class="card mb-2 option-review <?= $isJawabanBenar ? 'border-success' : ($isJawabanSiswa && !$isJawabanBenar ? 'border-danger' : '') ?>">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <span class="me-3"><?= $key ?>.</span>
                                    <span><?= $value ?></span>
                                    <?php if ($isJawabanBenar): ?>
                                        <i class="fas fa-check text-success ms-auto"></i>
                                    <?php elseif ($isJawabanSiswa): ?>
                                        <i class="fas fa-times text-danger ms-auto"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!$jawaban['is_correct']): ?>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Jawaban yang benar adalah: <?= $jawaban['jawaban_benar'] ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Tombol Kembali -->
            <div class="text-center mb-4">
                <a href="<?= base_url('siswa/hasil') ?>" class="btn btn-primary px-4">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Hasil
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.option-review {
    transition: all 0.2s ease;
}

.option-review.border-success {
    border-width: 2px !important;
    background-color: #f8fff8;
}

.option-review.border-danger {
    border-width: 2px !important;
    background-color: #fff8f8;
}
</style>
<?= $this->endSection() ?>