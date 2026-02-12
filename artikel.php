<?php 
include 'includes/db.php'; 
$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edukasi & Tips | Sticker MGL</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style-front.css">
    
    <style>
        :root { --primary: #0066ff; --dark: #0b0b0b; --dark-soft: #161616; }
        body { background-color: var(--dark); color: #fff; font-family: 'Poppins', sans-serif; padding-top: 100px; }
        
        .blog-card { 
            background: var(--dark-soft); 
            border: 1px solid #222; 
            border-radius: 15px; 
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            overflow: hidden; 
            height: 100%;
            position: relative;
        }
        
        .blog-card:hover { 
            border-color: var(--primary); 
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 102, 255, 0.1);
        }

        .blog-img-wrapper { height: 230px; overflow: hidden; position: relative; }
        .blog-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .blog-card:hover .blog-img-wrapper img { transform: scale(1.1); filter: brightness(0.7); }

        .blog-body { padding: 25px; }
        .blog-date { font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px; }
        .blog-title { font-family: 'Montserrat', sans-serif; font-weight: 800; color: white; text-decoration: none; font-size: 1.25rem; line-height: 1.3; }
        .blog-excerpt { color: #888; font-size: 0.9rem; margin-top: 12px; line-height: 1.6; }
        
        .category-badge { position: absolute; top: 15px; left: 15px; background: var(--primary); color: white; padding: 4px 12px; font-size: 10px; font-weight: 800; border-radius: 4px; z-index: 2; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="container py-5">
    <div class="text-center mb-5">
        <h5 class="text-primary fw-bold text-uppercase small" style="letter-spacing: 3px;">Wawasan & Pengetahuan</h5>
        <h2 class="display-6 fw-bold text-uppercase" style="font-family: Montserrat;">Pusat Edukasi Sticker</h2>
    </div>

    <div class="row g-4">
        <?php foreach($articles as $art): 
            $thumb = $art['thumbnail'];
            $path = "uploads/articles/" . $thumb;
            if (!file_exists($path) || empty($thumb)) { $path = "uploads/gallery/" . $thumb; }
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card blog-card shadow">
                <div class="blog-img-wrapper">
                    <span class="category-badge text-uppercase">Tips & News</span>
                    <img src="<?= $path ?>" alt="<?= $art['judul'] ?>">
                </div>
                <div class="blog-body">
                    <span class="blog-date"><?= date('d M Y', strtotime($art['created_at'])) ?></span>
                    <a href="baca.php?slug=<?= $art['slug'] ?>" class="blog-title stretched-link">
                        <?= $art['judul'] ?>
                    </a>
                    <p class="blog-excerpt">
                        <?= substr(strip_tags($art['konten']), 0, 100) ?>...
                    </p>
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