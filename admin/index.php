<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['del_gallery'])) {
    $id = $_GET['del_gallery'];

    $stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    if ($img) {
        $file_path = "../uploads/gallery/" . $img['file_name'];

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $stmt_del = $pdo->prepare("DELETE FROM galleries WHERE id = ?");
        $stmt_del->execute([$id]);

        $_SESSION['success'] = "Foto dan file fisik berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Data tidak ditemukan!";
    }
    header("Location: index.php");
    exit;
}

$total_gambar = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
$total_artikel = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$total_survey = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();
$stmt = $pdo->query("SELECT * FROM galleries ORDER BY id DESC LIMIT 16");
$recent_images = $stmt->fetchAll();

include 'header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card bg-primary text-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="flex-grow-1">
                    <h6 class="small text-uppercase opacity-75 fw-bold">Total Portofolio</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_gambar ?></h2>
                </div>
                <i class="fa-solid fa-images fa-3x opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card bg-success text-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="flex-grow-1">
                    <h6 class="small text-uppercase opacity-75 fw-bold">Artikel SEO</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_artikel ?></h2>
                </div>
                <i class="fa-solid fa-newspaper fa-3x opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card bg-warning text-dark border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="flex-grow-1">
                    <h6 class="small text-uppercase opacity-75 fw-bold">Survey Lapangan</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_survey ?></h2>
                </div>
                <i class="fa-solid fa-clipboard-check fa-3x opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i> Unggahan Terakhir (Baked)</h6>
        <a href="upload.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Upload Baru
        </a>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <?php if (count($recent_images) > 0): ?>
                <?php foreach ($recent_images as $img): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden position-relative gallery-card">
                            <a href="../uploads/gallery/<?= $img['file_name'] ?>" data-fancybox="gallery" data-caption="<?= $img['alt_text'] ?>">
                                <img src="../uploads/gallery/<?= $img['file_name'] ?>" class="card-img-top" style="height: 160px; object-fit: cover;">
                            </a>

                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-dark opacity-75 fw-normal" style="font-size: 8px;">WebP</span>
                            </div>

                            <div class="position-absolute top-0 end-0 m-2">
                                <a href="?del_gallery=<?= $img['id'] ?>"
                                    class="btn btn-danger btn-sm rounded-circle shadow btn-hapus"
                                    title="Hapus Foto">
                                    <i class="fa-solid fa-trash-can" style="font-size: 10px;"></i>
                                </a>
                            </div>

                            <div class="p-2 text-center bg-body-tertiary">
                                <p class="text-truncate mb-0 small opacity-75" title="<?= $img['file_name'] ?>" style="font-size: 10px;">
                                    <?= $img['file_name'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-folder-open fa-3x opacity-25 mb-3"></i>
                    <p class="text-muted">Belum ada portofolio. Silakan upload gambar pertama Anda.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .gallery-card {
        transition: all 0.3s ease;
    }

    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
    }

    .gallery-card .btn-danger {
        opacity: 0;
        transition: 0.3s;
    }

    .gallery-card:hover .btn-danger {
        opacity: 1;
    }
</style>

<?php include 'footer.php'; ?>