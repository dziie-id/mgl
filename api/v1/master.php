<?php
date_default_timezone_set('Asia/Jakarta');
error_reporting(0);
header('Content-Type: application/json');

include '../../includes/db.php';
// Helper untuk ambil header API KEY
function getApiKey() {
    return $_SERVER['HTTP_X_API_KEY'] ?? $_GET['key'] ?? '';
}

$action = $_GET['action'] ?? '';
$apiKey = getApiKey();

// --- 1. BYPASS LOGIN TANPA API KEY ---
if ($action == 'login') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $u = $stmt->fetch();
    if ($u && password_verify($pass, $u['password'])) {
        if (empty($u['api_token'])) {
            $token = bin2hex(random_bytes(16));
            $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?")->execute([$token, $u['id']]);
        } else { $token = $u['api_token']; }
        echo json_encode([
            'status' => 'success',
            'api_key' => $token,
            'user_data' => ['user_id' => $u['id'], 'nama' => $u['nama_lengkap'], 'role' => $u['role']]
        ]);
    } else { echo json_encode(['status' => 'error', 'message' => 'User/Pass Salah']); }
    exit;
}

// --- 2. CEK VALIDASI API KEY UNTUK ACTION LAINNYA ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ?");
$stmt->execute([$apiKey]);
$user_api = $stmt->fetch();
if (!$user_api) {
    die(json_encode(['status' => 'error', 'message' => 'Sesi Habis / API Key Salah']));
}

switch ($action) {
    case 'stats':
        $g = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
        $s = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();
        echo json_encode(['status' => 'success', 'data' => ['stats' => ['total_portfolio' => (int)$g, 'total_survey' => (int)$s]]]);
        break;

    case 'survey_list':
        // Hapus Survey (Induk)
        if (isset($_GET['del'])) {
            $id = $_GET['del'];
            $pdo->prepare("DELETE FROM surveys WHERE id = ?")->execute([$id]);
            echo json_encode(['status' => 'success']); break;
        }
        $data = $pdo->query("SELECT * FROM surveys ORDER BY id DESC")->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'survey_detail':
        $id = $_GET['id'];
        // Hapus Item
        if (isset($_GET['del_item'])) {
            $pdo->prepare("DELETE FROM survey_items WHERE id = ?")->execute([$_GET['del_item']]);
            echo json_encode(['status' => 'success']); break;
        }
        // Simpan / Update Item (Multipart)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $item_id = $_POST['item_id'] ?? null;
            $nama = $_POST['nama_bagian'];
            $p = $_POST['p']; $l = $_POST['l']; $t = $_POST['t']; $qty = $_POST['qty'];
            $foto = $_POST['foto_lama'] ?? "";
            if (!empty($_FILES['foto']['name'])) {
                $foto = "SRV-" . time() . ".jpg";
                move_uploaded_file($_FILES['foto']['tmp_name'], "../../uploads/survey/" . $foto);
            }
            if ($item_id) {
                $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=?, foto_item=? WHERE id=?")
                    ->execute([$nama, $p, $l, $t, $qty, $foto, $item_id]);
            } else {
                $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$id, $nama, $p, $l, $t, $qty, $foto]);
            }
            echo json_encode(['status' => 'success']); break;
        }
        $client = $pdo->query("SELECT * FROM surveys WHERE id = $id")->fetch();
        $items = $pdo->query("SELECT * FROM survey_items WHERE survey_id = $id ORDER BY id DESC")->fetchAll();
        foreach($items as &$itm) { $itm['url_foto'] = $itm['foto_item'] ? "https://mglstiker.com/uploads/survey/".$itm['foto_item'] : ""; }
        echo json_encode(['status' => 'success', 'client' => $client, 'items' => $items]);
        break;

    case 'survey_save_all': // Simpan dari form Tambah Survey
        $nama = $_POST['nama_klien'];
        $lok = $_POST['lokasi'];
        $gps = $_POST['koordinat'];
        $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi, koordinat) VALUES (?,?,?)")->execute([$nama, $lok, $gps]);
        $sid = $pdo->lastInsertId();
        $items = json_decode($_POST['items'], true);
        foreach($items as $i => $itm) {
            $f = "";
            if (!empty($_FILES["foto_$i"]['name'])) {
                $f = "SRV-" . time() . "-$i.jpg";
                move_uploaded_file($_FILES["foto_$i"]['tmp_name'], "../../uploads/survey/" . $f);
            }
            $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?,?,?,?,?,?,?)")
                ->execute([$sid, $itm['nama_bagian'], $itm['p'], $itm['l'], $itm['t'], $itm['qty'], $f]);
        }
        echo json_encode(['status' => 'success']);
        break;

    case 'gallery':
        if (isset($_GET['del_batch'])) {
            $ids = explode(',', $_POST['ids']);
            foreach($ids as $id) { $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]); }
            echo json_encode(['status' => 'success']); break;
        }
        $data = $pdo->query("SELECT * FROM galleries ORDER BY id DESC")->fetchAll();
        foreach($data as &$d) { $d['url_gambar'] = "https://mglstiker.com/uploads/gallery/".$d['file_name']; }
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'gallery_upload':
        include '../../admin/functions.php';
        $p_name = $_POST['project_name'];
        $kat = $_POST['kategori'];
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $p_name)));
        foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp) {
            $fn = $slug . "-mgl-" . time() . "-$key.webp";
            $res = resize_crop_image($tmp, 1200, 800);
            imagewebp($res, "../../uploads/gallery/" . $fn, 80);
            $pdo->prepare("INSERT INTO galleries (file_name, alt_text, kategori) VALUES (?,?,?)")->execute([$fn, $p_name, $kat]);
        }
        echo json_encode(['status' => 'success']);
        break;

    case 'users':
        if ($user_api['role'] != 'admin') die(json_encode(['status' => 'error', 'message' => 'Bukan Admin']));
        if ($_GET['do'] == 'add') {
            $h = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?,?,?,?)")
                ->execute([$_POST['username'], $h, $_POST['nama'], $_POST['role']]);
        } elseif ($_GET['do'] == 'del') {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$_POST['user_id']]);
        } elseif ($_GET['do'] == 'update') {
            $target = $_POST['user_id'];
            $nama = $_POST['nama_lengkap'];
            if (!empty($_POST['password'])) {
                $h = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET nama_lengkap=?, password=? WHERE id=?")->execute([$nama, $h, $target]);
            } else {
                $pdo->prepare("UPDATE users SET nama_lengkap=? WHERE id=?")->execute([$nama, $target]);
            }
        }
        $users = $pdo->query("SELECT id, username, nama_lengkap, role FROM users")->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $users]);
        break;
}
