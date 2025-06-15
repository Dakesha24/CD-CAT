<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Ujian - <?= esc($hasil['nama_ujian']) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Styles untuk tampilan normal */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .back-button {
            margin-bottom: 20px;
        }

        .report-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 18px;
            color: #6c757d;
        }

        .info-box {
            margin-bottom: 25px;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            margin-bottom: 10px;
        }

        .statistics {
            margin-bottom: 30px;
        }

        .correct {
            color: #28a745;
            font-weight: bold;
        }

        .incorrect {
            color: #dc3545;
            font-weight: bold;
        }

        .score {
            color: #007bff;
            font-weight: bold;
        }

        .pembahasan-container {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .pembahasan-item {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #dee2e6;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: block;
        }

        /* Styles untuk print */
        @media print {
            body {
                padding: 0;
                background-color: white;
            }

            .report-container {
                box-shadow: none;
                padding: 0;
            }

            .back-button,
            .print-button {
                display: none !important;
            }

            .header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }

            .title {
                font-size: 18px;
            }

            .subtitle {
                font-size: 16px;
            }

            table {
                font-size: 12px;
            }

            .page-break {
                page-break-after: always;
            }

            /* Menambahkan header dan footer pada setiap halaman cetak */
            @page {
                margin: 2cm;
            }
        }
    </style>
</head>

<body>
    <!-- Tombol kembali -->
    <div class="back-button">
        <a href="<?= base_url('siswa/hasil') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="report-container">
        <div class="header">
            <div class="title">LAPORAN HASIL UJIAN</div>
            <div class="subtitle"><?= esc($hasil['nama_ujian']) ?></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-label">Nama Siswa:</div>
                    <div class="info-value"><?= esc($siswa['nama_lengkap']) ?></div>

                    <div class="info-label">NIS:</div>
                    <div class="info-value"><?= esc($siswa['nomor_peserta']) ?></div>

                    <div class="info-label">Jenis Ujian:</div>
                    <div class="info-value"><?= esc($hasil['nama_jenis']) ?></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-label">Waktu Mulai:</div>
                    <div class="info-value"><?= $hasil['waktu_mulai_format'] ?></div>

                    <div class="info-label">Waktu Selesai:</div>
                    <div class="info-value"><?= $hasil['waktu_selesai_format'] ?></div>

                    <div class="info-label">Total Waktu Pengerjaan:</div>
                    <div class="info-value"><?= $hasil['durasi_total_format'] ?></div>

                    <div class="info-label">Rata-rata per Soal:</div>
                    <div class="info-value"><?= $rataRataWaktuFormat ?></div>
                </div>
            </div>
        </div>

        <div class="statistics">
            <h4 class="mb-3">Statistik Hasil</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Total Soal</th>
                            <th>Jawaban Benar</th>
                            <th>Jawaban Salah</th>
                            <th>Skor Ujian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $totalSoal ?></td>
                            <td class="correct"><?= $jawabanBenar ?></td>
                            <td class="incorrect"><?= $totalSoal - $jawabanBenar ?></td>
                            <td class="score"><?= $skor ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="detail-jawaban">
            <h4 class="mb-3">Detail Jawaban</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Pertanyaan</th>
                            <th width="10%">Jawaban Anda</th>
                            <th width="10%">Jawaban Benar</th>
                            <th width="10%">Status</th>
                            <th width="15%">Waktu Jawab</th>
                            <th width="15%">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detailJawaban as $i => $jawaban): ?>
                            <tr>
                                <td><?= $jawaban['nomor_soal'] ?></td>
                                <td><?= esc($jawaban['pertanyaan']) ?></td>
                                <td><?= $jawaban['jawaban_siswa'] ?></td>
                                <td><?= $jawaban['jawaban_benar'] ?></td>
                                <td class="<?= $jawaban['is_correct'] ? 'correct' : 'incorrect' ?>">
                                    <?= $jawaban['is_correct'] ? 'Benar' : 'Salah' ?>
                                </td>
                                <td><small><?= $jawaban['waktu_menjawab_format'] ?></small></td>
                                <td><strong><?= $jawaban['durasi_pengerjaan_format'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="page-break"></div>

        <div class="pembahasan-container">
            <h4 class="mb-4">Pembahasan Soal</h4>

            <?php foreach ($detailJawaban as $i => $jawaban): ?>
                <?php if (isset($jawaban['pembahasan']) && !empty($jawaban['pembahasan'])): ?>
                    <div class="pembahasan-item">
                        <div class="fw-bold mb-2">Soal #<?= $jawaban['nomor_soal'] ?>:</div>
                        <div class="mb-3"><?= esc($jawaban['pertanyaan']) ?></div>
                        <div class="fw-bold mb-2">Pembahasan:</div>
                        <div><?= $jawaban['pembahasan'] ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="text-end mt-4 text-muted small">
            Dicetak pada: <?= date('d M Y H:i:s') ?>
        </div>
    </div>

    <!-- Tombol Print -->
    <button class="btn btn-primary btn-lg print-button" onclick="window.print()">
        <i class="bi bi-printer"></i> Cetak Laporan
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script untuk otomatis print -->
    <script>
        // Jika parameter autoprint ada di URL, langsung print
        if (new URLSearchParams(window.location.search).get('autoprint') === 'true') {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 1000); // Delay 1 detik agar halaman selesai di-render
            };
        }
    </script>
</body>

</html>