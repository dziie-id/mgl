<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. PROTEKSI LOGIN & ROLE
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// 2. LOGIKA PROSES SIMPAN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wm_type     = $_POST['wm_type'];
    $wm_text     = $_POST['wm_text'];
    $wm_opacity  = $_POST['wm_opacity'];
    $wm_rotate   = $_POST['wm_rotate'];
    $wm_font_size = $_POST['wm_font_size'];
    $wm_color    = $_POST['wm_color'];
    $wm_position = $_POST['wm_position'];
    $wm_image    = $_POST['old_wm_image'];

    // Handle Upload Logo PNG baru jika ada
    if (!empty($_FILES['wm_file']['name'])) {
        $ext = pathinfo($_FILES['wm_file']['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) == 'png') {
            $new_logo_name = "logo-wm.png"; // Nama fix agar tidak nyampah
            move_uploaded_file($_FILES['wm_file']['tmp_name'], "../assets/img/" . $new_logo_name);
            $wm_image = $new_logo_name;
        }
    }

    $sql = "UPDATE settings SET 
            wm_type = ?, wm_text = ?, wm_opacity = ?, 
            wm_rotate = ?, wm_font_size = ?, wm_color = ?, 
            wm_position = ?, wm_image = ? 
            WHERE id = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $wm_type,
        $wm_text,
        $wm_opacity,
        $wm_rotate,
        $wm_font_size,
        $wm_color,
        $wm_position,
        $wm_image
    ]);

    $_SESSION['success'] = "Pengaturan watermark berhasil disimpan!";
    header("Location: setting-watermark.php");
    exit;
}

// 3. AMBIL DATA TERBARU
$s = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
include 'header.php';
?>

<div class="row g-4">
    <!-- KIRI: FORM PENGATURAN -->
    <div class="col-md-7">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_wm_image" value="<?= $s['wm_image'] ?>">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold py-3">
                    <i class="fa-solid fa-copyright me-2"></i> PILIH JENIS WATERMARK
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="wm_type" id="typeText" value="text" <?= $s['wm_type'] == 'text' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="typeText">
                                <i class="fa-solid fa-font fa-xl mb-2 d-block"></i> GUNAKAN TEKS
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="wm_type" id="typeImage" value="image" <?= $s['wm_type'] == 'image' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="typeImage">
                                <i class="fa-solid fa-image fa-xl mb-2 d-block"></i> GUNAKAN LOGO PNG
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Input Khusus Teks -->
                    <div id="boxText" class="<?= $s['wm_type'] == 'image' ? 'd-none' : '' ?>">
                        <div class="mb-3">
                            <label class="fw-bold small mb-1">Isi Teks Watermark</label>
                            <input type="text" name="wm_text" id="inText" class="form-control" value="<?= $s['wm_text'] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small mb-1">Warna Teks</label>
                            <input type="color" name="wm_color" id="inColor" class="form-control form-control-color w-100" value="<?= $s['wm_color'] ?>">
                        </div>
                    </div>

                    <!-- Input Khusus Image -->
                    <div id="boxImage" class="<?= $s['wm_type'] == 'text' ? 'd-none' : '' ?>">
                        <div class="mb-3 p-3 border rounded bg-light text-dark">
                            <label class="fw-bold small mb-1">Upload Logo PNG (Transparan)</label>
                            <input type="file" name="wm_file" class="form-control mb-2" accept="image/png">
                            <?php if ($s['wm_image']): ?>
                                <small class="text-muted d-block">File aktif: <?= $s['wm_image'] ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Setting Umum -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold small mb-1" id="labelSize"><?= $s['wm_type'] == 'text' ? 'Ukuran Font' : 'Lebar Logo' ?></label>
                            <input type="number" name="wm_font_size" id="inSize" class="form-control" value="<?= $s['wm_font_size'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold small mb-1">Opacity (0-100)</label>
                            <input type="number" name="wm_opacity" id="inOpacity" class="form-control" value="<?= $s['wm_opacity'] ?>" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold small mb-1">Rotasi (°)</label>
                            <input type="number" name="wm_rotate" id="inRotate" class="form-control" value="<?= $s['wm_rotate'] ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold small mb-1">Posisi Watermark</label>
                        <select name="wm_position" id="inPos" class="form-select">
                            <option value="center" <?= $s['wm_position'] == 'center' ? 'selected' : '' ?>>Tengah (Center)</option>
                            <option value="top-left" <?= $s['wm_position'] == 'top-left' ? 'selected' : '' ?>>Kiri Atas</option>
                            <option value="top-right" <?= $s['wm_position'] == 'top-right' ? 'selected' : '' ?>>Kanan Atas</option>
                            <option value="bottom-left" <?= $s['wm_position'] == 'bottom-left' ? 'selected' : '' ?>>Kiri Bawah</option>
                            <option value="bottom-right" <?= $s['wm_position'] == 'bottom-right' ? 'selected' : '' ?>>Kanan Bawah</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow">
                        <i class="fa-solid fa-save me-2"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- KANAN: LIVE PREVIEW -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-dark text-white fw-bold small py-3 text-center">SIMULASI LIVE PREVIEW</div>
            <div class="card-body p-0 position-relative bg-secondary overflow-hidden d-flex" id="previewArea" style="height: 350px;">
                <!-- Gambar Contoh -->
                <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=800&auto=format&fit=crop" class="w-100 h-100" style="object-fit: cover; opacity: 0.5;">

                <!-- Overlay Watermark -->
                <div id="wm-overlay" style="position: absolute; width: 100%; height: 100%; display: flex; pointer-events: none; padding: 20px;">
                    <div id="wm-content" style="transition: all 0.2s ease;">
                        <span id="txt-prev" class="fw-bold"></span>
                        <img id="img-prev" src="../assets/img/<?= $s['wm_image'] ?>" style="max-width: 100%; display:none;">
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <small class="text-muted italic">* Hasil "Bake" pada foto asli akan lebih presisi mengikuti resolusi gambar.</small>
            </div>
        </div>
    </div>
