<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 py-5">
        <div>
            <h2 class="mb-1"><?= esc($ujian['nama_ujian']) ?></h2>
            <p class="text-muted mb-0">
                <?= esc($ujian['nama_jenis']) ?> - <?= esc($ujian['nama_kelas']) ?>
            </p>
        </div>
        <a href="<?= base_url('guru/hasil-ujian') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>No. Peserta</th>
                        <th>Waktu Selesai</th>
                        <th>Durasi</th>
                        <th>Soal Dikerjakan</th>
                        <th>Jawaban Benar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($daftarSiswa as $i => $siswa): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($siswa['nama_lengkap']) ?></td>
                            <td><?= esc($siswa['nomor_peserta']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($siswa['waktu_selesai'])) ?></td>
                            <td><?= date('H:i:s', strtotime($siswa['waktu_selesai']) - strtotime($siswa['waktu_mulai'])) ?></td>
                            <td><?= $siswa['jumlah_soal'] ?></td>
                            <td>
                                <span class="badge bg-success">
                                    <?= $siswa['jawaban_benar'] ?>/<?= $siswa['jumlah_soal'] ?>
                                    <?php if($siswa['jumlah_soal'] > 0): ?>
                                        (<?= round(($siswa['jawaban_benar']/$siswa['jumlah_soal'])*100) ?>%)
                                    <?php else: ?>
                                        (0%)
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('guru/hasil-ujian/detail/' . $siswa['peserta_ujian_id']) ?>" 
                                   class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>