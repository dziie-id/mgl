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
    <title><?= $art['judul'] ?> | MGL Sticker</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/img/favicon.png">
    <meta name="description" content="<?= $art['meta_desc'] ?>">
    <meta name="keywords" content="<?= $art['keyword'] ?>">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
    
    <style>
        :root { --primary: #0066ff; --dark: #0b0b0b; --dark-soft: #161616; }
        body { background-color: var(--dark); color: #ffffff; font-family: 'Poppins', sans-serif; padding-top: 100px; }
        
        .breadcrumb { background: transparent; padding: 0; margin-bottom: 0; }
        .breadcrumb-item + .breadcrumb-item::before { content: ">"; color: #444; font-size: 10px; padding: 0 10px; }
        .breadcrumb-item a { color: var(--primary); text-decoration: none; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .breadcrumb-item.active { color: #666; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }

        .btn-close-article { 
            width: 40px; height: 40px; background: #222; border: 1px solid #333; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; transition: 0.3s; text-decoration: none;
        }
        .btn-close-article:hover { background: #ff0000; border-color: #ff0000; color: white; transform: rotate(90deg); }

        .article-card { background: var(--dark-soft); border-radius: 20px; border: 1px solid #222; overflow: hidden; margin-bottom: 50px; }
        .main-img { width: 100%; max-height: 550px; object-fit: cover; border-bottom: 1px solid #333; }

        .content-body { padding: 50px 70px; }
        @media (max-width: 768px) { .content-body { padding: 30px 20px; } }

        .judul-utama { font-family: 'Montserrat', sans-serif; font-weight: 800; line-height: 1.2; margin-bottom: 20px; color: #fff; text-transform: uppercase; }
        
        .meta-info { display: flex; gap: 25px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #222; }
        .meta-item { color: #ffffff !important; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; }
        .meta-item i { color: var(--primary); margin-right: 8px; font-size: 1rem; }
        
        .text-artikel { 
            font-size: 1.1rem; 
            color: #ffffff !important;
            line-height: 1.9; 
        }
        
        .text-artikel h2, .text-artikel h3 { 
            color: var(--primary); font-weight: 800; margin-top: 45px; margin-bottom: 20px; 
            font-family: 'Montserrat', sans-serif; text-transform: uppercase;
        }
        .text-artikel p { margin-bottom: 25px; opacity: 0.95; }
        .text-artikel ul, .text-artikel ol { margin-bottom: 30px; padding-left: 20px; color: #eee; }
        .text-artikel li { margin-bottom: 12px; }

        .sidebar-box { background: var(--dark-soft); border: 1px solid #222; border-radius: 15px; padding: 25px; position: sticky; top: 120px; }
        .hover-link:hover { color: var(--primary) !important; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">BERANDA</a></li>
                <li class="breadcrumb-item"><a href="artikel.php">EDUKASI</a></li>
                <li class="breadcrumb-item active d-none d-md-inline-block" aria-current="page">BACA ARTIKEL</li>
            </ol>
        </nav>
        <a href="artikel.php" class="btn-close-article shadow" title="Kembali">
            <i class="fa-solid fa-xmark"></i>
        </a>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="article-card shadow-lg">
                <img src="<?= $path ?>" class="main-img" alt="<?= $art['judul'] ?>">
                
                <div class="content-body">
                    <h1 class="judul-utama display-5"><?= $art['judul'] ?></h1>
                    
                    <div class="meta-info">
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar-check"></i> 
                            <?= date('d F Y', strtotime($art['created_at'])) ?>
                        </div>
                        <div class="meta-item text-uppercase">
                            <i class="fa-solid fa-user-shield"></i> 
                            ADMIN MGL
                        </div>
                    </div>

                    <div class="text-artikel">
                        <?= $art['konten'] ?>
                    </div>

                    <div class="mt-5 p-5 rounded-4 bg-black text-center border border-primary border-opacity-25">
                        <h4 class="text-white fw-bold mb-3 text-uppercase">Konsultasi Gratis Sekarang!</h4>
                        <p class="text-white-50 mb-4">Punya pertanyaan seputar branding atau ingin tanya harga? Hubungi tim MGL Sticker.</p>
                        <a href="https://wa.me/6281399252950" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-lg">
                            <i class="fa-brands fa-whatsapp me-2"></i> CHAT VIA WHATSAPP
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-box shadow">
                <h6 class="fw-bold text-white mb-4 text-uppercase small border-start border-primary border-4 ps-3" style="letter-spacing:1px;">Rekomendasi Lainnya</h6>
                <?php 
                $related = $pdo->query("SELECT judul, slug, thumbnail FROM articles WHERE slug != '$slug' ORDER BY RAND() LIMIT 4")->fetchAll();
                foreach($related as $r):
                    $r_path = "uploads/articles/" . $r['thumbnail'];
                    if (!file_exists($r_path) || empty($r['thumbnail'])) { $r_path = "uploads/gallery/" . $r['thumbnail']; }
                    if (!file_exists($r_path) || empty($r['thumbnail'])) { $r_path = "https://placehold.co/100x100/111/fff?text=MGL"; }
                ?>
                <div class="d-flex align-items-center mb-4">
                    <img src="<?= $r_path ?>" class="rounded-3 shadow" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #333;">
                    <div class="ms-3">
                        <a href="baca.php?slug=<?= $r['slug'] ?>" class="text-decoration-none text-white small fw-bold d-block mb-1 hover-link" style="line-height: 1.4;"><?= $r['judul'] ?></a>
                        <small class="text-primary fw-bold" style="font-size: 9px; text-transform: uppercase;">Wawasan Sticker</small>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <hr class="opacity-25 my-4">
                
                <div class="p-3 rounded-4 bg-dark text-center border border-secondary border-opacity-10">
                    <p class="small text-white-50 mb-0">MGL Sticker Specialist: Wrapping, Branding, & Sandblast Premium.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="py-5 bg-black border-top border-secondary border-opacity-25 text-center mt-5">
    <div class="container small text-secondary opacity-50">
        &copy; <?= date('Y') ?> STICKER MGL.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>