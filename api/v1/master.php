<?php
date_default_timezone_set('Asia/Jakarta');
error_reporting(0); // Matikan error PHP biar JSON gak rusak
header('Content-Type: application/json');

include '../../includes/db.php';
// Include fungsi watermark dari admin panel
include '../../admin/functions.php';

// --- HELPER: AMBIL API KEY ---
function getApiKey()
{
    // Cek Header (Android) atau GET (Browser)
    if (isset($_SERVER['HTTP_X_API_KEY'])) return $_SERVER['HTTP_X_API_KEY'];
    if (isset($_SERVER['X_API_KEY'])) return $_SERVER['X_API_KEY'];
    return $_GET['key'] ?? '';
}

$action = $_GET['action'] ?? '';
$apiKey = getApiKey();

// =======================================================================
// 1. LOGIN (Tanpa Cek Token)
// =======================================================================
if ($action == 'login') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['password'])) {
        // Generate token baru jika belum ada
        if (empty($u['api_token'])) {
            $token = bin2hex(random_bytes(16));
            $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?")->execute([$token, $u['id']]);
        } else {
            $token = $u['api_token'];
        }

        echo json_encode([
            'status' => 'success',
            'api_key' => $token,
            'user_data' => [
                'user_id' => $u['id'],
                'nama' => $u['nama_lengkap'],
                'role' => $u['role']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username atau Password Salah']);
    }
    exit;
}

// =======================================================================
// CEK VALIDASI TOKEN (Gatekeeper)
// =======================================================================
if (empty($apiKey)) {
    die(json_encode(['status' => 'error', 'message' => 'API Key tidak ditemukan']));
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ?");
$stmt->execute([$apiKey]);
$user_api = $stmt->fetch();

if (!$user_api) {
    die(json_encode(['status' => 'error', 'message' => 'API Key tidak valid atau sesi habis']));
}

// =======================================================================
// ROUTING ACTION
// =======================================================================
try {
    switch ($action) {

        // --- A. DASHBOARD STATS ---
        case 'stats':
            $g = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
            $s = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();
            $a = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
            echo json_encode([
                'status' => 'success',
                'data' => ['stats' => ['total_portfolio' => (int)$g, 'total_survey' => (int)$s, 'total_artikel' => (int)$a]]
            ]);
            break;

        // --- B. GALLERY (LIST & DELETE) ---
        case 'gallery':
            // Hapus Batch (Banyak sekaligus)
            if (isset($_GET['del_batch']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
                $ids = explode(',', $_POST['ids']);
                foreach ($ids as $id) {
                    $stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");
                    $stmt->execute([$id]);
                    $img = $stmt->fetch();
                    if ($img) {
                        @unlink("../../uploads/gallery/" . $img['file_name']);
                        $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]);
                    }
                }
                echo json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus']);
                break;
            }

            // AMBIL LIST DENGAN PAGINATION
            $limit = 20; // Muat 20 foto per scroll
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Hitung total halaman (biar Android tau kapan stop loading)
            $total_data = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
            $total_page = ceil($total_data / $limit);

            $stmt = $pdo->prepare("SELECT * FROM galleries ORDER BY id DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll();

            foreach ($data as &$d) {
                $d['url_gambar'] = "https://mglstiker.com/uploads/gallery/" . $d['file_name'];
            }

            echo json_encode([
                'status' => 'success',
                'total_page' => $total_page,
                'current_page' => $page,
                'data' => $data
            ]);
            break;

        // --- C. UPLOAD GALLERY (BAKED WATERMARK) ---
        case 'gallery_upload':
            if (empty($_FILES['fotos']['name'][0])) {
                die(json_encode(['status' => 'error', 'message' => 'Tidak ada file diupload']));
            }

            $p_name = $_POST['project_name'];
            $kat    = $_POST['kategori'];
            $slug   = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $p_name)));

            // Ambil setting watermark terbaru
            $wm = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();

            $success_count = 0;
            foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                $new_filename = $slug . "-mgl-" . time() . "-$key.webp";
                $target_path  = "../../uploads/gallery/" . $new_filename;

                // 1. Resize Gambar (Biar ringan)
                $image_res = resize_crop_image($tmp_name, 1200, 800);

                if ($image_res) {
                    // 2. Terapkan Watermark (Baking)
                    // Fungsi ini ada di admin/functions.php
                    apply_baked_watermark($image_res, $target_path, [
                        'type' => $wm['wm_type'],
                        'wm_image' => $wm['wm_image'],
                        'text' => $wm['wm_text'],
                        'opacity' => $wm['wm_opacity'],
                        'rotate' => $wm['wm_rotate'],
                        'font_size' => $wm['wm_font_size'],
                        'color_hex' => $wm['wm_color'],
                        'position' => $wm['wm_position']
                    ]);

                    // 3. Simpan ke DB
                    $alt = ucwords(str_replace('-', ' ', $slug)) . " - MGL Sticker";
                    $stmt = $pdo->prepare("INSERT INTO galleries (file_name, alt_text, kategori, watermark_applied) VALUES (?, ?, ?, '1')");
                    $stmt->execute([$new_filename, $alt, $kat]);
                    $success_count++;
                }
            }
            echo json_encode(['status' => 'success', 'message' => "$success_count Foto berhasil diupload & watermark!"]);
            break;

        // --- D. SURVEY LIST & DELETE ---
        case 'survey_list':
            // 1. FITUR FAST DELETE (TETAP DIPERTAHANKAN)
            if (isset($_GET['del'])) {
                $id = $_GET['del'];

                // Ambil semua foto item dulu buat dihapus dari folder
                $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE survey_id = ?");
                $stmt->execute([$id]);
                foreach ($stmt->fetchAll() as $itm) {
                    if (!empty($itm['foto_item'])) {
                        @unlink("../../uploads/survey/" . $itm['foto_item']);
                    }
                }

                // Hapus data di DB (Items otomatis hilang kalau pakai Cascade, tapi aman manual aja)
                $pdo->prepare("DELETE FROM survey_items WHERE survey_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM surveys WHERE id = ?")->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Survey dan semua item berhasil dihapus']);
                break; // Stop di sini kalau cuma mau hapus
            }

            // 2. LIST DATA DENGAN PAGINATION (BIAR HP GAK LEMOT)
            $limit = 10; // Muat 10 survey per scroll
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Query ambil data survey
            $sql = "SELECT * FROM surveys ORDER BY id DESC LIMIT $limit OFFSET $offset";
            $data = $pdo->query($sql)->fetchAll();

            // Hitung total buat info ke Android
            $total = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();

            echo json_encode([
                'status' => 'success',
                'total_data' => $total,
                'current_page' => $page,
                'data' => $data
            ]);
            break;

        // --- E. SURVEY DETAIL (ADD/EDIT ITEM) ---
        case 'survey_detail':
            $id = $_GET['id']; // ID Survey

            // 1. HAPUS ITEM (TETAP SAMA)
            if (isset($_GET['del_item'])) {
                $iid = $_GET['del_item'];
                $f = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
                $f->execute([$iid]);
                $img = $f->fetch();
                if ($img && !empty($img['foto_item'])) @unlink("../../uploads/survey/" . $img['foto_item']);

                $pdo->prepare("DELETE FROM survey_items WHERE id = ?")->execute([$iid]);
                echo json_encode(['status' => 'success', 'message' => 'Item dihapus']);
                break;
            }

            // 2. MENANGANI DATA MASUK (POST)
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $sub = $_GET['sub_action'] ?? ''; // Pembeda aksi

                // --- BAGIAN 2A: UPDATE MASTER SURVEY (NAMA & ALAMAT) ---
                // Android request ke: ?action=survey_detail&id=1&sub_action=update_master
                if ($sub == 'update_master') {
                    $nama = $_POST['nama_klien'];
                    $lokasi = $_POST['lokasi'];
                    // Koordinat opsional, kalau mau diupdate sekalian
                    $gps = $_POST['koordinat'] ?? '';

                    // Update Tabel Surveys (Induk)
                    $sql = "UPDATE surveys SET nama_klien=?, lokasi=?";
                    $params = [$nama, $lokasi];

                    if (!empty($gps)) {
                        $sql .= ", koordinat=?";
                        $params[] = $gps;
                    }
                    $sql .= " WHERE id=?";
                    $params[] = $id;

                    $pdo->prepare($sql)->execute($params);
                    echo json_encode(['status' => 'success', 'message' => 'Data pelanggan berhasil diubah']);
                    break; // Stop biar gak lari ke bawah
                }

                // --- BAGIAN 2B: SIMPAN / UPDATE ITEM (KODE LAMA ABANG) ---
                // Android request ke: ?action=survey_detail&id=1&sub_action=item_save
                $act_item = $_POST['mode_item'] ?? 'add'; // add atau update
                $nama_bg = $_POST['nama_bagian'];
                $p = $_POST['p'];
                $l = $_POST['l'];
                $t = $_POST['t'];
                $qty = $_POST['qty'];
                $foto_db = $_POST['foto_lama'] ?? "";

                if (!empty($_FILES['foto']['name'])) {
                    if (!empty($foto_db)) @unlink("../../uploads/survey/" . $foto_db);
                    $foto_db = "API-SRV-" . time() . ".jpg";
                    move_uploaded_file($_FILES['foto']['tmp_name'], "../../uploads/survey/" . $foto_db);
                }

                if ($act_item == 'update') {
                    $iid = $_POST['item_id'];
                    $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=?, foto_item=? WHERE id=?")
                        ->execute([$nama_bg, $p, $l, $t, $qty, $foto_db, $iid]);
                } else {
                    $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?,?,?,?,?,?,?)")
                        ->execute([$id, $nama_bg, $p, $l, $t, $qty, $foto_db]);
                }
                echo json_encode(['status' => 'success', 'message' => 'Item berhasil disimpan']);
                break;
            }

            // 3. GET DATA (TAMPILAN DETAIL)
            $client = $pdo->query("SELECT * FROM surveys WHERE id = $id")->fetch();
            $items = $pdo->query("SELECT * FROM survey_items WHERE survey_id = $id ORDER BY id DESC")->fetchAll();

            // Tambah full URL buat foto item
            foreach ($items as &$i) {
                $i['url_foto'] = $i['foto_item'] ? "https://mglstiker.com/uploads/survey/" . $i['foto_item'] : "";
            }

            echo json_encode(['status' => 'success', 'client' => $client, 'items' => $items]);
            break;

        // --- F. SIMPAN SURVEY MASSAL (DARI FORM INPUT) ---
        case 'survey_save_all':
            $nama = $_POST['nama_klien'];
            $lok = $_POST['lokasi'];
            $gps = $_POST['koordinat'];

            $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi, koordinat) VALUES (?,?,?)")->execute([$nama, $lok, $gps]);
            $sid = $pdo->lastInsertId();

            $items = json_decode($_POST['items'], true);
            foreach ($items as $idx => $itm) {
                $f = "";
                if (!empty($_FILES["foto_$idx"]['name'])) {
                    $f = "API-SRV-" . time() . "-$idx.jpg";
                    move_uploaded_file($_FILES["foto_$idx"]['tmp_name'], "../../uploads/survey/" . $f);
                }
                $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$sid, $itm['nama_bagian'], $itm['p'], $itm['l'], $itm['t'], $itm['qty'], $f]);
            }
            echo json_encode(['status' => 'success']);
            break;

        // --- G. USER MANAGER (TAMBAH, LIST, UPDATE, DELETE) ---
        case 'users':
            $do = $_GET['do'] ?? 'list';

            // 1. LIST USERS (Admin Only)
            if ($do == 'list') {
                if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Hanya Admin']));
                $data = $pdo->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY role ASC")->fetchAll();
                echo json_encode(['status' => 'success', 'data' => $data]);
            }

            // 2. TAMBAH USER (Admin Only)
            elseif ($do == 'add') {
                if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Hanya Admin']));
                $u = $_POST['username'];
                $n = $_POST['nama_lengkap'];
                $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $r = $_POST['role'];

                // Cek username kembar
                $cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $cek->execute([$u]);
                if ($cek->rowCount() > 0) die(json_encode(['status' => 'error', 'message' => 'Username sudah ada']));

                $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?,?,?,?)")
                    ->execute([$u, $p, $n, $r]);
                echo json_encode(['status' => 'success', 'message' => 'User berhasil dibuat']);
            }

            // 3. DELETE USER (Admin Only)
            elseif ($do == 'delete') {
                if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Hanya Admin']));
                $uid = $_POST['user_id'];
                if ($uid == $user_api['id']) die(json_encode(['status' => 'error', 'message' => 'Jangan hapus diri sendiri']));

                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
                echo json_encode(['status' => 'success', 'message' => 'User dihapus']);
            }

            // 4. UPDATE (Ganti Password & Nama)
            elseif ($do == 'update') {
                $uid = $_POST['user_id'];
                $nama = $_POST['nama_lengkap'];
                $pass = $_POST['password'];

                // Staff cuma boleh edit diri sendiri
                if ($user_api['role'] != 'admin' && $user_api['id'] != $uid) {
                    die(json_encode(['status' => 'error', 'message' => 'Akses ditolak']));
                }

                // Ambil data lama biar nama gak ilang
                $old = $pdo->query("SELECT nama_lengkap FROM users WHERE id = $uid")->fetch();
                $final_nama = !empty($nama) ? $nama : $old['nama_lengkap'];

                if (!empty($pass)) {
                    $h = password_hash($pass, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE users SET nama_lengkap=?, password=? WHERE id=?")->execute([$final_nama, $h, $uid]);
                } else {
                    $pdo->prepare("UPDATE users SET nama_lengkap=? WHERE id=?")->execute([$final_nama, $uid]);
                }
                echo json_encode(['status' => 'success', 'message' => 'Profil diperbarui']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Action tidak dikenal']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
