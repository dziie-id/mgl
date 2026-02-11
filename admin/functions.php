<?php
function generate_seo_name($title, $category)
{
    $clean_title = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $clean_cat = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category)));
    return $clean_cat . "-" . $clean_title . "-" . rand(100, 999);
}

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
        default:
            return false;
    }

    $canvas = imagecreatetruecolor($target_width, $target_height);
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);

    $source_aspect_ratio = $width / $height;
    $target_aspect_ratio = $target_width / $target_height;

    if ($source_aspect_ratio > $target_aspect_ratio) {
        $temp_height = $target_height;
        $temp_width = (int) ($target_height * $source_aspect_ratio);
    } else {
        $temp_width = $target_width;
        $temp_height = (int) ($target_width / $source_aspect_ratio);
    }

    $temp_gbr = imagecreatetruecolor($temp_width, $temp_height);
    imagecopyresampled($temp_gbr, $source_image, 0, 0, 0, 0, $temp_width, $temp_height, $width, $height);

    $x0 = ($temp_width - $target_width) / 2;
    $y0 = ($temp_height - $target_height) / 2;

    imagecopy($canvas, $temp_gbr, 0, 0, (int)$x0, (int)$y0, $target_width, $target_height);

    imagedestroy($source_image);
    imagedestroy($temp_gbr);

    return $canvas;
}

function apply_baked_watermark($image_resource, $target_path, $settings)
{
    $width = imagesx($image_resource);
    $height = imagesy($image_resource);

    if ($settings['type'] == 'image' && !empty($settings['wm_image'])) {

        $wm_path = __DIR__ . '/../assets/img/' . $settings['wm_image'];
        if (file_exists($wm_path)) {
            $watermark = imagecreatefrompng($wm_path);

            $wm_w = imagesx($watermark);
            $wm_h = imagesy($watermark);
            $new_wm_w = $settings['font_size'];
            $new_wm_h = ($wm_h / $wm_w) * $new_wm_w;

            $resized_wm = imagecreatetruecolor($new_wm_w, $new_wm_h);
            imagealphablending($resized_wm, false);
            imagesavealpha($resized_wm, true);
            imagecopyresampled($resized_wm, $watermark, 0, 0, 0, 0, $new_wm_w, $new_wm_h, $wm_w, $wm_h);

            switch ($settings['position']) {
                case 'center':
                    $dest_x = ($width / 2) - ($new_wm_w / 2);
                    $dest_y = ($height / 2) - ($new_wm_h / 2);
                    break;
                case 'top-left':
                    $dest_x = 30;
                    $dest_y = 30;
                    break;
                case 'top-right':
                    $dest_x = $width - $new_wm_w - 30;
                    $dest_y = 30;
                    break;
                case 'bottom-left':
                    $dest_x = 30;
                    $dest_y = $height - $new_wm_h - 30;
                    break;
                default:
                    $dest_x = $width - $new_wm_w - 30;
                    $dest_y = $height - $new_wm_h - 30;
                    break;
            }
            imagecopy($image_resource, $resized_wm, (int)$dest_x, (int)$dest_y, 0, 0, $new_wm_w, $new_wm_h);

            imagedestroy($watermark);
            imagedestroy($resized_wm);
        }
    } else {

        $hex = str_replace("#", "", $settings['color_hex']);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $gd_opacity = (int)(127 - ($settings['opacity'] * 1.27));
        $color = imagecolorallocatealpha($image_resource, $r, $g, $b, $gd_opacity);

        $font_path = __DIR__ . '/../assets/fonts/Roboto-Bold.ttf';
        if (file_exists($font_path)) {
            $text_box = imagettfbbox($settings['font_size'], $settings['rotate'], $font_path, $settings['text']);
            $text_width = abs($text_box[4] - $text_box[0]);
            $text_height = abs($text_box[5] - $text_box[1]);
            switch ($settings['position']) {
                case 'center':
                    $x = ($width / 2) - ($text_width / 2);
                    $y = ($height / 2) + ($text_height / 2);
                    break;
                case 'bottom-right':
                    $x = $width - $text_width - 30;
                    $y = $height - 30;
                    break;
                default:
                    $x = 30;
                    $y = 50;
                    break;
            }
            imagettftext($image_resource, $settings['font_size'], $settings['rotate'], (int)$x, (int)$y, $color, $font_path, $settings['text']);
        }
    }

    $save = imagewebp($image_resource, $target_path, 80);
    imagedestroy($image_resource);
    return $save;
}
