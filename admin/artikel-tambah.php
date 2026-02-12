<?php 
include '../includes/db.php';
include 'functions.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }

// --- LOGIKA SIMPAN ARTIKEL ---
if (isset($_POST['simpan_artikel'])) {
    $judul = $_POST['judul'];
    $slug  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    $konten = $_POST['konten'];
    $meta_desc = $_POST['meta_desc'];
    $keyword = $_POST['keyword'];
    $thumb_name = $_POST['selected_image']; 
    
    if (!empty($_FILES['thumbnail']['name'])) {
        $tmp_name = $_FILES['thumbnail']['tmp_name'];
        $thumb_name = "blog-" . time() . ".webp";
        $target_path = "../uploads/articles/" . $thumb_name;
        $image_res = resize_crop_image($tmp_name, 800, 500);
        imagewebp($image_res, $target_path, 80);
        imagedestroy($image_res);
    }

    $stmt = $pdo->prepare("INSERT INTO articles (judul, slug, konten, thumbnail, meta_desc, keyword) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$judul, $slug, $konten, $thumb_name, $meta_desc, $keyword]);
    
    $_SESSION['success'] = "Artikel berhasil diterbitkan!";
    header("Location: artikel.php"); exit;
}

include 'header.php'; 
$gallery = $pdo->query("SELECT file_name FROM galleries ORDER BY id DESC LIMIT 32")->fetchAll();
?>

<style>
    /* FIX DARK MODE INPUTS */
    [data-bs-theme="dark"] .form-control, 
    [data-bs-theme="dark"] .note-editor { background-color: #121212 !important; color: #fff !important; border-color: #333 !important; }
    [data-bs-theme="dark"] .note-editable { color: #ffffff !important; }
    [data-bs-theme="dark"] .note-toolbar { background-color: #1a1a1a !important; }
    [data-bs-theme="dark"] .note-btn { background-color: #222 !important; color: #fff !important; border-color: #444 !important; }
    
    /* PREVIEW BOX */
    .img-picker-box { transition: 0.2s; border: 2px solid transparent; border-radius: 8px; cursor: pointer; height: 100px; overflow: hidden; }
    .img-picker-box:hover { border-color: var(--primary); transform: scale(1.05); }
    .meta-status { font-size: 10px; font-weight: bold; }
</style>

<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-pen-nib me-2"></i>Tulis Artikel Baru</h4>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" id="formArtikel">
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="post_title" class="form-control form-control-lg border-primary" placeholder="Ketik judul artikel..." required autocomplete="off">
                    </div>
                    
                    <div class="mb-0">
                        <label class="fw-bold mb-2">Isi Artikel</label>
                        <textarea name="konten" class="summernote" id="post_content"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold py-3 small">THUMBNAIL & SEO</div>
                <div class="card-body p-4">
                    
                    <!-- Preview Thumbnail -->
                    <div id="preview-area" class="mb-3 text-center p-2 border rounded bg-dark bg-opacity-25 d-none">
                        <img id="img-chosen" src="" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                        <input type="hidden" name="selected_image" id="input-chosen">
                        <button type="button" class="btn btn-sm btn-link text-danger mt-2 text-decoration-none fw-bold" id="btn-remove-img">Ganti Gambar</button>
                    </div>

                    <div id="upload-instruction" class="d-grid gap-2 mb-4">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#galleryModal">
                            <i class="fa-solid fa-images me-2"></i>Pilih dari Galeri
                        </button>
                        <div class="text-center small text-muted my-1">Atau Upload File:</div>
                        <input type="file" name="thumbnail" class="form-control form-control-sm" accept="image/*">
                    </div>

                    <hr class="opacity-25 my-4">

                    <!-- SEO OTOMATIS -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small fw-bold text-primary">Meta Description (SEO)</label>
                            <span id="meta-badge" class="meta-status text-danger">MENGISI...</span>
                        </div>
                        <textarea name="meta_desc" id="meta_desc" class="form-control small" rows="4" placeholder="Otomatis dari isi artikel..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-primary mb-1">Keywords (Ciamik)</label>
                        <textarea name="keyword" id="keyword" class="form-control small" rows="4" placeholder="Otomatis dari judul & konten..."></textarea>
                    </div>

                    <button type="submit" name="simpan_artikel" class="btn btn-primary w-100 shadow py-3 fw-bold">
                        <i class="fa-solid fa-paper-plane me-2"></i> TERBITKAN ARTIKEL
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- MODAL GALLERY (Sama seperti sebelumnya) -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-dark">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">Pilih Portofolio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 500px; overflow-y: auto;">
                <div class="row g-2">
                    <?php foreach($gallery as $g): ?>
                    <div class="col-4 col-md-3">
                        <div class="img-picker-box"><img src="../uploads/gallery/<?= $g['file_name'] ?>" class="img-fluid w-100 h-100 img-click" style="object-fit: cover;" data-filename="<?= $g['file_name'] ?>"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 1. JURUS ANTI BLOCK & MEDIA PICKER
function cleanupModal() {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
    document.body.style.paddingRight = '0';
    $('.modal-backdrop').remove();
}

document.querySelectorAll('.img-click').forEach(img => {
    img.addEventListener('click', function() {
        const filename = this.getAttribute('data-filename');
        document.getElementById('img-chosen').src = "../uploads/gallery/" + filename;
        document.getElementById('input-chosen').value = filename;
        document.getElementById('preview-area').classList.remove('d-none');
        document.getElementById('upload-instruction').classList.add('d-none');
        bootstrap.Modal.getInstance(document.getElementById('galleryModal')).hide();
        setTimeout(cleanupModal, 400); 
    });
});

document.getElementById('btn-remove-img').addEventListener('click', function() {
    document.getElementById('preview-area').classList.add('d-none');
    document.getElementById('upload-instruction').classList.remove('d-none');
    document.getElementById('input-chosen').value = '';
});

// 2. JURUS SMART SEO (CIAMIK)
function updateSEO() {
    const title = document.getElementById('post_title').value;
    const content = $('#post_content').summernote('code');
    const plainText = content.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();

    // A. Update Meta Description (Gacor!)
    if (plainText.length > 10) {
        document.getElementById('meta_desc').value = plainText.substring(0, 160);
        document.getElementById('meta-badge').innerText = "OK (" + plainText.substring(0, 160).length + ")";
        document.getElementById('meta-badge').classList.replace('text-danger', 'text-success');
    }

    // B. Update Keywords Ciamik
    const dictionary = ['branding', 'wrapping', 'sticker', 'stiker', 'mobil', 'kaca', 'sandblast', 'cutting', 'premium', 'jakarta', 'tangerang', 'ciledug', 'custom', 'decal', 'livery', 'iklan', 'promosi', 'bus', 'truk', 'kantor', 'film'];
    
    // Gabungkan Judul + Awal konten
    let sourceText = (title + " " + plainText.substring(0, 200)).toLowerCase();
    
    // Cari kecocokan kata di dictionary
    let foundKeywords = dictionary.filter(word => sourceText.includes(word));
    
    // Tambahkan kata dari judul yang panjangnya > 3
    let titleWords = title.toLowerCase().replace(/[^a-zA-Z ]/g, '').split(' ').filter(w => w.length > 3);
    
    // Gabungkan & Hilangkan Duplikat
    let finalKeywords = [...new Set([...foundKeywords, ...titleWords])];
    
    // Tambahkan branding wajib
    finalKeywords.push('sticker mgl', 'branding mobil jakarta');

    document.getElementById('keyword').value = finalKeywords.join(', ');
}

// Trigger SEO saat ngetik di Judul
document.getElementById('post_title').addEventListener('keyup', updateSEO);

// Trigger SEO saat ngetik di Summernote (Pakai interval biar gak berat)
$('.summernote').on('summernote.keyup', function() {
    updateSEO();
});

// Tambahan tombol paksa sitemap/cleanup
$('#galleryModal').on('hidden.bs.modal', cleanupModal);
</script>

<?php include 'footer.php'; ?>