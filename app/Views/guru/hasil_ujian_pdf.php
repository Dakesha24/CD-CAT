<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian - <?= $hasil['nama_lengkap'] ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1,
        h2,
        h3 {
            color: #2c3e50;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        h1 {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            font-size: 20px;
        }

        h2 {
            font-size: 16px;
            background-color: #f5f5f5;
            padding: 5px 10px;
            border-left: 4px solid #3498db;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-header .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-header .report-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.info {
            margin-bottom: 20px;
        }

        table.info td {
            padding: 5px 10px;
        }

        table.info td:first-child {
            width: 150px;
            font-weight: bold;
        }

        table.detail {
            border: 1px solid #ccc;
        }

        table.detail th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }

        table.detail td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .text-success {
            color: #27ae60;
        }

        .text-danger {
            color: #e74c3c;
        }

        .text-center {
            text-align: center;
        }

        .highlight {
            font-weight: bold;
            font-size: 18px;
            color: #2980b9;
        }

        .row {
            display: flex;
            margin-bottom: 15px;
        }

        .col {
            flex: 1;
            padding: 0 10px;
        }

        .chart-container {
            width: 100%;
            height: 300px;
            margin: 20px 0;
        }

        .chart-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .chart-col {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
            box-sizing: border-box;
        }

        /* BARU: Styles untuk kemampuan kognitif */
        .kognitif-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .kognitif-sangat-tinggi {
            color: #28a745 !important;
            font-weight: bold;
        }

        .kognitif-tinggi {
            color: #17a2b8 !important;
            font-weight: bold;
        }

        .kognitif-sedang {
            color: #ffc107 !important;
            font-weight: bold;
        }

        .kognitif-rendah {
            color: #fd7e14 !important;
            font-weight: bold;
        }

        .kognitif-sangat-rendah {
            color: #dc3545 !important;
            font-weight: bold;
        }

        .recommendation-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        @media print {
            body {
                padding: 0;
                background-color: white;
            }

            .container {
                width: 100%;
                max-width: none;
                padding: 0;
            }

            h1 {
                font-size: 18px;
            }

            h2 {
                font-size: 14px;
            }

            h3 {
                font-size: 13px;
            }

            .page-break {
                page-break-before: always;
            }

            .chart-col {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="report-header">
            <div class="report-title">HASIL UJIAN ADAPTIF SISWA</div>
            <div class="report-subtitle"><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></div>
            <div class="report-subtitle">Kode Ujian: <?= esc($hasil['kode_ujian']) ?></div>
        </div>

        <div class="row">
            <div class="col">
                <h2>Informasi Ujian</h2>
                <table class="info">
                    <tr>
                        <td>Nama Ujian</td>
                        <td>: <?= esc($hasil['nama_ujian']) ?></td>
                    </tr>
                    <tr>
                        <td>Kode Ujian</td>
                        <td>: <?= esc($hasil['kode_ujian']) ?></td>
                    </tr>
                    <tr>
                        <td>Mata Pelajaran</td>
                        <td>: <?= esc($hasil['nama_jenis']) ?></td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: <?= esc($hasil['nama_kelas']) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col">
                <h2>Informasi Siswa</h2>
                <table class="info">
                    <tr>
                        <td>Nama Siswa</td>
                        <td>: <?= esc($hasil['nama_lengkap']) ?></td>
                    </tr>
                    <tr>
                        <td>NIS</td>
                        <td>: <?= esc($hasil['nomor_peserta']) ?></td>
                    </tr>
                    <tr>
                        <td>Waktu Mulai</td>
                        <td>: <?= $hasil['waktu_mulai_format'] ?></td>
                    </tr>
                    <tr>
                        <td>Waktu Selesai</td>
                        <td>: <?= $hasil['waktu_selesai_format'] ?></td>
                    </tr>
                    <tr>
                        <td>Total Durasi</td>
                        <td>: <?= $hasil['durasi_total_format'] ?></td>
                    </tr>
                    <tr>
                        <td>Rata-rata/Soal</td>
                        <td>: <?= $rataRataWaktuFormat ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <h2>Hasil Akhir</h2>
        <?php
        // Ambil theta terakhir (dari jawaban terakhir)
        $lastTheta = end($detailJawaban)['theta_saat_ini'];
        // Hitung nilai akhir: 50 + 16.6 * theta
        $finalScore = 50 + (16.6 * $lastTheta);
        // Nilai dalam skala 0-100
        $finalGrade = min(100, max(0, round(($finalScore / 100) * 100)));
        ?>
        <div class="row">
            <div class="col">
                <table class="info">
                    <tr>
                        <td>Total Soal</td>
                        <td>: <b><?= count($detailJawaban) ?></b> soal</td>
                    </tr>
                    <tr>
                        <td>Jawaban Benar</td>
                        <td>: <b><?= $jawabanBenar ?></b> soal</td>
                    </tr>
                    <tr>
                        <td>Theta Akhir (θ)</td>
                        <td>: <b><?= number_format($lastTheta, 3) ?></b></td>
                    </tr>
                    <tr>
                        <td>Standard Error Akhir</td>
                        <td>: <b><?= number_format(end($detailJawaban)['se_saat_ini'], 3) ?></b></td>
                    </tr>


                </table>
            </div>
            <div class="col">
                <table class="info">


                    <tr>
                        <td>Skor</td>
                        <td>: <span class="highlight"><?= number_format($finalScore, 1) ?></span></td>
                    </tr>
                    <tr>
                        <td>Nilai (Skala 0-100)</td>
                        <td>: <span class="highlight"><?= $finalGrade ?></span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- BARU: Analisis Kemampuan Kognitif -->
        <div class="kognitif-box">
            <h2>Analisis Kemampuan Kognitif</h2>
            <div class="row">
                <div class="col">
                    <div class="info-label">Skor Kemampuan Kognitif:</div>
                    <div class="info-value">
                        <span class="
                            <?php
                            if ($kemampuanKognitif['skor'] > 80) echo 'kognitif-sangat-tinggi';
                            elseif ($kemampuanKognitif['skor'] > 60) echo 'kognitif-tinggi';
                            elseif ($kemampuanKognitif['skor'] > 40) echo 'kognitif-sedang';
                            elseif ($kemampuanKognitif['skor'] > 20) echo 'kognitif-rendah';
                            else echo 'kognitif-sangat-rendah';
                            ?>
                        ">
                            <?= $kemampuanKognitif['skor'] ?>% - <?= $klasifikasiKognitif['kategori'] ?>
                        </span>
                    </div>

                    <div class="info-label mt-3">Interpretasi:</div>
                    <div class="info-value">
                        <?php if ($kemampuanKognitif['skor'] > 80): ?>
                            Kemampuan kognitif sangat tinggi. Siswa menunjukkan pemahaman yang excellent terhadap materi.
                        <?php elseif ($kemampuanKognitif['skor'] > 60): ?>
                            Kemampuan kognitif tinggi. Siswa memiliki pemahaman yang baik terhadap materi.
                        <?php elseif ($kemampuanKognitif['skor'] > 40): ?>
                            Kemampuan kognitif rata-rata. Masih ada ruang untuk peningkatan pemahaman.
                        <?php elseif ($kemampuanKognitif['skor'] > 20): ?>
                            Kemampuan kognitif rendah. Disarankan untuk review ulang materi pembelajaran.
                        <?php else: ?>
                            Kemampuan kognitif sangat rendah. Sangat disarankan untuk mempelajari kembali materi secara menyeluruh.
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col">
                    <div class="info-label">Detail Perhitungan:</div>
                    <small>
                        • Jawaban Benar: <?= $kemampuanKognitif['total_benar'] ?><br>
                        • Jawaban Salah: <?= $kemampuanKognitif['total_salah'] ?><br>
                        • Rata-rata Pilihan: <?= $kemampuanKognitif['rata_rata_pilihan'] ?><br>
                        • Total Soal: <?= count($detailJawaban) ?><br>
                        <br>
                        <strong>Rumus:</strong><br>
                        <code>Skor = (B - (S/(P-1))) / N × 100</code>
                    </small>
                </div>
            </div>
        </div>

        <!-- BARU: Rekomendasi Pembelajaran untuk Guru -->
        <div class="recommendation-box">
            <h3><i class="bi bi-lightbulb"></i> Rekomendasi Pembelajaran</h3>
            <?php if ($kemampuanKognitif['skor'] > 80): ?>
                <p><strong>Strategi Pengajaran:</strong> Siswa menunjukkan pemahaman yang sangat baik.
                    Berikan tantangan lebih lanjut dengan soal-soal aplikatif dan analisis tingkat tinggi.
                    Fokuskan pada pengembangan kemampuan berpikir kritis dan problem solving.</p>
            <?php elseif ($kemampuanKognitif['skor'] > 60): ?>
                <p><strong>Strategi Pengajaran:</strong> Kemampuan siswa sudah baik.
                    Fokuskan pada pendalaman materi dan latihan soal dengan variasi yang lebih kompleks.
                    Berikan kesempatan untuk mengeksplorasi aplikasi konsep dalam konteks yang berbeda.</p>
            <?php elseif ($kemampuanKognitif['skor'] > 40): ?>
                <p><strong>Strategi Pengajaran:</strong> Perlu perbaikan dalam pemahaman konsep dasar.
                    Berikan penjelasan ulang materi dengan pendekatan yang berbeda, gunakan lebih banyak contoh konkret,
                    dan sediakan latihan tambahan dengan tingkat kesulitan bertahap.</p>
            <?php else: ?>
                <p><strong>Strategi Pengajaran:</strong> Kemampuan memerlukan perhatian khusus.
                    Lakukan remedial pembelajaran dengan pendekatan individual, evaluasi ulang metode pengajaran,
                    dan pertimbangkan penggunaan media pembelajaran yang lebih interaktif dan mudah dipahami.</p>
            <?php endif; ?>
        </div>

        <h2>Grafik Perkembangan</h2>
        <div class="chart-row">
            <div class="chart-col">
                <h3>Grafik Theta (θ)</h3>
                <div class="chart-container">
                    <canvas id="thetaChart"></canvas>
                </div>
                <div id="thetaChartImage"></div>
            </div>
            <div class="chart-col">
                <h3>Grafik Standard Error (SE)</h3>
                <div class="chart-container">
                    <canvas id="seChart"></canvas>
                </div>
                <div id="seChartImage"></div>
            </div>
        </div>

        <h2>Detail Jawaban</h2>
        <table class="detail">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Soal</th>
                    <th>ID Soal</th>
                    <th>Pertanyaan</th>
                    <th>Tingkat Kesulitan</th>
                    <th>Jawaban</th>
                    <th>Status</th>
                    <th>Waktu Jawab</th>
                    <th>Durasi</th>
                    <th>Pi</th>
                    <th>Qi</th>
                    <th>Ii</th>
                    <th>SE</th>
                    <th>ΔSE</th>
                    <th>θ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detailJawaban as $i => $jawaban): ?>
                    <tr>
                        <td class="text-center"><?= $jawaban['nomor_soal'] ?></td>
                        <td class="text-center"><?= esc($jawaban['kode_soal']) ?></td>
                        <td class="text-center"><?= $jawaban['soal_id'] ?></td>
                        <td><?= $jawaban['pertanyaan'] ?></td>
                        <td class="text-center"><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
                        <td class="text-center"><?= $jawaban['jawaban_siswa'] ?></td>
                        <td class="text-center <?= $jawaban['is_correct'] ? 'text-success' : 'text-danger' ?>">
                            <?= $jawaban['is_correct'] ? 'Benar' : 'Salah' ?>
                        </td>
                        <td class="text-center"><?= $jawaban['waktu_menjawab_format'] ?></td>
                        <td class="text-center"><?= $jawaban['durasi_pengerjaan_format'] ?></td>
                        <td class="text-center"><?= isset($jawaban['pi_saat_ini']) ? number_format($jawaban['pi_saat_ini'], 3) : '-' ?></td>
                        <td class="text-center"><?= isset($jawaban['qi_saat_ini']) ? number_format($jawaban['qi_saat_ini'], 3) : '-' ?></td>
                        <td class="text-center"><?= isset($jawaban['ii_saat_ini']) ? number_format($jawaban['ii_saat_ini'], 3) : '-' ?></td>
                        <td class="text-center"><?= number_format($jawaban['se_saat_ini'], 3) ?></td>
                        <td class="text-center"><?= number_format(abs($jawaban['delta_se_saat_ini']), 3) ?></td>
                        <td class="text-center"><?= number_format($jawaban['theta_saat_ini'], 3) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="page-break"></div>

        <!-- BARU: Klasifikasi Kemampuan Kognitif -->
        <h2>Klasifikasi Kemampuan Kognitif</h2>
        <table class="detail">
            <thead>
                <tr>
                    <th>Rentang Skor</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Rekomendasi Pengajaran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>80% - 100%</td>
                    <td class="kognitif-sangat-tinggi">Sangat Tinggi</td>
                    <td>Pemahaman excellent, mampu analisis dan aplikasi sangat baik</td>
                    <td>Berikan tantangan tingkat tinggi dan problem solving kompleks</td>
                </tr>
                <tr>
                    <td>60% - 80%</td>
                    <td class="kognitif-tinggi">Tinggi</td>
                    <td>Pemahaman baik, mampu analisis dan aplikasi dengan baik</td>
                    <td>Pendalaman materi dan variasi soal kompleks</td>
                </tr>
                <tr>
                    <td>40% - 60%</td>
                    <td class="kognitif-sedang">Rata-rata (Sedang)</td>
                    <td>Pemahaman cukup, perlu peningkatan analisis dan aplikasi</td>
                    <td>Penjelasan ulang dengan pendekatan berbeda dan latihan tambahan</td>
                </tr>
                <tr>
                    <td>20% - 40%</td>
                    <td class="kognitif-rendah">Rendah</td>
                    <td>Pemahaman terbatas, perlu review konsep dasar</td>
                    <td>Remedial pembelajaran dengan media interaktif</td>
                </tr>
                <tr>
                    <td>0% - 20%</td>
                    <td class="kognitif-sangat-rendah">Sangat Rendah</td>
                    <td>Pemahaman sangat terbatas, perlu pembelajaran ulang menyeluruh</td>
                    <td>Pendekatan individual dan evaluasi metode pengajaran</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 30px; margin-bottom: 50px;">
            <p>
                <?= date('d F Y') ?><br>
                Guru Pengampu<br><br><br><br>
                .................................
            </p>
        </div>
    </div>

    <script>
        // Data untuk grafik
        const labels = <?= json_encode(array_map(function ($item) {
                            return 'Soal ' . $item['nomor_soal'];
                        }, $detailJawaban)) ?>;

        const thetaData = <?= json_encode(array_map(function ($item) {
                                return $item['theta_saat_ini'];
                            }, $detailJawaban)) ?>;

        const seData = <?= json_encode(array_map(function ($item) {
                            return $item['se_saat_ini'];
                        }, $detailJawaban)) ?>;

        // Fungsi untuk membuat grafik Theta
        function createThetaChart() {
            const ctx = document.getElementById('thetaChart').getContext('2d');
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Theta (θ)',
                        data: thetaData,
                        borderColor: '#4e73df',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Perkembangan Estimasi Kemampuan (θ)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Nilai Theta'
                            }
                        }
                    }
                }
            });
        }

        // Fungsi untuk membuat grafik SE
        function createSEChart() {
            const ctx = document.getElementById('seChart').getContext('2d');
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Standard Error',
                        data: seData,
                        borderColor: '#1cc88a',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Perkembangan Standard Error'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Nilai SE'
                            }
                        }
                    }
                }
            });
        }

        // Fungsi untuk mengkonversi chart ke gambar
        async function chartToImage(chart, containerId) {
            await new Promise(resolve => setTimeout(resolve, 100));

            const canvas = chart.canvas;

            html2canvas(canvas).then(canvas => {
                const container = document.getElementById(containerId);

                const img = document.createElement('img');
                img.src = canvas.toDataURL('image/png');
                img.style.width = '100%';
                img.style.maxWidth = '100%';
                container.appendChild(img);

                chart.canvas.parentNode.style.display = 'none';
            });
        }

        // Jalankan saat window load
        window.onload = async function() {
            const thetaChart = createThetaChart();
            const seChart = createSEChart();

            await chartToImage(thetaChart, 'thetaChartImage');
            await chartToImage(seChart, 'seChartImage');

            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
</body>

</html>