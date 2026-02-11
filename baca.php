<?php
include 'includes/db.php';
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ?");
$stmt->execute([$slug]);
$art = $stmt->fetch();
if (!$art) {
    header("Location: artikel.php");
    exit;
}

$thumb = $art['thumbnail'];
$path = "uploads/articles/" . $thumb;
if (!file_exists($path) || empty($thumb)) {
    $path = "uploads/gallery/" . $thumb;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $art['judul'] ?> | Sticker MGL</title>
    <meta name="description" content="<?= !empty($art['meta_desc']) ? $art['meta_desc'] : substr(strip_tags($art['konten']), 0, 160) ?>">
    <meta name="keywords" content="<?= !empty($art['keyword']) ? $art['keyword'] : 'sticker, branding mobil, jasa pasang sticker' ?>">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="article-card shadow-lg">
                    <img src="<?= $path ?>" class="w-100" style="height: 400px; object-fit: cover; border-bottom: 1px solid #333;">
                    <div class="p-4 p-md-5">
                        <h1 class="judul-artikel mb-4"><?= $art['judul'] ?></h1>
                        <div class="text-white-50 lh-lg"><?= $art['konten'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="sidebar-box mb-4">
                    <h5 class="fw-bold text-white mb-3">BUTUH BANTUAN?</h5>
                    <p class="small sidebar-text mb-4">Tim kami siap memberikan solusi branding terbaik untuk kendaraan dan kantor Anda.</p>
                    <a href="https://wa.me/62895333029272" class="btn-wa shadow-sm"><i class="fa-brands fa-whatsapp me-2"></i> KONSULTASI GRATIS</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>