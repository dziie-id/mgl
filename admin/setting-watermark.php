<?php
include '../includes/db.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wm_type = $_POST['wm_type'];
    $wm_image = $_POST['old_wm_image'];

    if (!empty($_FILES['wm_file']['name'])) {
        $ext = pathinfo($_FILES['wm_file']['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) == 'png') {
            $wm_image = "logo-wm-" . time() . ".png";
            move_uploaded_file($_FILES['wm_file']['tmp_name'], "../assets/img/" . $wm_image);
        }
    }
    $_SESSION['success'] = "Pengaturan watermark berhasil diperbarui!";
    header("Location: setting-watermark.php");

    $stmt = $pdo->prepare("UPDATE settings SET wm_text=?, wm_opacity=?, wm_rotate=?, wm_font_size=?, wm_color=?, wm_position=?, wm_type=?, wm_image=? WHERE id=1");
    $stmt->execute([$_POST['wm_text'], $_POST['wm_opacity'], $_POST['wm_rotate'], $_POST['wm_font_size'], $_POST['wm_color'], $_POST['wm_position'], $wm_type, $wm_image]);
    echo "<script>window.location.href='setting-watermark.php';</script>";
    exit;
}
$s = $pdo->query("SELECT * FROM settings WHERE id=1")->fetch();
?>
<div class="row">
    <div class="col-md-7">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_wm_image" value="<?= $s['wm_image'] ?>">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label fw-bold mb-3">Pilih Mode Watermark:</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="wm_type" id="typeText" value="text" <?= $s['wm_type'] == 'text' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary w-100" for="typeText">TEKS</label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="wm_type" id="typeImage" value="image" <?= $s['wm_type'] == 'image' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary w-100" for="typeImage">LOGO PNG</label>
                        </div>
                    </div>

                    <div id="sectionTextOnly" class="mt-4 <?= $s['wm_type'] == 'image' ? 'd-none' : '' ?>">
                        <label class="fw-bold">Isi Teks</label>
                        <input type="text" name="wm_text" id="inText" class="form-control mb-3" value="<?= $s['wm_text'] ?>">
                        <label class="fw-bold">Warna</label>
                        <input type="color" name="wm_color" id="inColor" class="form-control form-control-color w-100 mb-3" value="<?= $s['wm_color'] ?>">
                    </div>

                    <div id="sectionImageOnly" class="mt-4 <?= $s['wm_type'] == 'text' ? 'd-none' : '' ?>">
                        <label class="fw-bold">Upload Logo PNG</label>
                        <input type="file" name="wm_file" id="inLogoFile" class="form-control mb-3" accept="image/png">
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <label class="fw-bold small" id="labelSize">Ukuran</label>
                            <input type="number" name="wm_font_size" id="inSize" class="form-control" value="<?= $s['wm_font_size'] ?>">
                        </div>
                        <div class="col-4">
                            <label class="fw-bold small">Opacity</label>
                            <input type="number" name="wm_opacity" id="inOpacity" class="form-control" value="<?= $s['wm_opacity'] ?>">
                        </div>
                        <div class="col-4">
                            <label class="fw-bold small">Rotasi</label>
                            <input type="number" name="wm_rotate" id="inRotate" class="form-control" value="<?= $s['wm_rotate'] ?>">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="fw-bold">Posisi</label>
                        <select name="wm_position" id="inPos" class="form-select">
                            <option value="center" <?= $s['wm_position'] == 'center' ? 'selected' : '' ?>>Tengah</option>
                            <option value="top-left" <?= $s['wm_position'] == 'top-left' ? 'selected' : '' ?>>Kiri Atas</option>
                            <option value="top-right" <?= $s['wm_position'] == 'top-right' ? 'selected' : '' ?>>Kanan Atas</option>
                            <option value="bottom-left" <?= $s['wm_position'] == 'bottom-left' ? 'selected' : '' ?>>Kiri Bawah</option>
                            <option value="bottom-right" <?= $s['wm_position'] == 'bottom-right' ? 'selected' : '' ?>>Kanan Bawah</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-4 shadow">SIMPAN SETTING</button>

                </div>
            </div>
        </form>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header text-center">LIVE PREVIEW SIMULASI</div>
            <div class="card-body p-0 position-relative bg-secondary" id="previewContainer" style="height: 300px; overflow: hidden; display: flex;">
                <img src="https://images.unsplash.com/photo-1600585154340-be6199f7d009?auto=format&fit=crop&w=800&q=60" class="w-100 h-100" style="object-fit: cover; opacity: 0.6;">

                <div id="wm-overlay" style="position: absolute; width: 100%; height: 100%; display: flex; pointer-events: none; padding: 20px;">
                    <div id="wm-content" style="transition: all 0.2s;">
                        <span id="wm-text-preview"></span>
                        <img id="wm-img-preview" src="../assets/img/<?= $s['wm_image'] ?>" class="d-none" style="max-width: 100%;">
                    </div>
                </div>
            </div>
            <div class="card-header text-center text-center justify-content-between align-items-center py-3">Tampilan simulasi mungkin sedikit berbeda dengan hasil baking WebP</div>
        </div>
    </div>
</div>

<script>
    function updatePreview() {
        const type = document.querySelector('input[name="wm_type"]:checked').value;
        const text = document.getElementById('inText').value;
        const color = document.getElementById('inColor').value;
        const size = document.getElementById('inSize').value;
        const opacity = document.getElementById('inOpacity').value / 100;
        const rotate = document.getElementById('inRotate').value;
        const pos = document.getElementById('inPos').value;

        const content = document.getElementById('wm-content');
        const textPrev = document.getElementById('wm-text-preview');
        const imgPrev = document.getElementById('wm-img-preview');
        const overlay = document.getElementById('wm-overlay');

        if (type === 'text') {
            textPrev.classList.remove('d-none');
            imgPrev.classList.add('d-none');
            textPrev.innerText = text;
            textPrev.style.color = color;
            textPrev.style.fontSize = (size / 3) + "px";
            document.getElementById('sectionTextOnly').classList.remove('d-none');
            document.getElementById('sectionImageOnly').classList.add('d-none');
            document.getElementById('labelSize').innerText = 'Ukuran Font';
        } else {
            imgPrev.classList.remove('d-none');
            textPrev.classList.add('d-none');
            imgPrev.style.width = size + "px";
            document.getElementById('sectionImageOnly').classList.remove('d-none');
            document.getElementById('sectionTextOnly').classList.add('d-none');
            document.getElementById('labelSize').innerText = 'Lebar Logo';
        }

        content.style.opacity = opacity;
        content.style.transform = `rotate(${rotate}deg)`;

        overlay.style.justifyContent = pos.includes('left') ? 'flex-start' : (pos.includes('right') ? 'flex-end' : 'center');
        overlay.style.alignItems = pos.includes('top') ? 'flex-start' : (pos.includes('bottom') ? 'flex-end' : 'center');
    }

    ['inText', 'inColor', 'inSize', 'inOpacity', 'inRotate', 'inPos'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });
    document.querySelectorAll('input[name="wm_type"]').forEach(el => {
        el.addEventListener('change', updatePreview);
    });
    window.onload = updatePreview;
</script>
<?php include 'footer.php'; ?>