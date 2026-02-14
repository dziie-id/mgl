<?php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Jasa Pasang Sticker & Branding Mobil Profesional | Sticker MGL</title>
    <meta name="description" content="Daftar layanan kami: Jasa branding mobil operasional, wrapping sticker mobil, pasang kaca film gedung, dan cutting sticker custom. Hubungi kami untuk penawaran terbaik.">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/img/favicon.png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">

    <style>
        .service-detail-card {
            background: var(--dark-soft);
            border: 1px solid #333;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
        }

        .service-detail-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.2);
        }

        .service-img-wrapper {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .service-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        .service-detail-card:hover .service-img-wrapper img {
            transform: scale(1.1);
        }

        .service-icon-float {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        .service-body {
            padding: 30px 25px;
        }

        .text-desc {
            color: #d1d1d1 !important;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .service-list li {
            margin-bottom: 12px;
            font-size: 0.9rem;
            color: #ccc;
            display: flex;
            align-items: start;
        }

        .service-list li i {
            color: var(--primary);
            margin-right: 10px;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container py-5 text-center">
        <h5 class="text-primary fw-bold small mb-2" style="letter-spacing: 3px;">OUR EXPERTISE</h5>
        <h2 class="display-5 fw-bold text-uppercase text-white">LAYANAN PROFESIONAL</h2>
        <p class="text-white-50 mx-auto mt-3 fs-5" style="max-width: 700px;">
            Kami menyediakan solusi lengkap untuk kebutuhan visual kendaraan dan bangunan Anda.
        </p>
    </div>
    <div class="container pb-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="service-detail-card">
                    <div class="service-img-wrapper">
                        <img src="https://mglstiker.com/uploads/gallery/branding-mobil-sticker-mgl-jakarta-8-1770928313.webp" alt="Branding Mobil">
                        <div class="service-icon-float">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4 class="fw-bold mb-3 text-uppercase text-white">Branding Mobil</h4>
                        <p class="text-desc">Ubah kendaraan operasional menjadi media promosi berjalan yang efektif dan tahan lama.</p>
                        <ul class="list-unstyled service-list">
                            <li><i class="fa-solid fa-check"></i> Full Body Wrapping</li>
                            <li><i class="fa-solid fa-check"></i> Partial Branding</li>
                            <li><i class="fa-solid fa-check"></i> Material Oracal / 3M</li>
                            <li><i class="fa-solid fa-check"></i> Desain Custom Perusahaan</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="service-detail-card">
                    <div class="service-img-wrapper">
                        <img src="https://mglstiker.com/uploads/gallery/branding-mobil-sticker-mgl-jakarta-4-1770928311.webp" alt="Sticker Kaca">
                        <div class="service-icon-float">
                            <i class="fa-solid fa-building"></i>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4 class="fw-bold mb-3 text-uppercase text-white">Sticker Kaca</h4>
                        <p class="text-desc">Solusi privasi dan estetika untuk kaca kantor, ruko, maupun hunian pribadi.</p>
                        <ul class="list-unstyled service-list">
                            <li><i class="fa-solid fa-check"></i> Sandblast Polos / Motif</li>
                            <li><i class="fa-solid fa-check"></i> Kaca Film Tolak Panas</li>
                            <li><i class="fa-solid fa-check"></i> One Way Vision</li>
                            <li><i class="fa-solid fa-check"></i> Cutting Sticker Logo</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="service-detail-card">
                    <div class="service-img-wrapper">
                        <img src="https://mglstiker.com/uploads/gallery/branding-mobil-sticker-mgl-jakarta-11-1770928314.webp" alt="Cutting Sticker">
                        <div class="service-icon-float">
                            <i class="fa-solid fa-scissors"></i>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4 class="fw-bold mb-3 text-uppercase text-white">Custom Cutting</h4>
                        <p class="text-desc">Pembuatan sticker cutting presisi tinggi untuk komunitas atau kebutuhan dekorasi.</p>
                        <ul class="list-unstyled service-list">
                            <li><i class="fa-solid fa-check"></i> Sticker Komunitas</li>
                            <li><i class="fa-solid fa-check"></i> Label Produk (Die Cut)</li>
                            <li><i class="fa-solid fa-check"></i> Striping Variasi Motor/Mobil</li>
                            <li><i class="fa-solid fa-check"></i> Bahan Reflective / Hologram</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="service-detail-card">
                    <div class="service-img-wrapper">
                        <img src="https://mglstiker.com/uploads/gallery/branding-mobil-sticker-mgl-jakarta-16-1770928316.webp" alt="Premium Wrapping">
                        <div class="service-icon-float">
                            <i class="fa-solid fa-spray-can"></i>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4 class="fw-bold mb-3 text-uppercase text-white">Premium Wrapping</h4>
                        <p class="text-desc">Ganti warna kendaraan tanpa cat ulang. Melindungi cat asli dari goresan dan sinar UV.</p>
                        <ul class="list-unstyled service-list">
                            <li><i class="fa-solid fa-check"></i> Color Change (Doff/Glossy)</li>
                            <li><i class="fa-solid fa-check"></i> Carbon / Chrome Wrap</li>
                            <li><i class="fa-solid fa-check"></i> Roof & Hood Wrap</li>
                            <li><i class="fa-solid fa-check"></i> Paint Protection Film (PPF)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="service-detail-card">
                    <div class="service-img-wrapper">
                        <img src="https://mglstiker.com/uploads/gallery/branding-mobil-sticker-mgl-jakarta-17-1770928316.webp" alt="Decal Printing">
                        <div class="service-icon-float">
                            <i class="fa-solid fa-print"></i>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4 class="fw-bold mb-3 text-uppercase text-white">Decal Printing</h4>
                        <p class="text-desc">Sticker full body dengan desain grafis rumit yang dicetak menggunakan mesin high-res.</p>
                        <ul class="list-unstyled service-list">
                            <li><i class="fa-solid fa-check"></i> Decal Motor Trail / Sport</li>
                            <li><i class="fa-solid fa-check"></i> Livery Mobil Balap</li>
                            <li><i class="fa-solid fa-check"></i> Laminasi Anti Gores</li>
                            <li><i class="fa-solid fa-check"></i> Desain Bebas (Custom)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="service-detail-card">
                    <div class="service-img-wrapper">
                        <img src="https://mglstiker.com/uploads/gallery/branding-mobil-sticker-mgl-jakarta-5-1770928312.webp" alt="Jasa Pasang">
                        <div class="service-icon-float">
                            <i class="fa-solid fa-tools"></i>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4 class="fw-bold mb-3 text-uppercase text-white">Jasa Pasang</h4>
                        <p class="text-desc">Hanya butuh tenaga ahli untuk pemasangan? Tim kami siap datang ke lokasi Anda.</p>
                        <ul class="list-unstyled service-list">
                            <li><i class="fa-solid fa-check"></i> Pemasangan di Tempat (Onsite)</li>
                            <li><i class="fa-solid fa-check"></i> Bongkar Sticker Lama</li>
                            <li><i class="fa-solid fa-check"></i> Cleaning Sisa Lem</li>
                            <li><i class="fa-solid fa-check"></i> Garansi Pemasangan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mb-5">
        <div class="bg-primary rounded-4 p-5 text-center shadow-lg position-relative overflow-hidden">
            <div class="position-relative z-1">
                <h2 class="fw-bold text-white mb-3">Bingung Memilih Layanan?</h2>
                <p class="text-white-50 mb-4 mx-auto" style="max-width: 600px;">Konsultasikan kebutuhan dan budget Anda dengan tim kami. Kami akan memberikan solusi terbaik untuk branding kendaraan Anda.</p>
                <a href="https://wa.me/62895333029272" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary shadow">
                    <i class="fa-brands fa-whatsapp me-2"></i> HUBUNGI KAMI SEKARANG
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>