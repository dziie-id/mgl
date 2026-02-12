<?php 
include '../includes/db.php';
include 'functions.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }

// Ambil data artikel berdasarkan ID
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) { header("Location: artikel.php"); exit; }

// --- LOGIKA UPDATE ARTIKEL ---
if (isset($_POST['update_artikel'])) {
    $judul = $_POST['judul'];
    $slug  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    $konten = $_POST['konten'];
    $meta_desc = $_POST['meta_desc'];
    $keyword = $_POST['keyword'];
    $thumb_name = $_POST['selected_image']; // Nama file dari picker (hidden input)

    // Jika ada upload file baru (menimpa pilihan picker)
    if (!empty($_FILES['thumbnail']['name'])) {
        $tmp_name = $_FILES['thumbnail']['tmp_name'];
        $thumb_name = "blog-" . time() . ".webp";
        $target_path = "../uploads/articles/" . $thumb_name;
        
        $image_res = resize_crop_image($tmp_name, 800, 500);
        imagewebp($image_res, $target_path, 80);
        imagedestroy($image_res);
    }

    $stmt = $pdo->prepare("UPDATE articles SET judul=?, slug=?, konten=?, thumbnail=?, meta_desc=?, keyword=? WHERE id=?");
    $stmt->execute([$judul, $slug, $konten, $thumb_name, $meta_desc, $keyword, $id]);
    
    $_SESSION['success'] = "Artikel berhasil diperbarui!";
    header("Location: artikel.php"); exit;
}

include 'header.php'; 
$gallery = $pdo->query("SELECT file_name FROM galleries ORDER BY id DESC LIMIT 32")->fetchAll();
?>

