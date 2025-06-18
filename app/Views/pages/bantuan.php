<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="contact-container">
    <div class="container py-5">
        <h1 class="contact-title text-center mb-5">Hubungi Kami</h1>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="contact-card">
                    <div class="contact-card-header">
                        <i class="bi bi-envelope-fill header-icon"></i>
                        <h4>Kritik dan Saran Penggunaan Asesmen PHY-FA-CAT</h4>
                    </div>

                    <div class="contact-card-body">
                        <form id="contactForm" onsubmit="prepareEmail(event)">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control custom-input" id="name" name="name" placeholder="Nama" required>
                                <label for="name"><i class="bi bi-person"></i> Nama</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control custom-input" id="email" name="email" placeholder="Email" required>
                                <label for="email"><i class="bi bi-envelope"></i> <i>Email</i></label>
                            </div>

                            <div class="form-floating mb-4">
                                <textarea class="form-control custom-input" id="message" name="message" placeholder="Pesan" style="height: 150px" required></textarea>
                                <label for="message"><i class="bi bi-chat-text"></i> Pesan Anda</label>
                            </div>

                            <button type="submit" class="custom-button">
                                <i class="bi bi-envelope-paper"></i> Buka Aplikasi Email
                            </button>
                        </form>

                        <div id="alert-container"></div>
                    </div>
                </div>

                <div class="info-card mt-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <h6><i>Email</i></h6>
                                <p>jauzaamalia@upi.edu</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <h6>Telepon</h6>
                                <p>+62 857 9412 4143</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <h6>Lokasi</h6>
                                <p>Bandung, Indonesia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function prepareEmail(e) {
        e.preventDefault();

        // Ambil nilai dari form
        const name = document.getElementById('name').value;
        const fromEmail = document.getElementById('email').value;
        const message = document.getElementById('message').value;

        // Email tujuan
        const toEmail = 'jauzaamalia@upi.edu';
        const subject = 'Kritik dan Saran PHY-FA-CAT';

        // Format body email dengan lebih rapi
        const body = `
Pesan dari: ${name} (${fromEmail})

Isi Pesan:
${message}

---
Email ini dibuat melalui form kontak di website PHY-FA-CAT.
        `;

        // Buat mailto URL
        const mailtoURL = `mailto:${toEmail}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

        // Tampilkan pesan konfirmasi yang lebih jelas
        showAlert('Membuka aplikasi email Anda. Silakan periksa dan klik kirim dari sana.', 'success');

        // Buka email client default setelah sedikit jeda agar pengguna sempat membaca alert
        setTimeout(() => {
            window.location.href = mailtoURL;
        }, 1500);
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');

        // Buat alert baru
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert custom-alert alert-${type} mt-3`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i>
            ${message}
        `;

        // Hapus alert lama dan tampilkan yang baru
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alertDiv);
    }
</script>

<style>
    .contact-container {
        background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
        min-height: 100vh;
        color: white;
    }

    .contact-title {
        font-weight: bold;
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .contact-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .contact-card-header {
        background: rgba(255, 255, 255, 0.15);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-icon {
        font-size: 1.5rem;
    }

    .contact-card-body {
        padding: 2rem;
    }

    .custom-input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        transition: all 0.3s ease;
    }

    .custom-input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        color: white;
    }

    .form-floating>label {
        color: rgba(255, 255, 255, 0.8);
    }

    .form-floating>.custom-input::placeholder {
        color: transparent;
    }

    .form-floating>.custom-input:focus~label,
    .form-floating>.custom-input:not(:placeholder-shown)~label {
        color: rgba(255, 255, 255, 0.8);
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }

    .custom-button {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        width: 100%;
        font-weight: 500;
    }

    .custom-button:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        color: white;
    }

    .info-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
    }

    .info-item {
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .info-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .info-item h6 {
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .info-item p {
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }

    .custom-alert {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 10px;
        padding: 1rem;
    }

    .alert-success {
        background: rgba(25, 135, 84, 0.2);
        border-left: 4px solid #198754;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.2);
        border-left: 4px solid #dc3545;
    }

    .text-muted {
        color: rgba(255, 255, 255, 0.6) !important;
    }

    @media (max-width: 768px) {
        .contact-title {
            font-size: 2rem;
        }

        .contact-card-header,
        .contact-card-body {
            padding: 1.5rem;
        }

        .info-item {
            padding: 1.5rem;
        }
    }
</style>
<?= $this->endSection() ?>