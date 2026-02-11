<?php
include '../includes/db.php';
include 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) {
    header("Location: artikel.php");
    exit;
}

if (isset($_POST['update_artikel'])) {
    $judul = $_POST['judul'];
    $slug  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    $konten = $_POST['konten'];
    $meta_desc = $_POST['meta_desc'];
    $keyword = $_POST['keyword'];
    $thumb_name = $_POST['selected_image'];

    if (!empty($_FILES['thumbnail']['name'])) {
        $tmp_name = $_FILES['thumbnail']['tmp_name'];
        $thumb_name = "blog-" . time() . ".webp";
        $image_res = resize_crop_image($tmp_name, 800, 500);
        imagewebp($image_res, "../uploads/articles/" . $thumb_name, 80);
    }

    $stmt = $pdo->prepare("UPDATE articles SET judul=?, slug=?, konten=?, thumbnail=?, meta_desc=?, keyword=? WHERE id=?");
    $stmt->execute([$judul, $slug, $konten, $thumb_name, $meta_desc, $keyword, $id]);

    $_SESSION['success'] = "Artikel berhasil diperbarui!";
    header("Location: artikel.php");
    exit;
}

include 'header.php';
$gallery = $pdo->query("SELECT file_name FROM galleries ORDER BY id DESC LIMIT 24")->fetchAll();
?>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Judul Artikel</label>
                        <input type="text" name="judul" id="post_title" class="form-control form-control-lg border-primary" value="<?= $art['judul'] ?>" required>
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold mb-2">Isi Konten</label>
                        <textarea name="konten" id="post_content" class="summernote"><?= $art['konten'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold py-3">Thumbnail & SEO</div>
                <div class="card-body">
                    <div id="preview-area" class="mb-3 text-center p-2 border rounded bg-light">
                        <?php
                        $thumb = $art['thumbnail'];
                        $view_path = "../uploads/articles/" . $thumb;
                        if (!file_exists($view_path)) {
                            $view_path = "../uploads/gallery/" . $thumb;
                        }
                        ?>
                        <img id="img-chosen" src="<?= $view_path ?>" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                        <input type="hidden" name="selected_image" id="input-chosen" value="<?= $art['thumbnail'] ?>">
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#galleryModal">
                            Ganti dari Galeri
                        </button>
                        <input type="file" name="thumbnail" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="small fw-bold">Meta Description</label>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" onclick="generateSEO()">Auto-Fill</button>
                    </div>
                    <textarea name="meta_desc" id="meta_desc" class="form-control mb-3 small" rows="4"><?= $art['meta_desc'] ?></textarea>
                    <label class="small fw-bold mb-2">Keywords</label>
                    <input type="text" name="keyword" id="keyword" class="form-control mb-4" value="<?= $art['keyword'] ?>">
                    <button type="submit" name="update_artikel" class="btn btn-warning w-100 shadow py-2 fw-bold">SIMPAN PERUBAHAN</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold">Pilih dari Portfolio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-body-tertiary" style="max-height: 400px; overflow-y: auto;">
                <div class="row g-2">
                    <?php foreach ($gallery as $g): ?>
                        <div class="col-4 col-md-3">
                            <img src="../uploads/gallery/<?= $g['file_name'] ?>"
                                class="img-fluid rounded shadow-sm img-picker"
                                style="height: 100px; width:100%; object-fit: cover; cursor: pointer;"
                                data-filename="<?= $g['file_name'] ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.img-picker').forEach(img => {
        img.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            const imgChosen = document.getElementById('img-chosen');
            const inputChosen = document.getElementById('input-chosen');

            imgChosen.src = "../uploads/gallery/" + filename;
            inputChosen.value = filename;

            const modal = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
            modal.hide();
        });
    });

    function generateSEO() {
        const title = document.getElementById('post_title').value;
        const content = $('#post_content').summernote('code');
        const plainText = content.replace(/<[^>]*>/g, '');
        document.getElementById('meta_desc').value = plainText.trim().substring(0, 160);
        document.getElementById('keyword').value = title.toLowerCase().split(' ').join(', ');
    }
</script>

<?php include 'footer.php'; ?>