<?php 
include 'includes/db.php'; 

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ?");
$stmt->execute([$slug]);
$art = $stmt->fetch();

if (!$art) { header("Location: artikel.php"); exit; }

$thumb = $art['thumbnail'];
$path = "uploads/articles/" . $thumb;
if (!file_exists($path) || empty($thumb)) { $path = "uploads/gallery/" . $thumb; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $art['judul'] ?> | Sticker MGL</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
    
    <meta name="description" content="<?= $art['meta_desc'] ?>">
    <meta name="keywords" content="<?= $art['keyword'] ?>">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
    
    <style>
        :root { --primary: #0066ff; --dark: #0b0b0b; --dark-soft: #161616; }
        body { background-color: var(--dark); color: #ffffff; font-family: 'Poppins', sans-serif; padding-top: 100px; }
        
        .breadcrumb-item + .breadcrumb-item::before { content: ">"; color: #444; }
        .breadcrumb-item a { color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600; }
        .breadcrumb-item.active { color: #888; font-size: 0.85rem; }

        .btn-back-circle { 
            width: 35px; height: 35px; background: #222; border: 1px solid #333; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; transition: 0.3s; text-decoration: none;
        }
        .btn-back-circle:hover { background: var(--primary); border-color: var(--primary); color: white; }

        .article-card { background: var(--dark-soft); border-radius: 24px; border: 1px solid #222; overflow: hidden; margin-bottom: 50px; }
        .main-img { width: 100%; max-height: 500px; object-fit: cover; }

        .content-body { padding: 40px 60px; }
        @media (max-width: 768px) { .content-body { padding: 30px 20px; } }

        .judul-utama { font-family: 'Montserrat', sans-serif; font-weight: 800; line-height: 1.2; margin-bottom: 25px; color: #fff; }
        
        .text-artikel { 
            font-size: 1.05rem; 
            color: #eeeeee !important;
            line-height: 1.8; 
        }
        
        .text-artikel h2, .text-artikel h3 { 
            color: var(--primary); font-weight: 700; margin-top: 40px; margin-bottom: 20px; 
            font-family: 'Montserrat', sans-serif; text-transform: uppercase; letter-spacing: 1px;
        }
        .text-artikel p { margin-bottom: 20px; opacity: 0.9; }
        .text-artikel ul, .text-artikel ol { margin-bottom: 25px; padding-left: 20px; }
        .text-artikel li { margin-bottom: 10px; }

        .sidebar-box { background: var(--dark-soft); border: 1px solid #222; border-radius: 15px; padding: 25px; position: sticky; top: 120px; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">BERANDA</a></li>
                <li class="breadcrumb-item"><a href="artikel.php">EDUKASI</a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 150px;"><?= strtoupper($art['judul']) ?></li>
            </ol>
        </nav>
        <a href="artikel.php" class="btn-back-circle shadow" title="Kembali ke Daftar">
            <i class="fa-solid fa-xmark"></i>
        </a>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="article-card shadow-lg">
                <img src="<?= $path ?>" class="main-img" alt="<?= $art['judul'] ?>">
                
                <div class="content-body">
                    <h1 class="judul-utama display-5 text-uppercase"><?= $art['judul'] ?></h1>
                    
                    <div class="d-flex align-items-center mb-5 text-muted small border-bottom border-secondary pb-3">
                        <span class="me-4"><i class="fa-solid fa-calendar-day me-2 text-primary"></i> <?= date('d F Y', strtotime($art['created_at'])) ?></span>
                        <span><i class="fa-solid fa-user-pen me-2 text-primary"></i> ADMIN MGL</span>
                    </div>

                    <div class="text-artikel">
                        <?= $art['konten'] ?>
                    </div>

                    <div class="mt-5 p-4 rounded-4 bg-black text-center border border-primary border-opacity-25">
                        <h5 class="text-white fw-bold mb-3">Tanyakan Harga & Konsultasi Sekarang!</h5>
                        <p class="small text-white-50 mb-4">Tim kami siap memberikan solusi wrapping terbaik sesuai budget Anda.</p>
                        <a href="https://wa.me/6281399252950" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow">
                            <i class="fa-brands fa-whatsapp me-2"></i> HUBUNGI VIA WHATSAPP
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-box shadow">
                <h5 class="fw-bold text-white mb-4 text-uppercase small border-start border-primary border-4 ps-3">Artikel Terkait</h5>
                <?php 
                $related = $pdo->query("SELECT judul, slug, thumbnail FROM articles WHERE slug != '$slug' ORDER BY RAND() LIMIT 4")->fetchAll();
                foreach($related as $r):
                    $r_path = "uploads/articles/" . $r['thumbnail'];
                    if (!file_exists($r_path) || empty($r['thumbnail'])) { $r_path = "uploads/gallery/" . $r['thumbnail']; }
                    if (!file_exists($r_path) || empty($r['thumbnail'])) { $r_path = "https://placehold.co/100x100/1a1a1a/ffffff?text=MGL"; }
                ?>
                <div class="d-flex align-items-start mb-4">
                    <img src="<?= $r_path ?>" class="rounded-3 shadow-sm" style="width: 75px; height: 75px; object-fit: cover; border: 1px solid #333;">
                    <div class="ms-3">
                        <a href="baca.php?slug=<?= $r['slug'] ?>" class="text-decoration-none text-white small fw-bold d-block mb-1" style="line-height: 1.4;"><?= $r['judul'] ?></a>
                        <small class="text-primary" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Baca Sekarang</small>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <hr class="opacity-25 my-4">
                
                <div class="text-center p-3 rounded-4" style="background: rgba(0,102,255,0.05); border: 1px dashed rgba(0,102,255,0.3);">
                    <p class="small text-white-50 mb-0 italic">"Ahlinya Sticker & Branding Premium Jakarta & Tangerang"</p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="py-5 bg-black border-top border-secondary border-opacity-25 text-center mt-5">
    <div class="container small text-secondary">
        &copy; <?= date('Y') ?> STICKER MGL SPECIALIST INDONESIA. ALL RIGHTS RESERVED.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>