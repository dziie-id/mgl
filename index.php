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
    <title>Sticker MGL | Spesialis Branding Mobil & Sticker Premium</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/img/favicon.png">
    <meta name="description" content="Ahlinya jasa branding mobil, wrapping full body, dan pasang sticker kaca film di Tangerang. Menggunakan bahan premium (Oracal/3M) presisi & bergaransi.">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfessionalService",
      "name": "MGL Sticker",
      "url": "https://mglstiker.com",
      "telephone": "+6281399252950",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Jl. Saji 1 No.74, Ciledug",
        "addressLocality": "Tangerang",
        "postalCode": "15151",
        "addressCountry": "ID"
      }
    }
    </script>

    <style>
        :root { --primary: #0066ff; --dark: #0b0b0b; --dark-soft: #161616; }
        body { background-color: var(--dark); color: #ffffff; font-family: 'Poppins', sans-serif; }
        
        .navbar { background: rgba(11, 11, 11, 0.95); backdrop-filter: blur(15px); border-bottom: 1px solid #222; }
        
        .hero { 
            min-height: 100vh; 
            background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.8)), url('/uploads/gallery/balut-mobil-wrapping-car-sticker-mgl-jakarta-1-1770834169.webp?auto=format&fit=crop&w=1920&q=80'); 
            background-size: cover; background-position: center;
            display: flex; align-items: center;
            padding-top: 80px;
        }

        .hero h1 { font-size: 4.5rem; font-weight: 800; line-height: 1; margin-bottom: 20px; }
        .hero p { color: #eee; max-width: 650px; font-size: 1.1rem; margin-bottom: 35px; }

        .hero-btns { display: flex; gap: 15px; }
        .hero-btns .btn { padding: 16px 35px; font-weight: 700; border-radius: 50px; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px; }

        @media (max-width: 768px) {
            .hero { text-align: center; justify-content: center; }
            .hero h1 { font-size: 2.8rem; }
            .hero p { margin: 0 auto 30px auto; font-size: 1rem; }
            /* Tombol jadi numpuk di HP biar gak penyok */
            .hero-btns { flex-direction: column; width: 100%; max-width: 300px; margin: 0 auto; }
            .hero-btns .btn { width: 100%; }
        }

        .section-padding { padding: 80px 0; }
        .portfolio-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; }
        .portfolio-item { position: relative; overflow: hidden; height: 240px; border-radius: 12px; border: 1px solid #222; }
        
        @media (max-width: 992px) {
            .portfolio-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .portfolio-item { height: 180px; }
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="hero">
    <div class="container">
        <div data-aos="fade-right">
            <h5 class="text-primary fw-bold mb-3" style="letter-spacing: 4px; font-size: 0.8rem;">SPECIALIST WRAPPING & BRANDING</h5>
            <h1>SENI BRANDING<br><span class="text-primary">KENDARAAN</span> ANDA.</h1>
            <p>Ubah kendaraan operasional atau pribadi Anda menjadi media promosi berjalan yang elegan. Kami menjamin hasil presisi dengan material kelas dunia.</p>
            
            <div class="hero-btns mt-4">
                <a href="<?= BASE_URL ?>galeri.php" class="btn btn-primary shadow-lg">
                    <i class="fa-solid fa-images me-2"></i> Lihat Hasil Kerja
                </a>
                <a href="https://wa.me/6281399252950" class="btn btn-outline-light">
                    <i class="fa-brands fa-whatsapp me-2"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section-padding bg-black" id="layanan">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 3px;">KEAHLIAN KAMI</h6>
            <h2 class="display-6 fw-bold">APA YANG KAMI KERJAKAN?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card bg-dark border-secondary border-opacity-25 p-4 h-100 text-center shadow-sm">
                    <i class="fa-solid fa-car-side fa-3x text-primary mb-4"></i>
                    <h4 class="fw-bold mb-3">BRANDING MOBIL</h4>
                    <p class="small text-white-50">Wrapping full body atau partisi menggunakan material High-Grade (3M/Oracal) untuk promosi bisnis Anda.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card bg-dark border-secondary border-opacity-25 p-4 h-100 text-center shadow-sm">
                    <i class="fa-solid fa-building fa-3x text-primary mb-4"></i>
                    <h4 class="fw-bold mb-3">STICKER KACA</h4>
                    <p class="small text-white-50">Instalasi sandblast motif/polos, kaca film gedung, dan cutting logo untuk estetika ruang kantor.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card bg-dark border-secondary border-opacity-25 p-4 h-100 text-center shadow-sm">
                    <i class="fa-solid fa-scissors fa-3x text-primary mb-4"></i>
                    <h4 class="fw-bold mb-3">CUTTING CUSTOM</h4>
                    <p class="small text-white-50">Produksi sticker cutting presisi tinggi untuk branding produk, komunitas, hingga interior custom.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container mb-5 text-center" data-aos="fade-up">
        <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 3px;">Hasil Kerja Nyata</h6>
        <h2 class="display-6 fw-bold">PORTOFOLIO TERBARU</h2>
    </div>
    <div class="portfolio-grid container">
        <?php foreach($random_gal as $img): ?>
        <div class="portfolio-item" data-aos="zoom-in">
            <a href="<?= BASE_URL ?>uploads/gallery/<?= $img['file_name'] ?>" data-fancybox="gallery" data-caption="<?= $img['alt_text'] ?>">
                <img src="<?= BASE_URL ?>uploads/gallery/<?= $img['file_name'] ?>" alt="<?= $img['alt_text'] ?>" loading="lazy">
                <div class="position-absolute inset-0 d-flex align-items-center justify-content-center opacity-0 hover-opacity-100 bg-dark bg-opacity-50 transition-all">
                    <i class="fa-solid fa-magnifying-glass-plus fa-2x text-white"></i>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-padding bg-black">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 3px;">Edukasi & Tips</h6>
            <h2 class="display-6 fw-bold">ARTIKEL TERBARU</h2>
        </div>
        <div class="row g-4">
            <?php foreach($random_art as $art): 
                $thumb = $art['thumbnail'];
                $path = "uploads/articles/" . $thumb;
                if (!file_exists($path) || empty($thumb)) { $path = "uploads/gallery/" . $thumb; }
            ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="card bg-dark border-0 rounded-4 overflow-hidden h-100 shadow">
                    <img src="<?= $path ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-4">
                        <small class="text-primary fw-bold"><?= date('d M Y', strtotime($art['created_at'])) ?></small>
                        <a href="<?= BASE_URL ?>baca.php?slug=<?= $art['slug'] ?>" class="text-white text-decoration-none d-block my-2 h5 fw-bold hover-primary"><?= $art['judul'] ?></a>
                        <p class="small text-white-50 mb-0"><?= substr(strip_tags($art['konten']), 0, 100) ?>...</p>
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
        <p class="text-white-50 small mb-5 mx-auto" style="max-width: 600px;">Kami adalah bengkel spesialis sticker profesional di Ciledug, Tangerang. Mengedepankan kualitas pengerjaan detail dan material premium untuk kepuasan Anda.</p>
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
    AOS.init({ duration: 1000, once: true });
</script>
</body>
</html>