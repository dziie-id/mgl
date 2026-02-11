<?php
include 'includes/db.php';
$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel & Tips Perawatan Sticker Mobil | Sticker MGL</title>
    <meta name="description" content="Pusat informasi dan tips seputar perawatan sticker mobil, branding kendaraan, dan tren modifikasi sticker terbaru dari ahli sticker profesional.">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <section class="container py-5">
        <div class="text-center mb-5">
            <h5 class="text-primary fw-bold text-uppercase small" style="letter-spacing: 3px;">Tips & News</h5>
            <h2 class="text-uppercase">Pusat Edukasi Sticker</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($articles as $art):
                $thumb = $art['thumbnail'];
                $path = "uploads/articles/" . $thumb;
                if (!file_exists($path) || empty($thumb)) {
                    $path = "uploads/gallery/" . $thumb;
                }
            ?>
                <div class="col-md-4">
                    <div class="card blog-card shadow">
                        <img src="<?= $path ?>" class="blog-img" alt="<?= $art['judul'] ?>">
                        <div class="p-4">
                            <small class="text-primary fw-bold text-uppercase"><?= date('d M Y', strtotime($art['created_at'])) ?></small>
                            <a href="baca.php?slug=<?= $art['slug'] ?>" class="blog-title d-block my-3"><?= $art['judul'] ?></a>
                            <p class="small text-secondary mb-0"><?= substr(strip_tags($art['konten']), 0, 100) ?>...</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>