<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
  <h1 class="mt-4">Bank Soal</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="/guru/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Bank Soal</li>
  </ol>

  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-table me-1"></i>
      Form Input Soal
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

      <form action="/guru/bank-soal/tambah" method="POST" id="formSoal">
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
          <label for="pertanyaan" class="form-label">Pertanyaan</label>
          <textarea class="form-control" id="pertanyaan" name="pertanyaan" rows="3" required></textarea>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="pilihan_a" class="form-label">Pilihan A</label>
            <textarea class="form-control" id="pilihan_a" name="pilihan_a" rows="2" required></textarea>
          </div>
          <div class="col-md-6">
            <label for="pilihan_b" class="form-label">Pilihan B</label>
            <textarea class="form-control" id="pilihan_b" name="pilihan_b" rows="2" required></textarea>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="pilihan_c" class="form-label">Pilihan C</label>
            <textarea class="form-control" id="pilihan_c" name="pilihan_c" rows="2" required></textarea>
          </div>
          <div class="col-md-6">
            <label for="pilihan_d" class="form-label">Pilihan D</label>
            <textarea class="form-control" id="pilihan_d" name="pilihan_d" rows="2" required></textarea>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="jawaban_benar" class="form-label">Jawaban Benar</label>
            <select class="form-select" name="jawaban_benar" id="jawaban_benar" required>
              <option value="">Pilih Jawaban Benar</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="tingkat_kesulitan" class="form-label">Tingkat Kesulitan</label>
            <select class="form-select" name="tingkat_kesulitan" id="tingkat_kesulitan" required>
              <option value="">Pilih Tingkat Kesulitan</option>
              <option value="1">Sangat Mudah</option>
              <option value="2">Mudah</option>
              <option value="3">Sedang</option>
              <option value="4">Sulit</option>
              <option value="5">Sangat Sulit</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Soal</button>
      </form>
    </div>
  </div>

  <!-- Tabel Daftar Soal -->
  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-table me-1"></i>
      Daftar Soal
    </div>
    <div class="card-body">
      <table id="dataSoal" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>Jenis Ujian</th>
            <th>Pertanyaan</th>
            <th>Tingkat Kesulitan</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          foreach ($soal as $s): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $s['nama_ujian'] ?></td>
              <td><?= $s['pertanyaan'] ?></td>
              <td><?= $s['tingkat_kesulitan'] ?></td>
              <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
              <td>
                <button class="btn btn-sm btn-info" onclick="editSoal(<?= $s['soal_id'] ?>)">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteSoal(<?= $s['soal_id'] ?>)">Hapus</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $('#dataSoal').DataTable();
    });

    function editSoal(soalId) {
      // Implementasi edit soal
      window.location.href = `/guru/bank-soal/edit/${soalId}`;
    }

    function deleteSoal(soalId) {
      if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
        fetch(`/guru/bank-soal/delete/${soalId}`, {
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
              alert('Gagal menghapus soal');
            }
          });
      }
    }
  </script>
  <?= $this->endSection() ?>