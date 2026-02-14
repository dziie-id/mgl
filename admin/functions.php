<?php
// admin/functions.php

// 1. Fungsi Resize & Crop (Biar ukuran sama rata)
function resize_crop_image($source_path, $target_width = 1200, $target_height = 800)
{
    list($width, $height, $type) = getimagesize($source_path);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_WEBP:
            $source_image = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }

    $canvas = imagecreatetruecolor($target_width, $target_height);

    // Handle Transparansi Background
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
    imagefilledrectangle($canvas, 0, 0, $target_width, $target_height, $transparent);

    // Hitung Rasio Crop Center
    $source_aspect = $width / $height;
    $target_aspect = $target_width / $target_height;

    if ($source_aspect > $target_aspect) {
        $temp_height = $target_height;
        $temp_width = (int) ($target_height * $source_aspect);
    } else {
        $temp_width = $target_width;
        $temp_height = (int) ($target_width / $source_aspect);
    }

    $temp_gbr = imagecreatetruecolor($temp_width, $temp_height);

    // Handle transparansi saat resize
    imagealphablending($temp_gbr, false);
    imagesavealpha($temp_gbr, true);

    imagecopyresampled($temp_gbr, $source_image, 0, 0, 0, 0, $temp_width, $temp_height, $width, $height);

    $x0 = ($temp_width - $target_width) / 2;
    $y0 = ($temp_height - $target_height) / 2;

    imagecopy($canvas, $temp_gbr, 0, 0, (int)$x0, (int)$y0, $target_width, $target_height);

    imagedestroy($source_image);
    imagedestroy($temp_gbr);

    return $canvas;
}

// 2. Fungsi Utama: BAKED WATERMARK
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
    // Buat cut-out dari gambar asli
    $cut = imagecreatetruecolor($src_w, $src_h);
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
    
    // Copy logo ke cut-out
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    
    // Gabungkan cut-out ke gambar asli dengan PCT (Opacity)
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}

function apply_baked_watermark($image_resource, $target_path, $settings) {
    $img_w = imagesx($image_resource);
    $img_h = imagesy($image_resource);
    imagealphablending($image_resource, true);

    if ($settings['type'] == 'image') {
        $path_logo = dirname(__DIR__) . '/assets/img/' . $settings['wm_image'];
        if (file_exists($path_logo) && !empty($settings['wm_image'])) {
            $logo = imagecreatefrompng($path_logo);
            $l_w = imagesx($logo); $l_h = imagesy($logo);
            
            $new_l_w = (int)$settings['font_size'] ?: 200;
            $new_l_h = (int)(($l_h / $l_w) * $new_l_w);
            
            $resized_logo = imagecreatetruecolor($new_l_w, $new_l_h);
            imagealphablending($resized_logo, false);
            imagesavealpha($resized_logo, true);
            imagecopyresampled($resized_logo, $logo, 0, 0, 0, 0, $new_l_w, $new_l_h, $l_w, $l_h);

            // Tentukan Posisi
            $pad = 30;
            if($settings['position'] == 'center') { $x = ($img_w/2)-($new_l_w/2); $y = ($img_h/2)-($new_l_h/2); }
            elseif($settings['position'] == 'top-left') { $x = $pad; $y = $pad; }
            elseif($settings['position'] == 'top-right') { $x = $img_w-$new_l_w-$pad; $y = $pad; }
            elseif($settings['position'] == 'bottom-left') { $x = $pad; $y = $img_h-$new_l_h-$pad; }
            else { $x = $img_w-$new_l_w-$pad; $y = $img_h-$new_l_h-$pad; }

            // JURUS PAMUNGKAS: Panggil fungsi merge alpha
            imagecopymerge_alpha($image_resource, $resized_logo, (int)$x, (int)$y, 0, 0, $new_l_w, $new_l_h, (int)$settings['opacity']);
            
            imagedestroy($logo); imagedestroy($resized_logo);
        }
    } else {
        // --- LOGIKA TEKS TETAP SAMA ---
        $hex = str_replace("#", "", $settings['color_hex']);
        $r = hexdec(substr($hex, 0, 2)); $g = hexdec(substr($hex, 2, 2)); $b = hexdec(substr($hex, 4, 2));
        $alpha = (int)(127 - ($settings['opacity'] * 1.27));
        $color = imagecolorallocatealpha($image_resource, $r, $g, $b, $alpha);
        
        $font_path = dirname(__DIR__) . '/assets/fonts/Roboto-Bold.ttf';
        $font_size = (int)$settings['font_size'] ?: 40;
        $angle = (int)$settings['rotate'] ?: 0;
        $text = $settings['text'] ?: 'MGL STICKER';

        if (file_exists($font_path)) {
            $box = imagettfbbox($font_size, $angle, $font_path, $text);
            $text_w = abs($box[4] - $box[0]); $text_h = abs($box[5] - $box[1]);
            $pad = 50;
            switch ($settings['position']) {
                case 'center': $x = ($img_w / 2) - ($text_w / 2); $y = ($img_h / 2) + ($text_h / 2); break;
                case 'top-left': $x = $pad; $y = $pad + $text_h; break;
                case 'top-right': $x = $img_w - $text_w - $pad; $y = $pad + $text_h; break;
                case 'bottom-left': $x = $pad; $y = $img_h - $pad; break;
                case 'bottom-right': $x = $img_w - $text_w - $pad; $y = $img_h - $pad; break;
                default: $x = ($img_w / 2) - ($text_w / 2); $y = ($img_h / 2) + ($text_h / 2); break;
            }
            imagettftext($image_resource, $font_size, $angle, (int)$x, (int)$y, $color, $font_path, $text);
        }
    }

    $res = imagewebp($image_resource, $target_path, 80);
    imagedestroy($image_resource);
    return $res;
}
