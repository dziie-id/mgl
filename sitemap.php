<?php
include 'includes/db.php';

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    define('BASE_URL', $protocol . "://" . $host . $path . "/");
}

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= BASE_URL ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.00</priority>
    </url>
    <url>
        <loc><?= BASE_URL ?>layanan.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.80</priority>
    </url>
    <url>
        <loc><?= BASE_URL ?>galeri.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
    </url>
    <url>
        <loc><?= BASE_URL ?>artikel.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
    </url>

    <?php
    $stmt = $pdo->query("SELECT slug, created_at FROM articles ORDER BY created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $url = BASE_URL . "baca.php?slug=" . $row['slug'];
        $date = date('Y-m-d', strtotime($row['created_at']));
    ?>
        <url>
            <loc><?= $url ?></loc>
            <lastmod><?= $date ?></lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.70</priority>
        </url>
    <?php } ?>
</urlset>