<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Jadwal Ujian</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/guru/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Jadwal Ujian</li>
    </ol>

    <a href="/guru/jadwal-ujian/tambah" class="btn btn-primary mb-3">Tambah Jadwal Ujian</a>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Jadwal Ujian
        </div>
        <div class="card-body">
            <table id="dataUjian" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Ujian</th>
                        <th>Kelas</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($ujian as $u): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $u['nama_ujian'] ?></td>
                            <td><?= $u['nama_kelas'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($u['tanggal_mulai'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($u['tanggal_selesai'])) ?></td>
                            <td>
                                <a href="/guru/jadwal-ujian/edit/<?= $u['jadwal_id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteJadwal(<?= $u['jadwal_id'] ?>)">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataUjian').DataTable();
    });

    function deleteJadwal(jadwalId) {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
            fetch(`/guru/jadwal-ujian/delete/${jadwalId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal menghapus jadwal ujian');
                    }
                });
        }
    }
</script>
<?= $this->endSection() ?>