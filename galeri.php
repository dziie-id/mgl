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

$sql = "SELECT * FROM galleries WHERE kategori LIKE ? ORDER BY id DESC"; // Pake DESC biar yang terbaru di atas
$stmt = $pdo->prepare($sql);
$stmt->execute(['%' . $nama_kategori_asli . '%']);
$galleries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio <?= $nama_kategori_asli ?> | Sticker MGL</title>
    <meta name="description" content="Lihat hasil pengerjaan <?= $nama_kategori_asli ?> kami. Kualitas premium dengan pengerjaan presisi oleh Sticker MGL.">
    
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">

    <style>
        body { padding-top: 100px; background-color: #0f0f0f; color: white; }
        .filter-btn {
            padding: 8px 25px;
            border-radius: 50px;
            text-decoration: none;
            color: #fff;
            border: 1px solid #333;
            transition: all 0.3s;
            display: inline-block;
            margin: 5px;
            font-size: 0.9rem;
        }
        .filter-btn.active {
            background-color: #007bff;
            border-color: #007bff;
        }
        .portfolio-item {
            position: relative;
            display: block;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1 / 1; /* BIAR GAK CELENG: Gambar kotak konsisten */
            background-color: #222;
        }
        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .portfolio-item:hover img {
            transform: scale(1.1);
        }
        .overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,123,255,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
        }
        .portfolio-item:hover .overlay { opacity: 1; }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container mb-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-uppercase">Portofolio <span class="text-primary">MGL</span></h1>
            <p class="text-secondary">Hasil pengerjaan terbaik untuk kepuasan pelanggan kami.</p>
        </div>

        <div class="text-center mb-5">
            <?php foreach ($allowed_kategori as $kat): 
                $slug = str_replace(' ', '-', $kat); ?>
                <a href="?kategori=<?= $slug ?>" class="filter-btn <?= ($kategori_aktif == $slug) ? 'active' : '' ?>">
                    <?= $kat ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="row g-3">
            <?php if ($galleries): ?>
                <?php foreach ($galleries as $g): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="<?= BASE_URL ?>uploads/gallery/<?= $g['file_name'] ?>" 
                           class="portfolio-item shadow-sm"
                           data-fancybox="gallery" 
                           data-caption="<?= $g['alt_text'] ?>">
                            
                            <img src="<?= BASE_URL ?>uploads/gallery/<?= $g['file_name'] ?>" 
                                 alt="<?= $g['alt_text'] ?>"
                                 width="400" height="400"
                                 loading="lazy">
                                 
                            <div class="overlay">
                                <i class="fa-solid fa-magnifying-glass-plus fa-2x text-white"></i>
                            </div>
                        </a>
                        <div class="text-center mt-2 small text-secondary"><?= $g['alt_text'] ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h5 class="text-muted">Belum ada foto di kategori ini.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", { Thumbs: { type: "modern" } });
    </script>
</body>
</html>