<style>
    /* FIX SUMMERNOTE DARK MODE - KONTRAST TINGGI */
    [data-bs-theme="dark"] .note-editor { background-color: #121212 !important; border-color: #333 !important; }
    [data-bs-theme="dark"] .note-editable { background-color: #121212 !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .note-toolbar { background-color: #1a1a1a !important; border-bottom: 1px solid #333 !important; }
    [data-bs-theme="dark"] .note-btn { background-color: #222 !important; border-color: #444 !important; color: #fff !important; }
    [data-bs-theme="dark"] .note-dropdown-menu { background-color: #222 !important; color: #fff !important; border: 1px solid #444; }
    
    /* STYLE PICKER */
    .img-picker-box { transition: 0.2s; border: 2px solid transparent; border-radius: 8px; cursor: pointer; height: 100px; overflow: hidden; }
    .img-picker-box:hover { border-color: var(--primary); transform: scale(1.05); }
    .meta-status { font-size: 10px; font-weight: bold; }
</style>

<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item small"><a href="artikel.php">Daftar Artikel</a></li>
    <li class="breadcrumb-item small active">Edit Artikel</li>
  </ol>
</nav>

<form method="POST" enctype="multipart/form-data" id="formArtikel">
    <div class="row g-4">
        <!-- KIRI: KONTEN -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-primary">Judul Artikel</label>
                        <input type="text" name="judul" id="post_title" class="form-control form-control-lg border-primary" value="<?= htmlspecialchars($art['judul']) ?>" required autocomplete="off">
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold mb-2 text-primary">Isi Artikel</label>
                        <textarea name="konten" class="summernote" id="post_content"><?= $art['konten'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN: SEO & THUMBNAIL -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold py-3 small">THUMBNAIL & SEO</div>
                <div class="card-body p-4">
                    
                    <!-- Preview Thumbnail -->
                    <div id="preview-area" class="mb-3 text-center p-2 border rounded bg-dark bg-opacity-25">
                        <label class="x-small d-block mb-2 fw-bold text-primary">THUMBNAIL SAAT INI</label>
                        <?php 
                            $thumb = $art['thumbnail'];
                            $path = "../uploads/articles/" . $thumb;
                            if (!file_exists($path) || empty($thumb)) { $path = "../uploads/gallery/" . $thumb; }
                        ?>
                        <img id="img-chosen" src="<?= $path ?>" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                        <input type="hidden" name="selected_image" id="input-chosen" value="<?= $art['thumbnail'] ?>">
                        <button type="button" class="btn btn-sm btn-link text-danger mt-2 text-decoration-none fw-bold" id="btn-change-img">Ganti Gambar</button>
                    </div>

                    <div id="upload-instruction" class="d-none d-grid gap-2 mb-4">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#galleryModal">
                            <i class="fa-solid fa-images me-2"></i>Pilih dari Galeri
                        </button>
                        <div class="text-center small text-muted my-1 text-uppercase">Atau Upload:</div>
                        <input type="file" name="thumbnail" class="form-control form-control-sm" accept="image/*">
                    </div>

                    <hr class="opacity-25 my-4">

                    <!-- SEO SECTION -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small fw-bold text-primary">Meta Description</label>
                            <span id="meta-badge" class="meta-status text-success small">OK</span>
                        </div>
                        <textarea name="meta_desc" id="meta_desc" class="form-control small" rows="4"><?= htmlspecialchars($art['meta_desc']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-primary mb-1">Keywords (Ciamik)</label>
                        <textarea name="keyword" id="keyword" class="form-control small" rows="4"><?= htmlspecialchars($art['keyword']) ?></textarea>
                    </div>

                    <button type="submit" name="update_artikel" class="btn btn-primary w-100 shadow py-3 fw-bold">
                        <i class="fa-solid fa-save me-2"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- MODAL MEDIA PICKER -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-dark">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">Ganti Portofolio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 500px; overflow-y: auto;">
                <div class="row g-2">
                    <?php foreach($gallery as $g): ?>
                    <div class="col-4 col-md-3">
                        <div class="img-picker-box">
                            <img src="../uploads/gallery/<?= $g['file_name'] ?>" 
                                 class="img-fluid w-100 h-100 img-click" 
                                 style="object-fit: cover;"
                                 data-filename="<?= $g['file_name'] ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// --- JURUS ANTI BLOCK MODAL ---
function cleanupModal() {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
    document.body.style.paddingRight = '0';
    $('.modal-backdrop').remove();
}

// Logic Media Picker
document.querySelectorAll('.img-click').forEach(img => {
    img.addEventListener('click', function() {
        const filename = this.getAttribute('data-filename');
        document.getElementById('img-chosen').src = "../uploads/gallery/" + filename;
        document.getElementById('input-chosen').value = filename;
        document.getElementById('preview-area').classList.remove('d-none');
        document.getElementById('upload-instruction').classList.add('d-none');

        const modalEl = document.getElementById('galleryModal');
        const modalIns = bootstrap.Modal.getInstance(modalEl);
        if (modalIns) modalIns.hide();
        setTimeout(cleanupModal, 400); 
    });
});

document.getElementById('btn-change-img').addEventListener('click', function() {
    document.getElementById('preview-area').classList.add('d-none');
    document.getElementById('upload-instruction').classList.remove('d-none');
    // Bersihkan input hidden agar sistem tahu abang mau ganti
    document.getElementById('input-chosen').value = '';
});

// --- JURUS SMART SEO (RE-GENERATE) ---
function updateSEO() {
    const title = document.getElementById('post_title').value;
    const content = $('#post_content').summernote('code');
    const plainText = content.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();

    // A. Meta Description
    if (plainText.length > 5) {
        document.getElementById('meta_desc').value = plainText.substring(0, 160);
        document.getElementById('meta-badge').innerText = "AUTO OK";
    }

    // B. Keywords Ciamik
    const dictionary = ['branding', 'wrapping', 'sticker', 'stiker', 'mobil', 'kaca', 'sandblast', 'cutting', 'premium', 'jakarta', 'tangerang', 'ciledug', 'custom', 'decal', 'livery', 'iklan', 'promosi', 'bus', 'truk', 'kantor', 'film', 'kantor', 'ruko', 'gedung'];
    
    let sourceText = (title + " " + plainText.substring(0, 300)).toLowerCase();
    let foundKeywords = dictionary.filter(word => sourceText.includes(word));
    let titleWords = title.toLowerCase().replace(/[^a-zA-Z ]/g, '').split(' ').filter(w => w.length > 3);
    
    let finalKeywords = [...new Set([...foundKeywords, ...titleWords])];
    finalKeywords.push('sticker mgl', 'specialist branding');

    document.getElementById('keyword').value = finalKeywords.join(', ');
}

// Trigger SEO saat ngetik
document.getElementById('post_title').addEventListener('keyup', updateSEO);
$('.summernote').on('summernote.keyup', function() {
    updateSEO();
});

// Pastikan modal tertutup bersih
$('#galleryModal').on('hidden.bs.modal', cleanupModal);
</script>

<?php include 'footer.php'; ?>