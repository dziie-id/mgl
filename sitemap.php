<?php
include 'includes/db.php';

header("Content-Type: application/xml; charset=utf-8");

$base = BASE_URL;

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$static_pages = [
    '' => '1.0', // Home
    'galeri.php' => '0.8',
    'layanan.php' => '0.8',
    'artikel.php' => '0.8'
];

foreach ($static_pages as $page => $priority) {
    echo '<url>';
    echo '<loc>' . $base . $page . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>' . $priority . '</priority>';
    echo '</url>';
}

$stmt_art = $pdo->query("SELECT slug, created_at FROM articles ORDER BY created_at DESC");
while ($row = $stmt_art->fetch()) {
    echo '<url>';
    echo '<loc>' . $base . 'baca.php?slug=' . $row['slug'] . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($row['created_at'])) . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

$stmt_cat = $pdo->query("SELECT nama_kategori FROM categories");
while ($row = $stmt_cat->fetch()) {
    $slug_cat = str_replace(' ', '-', $row['nama_kategori']);
    echo '<url>';
    echo '<loc>' . $base . 'galeri.php?kategori=' . $slug_cat . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

echo '</urlset>';