</div>

<script>
    function updateLivePreview() {
        const type = document.querySelector('input[name="wm_type"]:checked').value;
        const text = document.getElementById('inText').value;
        const color = document.getElementById('inColor').value;
        const size = document.getElementById('inSize').value;
        const opacity = document.getElementById('inOpacity').value / 100;
        const rotate = document.getElementById('inRotate').value;
        const pos = document.getElementById('inPos').value;

        const boxText = document.getElementById('boxText');
        const boxImage = document.getElementById('boxImage');
        const labelSize = document.getElementById('labelSize');

        const wmContent = document.getElementById('wm-content');
        const txtPrev = document.getElementById('txt-prev');
        const imgPrev = document.getElementById('img-prev');
        const wmOverlay = document.getElementById('wm-overlay');

        if (type === 'text') {
            boxText.classList.remove('d-none');
            boxImage.classList.add('d-none');
            labelSize.innerText = 'Ukuran Font';

            txtPrev.style.display = 'block';
            imgPrev.style.display = 'none';

            txtPrev.innerText = text;
            txtPrev.style.color = color;
            txtPrev.style.fontSize = (size / 3) + "px"; // Skala diperkecil untuk preview
        } else {
            boxImage.classList.remove('d-none');
            boxText.classList.add('d-none');
            labelSize.innerText = 'Lebar Logo';

            txtPrev.style.display = 'none';
            imgPrev.style.display = 'block';
            imgPrev.style.width = (size / 2) + "px"; // Skala diperkecil untuk preview
        }

        // Transform & Opacity
        wmContent.style.opacity = opacity;
        wmContent.style.transform = `rotate(${rotate}deg)`;

        // Posisi (Flexbox Logic)
        wmOverlay.style.justifyContent = pos.includes('left') ? 'flex-start' : (pos.includes('right') ? 'flex-end' : 'center');
        wmOverlay.style.alignItems = pos.includes('top') ? 'flex-start' : (pos.includes('bottom') ? 'flex-end' : 'center');
    }

    // Listeners
    ['inText', 'inColor', 'inSize', 'inOpacity', 'inRotate', 'inPos'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateLivePreview);
    });
    document.querySelectorAll('input[name="wm_type"]').forEach(el => {
        el.addEventListener('change', updateLivePreview);
    });

    // Jalankan pertama kali saat load
    window.onload = updateLivePreview;
</script>

<?php include 'footer.php'; ?>