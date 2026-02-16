<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');
error_reporting(0);

include '../../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$endpoint = end($uri);
$id = is_numeric($endpoint) ? $endpoint : null;

function response($status, $data = [], $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'data'   => $data
    ]);
    exit;
}

function getApiKey() {
    return $_SERVER['HTTP_X_API_KEY'] ?? '';
}

function auth($pdo) {
    $key = getApiKey();
    if (!$key) response('error', 'API Key kosong', 401);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE api_token=?");
    $stmt->execute([$key]);
    $user = $stmt->fetch();

    if (!$user) response('error', 'API Key salah / expired', 401);

    return $user;
}

/* =====================
   LOGIN (NO AUTH)
===================== */
if (strpos($_SERVER['REQUEST_URI'], 'login') !== false) {

    $input = json_decode(file_get_contents("php://input"), true);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$input['username']]);
    $u = $stmt->fetch();

    if (!$u || !password_verify($input['password'], $u['password'])) {
        response('error', 'Username / Password salah', 401);
    }

    if (!$u['api_token']) {
        $token = bin2hex(random_bytes(16));
        $pdo->prepare("UPDATE users SET api_token=? WHERE id=?")
            ->execute([$token, $u['id']]);
    } else {
        $token = $u['api_token'];
    }

    response('success', [
        'api_key' => $token,
        'user' => [
            'id' => $u['id'],
            'nama' => $u['nama_lengkap'],
            'role' => $u['role']
        ]
    ]);
}

/* =====================
   AUTH MIDDLEWARE
===================== */
$user = auth($pdo);


/* =====================
   ROUTING
===================== */

switch (true) {

    /* ================= USERS ================= */

    case strpos($_SERVER['REQUEST_URI'], 'users') !== false:

        if ($method == 'GET') {
            if ($user['role'] !== 'admin')
                response('error', 'Admin only', 403);

            $data = $pdo->query("SELECT id,username,nama_lengkap,role FROM users")->fetchAll();
            response('success', $data);
        }

        if ($method == 'POST') {
            if ($user['role'] !== 'admin')
                response('error', 'Admin only', 403);

            $input = json_decode(file_get_contents("php://input"), true);

            $hash = password_hash($input['password'], PASSWORD_DEFAULT);

            $pdo->prepare("INSERT INTO users (username,password,nama_lengkap,role)
                           VALUES (?,?,?,?)")
                ->execute([
                    $input['username'],
                    $hash,
                    $input['nama_lengkap'],
                    $input['role'] ?? 'staff'
                ]);

            response('success', 'User dibuat');
        }

        if ($method == 'PUT' && $id) {

            $input = json_decode(file_get_contents("php://input"), true);

            if ($user['role'] !== 'admin' && $user['id'] != $id)
                response('error', 'Tidak boleh edit user lain', 403);

            $query = "UPDATE users SET nama_lengkap=?";
            $params = [$input['nama_lengkap']];

            if (!empty($input['password'])) {
                $query .= ", password=?";
                $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            $query .= " WHERE id=?";
            $params[] = $id;

            $pdo->prepare($query)->execute($params);
            response('success', 'User diupdate');
        }

        if ($method == 'DELETE' && $id) {
            if ($user['role'] !== 'admin')
                response('error', 'Admin only', 403);

            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
            response('success', 'User dihapus');
        }

        break;


    /* ================= STATS ================= */

    case strpos($_SERVER['REQUEST_URI'], 'stats') !== false:

        $g = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
        $s = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();

        response('success', [
            'total_portfolio' => (int)$g,
            'total_survey' => (int)$s
        ]);
        break;


    /* ================= SURVEYS ================= */

    case strpos($_SERVER['REQUEST_URI'], 'surveys') !== false:

        if ($method == 'GET') {
            $data = $pdo->query("SELECT * FROM surveys ORDER BY id DESC")->fetchAll();
            response('success', $data);
        }

        if ($method == 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM surveys WHERE id=?")->execute([$id]);
            response('success', 'Survey dihapus');
        }

        break;


    /* ================= GALLERIES ================= */

    case strpos($_SERVER['REQUEST_URI'], 'galleries') !== false:

        if ($method == 'GET') {
            $data = $pdo->query("SELECT * FROM galleries ORDER BY id DESC")->fetchAll();
            response('success', $data);
        }

        if ($method == 'DELETE' && $id) {
            $pdo->prepare("DELETE FROM galleries WHERE id=?")->execute([$id]);
            response('success', 'Gallery dihapus');
        }

        break;


    default:
        response('error', 'Endpoint tidak ditemukan', 404);
}
