<?php
include 'includes/db.php';

$allowed_kategori = [
    'Branding Mobil',
    'Kaca Film',
    'Sticker'
];

$kategori_aktif = isset($_GET['kategori']) ? $_GET['kategori'] : 'Branding-Mobil';

$nama_kategori_asli = str_replace('-', ' ', $kategori_aktif);

if (!in_array($nama_kategori_asli, $allowed_kategori)) {
    $nama_kategori_asli = 'Branding Mobil';
    $kategori_aktif = 'Branding-Mobil';
}

$sql = "SELECT * FROM galleries 
        WHERE kategori LIKE ? 
        ORDER BY RAND()";

$stmt = $pdo->prepare($sql);
$stmt->execute(['%' . $nama_kategori_asli . '%']);

$galleries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<meta charset="UTF-8">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Branding Mobil & Sticker Custom | Sticker MGL</title>
    <meta name="description" content="Lihat hasil pengerjaan jasa branding mobil dan pemasangan sticker kami. Galeri foto wrapping mobil, decal motor, dan sticker kaca film gedung.">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/img/favicon.png">

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h5 class="text-primary fw-bold small mb-2" style="letter-spacing: 3px;">GALLERY PROJECTS</h5>
            <h2 class="display-5 fw-bold text-uppercase">HASIL PENGERJAAN KAMI</h2>
            <div class="mt-4 d-flex justify-content-center flex-wrap gap-2">
                <a href="?kategori=Branding-Mobil"
                    class="filter-btn <?= $kategori_aktif == 'Branding-Mobil' ? 'active' : '' ?>">
                    BRANDING
                </a>

                <a href="?kategori=Kaca-Film"
                    class="filter-btn <?= $kategori_aktif == 'Kaca-Film' ? 'active' : '' ?>">
                    KACA FILM
                </a>

                <a href="?kategori=Sticker"
                    class="filter-btn <?= $kategori_aktif == 'Sticker' ? 'active' : '' ?>">
                    STICKER
                </a>

            </div>
        </div>


        <div class="row g-4">
            <?php if (count($galleries) > 0): ?>
                <?php foreach ($galleries as $g): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="<?= BASE_URL ?>uploads/gallery/<?= $g['file_name'] ?>" class="portfolio-item shadow-sm"
                            data-fancybox="gallery" data-caption="<?= $g['alt_text'] ?>">
                            <img src="<?= BASE_URL ?>uploads/gallery/<?= $g['file_name'] ?>" alt="<?= $g['alt_text'] ?>"
                                loading="lazy">
                            <div class="overlay">
                                <i class="fa-solid fa-expand fa-2x text-white"></i>
                            </div>
                        </a>

                        <div class="text-center mt-2 small text-white"><?= $g['kategori'] ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-regular fa-images fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 class="text-muted">Belum ada foto untuk kategori ini.</h5>
                    <a href="?kategori=semua" class="btn btn-outline-primary mt-3 btn-sm rounded-pill">Lihat Semua
                        Galeri</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {

            Thumbs: {
                type: "modern"
            }
        });
    </script>

</body>

</html>