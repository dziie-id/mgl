<?php
$current_page = basename($_SERVER['PHP_SELF']);
// Cek jika BASE_URL belum didefinisikan (biar gak error kalau belum ada)
if (!defined('BASE_URL')) {
  define('BASE_URL', '');
}
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
      STICKER<span class="text-primary">MGL</span>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Buka Menu Navigasi">
      <i class="fa-solid fa-bars-staggered"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>"
            href="<?= BASE_URL ?>index.php">Beranda</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'layanan.php') ? 'active' : ''; ?>"
            href="<?= BASE_URL ?>layanan.php">Layanan</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'galeri.php') ? 'active' : ''; ?>"
            href="<?= BASE_URL ?>galeri.php">Portofolio</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'artikel.php' || $current_page == 'baca.php') ? 'active' : ''; ?>"
            href="<?= BASE_URL ?>artikel.php">Edukasi</a>
        </li>

        <li class="nav-item ms-lg-3">
          <a class="nav-link bg-primary px-4 rounded-pill text-white shadow-sm"
            href="https://wa.me/62895333029272"
            target="_blank"
            aria-label="Hubungi kami via WhatsApp">
            <i class="fa-brands fa-whatsapp me-2"></i>Kontak
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* CSS Tambahan biar navigasi gak tumpang tindih */
  .navbar {
    transition: all 0.3s ease;
    padding: 15px 0;
  }

  .navbar.shadow-lg {
    background: rgba(0, 0, 0, 0.9) !important;
    /* Biar kebaca pas di-scroll */
    padding: 10px 0;
  }

  /* Style buat menu active */
  .nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 600;
  }
</style>