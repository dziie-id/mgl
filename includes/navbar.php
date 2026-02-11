<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="assets/img/logo.png" alt="Logo MGL Stiker" width="120" height="120" class="d-inline-block align-top">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>"
            href="index.php"
            <?= ($current_page == 'index.php') ? 'aria-current="page"' : ''; ?>>Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'layanan.php') ? 'active' : ''; ?>"
            href="layanan.php"
            <?= ($current_page == 'layanan.php') ? 'aria-current="page"' : ''; ?>>Layanan</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'artikel.php' || $current_page == 'baca.php') ? 'active' : ''; ?>"
            href="artikel.php"
            <?= ($current_page == 'artikel.php') ? 'aria-current="page"' : ''; ?>>Artikel</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'galeri.php') ? 'active' : ''; ?>"
            href="galeri.php"
            <?= ($current_page == 'galeri.php') ? 'aria-current="page"' : ''; ?>>Galeri</a>
        </li>
      </ul>
    </div>
  </div>
</nav>