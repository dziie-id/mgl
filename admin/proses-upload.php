<?php
session_start();
include '../includes/db.php';
include 'functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    die("Akses Ditolak");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = $_POST['project_name'];
    $use_wm       = $_POST['use_watermark'];
    $kategori_array = $_POST['kategori'] ?? [];
    $kategori_db    = implode(', ', $kategori_array);
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $wm = $stmt->fetch();
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $project_name)));
    $branding_suffix = "sticker-mgl-jakarta";
    $files = $_FILES['fotos'];
    $success_count = 0;
    $error_count = 0;

    foreach ($files['name'] as $key => $val) {
        $tmp_name = $files['tmp_name'][$key];
        $new_filename = $slug . "-" . $branding_suffix . "-" . ($key + 1) . "-" . time() . ".webp";
        $target_path = "../uploads/gallery/" . $new_filename;
        $image_res = resize_crop_image($tmp_name, 1200, 800);

        if ($image_res) {
            if ($use_wm == "1") {
                $baked = apply_baked_watermark($image_res, $target_path, [
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
                $baked = imagewebp($image_res, $target_path, 80);
                imagedestroy($image_res);
            }

            if ($baked) {
                $alt_text = ucwords(str_replace('-', ' ', $slug)) . " - Sticker MGL Jakarta";
                $sql = "INSERT INTO galleries (file_name, alt_text, watermark_applied, kategori) VALUES (?, ?, ?, ?)";
                $stmt_gal = $pdo->prepare($sql);
                $stmt_gal->execute([$new_filename, $alt_text, $use_wm, $kategori_db]);

                $success_count++;
            } else {
                $error_count++;
            }
        } else {
            $error_count++;
        }
    }

    $_SESSION['success'] = "$success_count Gambar berhasil disimpan dengan nama SEO!";
    header("Location: upload.php");
    exit();
}
