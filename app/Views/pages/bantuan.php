<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="help-container">
    <div class="container py-5">
        <h1 class="help-title text-center mb-5">Pusat Bantuan</h1>
        
        <!-- Help Categories -->
        <div class="help-section">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h3>Masalah Login</h3>
                        <p>Apabila lupa password, silahkan hubungi admin untuk mereset password anda.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-question-circle-fill"></i>
                        </div>
                        <h3>Soal Tidak Muncul</h3>
                        <p>Periksa koneksi internet Anda. Jika masalah berlanjut, hubungi admin melalui menu Hubungi Kami.</p>
                        <ul class="help-checklist">
                            <li>Periksa koneksi internet</li>
                            <li>Refresh halaman</li>
                            <li>Coba browser berbeda</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <h3>Laporan Hasil</h3>
                        <p>Tunggu beberapa saat setelah ujian selesai. Jika masih bermasalah, laporkan melalui pusat bantuan.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h3>Akses Guru/Admin</h3>
                        <p>Pastikan Anda telah terdaftar sebagai guru atau admin. Hubungi admin utama untuk memverifikasi akun Anda.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <h3>Pengaturan Akun</h3>
                        <p>Temukan bantuan untuk mengubah profil, kata sandi, dan pengaturan akun lainnya.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-laptop-fill"></i>
                        </div>
                        <h3>Teknis Platform</h3>
                        <p>Panduan umum untuk menggunakan platform, mengatasi masalah teknis, dan pertanyaan umum.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-section text-center mt-5">
            <h2 class="mb-4">Butuh Bantuan Lebih Lanjut?</h2>
            <p class="mb-4">Jika pertanyaan Anda belum terjawab, hubungi kami melalui email atau formulir kontak.</p>
            <div class="contact-options">
                <a href="mailto:jauzaamalia@upi.edu" class="btn btn-outline-light me-2">
                    <i class="bi bi-envelope"></i> Email
                </a>
                <a href="https://wa.me/6285794124143" target="_blank" class="btn btn-outline-light">
                    <i class="bi bi-chat-dots"></i> Kontak
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.help-container {
    background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
    color: white;
    min-height: 100vh;
}

.help-title {
    font-weight: bold;
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

.help-section {
    margin-bottom: 3rem;
}

.help-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    height: 100%;
    transition: transform 0.3s ease;
    backdrop-filter: blur(10px);
}

.help-card:hover {
    transform: translateY(-5px);
}

.help-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.help-card h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.help-checklist {
    list-style: none;
    padding: 0;
    text-align: left;
    margin-top: 1rem;
}

.help-checklist li {
    padding-left: 1.5rem;
    position: relative;
    margin-bottom: 0.5rem;
}

.help-checklist li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: white;
}

.contact-section {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 2rem;
    backdrop-filter: blur(10px);
}

.contact-options .btn {
    min-width: 120px;
}

@media (max-width: 768px) {
    .help-title {
        font-size: 2rem;
    }

    .help-card {
        margin-bottom: 1rem;
    }

    .contact-options .btn {
        width: 100%;
        margin: 0.5rem 0;
    }
}
</style>
<?= $this->endSection() ?>