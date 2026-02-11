<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>index.php">STICKER<span class="text-primary">MGL</span></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'layanan.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>layanan.php">Layanan</a></li>
                <li class="nav-item"><a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'galeri.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>galeri.php">Portofolio</a></li>
                <li class="nav-item"><a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'artikel.php' || basename($_SERVER['PHP_SELF']) == 'baca.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>artikel.php">Edukasi</a></li>
                <li class="nav-item"><a class="nav-link bg-primary px-4 ms-lg-3 rounded-pill text-center shadow" href="https://wa.me/62895333029272"><i class="fa-brands fa-whatsapp me-2"></i>Hubungi Kami</a></li>
            </ul>
        </div>
    </div>
</nav>