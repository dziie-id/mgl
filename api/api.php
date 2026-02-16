<?php
/**
 * API MASTER - MGL STICKER (NATIVE ANDROID VERSION)
 * Fitur: Login, Dashboard Stats, Gallery (Baked Watermark), Survey (Multi-item), User Manager.
 * Keamanan: API Key (Token-based), Database Transaction (Rollback on failure).
 */

date_default_timezone_set('Asia/Jakarta');
error_reporting(0); 
header('Content-Type: application/json');

[span_0](start_span)include '../includes/db.php';[span_0](end_span)
[span_1](start_span)include '../admin/functions.php';[span_1](end_span)

// --- HELPER: AMBIL API KEY ---
function getApiKey() {
    [span_2](start_span)if (isset($_SERVER['HTTP_X_API_KEY'])) return $_SERVER['HTTP_X_API_KEY'];[span_2](end_span)
    [span_3](start_span)if (isset($_SERVER['X_API_KEY'])) return $_SERVER['X_API_KEY'];[span_3](end_span)
    return $_GET['key'] ?? [span_4](start_span)'';[span_4](end_span)
}

$action = $_GET['action'] ?? '';
$apiKey = getApiKey();

// =======================================================================
// 1. LOGIN (Tanpa Cek Token)
// =======================================================================
if ($action == 'login') {
    $user = $_POST['username'] ?? [span_5](start_span)'';[span_5](end_span)
    $pass = $_POST['password'] ?? [span_6](start_span)'';[span_6](end_span)
    
    [span_7](start_span)$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");[span_7](end_span)
    [span_8](start_span)$stmt->execute([$user]);[span_8](end_span)
    [span_9](start_span)$u = $stmt->fetch();[span_9](end_span)

    [span_10](start_span)if ($u && password_verify($pass, $u['password'])) {[span_10](end_span)
        [span_11](start_span)if (empty($u['api_token'])) {[span_11](end_span)
            [span_12](start_span)$token = bin2hex(random_bytes(16));[span_12](end_span)
            [span_13](start_span)$pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?")->execute([$token, $u['id']]);[span_13](end_span)
        } else {
            [span_14](start_span)$token = $u['api_token'];[span_14](end_span)
        }
        
        echo json_encode([
            'status' => 'success',
            'api_key' => $token,
            'user_data' => [
                'user_id' => $u['id'],
                'nama' => $u['nama_lengkap'],
                [span_15](start_span)'role' => $u['role'][span_15](end_span)
            ]
        ]);
    } else {
        [span_16](start_span)echo json_encode(['status' => 'error', 'message' => 'Username atau Password Salah']);[span_16](end_span)
    }
    exit;
}

// =======================================================================
// CEK VALIDASI TOKEN (Gatekeeper)
// =======================================================================
[span_17](start_span)if (empty($apiKey)) {[span_17](end_span)
    [span_18](start_span)die(json_encode(['status' => 'error', 'message' => 'API Key tidak ditemukan']));[span_18](end_span)
}

[span_19](start_span)$stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ?");[span_19](end_span)
[span_20](start_span)$stmt->execute([$apiKey]);[span_20](end_span)
[span_21](start_span)$user_api = $stmt->fetch();[span_21](end_span)

[span_22](start_span)if (!$user_api) {[span_22](end_span)
    [span_23](start_span)die(json_encode(['status' => 'error', 'message' => 'API Key tidak valid atau sesi habis']));[span_23](end_span)
}

