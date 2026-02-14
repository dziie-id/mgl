<?php
include 'auth_check.php';
include '../../admin/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Method harus POST']));
}

try {
    $project_name = $_POST['project_name'] ?? 'Project Mobile';
    $kategori     = $_POST['kategori'] ?? 'Sticker'; // Ini akan berisi "Branding Mobil, Sticker"
    
    if (empty($_FILES['fotos']['name'][0])) {
        die(json_encode(['status' => 'error', 'message' => 'Pilih minimal satu foto bang']));
    }

    $wm = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $project_name)));
    $success_count = 0;

    foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
        $new_filename = $slug . "-mgl-jakarta-" . time() . "-$key.webp";
        $target_path = "../../uploads/gallery/" . $new_filename;

        // Resize & Bake
        $image_res = resize_crop_image($tmp_name, 1200, 800);
        if ($image_res) {
            apply_baked_watermark($image_res, $target_path, [
                'type' => $wm['wm_type'], 'wm_image' => $wm['wm_image'], 'text' => $wm['wm_text'],
                'opacity' => $wm['wm_opacity'], 'rotate' => $wm['wm_rotate'], 'font_size' => $wm['wm_font_size'],
                'color_hex' => $wm['wm_color'], 'position' => $wm['wm_position']
            ]);

            $alt_text = ucwords(str_replace('-', ' ', $slug)) . " - Sticker MGL Jakarta";
            $stmt = $pdo->prepare("INSERT INTO galleries (file_name, alt_text, kategori, watermark_applied) VALUES (?, ?, ?, '1')");
            $stmt->execute([$new_filename, $alt_text, $kategori]);
            $success_count++;
        }
    }

    echo json_encode(['status' => 'success', 'message' => "$success_count Foto berhasil di-bake!"]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
