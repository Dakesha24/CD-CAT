<footer class="footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 mb-4">
        <h5 class="footer-title">PHY-DA-CAT</h5>
        <p class="footer-description">
          Platform asesmen diagnosis untuk pembelajaran Fisika yang adaptif dan inovatif.
        </p>
        <div class="social-links">
          <a href="https://www.instagram.com/jauzaamalia" class="social-link" target="_blank"><i class="bi bi-instagram"></i></a>
          <a href="https://www.linkedin.com/in/jauza-amalia-906070328/?originalSubdomain=id" class="social-link" target="_blank"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h5 class="footer-subtitle">Tautan</h5>
        <ul class="footer-links">
          <li><a href="<?= base_url('about') ?>">Tentang Kami </a></li>
          <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
          <li><a href="<?= base_url('bantuan') ?>">Bantuan</a></li>
        </ul>
      </div>
      <div class="col-lg-4 col-md-6 mb-4">
        <h5 class="footer-subtitle">Kontak</h5>
        <ul class="footer-contact">
          <li><i class="bi bi-geo-alt"></i> Bandung, Indonesia</li>
            <li>
                <a href="mailto:jauzaamalia@upi.edu" style="text-decoration: none; color: inherit;">
                <i class="bi bi-envelope"></i> jauzaamalia@upi.edu
                </a>
            </li>
            <li>
                <a href="https://wa.me/6285794124143" style="text-decoration: none; color: inherit;" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-phone"></i> +62 857 9412 4143
                </a>
            </li>
        </ul>
      </div>
    </div>
    <hr class="footer-divider">
    <div class="row footer-bottom">
      <div class="col-md-6">
        <p class="copyright">&copy; <?= date('Y') ?> PHY-DA-CAT. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-md-end">
        <a href="#" class="footer-bottom-link">Kebijakan Privasi</a>
        <a href="#" class="footer-bottom-link">Syarat & Ketentuan</a>
      </div>
    </div>
  </div>
</footer>

<style>
  .footer {
    background: linear-gradient(276deg, #2B1238 -2.09%, #0A1B37 75.22%);
    color: #fff;
    padding: 60px 0 30px;
    box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
  }

  .footer-title {
    font-weight: bold;
    margin-bottom: 20px;
    font-size: 1.5rem;
  }

  .footer-subtitle {
    color: #fff;
    font-weight: 600;
    margin-bottom: 20px;
  }

  .footer-description {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 20px;
    line-height: 1.6;
  }

  .social-links {
    margin-top: 20px;
  }

  .social-link {
    color: #fff;
    font-size: 20px;
    margin-right: 15px;
    transition: all 0.3s ease;
    display: inline-block;
  }

  .social-link:hover {
    color: rgba(255, 255, 255, 0.8);
    transform: translateY(-3px);
  }

  .footer-links {
    list-style: none;
    padding: 0;
  }

  .footer-links li {
    margin-bottom: 12px;
  }

  .footer-links a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
  }

  .footer-links a:hover {
    color: #fff;
    padding-left: 5px;
  }

  .footer-contact {
    list-style: none;
    padding: 0;
  }

  .footer-contact li {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
  }

  .footer-contact li i {
    margin-right: 10px;
    font-size: 1.1rem;
  }

  .footer-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 30px 0;
  }

  .footer-bottom {
    color: rgba(255, 255, 255, 0.8);
  }

  .footer-bottom-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    margin-left: 20px;
    transition: all 0.3s ease;
  }

  .footer-bottom-link:hover {
    color: #fff;
  }

  @media (max-width: 768px) {
    .footer {
      padding: 40px 0 20px;
    }

    .footer-bottom {
      text-align: center;
    }

    .footer-bottom-link {
      display: block;
      margin: 10px 0;
    }
  }

  .footer-contact {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-contact li {
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.footer-contact li i {
  margin-right: 10px;
}
</style>