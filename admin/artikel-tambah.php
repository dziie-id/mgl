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
    header("Location: artikel.php");
    $_SESSION['success'] = "Artikel berhasil diterbitkan!";
    exit;
}

include 'header.php';
$gallery = $pdo->query("SELECT file_name FROM galleries ORDER BY id DESC LIMIT 24")->fetchAll();
?>

<form method="POST" enctype="multipart/form-data">
    <div class="card border-primary shadow-sm mb-4">
        <div class="card-body bg-primary bg-opacity-10 py-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary text-white rounded-circle p-2 me-3">
                    <i class="fa-solid fa-robot fa-xl"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-primary">Tulis Artikel dengan AI</h5>
                    <small class="text-muted">Masukkan topik atau kata kunci, AI akan menuliskan semuanya untuk Anda.</small>
                </div>
            </div>

            <div class="input-group">
                <input type="text" id="ai_topic" class="form-control form-control-lg border-primary" placeholder="Contoh: Manfaat pasang sticker sandblast untuk kaca kantor">
                <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="btn_generate_ai">
                    <span id="ai_spinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                    <span id="ai_text">BUAT SEKARANG</span>
                </button>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="post_title" class="form-control form-control-lg border-primary shadow-sm" placeholder="Masukkan judul artikel yang menarik..." required>
                    </div>

                    <div class="mb-0">
                        <label class="fw-bold mb-2">Isi Artikel / Konten</label>
                        <textarea name="konten" id="post_content" class="summernote"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold py-3">Thumbnail & SEO</div>
                <div class="card-body">
                    <div id="preview-area" class="mb-3 text-center p-2 border rounded <?= empty($art['thumbnail']) ? 'd-none' : '' ?>">
                        <img id="img-chosen" src="" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                        <input type="hidden" name="selected_image" id="input-chosen">
                    </div>

                    <label class="small fw-bold mb-2">Pilih Thumbnail</label>
                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#galleryModal">
                            <i class="fa-solid fa-images me-1"></i> Pilih dari Galeri
                        </button>
                        <input type="file" name="thumbnail" class="form-control form-control-sm" accept="image/*">
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="small fw-bold">Meta Description</label>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" onclick="generateSEO()">Auto-Generate</button>
                    </div>
                    <textarea name="meta_desc" id="meta_desc" class="form-control mb-3 small" rows="4" placeholder="Ringkasan untuk Google..."></textarea>

                    <label class="small fw-bold mb-2">Keywords (Pisahkan dengan koma)</label>
                    <input type="text" name="keyword" id="keyword" class="form-control mb-4" placeholder="sticker, branding, mobil...">

                    <button type="submit" name="simpan_artikel" class="btn btn-primary w-100 shadow py-2 fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> TERBITKAN ARTIKEL
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Pilih Gambar Galeri</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-body-tertiary" style="max-height: 400px; overflow-y: auto;">
                <div class="row g-2">
                    <?php foreach ($gallery as $g): ?>
                        <div class="col-4 col-md-3">
                            <div class="card h-100 border-0 shadow-sm cursor-pointer img-picker-box">
                                <img src="../uploads/gallery/<?= $g['file_name'] ?>"
                                    class="card-img img-picker"
                                    style="height: 100px; object-fit: cover; cursor: pointer;"
                                    data-filename="<?= $g['file_name'] ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer small text-muted">Klik pada gambar untuk memilih</div>
        </div>
    </div>
</div>

<script>
    function generateSEO() {
        const title = document.getElementById('post_title').value;
        const content = $('#post_content').summernote('code');
        const plainText = content.replace(/<[^>]*>/g, '');
        const snippet = plainText.trim().substring(0, 160);

        document.getElementById('meta_desc').value = snippet;
        document.getElementById('keyword').value = title.toLowerCase().split(' ').join(', ');
    }

    document.querySelectorAll('.img-picker').forEach(img => {
        img.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            const previewArea = document.getElementById('preview-area');
            const imgChosen = document.getElementById('img-chosen');
            const inputChosen = document.getElementById('input-chosen');
            imgChosen.src = "../uploads/gallery/" + filename;
            inputChosen.value = filename;
            previewArea.classList.remove('d-none');

            const modal = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
            modal.hide();
        });
    });
</script>
<script>
    document.getElementById('btn_generate_ai').addEventListener('click', function() {
        const topic = document.getElementById('ai_topic').value;
        if (!topic) return Swal.fire('Opps!', 'Isi topiknya dulu bang!', 'warning');

        const btn = this;
        const spinner = document.getElementById('ai_spinner');
        const btnText = document.getElementById('ai_text');

        // Loading State
        btn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.innerText = 'AI Sedang Menulis...';

        // Kirim permintaan via AJAX
        $.ajax({
            url: 'ajax-generate-ai.php',
            type: 'POST',
            data: {
                topic: topic
            },
            success: function(response) {
                try {
                    const res = JSON.parse(response);

                    // Isi otomatis ke field form
                    document.getElementById('post_title').value = res.judul;
                    $('#post_content').summernote('code', res.konten);
                    document.getElementById('meta_desc').value = res.meta_desc;
                    document.getElementById('keyword').value = res.keywords;

                    Swal.fire({
                        icon: 'success',
                        title: 'Artikel Selesai!',
                        text: 'AI sudah selesai menulis. Silakan diperiksa dan jangan lupa pilih gambarnya.',
                        background: getSwalTheme().bg,
                        color: getSwalTheme().text
                    });
                } catch (e) {
                    console.log(response);
                    Swal.fire('Gagal', 'AI sedang error atau kuota habis.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal tersambung ke server.', 'error');
            },
            complete: function() {
                btn.disabled = false;
                spinner.classList.add('d-none');
                btnText.innerText = 'BUAT SEKARANG';
            }
        });
    });
</script>
<?php include 'footer.php'; ?>