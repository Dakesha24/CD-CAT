<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian - <?= esc($hasil['nama_lengkap']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #34495e;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section h3 {
            background-color: #34495e;
            color: white;
            padding: 8px 15px;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-radius: 4px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #2c3e50;
        }
        
        .info-value {
            flex: 1;
            color: #333;
        }
        
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        
        .summary-item {
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 6px;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        /* **BARU: Style untuk kemampuan kognitif** */
        .cognitive-box {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .cognitive-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .cognitive-item {
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 6px;
        }
        
        .cognitive-value {
            font-size: 20px;
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }
        
        .cognitive-label {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        
        .detail-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        
        .detail-table td {
            padding: 8px 6px;
            border: 1px solid #bdc3c7;
            text-align: center;
        }
        
        .detail-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .correct-row {
            background-color: #d4edda !important;
        }
        
        .incorrect-row {
            background-color: #f8d7da !important;
        }
        
        .status-correct {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-incorrect {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .answer-badge {
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .graph-section {
            margin-top: 30px;
            page-break-before: always;
        }
        
        .graph-container {
            margin: 20px 0;
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            border-top: 2px solid #333;
            padding-top: 15px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN HASIL UJIAN ADAPTIVE TEST</h1>
        <h2><?= esc($hasil['nama_ujian']) ?></h2>
        <p><strong><?= esc($hasil['nama_jenis']) ?></strong></p>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB</p>
    </div>

    <!-- Informasi Siswa -->
    <div class="section">
        <h3>INFORMASI SISWA & UJIAN</h3>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Nama Lengkap:</span>
                    <span class="info-value"><?= esc($hasil['nama_lengkap']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nomor Peserta:</span>
                    <span class="info-value"><?= esc($hasil['nomor_peserta']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kelas:</span>
                    <span class="info-value"><?= esc($hasil['nama_kelas']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Sekolah:</span>
                    <span class="info-value"><?= esc($hasil['nama_sekolah']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Guru Pengawas:</span>
                    <span class="info-value"><?= esc($hasil['nama_guru']) ?></span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Waktu Mulai:</span>
                    <span class="info-value"><?= $hasil['waktu_mulai_format'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Waktu Selesai:</span>
                    <span class="info-value"><?= $hasil['waktu_selesai_format'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Durasi:</span>
                    <span class="info-value"><?= $hasil['durasi_total_format'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rata-rata per Soal:</span>
                    <span class="info-value"><?= $rataRataWaktuFormat ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kode Akses:</span>
                    <span class="info-value"><?= esc($hasil['kode_akses']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hasil Akhir -->
    <div class="summary-box">
        <h3 style="background: none; color: white; padding: 0; margin: 0 0 15px 0;">HASIL AKHIR</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-value"><?= number_format($finalScore, 1) ?></span>
                <span class="summary-label">SKOR AKHIR</span>
            </div>
            <div class="summary-item">
                <span class="summary-value"><?= min(100, max(0, round(($finalScore / 100) * 100))) ?></span>
                <span class="summary-label">NILAI (0-100)</span>
            </div>
            <div class="summary-item">
                <span class="summary-value"><?= round(($jawabanBenar / count($detailJawaban)) * 100, 1) ?>%</span>
                <span class="summary-label">PERSENTASE BENAR</span>
            </div>
        </div>
        <div style="margin-top: 15px; text-align: center; font-size: 14px;">
            <strong>Theta Akhir (θ): <?= number_format($lastTheta, 3) ?></strong> | 
            <strong>Total Soal: <?= count($detailJawaban) ?></strong> | 
            <strong>Jawaban Benar: <?= $jawabanBenar ?></strong>
        </div>
    </div>

    <!-- **BARU: Kemampuan Kognitif** -->
    <div class="cognitive-box">
        <h3 style="background: none; color: white; padding: 0; margin: 0 0 15px 0;">ANALISIS KEMAMPUAN KOGNITIF</h3>
        <div class="cognitive-grid">
            <div class="cognitive-item">
                <span class="cognitive-value"><?= $kemampuanKognitif['skor'] ?></span>
                <span class="cognitive-label">SKOR KOGNITIF</span>
            </div>
            <div class="cognitive-item">
                <span class="cognitive-value"><?= $klasifikasiKognitif['kategori'] ?></span>
                <span class="cognitive-label">KATEGORI</span>
            </div>
            <div class="cognitive-item">
                <span class="cognitive-value"><?= $kemampuanKognitif['total_benar'] ?>/<?= $kemampuanKognitif['total_salah'] ?></span>
                <span class="cognitive-label">BENAR/SALAH</span>
            </div>
            <div class="cognitive-item">
                <span class="cognitive-value"><?= $kemampuanKognitif['rata_rata_pilihan'] ?></span>
                <span class="cognitive-label">AVG PILIHAN</span>
            </div>
        </div>
        <div style="margin-top: 10px; text-align: center; font-size: 12px; opacity: 0.9;">
            <strong>Formula:</strong> Skor = (B - (S/(P-1))) / N × 100 
            <br><em>B=Benar, S=Salah, P=Rata-rata pilihan, N=Total soal</em>
        </div>
    </div>

    <!-- Detail Jawaban -->
    <div class="section page-break">
        <h3>DETAIL JAWABAN SOAL</h3>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 6%">ID</th>
                    <th style="width: 35%">Pertanyaan</th>
                    <th style="width: 8%">Tingkat Kesulitan</th>
                    <th style="width: 6%">Jawaban</th>
                    <th style="width: 6%">Benar</th>
                    <th style="width: 8%">Status</th>
                    <th style="width: 8%">Waktu</th>
                    <th style="width: 8%">Durasi</th>
                    <th style="width: 5%">θ</th>
                    <th style="width: 5%">SE</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detailJawaban as $i => $jawaban): ?>
                    <tr class="<?= $jawaban['is_correct'] ? 'correct-row' : 'incorrect-row' ?>">
                        <td><strong><?= $jawaban['nomor_soal'] ?></strong></td>
                        <td><?= $jawaban['soal_id'] ?></td>
                        <td style="text-align: left; padding: 5px;">
                            <?= strlen($jawaban['pertanyaan']) > 120 ? substr(esc($jawaban['pertanyaan']), 0, 120) . '...' : esc($jawaban['pertanyaan']) ?>
                            <?php if (!empty($jawaban['foto'])): ?>
                                <br><em style="color: #666; font-size: 9px;">[Soal dengan gambar]</em>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($jawaban['tingkat_kesulitan'], 2) ?></td>
                        <td><span class="answer-badge"><?= $jawaban['jawaban_siswa'] ?></span></td>
                        <td><span class="answer-badge"><?= $jawaban['jawaban_benar'] ?></span></td>
                        <td>
                            <span class="<?= $jawaban['is_correct'] ? 'status-correct' : 'status-incorrect' ?>">
                                <?= $jawaban['is_correct'] ? 'BENAR' : 'SALAH' ?>
                            </span>
                        </td>
                        <td style="font-size: 9px;"><?= $jawaban['waktu_menjawab_format'] ?></td>
                        <td style="font-size: 9px; font-weight: bold;"><?= $jawaban['durasi_pengerjaan_format'] ?></td>
                        <td><?= number_format($jawaban['theta_saat_ini'], 2) ?></td>
                        <td><?= number_format($jawaban['se_saat_ini'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Grafik -->
    <div class="graph-section">
        <h3>GRAFIK PERKEMBANGAN</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="graph-container">
                <h4>Perkembangan Theta (θ)</h4>
                <canvas id="thetaChart" width="300" height="200"></canvas>
            </div>
            <div class="graph-container">
                <h4>Perkembangan Standard Error</h4>
                <canvas id="seChart" width="300" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Sistem Computer Based Adaptive Test (CD-CAT)</strong></p>
        <p>Laporan ini dibuat secara otomatis • Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB</p>
        <p>© <?= date('Y') ?> - Semua hak cipta dilindungi</p>
    </div>

    <script>
        // Data untuk grafik
        const thetaData = <?= $thetaData ?>;
        const seData = <?= $seData ?>;
        const labels = <?= $labels ?>;

        // Grafik Theta
        new Chart(document.getElementById('thetaChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Theta (θ)',
                    data: thetaData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.1,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: '#e0e0e0' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { color: '#e0e0e0' },
                        ticks: { font: { size: 9 } }
                    }
                }
            }
        });

        // Grafik SE
        new Chart(document.getElementById('seChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Standard Error',
                    data: seData,
                    borderColor: '#764ba2',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    tension: 0.1,
                    fill: true,
                    pointBackgroundColor: '#764ba2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: '#e0e0e0' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { color: '#e0e0e0' },
                        ticks: { font: { size: 9 } }
                    }
                }
            }
        });
    </script>
</body>
</html>