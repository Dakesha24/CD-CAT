<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <h1 class="mb-4">Petunjuk Penggunaan PHY-DA-CAT</h1>
    
    <!-- Pengenalan -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Tentang PHY-DA-CAT</h3>
        </div>
        <div class="card-body">
            <p>PHY-DA-CAT (Physics Dynamic Adaptive Computer-Assisted Test) adalah platform ujian adaptif untuk mata pelajaran Fisika. Sistem ini akan menyesuaikan tingkat kesulitan soal berdasarkan kemampuan peserta ujian.</p>
        </div>
    </div>

    <!-- Langkah-langkah -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h3 class="card-title mb-0">Cara Mengikuti Ujian</h3>
        </div>
        <div class="card-body">
            <div class="timeline">
                <div class="step mb-4">
                    <h4 class="text-success">1. Login ke Akun Anda</h4>
                    <p>Gunakan username dan password yang telah diberikan untuk masuk ke sistem.</p>
                </div>

                <div class="step mb-4">
                    <h4 class="text-success">2. Memasukkan Token Ujian</h4>
                    <p>Token ujian akan diberikan oleh pengawas sebelum ujian dimulai. Token ini bersifat unik untuk setiap sesi ujian.</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Pastikan token yang dimasukkan benar dan sesuai dengan sesi ujian Anda.
                    </div>
                </div>

                <div class="step mb-4">
                    <h4 class="text-success">3. Mengikuti Ujian Adaptif</h4>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Soal Awal:</strong> Anda akan diberikan soal dengan tingkat kesulitan menengah
                        </li>
                        <li class="list-group-item">
                            <strong>Penyesuaian Tingkat:</strong> Sistem akan menyesuaikan tingkat kesulitan soal berikutnya berdasarkan jawaban Anda
                        </li>
                        <li class="list-group-item">
                            <strong>Durasi:</strong> Setiap soal memiliki batas waktu yang ditentukan
                        </li>
                        <li class="list-group-item">
                            <strong>Navigasi:</strong> Tidak dapat kembali ke soal sebelumnya
                        </li>
                    </ul>
                </div>

                <div class="step mb-4">
                    <h4 class="text-success">4. Penyelesaian Ujian</h4>
                    <p>Ujian akan berakhir ketika:</p>
                    <ul>
                        <li>Jumlah soal minimum telah terpenuhi dan tingkat kemampuan telah terukur dengan akurat</li>
                        <li>Batas waktu maksimum ujian telah tercapai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Peraturan dan Ketentuan -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title mb-0">Peraturan dan Ketentuan</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <h5>Hal-hal yang Tidak Diperbolehkan:</h5>
                <ul class="mb-0">
                    <li>Membuka tab/window browser lain selama ujian berlangsung</li>
                    <li>Menggunakan perangkat elektronik lain selain untuk ujian</li>
                    <li>Meninggalkan halaman ujian</li>
                    <li>Bekerja sama dengan peserta lain</li>
                </ul>
            </div>

            <div class="alert alert-info">
                <h5>Hal-hal yang Diperbolehkan:</h5>
                <ul class="mb-0">
                    <li>Menggunakan kalkulator scientific</li>
                    <li>Menggunakan kertas coret-coretan yang telah disediakan</li>
                    <li>Bertanya kepada pengawas jika ada kendala teknis</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tips Mengerjakan -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Tips Mengerjakan Ujian</h3>
        </div>
        <div class="card-body">
            <ol>
                <li class="mb-2">Pastikan koneksi internet stabil sebelum memulai ujian</li>
                <li class="mb-2">Baca soal dengan teliti dan perhatikan satuan yang digunakan</li>
                <li class="mb-2">Manfaatkan waktu sebaik mungkin untuk setiap soal</li>
                <li class="mb-2">Jika ragu dengan jawaban, gunakan penalaran logis dan eliminasi pilihan yang pasti salah</li>
                <li class="mb-2">Tetap tenang dan fokus selama ujian berlangsung</li>
            </ol>
        </div>
    </div>

    <!-- Bantuan Teknis -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h3 class="card-title mb-0">Bantuan Teknis</h3>
        </div>
        <div class="card-body">
            <p>Jika mengalami kendala teknis selama ujian, segera hubungi:</p>
            <ul>
                <li>Pengawas ujian di ruangan</li>
                <li>Tim support teknis: <strong>support@phydacat.com</strong></li>
                <li>Hotline: <strong>0812-3456-7890</strong> (WhatsApp)</li>
            </ul>
        </div>
    </div>
</div>

<style>
.timeline .step {
    position: relative;
    padding-left: 30px;
}

.timeline .step:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 2px;
    background-color: #28a745;
}

.timeline .step:after {
    content: '';
    position: absolute;
    left: -4px;
    top: 8px;
    height: 10px;
    width: 10px;
    border-radius: 50%;
    background-color: #28a745;
}
</style>
<?= $this->endSection() ?>