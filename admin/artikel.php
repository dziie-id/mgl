<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $stmt = $pdo->prepare("SELECT thumbnail FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $art = $stmt->fetch();

    if ($art) {
        $path = "../uploads/articles/" . $art['thumbnail'];
        if (strpos($art['thumbnail'], 'blog-') !== false) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $stmt_del = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt_del->execute([$id]);
        $_SESSION['success'] = "Artikel berhasil dihapus!";
    }
    header("Location: artikel.php");
    exit;
}

include 'header.php';
$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-primary">Artikel SEO</h4>
        <p class="text-muted small mb-0">Kelola konten edukasi dan promosi untuk pencarian Google.</p>
    </div>
    <a href="artikel-tambah.php" class="btn btn-primary rounded-pill shadow">
        <i class="fa-solid fa-pen-to-square me-1"></i> Tulis Artikel
    </a>
</div>

<div class="row g-3">
    <?php if (count($articles) > 0): ?>
        <?php foreach ($articles as $a):
            $thumb = $a['thumbnail'];
            $path = "../uploads/articles/" . $thumb;
            if (!file_exists($path) || empty($thumb)) {
                $path = "../uploads/gallery/" . $thumb;
            }
            if (!file_exists($path) || empty($thumb)) {
                $path = "https://placehold.co/400x300?text=No+Image";
            }
        ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="row g-0 h-100">
                        <div class="col-4">
                            <img src="<?= $path ?>" class="img-fluid h-100 w-100" style="object-fit: cover; min-height: 120px;">
                        </div>
                        <div class="col-8 p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1 text-truncate" title="<?= $a['judul'] ?>"><?= $a['judul'] ?></h6>
                                <small class="text-muted d-block mb-2"><i class="fa-regular fa-calendar-check me-1"></i> <?= date('d M Y', strtotime($a['created_at'])) ?></small>
                                <p class="small text-muted mb-0 text-truncate-2" style="font-size: 11px;"><?= $a['meta_desc'] ?></p>
                            </div>
                            <div class="text-end mt-2">
                                <a href="artikel-edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                    <i class="fa-solid fa-edit me-1"></i>Edit
                                </a>
                                <a href="?del=<?= $a['id'] ?>"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 border border-dashed rounded">
            <i class="fa-solid fa-newspaper fa-3x mb-3 opacity-25"></i>
            <p class="text-muted">Belum ada artikel yang diterbitkan.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php include 'footer.php'; ?>