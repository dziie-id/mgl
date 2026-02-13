<?php
include 'auth_check.php';
include '../../admin/functions.php'; // Panggil mesin resize & watermark abang

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method harus POST']);
    exit;
}

try {
    $project_name = $_POST['project_name'] ?? 'Project Mobile';
    $kategori     = $_POST['kategori'] ?? 'Sticker'; // Default kategori
    $use_wm       = $_POST['use_watermark'] ?? '1'; // Default pake watermark

    if (empty($_FILES['foto']['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'File foto tidak ditemukan']);
        exit;
    }

    // 1. Ambil Setting Watermark dari DB
    $wm = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();

    // 2. Olah Nama File (SEO Friendly)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $project_name)));
    $new_filename = $slug . "-mgl-jakarta-" . time() . ".webp";
    $target_path = "../../uploads/gallery/" . $new_filename;

    // 3. Jalankan Mesin Resize (1200x800)
    $image_res = resize_crop_image($_FILES['foto']['tmp_name'], 1200, 800);

    if ($image_res) {
        // 4. Jalankan Mesin Watermark (Baking)
        if ($use_wm == "1") {
            apply_baked_watermark($image_res, $target_path, [
                'type'       => $wm['wm_type'],
                'wm_image'   => $wm['wm_image'],
                'text'       => $wm['wm_text'],
                'opacity'    => $wm['wm_opacity'],
                'rotate'     => $wm['wm_rotate'],
                'font_size'  => $wm['wm_font_size'],
                'color_hex'  => $wm['wm_color'],
                'position'   => $wm['wm_position']
            ]);
        } else {
            imagewebp($image_res, $target_path, 80);
            imagedestroy($image_res);
        }

        // 5. Simpan ke Database
        $alt_text = ucwords(str_replace('-', ' ', $slug)) . " - Sticker MGL Jakarta";
        $sql = "INSERT INTO galleries (file_name, alt_text, kategori, watermark_applied) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_filename, $alt_text, $kategori, $use_wm]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Foto berhasil di-bake & diupload!',
            'url' => BASE_URL . "uploads/gallery/" . $new_filename
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memproses gambar']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}