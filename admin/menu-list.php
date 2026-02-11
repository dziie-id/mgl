<a href="index.php" class="nav-link text-body <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
<a href="upload.php" class="nav-link text-body <?= basename($_SERVER['PHP_SELF']) == 'upload.php' ? 'active' : '' ?>"><i class="fa-solid fa-upload me-2"></i> Upload Gambar</a>
<a href="artikel.php" class="nav-link text-body"><i class="fa-solid fa-newspaper me-2"></i> Artikel SEO</a>
<a href="survey.php" class="nav-link text-body"><i class="fa-solid fa-clipboard-check me-2"></i> Survey Lapangan</a>
<a href="setting-watermark.php" class="nav-link text-body <?= basename($_SERVER['PHP_SELF']) == 'setting-watermark.php' ? 'active' : '' ?>"><i class="fa-solid fa-copyright me-2"></i> Watermark Setting</a>
<?php if ($_SESSION['role'] == 'admin'): ?>
    <a href="user-manager.php" class="nav-link text-body <?= basename($_SERVER['PHP_SELF']) == 'user-manager.php' ? 'active' : '' ?>"><i class="fa-solid fa-users-gear me-2"></i> User Manager</a>
<?php endif; ?>
<hr class="mx-3 opacity-25">
<a href="logout.php" class="nav-link text-danger"><i class="fa-solid fa-power-off me-2"></i> Keluar</a>