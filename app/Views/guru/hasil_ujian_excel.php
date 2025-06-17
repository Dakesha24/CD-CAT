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
            <td>Kode Ujian</td>
            <td>:</td>
            <td><?= esc($hasil['kode_ujian']) ?></td>
            <td>NIS</td>
            <td>:</td>
            <td><?= esc($hasil['nomor_peserta']) ?></td>
        </tr>
        <tr>
            <td>Jenis Ujian</td>
            <td>:</td>
            <td><?= esc($hasil['nama_jenis']) ?></td>
            <td>NIS</td>
            <td>:</td>
            <td><?= esc($hasil['nomor_peserta']) ?></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td><?= esc($hasil['nama_kelas']) ?></td>
            <td>Waktu Mulai</td>
            <td>:</td>
            <td><?= $hasil['waktu_mulai_format'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Waktu Selesai</td>
            <td>:</td>
            <td><?= $hasil['waktu_selesai_format'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Total Durasi</td>
            <td>:</td>
            <td><?= $hasil['durasi_total_format'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Rata-rata/Soal</td>
            <td>:</td>
            <td><?= $rataRataWaktuFormat ?></td>
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
            <td><?= $jawabanBenar ?> soal</td>
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
                <th width="60">Kode Soal</th>
                <th width="50">ID Soal</th>
                <th>Pertanyaan</th>
                <th width="60">Tingkat Kesulitan</th>
                <th width="50">Jawaban</th>
                <th width="60">Status</th>
                <th width="80">Waktu Jawab</th>
                <th width="80">Durasi</th>
                <th width="50">Pi</th>
                <th width="50">Qi</th>
                <th width="50">Ii</th>
                <th width="50">SE</th>
                <th width="50">ΔSE</th>
                <th width="50">θ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detailJawaban as $i => $jawaban): ?>
                <tr>
                    <td class="text-center"><?= $jawaban['nomor_soal'] ?></td>
                    <td class="text-center"><?= esc($jawaban['kode_soal']) ?></td>
                    <td class="text-center"><?= $jawaban['soal_id'] ?></td>
                    <td><?= esc($jawaban['pertanyaan']) ?></td>
                    <td class="text-center"><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
                    <td class="text-center"><?= $jawaban['jawaban_siswa'] ?></td>
                    <td class="text-center <?= $jawaban['is_correct'] ? 'correct' : 'incorrect' ?>">
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

    <h2>GRAFIK PERKEMBANGAN</h2>
    <div style="margin: 20px 0;">
        <h3>Data Theta (θ)</h3>
        <?php
        // Data untuk grafik
        $thetaValues = array_map(function ($item) {
            return $item['theta_saat_ini'];
        }, $detailJawaban);

        $labelValues = array_map(function ($item) {
            return 'Soal ' . $item['nomor_soal'];
        }, $detailJawaban);

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

        echo "<tr><td>Durasi (detik)</td>";
        foreach ($detailJawaban as $jawaban) {
            echo "<td>" . $jawaban['durasi_pengerjaan_detik'] . "</td>";
        }
        echo "</tr>";
        echo "</table>";
        ?>

        <h3>Data Standard Error (SE)</h3>
        <?php
        $seValues = array_map(function ($item) {
            return $item['se_saat_ini'];
        }, $detailJawaban);

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

    <div style="text-align: right; margin-top: 30px;">
        <p>
            <?= date('d F Y') ?><br>
            Guru Pengampu<br><br><br><br>
            .................................
        </p>
    </div>
</body>

</html>