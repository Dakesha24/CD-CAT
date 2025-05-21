<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
    <h1 class="mt-3">Jadwal Ujian</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
        Tambah Jadwal Ujian
    </button>

    <div class="card mb-4">
        <div class="card-body px-0 px-md-3">
            <div class="table-responsive">
                <table id="jadwalTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ujian</th>
                            <th>Kelas</th>
                            <th class="d-none d-md-table-cell">Mulai</th>
                            <th class="d-none d-md-table-cell">Selesai</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($jadwal as $j): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $j['nama_ujian'] ?></td>
                                <td><?= $j['nama_kelas'] ?></td>
                                <td class="d-none d-md-table-cell"><?= date('d/m/Y H:i', strtotime($j['tanggal_mulai'])) ?></td>
                                <td class="d-none d-md-table-cell"><?= date('d/m/Y H:i', strtotime($j['tanggal_selesai'])) ?></td>
                                <td><?= $j['kode_akses'] ?></td>
                                <td><span class="badge bg-secondary"><?= str_replace('_', ' ', $j['status']) ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $j['jadwal_id'] ?>">Edit</button>
                                        <a href="<?= base_url('guru/jadwal-ujian/hapus/' . $j['jadwal_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahJadwalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/jadwal-ujian/tambah') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ujian</label>
                        <select name="ujian_id" class="form-control" required>
                            <option value="">Pilih Ujian</option>
                            <?php foreach ($ujian_tambah as $u): ?>
                                <option value="<?= $u['id_ujian'] ?>"><?= $u['nama_ujian'] ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['kelas_id'] ?>"><?= $k['nama_kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Guru</label>
                        <select name="guru_id" class="form-control" required>
                            <option value="">Pilih Guru</option>
                            <?php foreach ($guru as $g): ?>
                                <option value="<?= $g['guru_id'] ?>"><?= $g['nama_lengkap'] ?> - <?= $g['mata_pelajaran'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal & Waktu Mulai</label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal & Waktu Selesai</label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Akses</label>
                        <input type="text" name="kode_akses" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<?php foreach ($jadwal as $j): ?>
    <div class="modal fade" id="editJadwalModal<?= $j['jadwal_id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jadwal Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('guru/jadwal-ujian/edit/' . $j['jadwal_id']) ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ujian</label>
                            <select name="ujian_id" class="form-control" required>
                                <?php foreach ($ujian_edit as $u): ?>
                                    <option value="<?= $u['id_ujian'] ?>" <?= ($u['id_ujian'] == $j['ujian_id']) ? 'selected' : '' ?>><?= $u['nama_ujian'] ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-control" required>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['kelas_id'] ?>" <?= ($k['kelas_id'] == $j['kelas_id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-control" required>
                                <option value="">Pilih Guru</option>
                                <?php foreach ($guru as $g): ?>
                                    <option value="<?= $g['guru_id'] ?>" <?= ($g['guru_id'] == $j['guru_id']) ? 'selected' : '' ?>><?= $g['nama_lengkap'] ?> - <?= $g['mata_pelajaran'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal & Waktu Mulai</label>
                            <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($j['tanggal_mulai'])) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal & Waktu Selesai</label>
                            <input type="datetime-local" name="tanggal_selesai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($j['tanggal_selesai'])) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Akses</label>
                            <input type="text" name="kode_akses" class="form-control" value="<?= $j['kode_akses'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="belum_mulai" <?= ($j['status'] == 'belum_mulai') ? 'selected' : '' ?>>Belum Mulai</option>
                                <option value="sedang_berlangsung" <?= ($j['status'] == 'sedang_berlangsung') ? 'selected' : '' ?>>Sedang Berlangsung</option>
                                <option value="selesai" <?= ($j['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    $(document).ready(function() {
        $('#jadwalTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>