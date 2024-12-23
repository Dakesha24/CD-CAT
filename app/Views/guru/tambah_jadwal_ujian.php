<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Jadwal Ujian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/guru/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/guru/jadwal-ujian">Jadwal Ujian</a></li>
        <li class="breadcrumb-item active">Tambah Jadwal Ujian</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Form Input Jadwal Ujian
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/guru/jadwal-ujian/tambah" method="POST" id="formJadwalUjian">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="jenis_ujian_id" class="form-label">Jenis Ujian</label>
                    <select class="form-select" name="jenis_ujian_id" id="jenis_ujian_id" required>
                        <option value="">Pilih Jenis Ujian</option>
                        <?php foreach ($jenis_ujian as $ju): ?>
                            <option value="<?= $ju['jenis_ujian_id'] ?>"><?= $ju['nama_ujian'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="kelas_id" class="form-label">Kelas</label>
                    <select class="form-select" name="kelas_id" id="kelas_id" required>
                        <option value="">Pilih Kelas</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['kelas_id'] ?>"><?= $k['nama_kelas'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input type="datetime-local" class="form-control" name="tanggal_mulai" id="tanggal_mulai" required>
                </div>

                <div class="mb-3">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                    <input type="datetime-local" class="form-control" name="tanggal_selesai" id="tanggal_selesai" required>
                </div>

                <div class="mb-3">
                    <label for="durasi_menit" class="form-label">Durasi (Menit)</label>
                    <input type="number" class="form-control" name="durasi_menit" id="durasi_menit" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="kode_akses" class="form-label">Kode Akses</label>
                    <input type="text" class="form-control" name="kode_akses" id="kode_akses" maxlength="10" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status" required>
                        <option value="">Pilih Status</option>
                        <option value="belum_dimulai">Belum Dimulai</option>
                        <option value="sedang_berlangsung">Sedang Berlangsung</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Jadwal Ujian</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>