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
        h1, h2, h3 {
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
        @media print {
            body {
                font-size: 12px;
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
            <?php 
            // Generate kode soal dari tahun dan ID
            $tahunPembuatan = date('Y', strtotime($hasil['tanggal_mulai']));
            $kode_soal = $tahunPembuatan . str_pad($hasil['ujian_id'], 4, '0', STR_PAD_LEFT);
            ?>
            <div class="report-subtitle"><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></div>
            <div class="report-subtitle">Kode Soal: <?= $kode_soal ?></div>
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
                        <td>Jenis Ujian</td>
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
                        <td>Nomor Peserta</td>
                        <td>: <?= esc($hasil['nomor_peserta']) ?></td>
                    </tr>
                    <tr>
                        <td>Waktu Mulai</td>
                        <td>: <?= date('d/m/Y H:i', strtotime($hasil['waktu_mulai'])) ?></td>
                    </tr>
                    <tr>
                        <td>Waktu Selesai</td>
                        <td>: <?= date('d/m/Y H:i', strtotime($hasil['waktu_selesai'])) ?></td>
                    </tr>
                    <tr>
                        <td>Durasi</td>
                        <td>: <?= date('H:i:s', strtotime($hasil['waktu_selesai']) - strtotime($hasil['waktu_mulai'])) ?></td>
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
                        <td>Theta Akhir (θ)</td>
                        <td>: <b><?= number_format($lastTheta, 3) ?></b></td>
                    </tr>
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
            <div class="col">
                <table class="info">
                    <tr>
                        <td>Total Soal</td>
                        <td>: <b><?= count($detailJawaban) ?></b> soal</td>
                    </tr>
                    <tr>
                        <td>Jawaban Benar</td>
                        <td>: <b><?= array_reduce($detailJawaban, function ($carry, $item) {
                            return $carry + ($item['is_correct'] ? 1 : 0);
                          }, 0) ?></b> soal</td>
                    </tr>
                    <tr>
                        <td>Standard Error Akhir</td>
                        <td>: <b><?= number_format(end($detailJawaban)['se_saat_ini'], 3) ?></b></td>
                    </tr>
                </table>
            </div>
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
        
        <div class="chart-row">
            <div class="chart-col">
                <h3>Grafik Fungsi Informasi</h3>
                <div class="chart-container">
                    <canvas id="infoChart"></canvas>
                </div>
                <div id="infoChartImage"></div>
            </div>
        </div>
        
        <h2>Detail Jawaban</h2>
        <table class="detail">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Soal</th>
                    <th>Pertanyaan</th>
                    <th>Tingkat Kesulitan</th>
                    <th>Jawaban</th>
                    <th>Status</th>
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
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="text-center"><?= $jawaban['soal_id'] ?></td>
                    <td><?= esc($jawaban['pertanyaan']) ?></td>
                    <td class="text-center"><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
                    <td class="text-center"><?= $jawaban['jawaban_siswa'] ?></td>
                    <td class="text-center <?= $jawaban['is_correct'] ? 'text-success' : 'text-danger' ?>">
                        <?= $jawaban['is_correct'] ? 'Benar' : 'Salah' ?>
                    </td>
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
        const labels = <?= json_encode(array_map(function ($i) {
                        return 'Soal ' . ($i + 1);
                      }, range(0, count($detailJawaban) - 1))) ?>;
        
        const thetaData = <?= json_encode(array_map(function ($item) {
                            return $item['theta_saat_ini'];
                          }, $detailJawaban)) ?>;

        const seData = <?= json_encode(array_map(function ($item) {
                          return $item['se_saat_ini'];
                        }, $detailJawaban)) ?>;
                        
        // Data untuk grafik Fungsi Informasi
        const infoData = <?= json_encode(array_map(function ($item) {
                          return isset($item['ii_saat_ini']) ? $item['ii_saat_ini'] : null;
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
        
        // Fungsi untuk membuat grafik Informasi
        function createInfoChart() {
            const ctx = document.getElementById('infoChart').getContext('2d');
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Fungsi Informasi Soal',
                        data: infoData,
                        backgroundColor: '#36b9cc',
                        borderColor: '#2c9faf',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Fungsi Informasi Tiap Soal'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Informasi'
                            }
                        }
                    }
                }
            });
        }

        // Fungsi untuk mengkonversi chart ke gambar
        async function chartToImage(chart, containerId) {
            // Pastikan chart selesai dirender
            await new Promise(resolve => setTimeout(resolve, 100));
            
            // Konversi canvas chart ke gambar
            const canvas = chart.canvas;
            
            // Gunakan html2canvas untuk membuat gambar dari canvas
            html2canvas(canvas).then(canvas => {
                const container = document.getElementById(containerId);
                
                // Tambahkan gambar ke div
                const img = document.createElement('img');
                img.src = canvas.toDataURL('image/png');
                img.style.width = '100%';
                img.style.maxWidth = '100%';
                container.appendChild(img);
                
                // Sembunyikan canvas asli saat mencetak
                chart.canvas.parentNode.style.display = 'none';
            });
        }

        // Jalankan saat window load
        window.onload = async function() {
            // Buat chart
            const thetaChart = createThetaChart();
            const seChart = createSEChart();
            const infoChart = createInfoChart();
            
            // Konversi chart ke gambar untuk printing
            await chartToImage(thetaChart, 'thetaChartImage');
            await chartToImage(seChart, 'seChartImage');
            await chartToImage(infoChart, 'infoChartImage');
            
            // Tunggu sedikit untuk memastikan gambar sudah dimuat
            setTimeout(() => {
                // Otomatis print saat halaman dimuat
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>