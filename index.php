<?php
include 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM galleries ORDER BY RAND() LIMIT 10");
$random_gal = $stmt->fetchAll();
$stmt_art = $pdo->query("SELECT * FROM articles ORDER BY RAND() LIMIT 3");
$random_art = $stmt_art->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Sticker MGL">
    <title>Jasa Branding Mobil & Pasang Sticker Premium | Sticker MGL</title>
    <meta name="description" content="Ahlinya jasa branding mobil, wrapping full body, dan pasang sticker kaca film terdekat. Menggunakan bahan premium (Oracal/3M) dengan pengerjaan presisi dan bergaransi.">
    <meta name="keywords" content="jasa branding mobil, jasa pasang sticker, wrapping mobil, sticker mobil, cutting sticker, kaca film gedung, sticker mgl">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">

    <style>
        :root {
            --primary: #0066ff;
            --dark: #0b0b0b;
            --dark-soft: #161616;
            --gray: #888;
        }

        body {
            background-color: var(--dark);
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
        }

        .hero {
            height: 90vh;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.9)), url('https://images.unsplash.com/photo-1611310263923-356c9a0980ca?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .hero p {
            color: #ccc;
            max-width: 600px;
            font-size: 1.1rem;
            margin-bottom: 35px;
        }

        .section-title h5 {
            color: var(--primary);
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .section-title h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-top: 10px;
        }

        .service-box {
            background: var(--dark-soft);
            border: 1px solid #222;
            padding: 40px;
            transition: 0.4s;
            border-radius: 8px;
            height: 100%;
        }

        .service-box:hover {
            background: var(--primary);
            transform: translateY(-10px);
            border-color: var(--primary);
        }

        .service-box i {
            font-size: 3rem;
            margin-bottom: 25px;
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            padding: 0 15px;
        }

        .portfolio-item {
            position: relative;
            overflow: hidden;
            height: 220px;
            border-radius: 8px;
            border: 1px solid #222;
        }

        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.6s ease;
        }

        .portfolio-item:hover img {
            transform: scale(1.1);
            filter: brightness(0.4);
        }

        .portfolio-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
        }

        .portfolio-item:hover .portfolio-overlay {
            opacity: 1;
        }

        .blog-card {
            background: var(--dark-soft);
            border: 1px solid #222;
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s;
        }

        .blog-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }

        .blog-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .blog-body {
            padding: 25px;
        }

        .blog-title {
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .blog-title:hover {
            color: var(--primary);
        }

        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .portfolio-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <section class="hero" id="beranda">
        <div class="container" data-aos="fade-right">
            <h5 class="text-primary fw-bold mb-3" style="letter-spacing: 4px;">SPECIALIST WRAPPING & BRANDING</h5>
            <h1>SENI BRANDING<br><span class="text-primary">KENDARAAN</span> ANDA.</h1>
            <p>Ubah kendaraan operasional atau pribadi Anda menjadi karya seni promosi berjalan. Kami menjamin hasil presisi dengan material kualitas dunia.</p>
            <div class="d-flex gap-3 mt-4">
                <a href="galeri.php" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold py-3 shadow">LIHAT HASIL KERJA</a>
                <a href="https://wa.me/62895333029272" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold py-3">WHATSAPP</a>
            </div>
        </div>
    </section>
    <section class="py-5" id="layanan">
        <div class="container py-5">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h5>KEAHLIAN KAMI</h5>
                <h2>APA YANG KAMI KERJAKAN?</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-box">
                        <i class="fa-solid fa-car-side text-primary"></i>
                        <h4 class="fw-bold mb-3">BRANDING MOBIL</h4>
                        <p class="text-gray small opacity-75">Solusi promosi berjalan dengan wrapping full body atau partisi menggunakan material High-Grade seperti 3M & Oracal.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-box">
                        <i class="fa-solid fa-building text-primary"></i>
                        <h4 class="fw-bold mb-3">STICKER KACA</h4>
                        <p class="text-gray small opacity-75">Instalasi sandblast, kaca film gedung, dan cutting sticker logo kantor untuk estetika dan privasi ruangan.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-box">
                        <i class="fa-solid fa-scissors text-primary"></i>
                        <h4 class="fw-bold mb-3">CUTTING CUSTOM</h4>
                        <p class="text-gray small opacity-75">Produksi sticker cutting presisi tinggi untuk komunitas, promosi produk, hingga dekorasi interior custom.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-black">
        <div class="container mb-5">
            <div class="section-title text-center" data-aos="fade-up">
                <h5>HASIL KERJA NYATA</h5>
                <h2>PORTOFOLIO TERBARU</h2>
            </div>
        </div>
        <div class="portfolio-grid container">
            <?php foreach ($random_gal as $img): ?>
                <div class="portfolio-item" data-aos="zoom-in">
                    <a href="uploads/gallery/<?= $img['file_name'] ?>" data-fancybox="gallery" data-caption="<?= $img['alt_text'] ?>">
                        <img src="uploads/gallery/<?= $img['file_name'] ?>" alt="<?= $img['alt_text'] ?>" loading="lazy">
                        <div class="portfolio-overlay">
                            <i class="fa-solid fa-expand-arrows-alt fa-2x text-white"></i>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="py-5">
        <div class="container py-5">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h5>EDUKASI & TIPS</h5>
                <h2>ARTIKEL TERBARU</h2>
            </div>
            <div class="row g-4">
                <?php foreach ($random_art as $art):
                    $thumb = $art['thumbnail'];
                    $path = "uploads/articles/" . $thumb;
                    if (!file_exists($path) || empty($thumb)) {
                        $path = "uploads/gallery/" . $thumb;
                    }
                    if (!file_exists($path) || empty($thumb)) {
                        $path = "https://placehold.co/600x400/1a1a1a/ffffff?text=MGL";
                    }
                ?>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="card blog-card h-100 shadow">
                            <img src="<?= $path ?>" class="blog-img" alt="<?= $art['judul'] ?>">
                            <div class="blog-body">
                                <small class="text-primary fw-bold"><?= date('d M Y', strtotime($art['created_at'])) ?></small>
                                <a href="baca.php?slug=<?= $art['slug'] ?>" class="blog-title d-block my-2"><?= $art['judul'] ?></a>
                                <p class="small text-secondary mb-0"><?= substr(strip_tags($art['konten']), 0, 90) ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <footer class="py-5 bg-black border-top border-secondary border-opacity-25 mt-5">
        <div class="container text-center py-4">
            <h3 class="fw-bold mb-4">STICKER<span class="text-primary">MGL</span></h3>
            <p class="text-secondary small mb-5 mx-auto" style="max-width: 600px;">Kami adalah bengkel spesialis sticker profesional. Mengedepankan kualitas pengerjaan detail dan kepuasan pelanggan di setiap proyek kami.</p>
            <div class="d-flex justify-content-center gap-4 mb-5 fs-4">
                <a href="#" class="text-white opacity-50"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="text-white opacity-50"><i class="fa-brands fa-facebook"></i></a>
                <a href="https://wa.me/62895333029272" class="text-white opacity-50"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
            <div class="text-secondary small opacity-50">
                &copy; <?= date('Y') ?> STICKER MGL SPECIALIST. SELURUH HAK CIPTA DILINDUNGI.
            </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {});
        AOS.init({
            duration: 1000,
            once: true
        });

        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.navbar').css('padding', '10px 0').addClass('shadow-lg');
            } else {
                $('.navbar').css('padding', '15px 0').removeClass('shadow-lg');
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>