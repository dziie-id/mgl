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
    /* FIX SUMMERNOTE DARK MODE - BIAR TEKS KELIHATAN */
    [data-bs-theme="dark"] .note-editor { background-color: #1a1a1a !important; border-color: #333 !important; }
    [data-bs-theme="dark"] .note-editable { background-color: #1a1a1a !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .note-toolbar { background-color: #222 !important; border-bottom: 1px solid #333 !important; }
    [data-bs-theme="dark"] .note-btn { background-color: #333 !important; border-color: #444 !important; color: white !important; }
    [data-bs-theme="dark"] .note-resizebar { background-color: #222 !important; }
    
    /* GABUNGAN STYLE BARU */
    .img-picker-box { transition: 0.2s; border: 2px solid transparent; border-radius: 8px; cursor: pointer; }
    .img-picker-box:hover { border-color: var(--primary); transform: scale(1.02); }
    .img-selected { border-color: var(--primary) !important; background: rgba(0, 102, 255, 0.1); }
</style>

<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-pen-nib me-2"></i>Tulis Artikel Baru</h4>
        <p class="text-muted small">Buat konten manual untuk meningkatkan SEO website Anda.</p>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- KIRI: ISI KONTEN -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="post_title" class="form-control form-control-lg" placeholder="Masukkan judul..." required>
                    </div>
                    
                    <div class="mb-0">
                        <label class="fw-bold mb-2">Isi Artikel</label>
                        <!-- Textarea summernote otomatis kena CSS Fix di atas -->
                        <textarea name="konten" class="summernote" id="post_content"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN: SEO & THUMBNAIL -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold py-3">Thumbnail & SEO</div>
                <div class="card-body p-4">
                    
                    <!-- Preview Thumbnail -->
                    <div id="preview-area" class="mb-3 text-center p-2 border rounded bg-dark bg-opacity-25 d-none">
                        <label class="x-small d-block mb-2 fw-bold text-primary">GAMBAR TERPILIH</label>
                        <img id="img-chosen" src="" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                        <input type="hidden" name="selected_image" id="input-chosen">
                        <button type="button" class="btn btn-sm btn-danger mt-2 w-100" id="btn-remove-img">Hapus Gambar</button>
                    </div>

                    <div id="upload-instruction" class="d-grid gap-2 mb-4">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#galleryModal">
                            <i class="fa-solid fa-images me-2"></i>Pilih dari Galeri
                        </button>
                        <div class="text-center small text-muted my-1">Atau Upload Baru:</div>
                        <input type="file" name="thumbnail" class="form-control form-control-sm" accept="image/*">
                    </div>

                    <hr class="my-4 opacity-25">

                    <!-- SEO SECTION -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small fw-bold">Meta Description</label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" onclick="autoMeta()">Auto</button>
                        </div>
                        <textarea name="meta_desc" id="meta_desc" class="form-control small" rows="4" placeholder="Ringkasan singkat..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Keywords (SEO Kata Kunci)</label>
                        <textarea name="keyword" id="keyword" class="form-control" rows="3" placeholder="Contoh: sticker mobil, branding bus, wrapping jakarta, cutting sticker..."></textarea>
                        <div class="form-text" style="font-size: 10px;">Gunakan tanda koma (,) untuk memisahkan kata kunci.</div>
                    </div>

                    <button type="submit" name="simpan_artikel" class="btn btn-primary w-100 shadow py-3 fw-bold">
                        <i class="fa-solid fa-paper-plane me-2"></i> TERBITKAN SEKARANG
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- MODAL MEDIA PICKER -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold">Pilih Gambar dari Portofolio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-dark p-3" style="max-height: 500px; overflow-y: auto;">
                <div class="row g-2">
                    <?php foreach($gallery as $g): ?>
                    <div class="col-4 col-md-3">
                        <div class="img-picker-box p-1">
                            <img src="../uploads/gallery/<?= $g['file_name'] ?>" 
                                 class="img-fluid rounded shadow-sm img-click" 
                                 style="height: 110px; width: 100%; object-fit: cover;"
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
// 1. Logic Media Picker (Pilih Gambar)
document.querySelectorAll('.img-click').forEach(img => {
    img.addEventListener('click', function() {
        const filename = this.getAttribute('data-filename');
        
        // Tampilkan area preview
        document.getElementById('img-chosen').src = "../uploads/gallery/" + filename;
        document.getElementById('input-chosen').value = filename;
        document.getElementById('preview-area').classList.remove('d-none');
        document.getElementById('upload-instruction').classList.add('d-none');

        // Tutup Modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
        modal.hide();
    });
});

// 2. Logic Hapus Pilihan Gambar
document.getElementById('btn-remove-img').addEventListener('click', function() {
    document.getElementById('preview-area').classList.add('d-none');
    document.getElementById('upload-instruction').classList.remove('d-none');
    document.getElementById('input-chosen').value = '';
});

// 3. Logic Auto Generate Meta
function autoMeta() {
    const content = $('#post_content').summernote('code');
    const plainText = content.replace(/<[^>]*>/g, '').trim();
    document.getElementById('meta_desc').value = plainText.substring(0, 160);
}
</script>

<?php include 'footer.php'; ?>