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

// Pakai ORDER BY id DESC biar hasil terbaru di atas (lebih pro daripada RAND)
$sql = "SELECT * FROM galleries WHERE kategori LIKE ? ORDER BY id DESC";
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
    <meta name="description" content="Lihat galeri hasil pengerjaan <?= $nama_kategori_asli ?> terbaik dari Sticker MGL.">
    
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="assets/css/style-front.css">

    <style>
        /* Samain sama Index biar sinkron */
        body { 
            padding-top: 100px; 
            background-color: #0f0f0f; 
            color: white; 
            font-family: 'Poppins', sans-serif;
        }
        
        .filter-btn {
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            color: #fff;
            border: 1px solid #333;
            transition: 0.3s;
            display: inline-block;
            margin: 5px;
            background: rgba(255,255,255,0.05);
        }
        
        .filter-btn:hover, .filter-btn.active {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .portfolio-item {
            position: relative;
            display: block;
            border-radius: 15px;
            overflow: hidden;
            aspect-ratio: 1 / 1; /* Biar kotak presisi anti-celeng */
            background-color: #1a1a1a;
            border: 1px solid #333;
        }

        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s ease;
        }

        .portfolio-item:hover img {
            transform: scale(1.1);
        }

        .overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 123, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
        }

        .portfolio-item:hover .overlay {
            opacity: 1;
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <main class="container py-5">
        <div class="text-center mb-5">
            <h5 class="text-primary fw-bold text-uppercase small" style="letter-spacing: 3px;">Portofolio Kami</h5>
            <h1 class="display-5 fw-bold text-uppercase">Galeri <span class="text-primary">MGL</span></h1>
            <p class="text-secondary mx-auto" style="max-width: 600px;">Kumpulan hasil kerja nyata tim kami untuk berbagai jenis kendaraan dan kebutuhan sticker.</p>
        </div>

        <div class="text-center mb-5">
            <?php foreach ($allowed_kategori as $kat): 
                $slug = str_replace(' ', '-', $kat); ?>
                <a href="?kategori=<?= $slug ?>" class="filter-btn <?= ($kategori_aktif == $slug) ? 'active' : '' ?>">
                    <?= $kat ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="row g-4">
            <?php if ($galleries): ?>
                <?php foreach ($galleries as $g): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="uploads/gallery/<?= $g['file_name'] ?>" 
                           class="portfolio-item shadow"
                           data-fancybox="gallery" 
                           data-caption="<?= $g['alt_text'] ?>">
                            
                            <img src="uploads/gallery/<?= $g['file_name'] ?>" 
                                 alt="<?= $g['alt_text'] ?>"
                                 width="400" height="400"
                                 loading="lazy">
                                 
                            <div class="overlay">
                                <i class="fa-solid fa-expand fa-2x text-white"></i>
                            </div>
                        </a>
                        <div class="text-center mt-3">
                            <span class="badge bg-dark border border-secondary text-secondary fw-normal"><?= $g['alt_text'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-regular fa-face-frown fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Oops! Foto belum tersedia untuk kategori ini.</h5>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-5 border-top border-secondary">
        <div class="container text-center">
            <p class="small opacity-50 mb-0">&copy; <?= date('Y') ?> STICKER MGL SPECIALIST. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {
            Thumbs: { type: "modern" }
        });
    </script>
</body>
</html>