// =======================================================================
// ROUTING ACTION
// =======================================================================
try {
    switch ($action) {
        
        // --- A. DASHBOARD STATS (2 Card Utama) ---
        case 'stats':
            [span_24](start_span)$g = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();[span_24](end_span)
            [span_25](start_span)$s = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();[span_25](end_span)
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'total_survey' => (int)$s,
                    'total_portfolio' => (int)$g
                ]
            ]);
        break;

        // --- B. GALLERY (LIST & DELETE) ---
        case 'gallery':
            if (isset($_GET['del_batch']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
                [span_26](start_span)$ids = explode(',', $_POST['ids']);[span_26](end_span)
                [span_27](start_span)foreach($ids as $id) {[span_27](end_span)
                    [span_28](start_span)$stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");[span_28](end_span)
                    [span_29](start_span)$stmt->execute([$id]);[span_29](end_span)
                    [span_30](start_span)$img = $stmt->fetch();[span_30](end_span)
                    if ($img) {
                        [span_31](start_span)@unlink("../../uploads/gallery/" . $img['file_name']);[span_31](end_span)
                        [span_32](start_span)$pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]);[span_32](end_span)
                    }
                }
                [span_33](start_span)echo json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus']);[span_33](end_span)
                break;
            }
            
            [span_34](start_span)$data = $pdo->query("SELECT * FROM galleries ORDER BY id DESC")->fetchAll();[span_34](end_span)
            [span_35](start_span)foreach($data as &$d) {[span_35](end_span)
                $d['url_gambar'] = "https://mglstiker.com/uploads/gallery/" . [span_36](start_span)$d['file_name'];[span_36](end_span)
            }
            [span_37](start_span)echo json_encode(['status' => 'success', 'data' => $data]);[span_37](end_span)
        break;

        // --- C. UPLOAD GALLERY (ROLLBACK SYSTEM) ---
        case 'gallery_upload':
            if (empty($_FILES['fotos']['name'][0])) {
                [span_38](start_span)die(json_encode(['status' => 'error', 'message' => 'Tidak ada file diupload']));[span_38](end_span)
            }

            try {
                $pdo->beginTransaction(); // Mulai transaksi

                [span_39](start_span)$p_name = $_POST['project_name'];[span_39](end_span)
                [span_40](start_span)$kat    = $_POST['kategori'];[span_40](end_span)
                [span_41](start_span)$slug   = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $p_name)));[span_41](end_span)
                [span_42](start_span)$wm = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();[span_42](end_span)

                foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                    $new_filename = $slug . "-mgl-" . time() . [span_43](start_span)"-$key.webp";[span_43](end_span)
                    $target_path  = "../../uploads/gallery/" . [span_44](start_span)$new_filename;[span_44](end_span)

                    [span_45](start_span)$image_res = resize_crop_image($tmp_name, 1200, 800);[span_45](end_span)
                    if (!$image_res) throw new Exception("Gagal memproses gambar ke-" . ($key+1));

                    [span_46](start_span)apply_baked_watermark($image_res, $target_path, [[span_46](end_span)
                        [span_47](start_span)'type' => $wm['wm_type'],[span_47](end_span)
                        [span_48](start_span)'wm_image' => $wm['wm_image'],[span_48](end_span)
                        [span_49](start_span)'text' => $wm['wm_text'],[span_49](end_span)
                        [span_50](start_span)'opacity' => $wm['wm_opacity'],[span_50](end_span)
                        [span_51](start_span)'rotate' => $wm['wm_rotate'],[span_51](end_span)
                        [span_52](start_span)'font_size' => $wm['wm_font_size'],[span_52](end_span)
                        [span_53](start_span)'color_hex' => $wm['wm_color'],[span_53](end_span)
                        [span_54](start_span)'position' => $wm['wm_position'][span_54](end_span)
                    ]);

                    $alt = ucwords(str_replace('-', ' ', $slug)) . [span_55](start_span)" - MGL Sticker";[span_55](end_span)
                    $pdo->prepare("INSERT INTO galleries (file_name, alt_text, kategori, watermark_applied) VALUES (?, ?, ?, '1')")
                        -[span_56](start_span)>execute([$new_filename, $alt, $kat]);[span_56](end_span)
                }

                $pdo->commit(); 
                [span_57](start_span)echo json_encode(['status' => 'success', 'message' => 'Foto berhasil diupload']);[span_57](end_span)
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
            }
        break;

        // --- D. SURVEY (SIMPAN SEMUA / GAGAL SEMUA) ---
        case 'survey_save_all':
            try {
                $pdo->beginTransaction();

                [span_58](start_span)$nama = $_POST['nama_klien'];[span_58](end_span)
                [span_59](start_span)$lok = $_POST['lokasi'];[span_59](end_span)
                [span_60](start_span)$gps = $_POST['koordinat'];[span_60](end_span)
                
                [span_61](start_span)$pdo->prepare("INSERT INTO surveys (nama_klien, lokasi, koordinat) VALUES (?,?,?)")->execute([$nama, $lok, $gps]);[span_61](end_span)
                [span_62](start_span)$sid = $pdo->lastInsertId();[span_62](end_span)

                [span_63](start_span)$items = json_decode($_POST['items'], true);[span_63](end_span)
                foreach($items as $idx => $itm) {
                    $f = "";
                    [span_64](start_span)if (!empty($_FILES["foto_$idx"]['name'])) {[span_64](end_span)
                        $f = "API-SRV-" . time() . [span_65](start_span)"-$idx.jpg";[span_65](end_span)
                        [span_66](start_span)if(!move_uploaded_file($_FILES["foto_$idx"]['tmp_name'], "../../uploads/survey/" . $f)) {[span_66](end_span)
                            throw new Exception("Gagal upload foto item ke-$idx");
                        }
                    }
                    [span_67](start_span)$pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?,?,?,?,?,?,?)")[span_67](end_span)
                        -[span_68](start_span)>execute([$sid, $itm['nama_bagian'], $itm['p'], $itm['l'], $itm['t'], $itm['qty'], $f]);[span_68](end_span)
                }
                
                $pdo->commit();
                [span_69](start_span)echo json_encode(['status' => 'success', 'message' => 'Data survey berhasil disimpan']);[span_69](end_span)
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
            }
        break;

        // --- E. USER MANAGER (NOTIF MODEL PASSWORD) ---
        case 'users':
            $do = $_GET['do'] ?? [span_70](start_span)'list';[span_70](end_span)

            if ($do == 'list') {
                [span_71](start_span)if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Hanya Admin']));[span_71](end_span)
                [span_72](start_span)$data = $pdo->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY role ASC")->fetchAll();[span_72](end_span)
                [span_73](start_span)echo json_encode(['status' => 'success', 'data' => $data]);[span_73](end_span)
            }
            elseif ($do == 'add') {
                [span_74](start_span)if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Hanya Admin']));[span_74](end_span)
                [span_75](start_span)$u = $_POST['username'];[span_75](end_span)
                [span_76](start_span)$n = $_POST['nama_lengkap'];[span_76](end_span)
                [span_77](start_span)$p = password_hash($_POST['password'], PASSWORD_DEFAULT);[span_77](end_span)
                [span_78](start_span)$r = $_POST['role'];[span_78](end_span)
                
                [span_79](start_span)$cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");[span_79](end_span)
                [span_80](start_span)$cek->execute([$u]);[span_80](end_span)
                [span_81](start_span)if($cek->rowCount() > 0) die(json_encode(['status' => 'error', 'message' => 'Username sudah ada']));[span_81](end_span)
                
                [span_82](start_span)$pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?,?,?,?)")[span_82](end_span)
                    -[span_83](start_span)>execute([$u, $p, $n, $r]);[span_83](end_span)
                [span_84](start_span)echo json_encode(['status' => 'success', 'message' => 'User berhasil dibuat']);[span_84](end_span)
            }
            elseif ($do == 'delete') {
                [span_85](start_span)if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Hanya Admin']));[span_85](end_span)
                [span_86](start_span)$uid = $_POST['user_id'];[span_86](end_span)
                [span_87](start_span)if ($uid == $user_api['id']) die(json_encode(['status' => 'error', 'message' => 'Jangan hapus diri sendiri']));[span_87](end_span)
                [span_88](start_span)$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);[span_88](end_span)
                [span_89](start_span)echo json_encode(['status' => 'success', 'message' => 'User dihapus']);[span_89](end_span)
            }
            elseif ($do == 'update') {
                [span_90](start_span)$uid = $_POST['user_id'];[span_90](end_span)
                [span_91](start_span)$nama = $_POST['nama_lengkap'];[span_91](end_span)
                [span_92](start_span)$pass = $_POST['password'];[span_92](end_span)

                [span_93](start_span)if ($user_api['role'] != 'admin' && $user_api['id'] != $uid) {[span_93](end_span)
                    [span_94](start_span)die(json_encode(['status' => 'error', 'message' => 'Akses ditolak']));[span_94](end_span)
                }

                [span_95](start_span)$old = $pdo->query("SELECT nama_lengkap FROM users WHERE id = $uid")->fetch();[span_95](end_span)
                $final_nama = !empty($nama) ? [span_96](start_span)$nama : $old['nama_lengkap'];[span_96](end_span)

                if (!empty($pass)) {
                    [span_97](start_span)$h = password_hash($pass, PASSWORD_DEFAULT);[span_97](end_span)
                    [span_98](start_span)$pdo->prepare("UPDATE users SET nama_lengkap=?, password=? WHERE id=?")->execute([$final_nama, $h, $uid]);[span_98](end_span)
                } else {
                    [span_99](start_span)$pdo->prepare("UPDATE users SET nama_lengkap=? WHERE id=?")->execute([$final_nama, $uid]);[span_99](end_span)
                }
                [span_100](start_span)echo json_encode(['status' => 'success', 'message' => 'Profil diperbarui']);[span_100](end_span)
            }
        break;

        // --- F. SURVEY LIST & DETAIL (READ-ONLY) ---
        case 'survey_list':
            [span_101](start_span)$data = $pdo->query("SELECT * FROM surveys ORDER BY id DESC")->fetchAll();[span_101](end_span)
            [span_102](start_span)echo json_encode(['status' => 'success', 'data' => $data]);[span_102](end_span)
        break;

        case 'survey_detail':
            [span_103](start_span)$id = $_GET['id'];[span_103](end_span)
            [span_104](start_span)$client = $pdo->query("SELECT * FROM surveys WHERE id = $id")->fetch();[span_104](end_span)
            [span_105](start_span)$items = $pdo->query("SELECT * FROM survey_items WHERE survey_id = $id ORDER BY id DESC")->fetchAll();[span_105](end_span)
            [span_106](start_span)foreach($items as &$i) {[span_106](end_span)
                $i['url_foto'] = $i['foto_item'] ? [span_107](start_span)"https://mglstiker.com/uploads/survey/".$i['foto_item'] : "";[span_107](end_span)
            }
            [span_108](start_span)echo json_encode(['status' => 'success', 'client' => $client, 'items' => $items]);[span_108](end_span)
        break;

        default:
            [span_109](start_span)echo json_encode(['status' => 'error', 'message' => 'Action tidak dikenal']);[span_109](end_span)
        break;
    }

} catch (Exception $e) {
    [span_110](start_span)echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);[span_110](end_span)
}
?>
