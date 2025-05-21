<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian - <?= $hasil['nama_lengkap'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        h2 {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            background-color: #f0f0f0;
            padding: 5px;
            border-bottom: 1px solid #ccc;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }

        table.info td {
            padding: 3px;
        }

        table.detail {
            border: 1px solid #000;
        }

        table.detail th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
        }

        table.detail td {
            border: 1px solid #000;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        .highlight {
            font-weight: bold;
            color: #0066cc;
        }

        .correct {
            color: green;
        }

        .incorrect {
            color: red;
        }

        .chart-container {
            width: 600px;
            height: 400px;
            margin: 20px auto;
        }

        .chart-image {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <?php
    // Generate kode soal dari tahun dan ID
    $tahunPembuatan = date('Y', strtotime($hasil['tanggal_mulai']));
    $kode_soal = $tahunPembuatan . str_pad($hasil['ujian_id'], 4, '0', STR_PAD_LEFT);
    ?>
    <h1>HASIL UJIAN ADAPTIF SISWA</h1>
    <div style="text-align: center; margin-bottom: 10px;">
        <div><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></div>
        <div>Kode Soal: <?= $kode_soal ?></div>
    </div>

    <h2>INFORMASI UJIAN & SISWA</h2>
    <table class="info">
        <tr>
            <td width="120">Nama Ujian</td>
            <td width="10">:</td>
            <td width="200"><?= esc($hasil['nama_ujian']) ?></td>
            <td width="120">Nama Siswa</td>
            <td width="10">:</td>
            <td><?= esc($hasil['nama_lengkap']) ?></td>
        </tr>
        <tr>
            <td>Jenis Ujian</td>
            <td>:</td>
            <td><?= esc($hasil['nama_jenis']) ?></td>
            <td>Nomor Peserta</td>
            <td>:</td>
            <td><?= esc($hasil['nomor_peserta']) ?></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td><?= esc($hasil['nama_kelas']) ?></td>
            <td>Waktu Mulai</td>
            <td>:</td>
            <td><?= date('d/m/Y H:i', strtotime($hasil['waktu_mulai'])) ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Waktu Selesai</td>
            <td>:</td>
            <td><?= date('d/m/Y H:i', strtotime($hasil['waktu_selesai'])) ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Durasi</td>
            <td>:</td>
            <td><?= date('H:i:s', strtotime($hasil['waktu_selesai']) - strtotime($hasil['waktu_mulai'])) ?></td>
        </tr>
    </table>

    <?php
    // Ambil theta terakhir (dari jawaban terakhir)
    $lastTheta = end($detailJawaban)['theta_saat_ini'];
    // Hitung nilai akhir: 50 + 16.6 * theta
    $finalScore = 50 + (16.6 * $lastTheta);
    // Nilai dalam skala 0-100
    $finalGrade = min(100, max(0, round(($finalScore / 100) * 100)));
    ?>

    <h2>HASIL AKHIR</h2>
    <table class="info">
        <tr>
            <td width="150">Theta Akhir (θ)</td>
            <td width="10">:</td>
            <td width="150"><?= number_format($lastTheta, 3) ?></td>
            <td width="150">Total Soal</td>
            <td width="10">:</td>
            <td><?= count($detailJawaban) ?> soal</td>
        </tr>
        <tr>
            <td>Skor</td>
            <td>:</td>
            <td class="highlight"><?= number_format($finalScore, 1) ?></td>
            <td>Jawaban Benar</td>
            <td>:</td>
            <td><?= array_reduce($detailJawaban, function ($carry, $item) {
                    return $carry + ($item['is_correct'] ? 1 : 0);
                }, 0) ?> soal</td>
        </tr>
        <tr>
            <td>Nilai (Skala 0-100)</td>
            <td>:</td>
            <td class="highlight"><?= $finalGrade ?></td>
            <td>Standard Error Akhir</td>
            <td>:</td>
            <td><?= number_format(end($detailJawaban)['se_saat_ini'], 3) ?></td>
        </tr>
    </table>

    <h2>DETAIL JAWABAN</h2>
    <table class="detail">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="50">ID Soal</th>
                <th>Pertanyaan</th>
                <th width="60">Tingkat Kesulitan</th>
                <th width="50">Jawaban</th>
                <th width="60">Status</th>
                <th width="50">Pi</th>
                <th width="50">Qi</th>
                <th width="50">Ii</th>
                <th width="50">SE</th>
                <th width="50">ΔSE</th>
                <th width="50">θ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($detailJawaban as $i => $jawaban):
            ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="text-center"><?= $jawaban['soal_id'] ?></td>
                    <td><?= esc($jawaban['pertanyaan']) ?></td>
                    <td class="text-center"><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
                    <td class="text-center"><?= $jawaban['jawaban_siswa'] ?></td>
                    <td class="text-center <?= $jawaban['is_correct'] ? 'correct' : 'incorrect' ?>">
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

    <h2>GRAFIK PERKEMBANGAN</h2>
    <div class="chart-container">
        <h3>Grafik Theta (θ)</h3>
        <!-- Kita perlu membuat chart dengan Canvas API -->
        <div id="thetaChartContainer" style="width: 100%; height: 300px;">
            <?php
            // Data untuk grafik
            $thetaValues = array_map(function ($item) {
                return $item['theta_saat_ini'];
            }, $detailJawaban);

            $labelValues = array_map(function ($i) {
                return 'Soal ' . ($i + 1);
            }, range(0, count($detailJawaban) - 1));

            // Buat tabel representasi data untuk Excel
            echo "<table class='detail'>";
            echo "<tr><th>Soal</th>";
            foreach ($labelValues as $label) {
                echo "<th>$label</th>";
            }
            echo "</tr>";

            echo "<tr><td>Theta (θ)</td>";
            foreach ($thetaValues as $value) {
                echo "<td>" . number_format($value, 3) . "</td>";
            }
            echo "</tr>";
            echo "</table>";
            ?>
        </div>
        <h3>Grafik Standard Error (SE)</h3>
        <div id="seChartContainer" style="width: 100%; height: 300px;">
            <?php
            // Data untuk grafik
            $seValues = array_map(function ($item) {
                return $item['se_saat_ini'];
            }, $detailJawaban);

            // Buat tabel representasi data untuk Excel
            echo "<table class='detail'>";
            echo "<tr><th>Soal</th>";
            foreach ($labelValues as $label) {
                echo "<th>$label</th>";
            }
            echo "</tr>";

            echo "<tr><td>Standard Error (SE)</td>";
            foreach ($seValues as $value) {
                echo "<td>" . number_format($value, 3) . "</td>";
            }
            echo "</tr>";
            echo "</table>";
            ?>
        </div>

        <h3>Grafik Fungsi Informasi</h3>
        <div id="infoChartContainer" style="width: 100%; height: 300px;">
            <?php
            // Data untuk grafik info
            $infoValues = [];
            foreach ($detailJawaban as $jawaban) {
                if (isset($jawaban['ii_saat_ini'])) {
                    $infoValues[] = $jawaban['ii_saat_ini'];
                } else {
                    // Jika ii_saat_ini tidak ada, hitung secara manual
                    $e = 2.71828;
                    $theta = $jawaban['theta_saat_ini'];
                    $b = $jawaban['tingkat_kesulitan'];
                    $Pi = round(pow($e, ($theta - $b)) / (1 + pow($e, ($theta - $b))), 3);
                    $Qi = round(1 - $Pi, 3);
                    $Ii = round($Pi * $Qi, 3);
                    $infoValues[] = $Ii;
                }
            }

            // Buat tabel representasi data untuk Excel
            echo "<table class='detail'>";
            echo "<tr><th>Soal</th>";
            foreach ($labelValues as $label) {
                echo "<th>$label</th>";
            }
            echo "</tr>";

            echo "<tr><td>Fungsi Informasi</td>";
            foreach ($infoValues as $value) {
                echo "<td>" . number_format($value, 3) . "</td>";
            }
            echo "</tr>";
            echo "</table>";
            ?>
        </div>
    </div>

    <div style="text-align: right; margin-top: 30px;">
        <p>
            <?= date('d F Y') ?><br>
            Guru Pengampu<br><br><br><br>
            .................................
        </p>
    </div>
</body>

</html>