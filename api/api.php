<?php
header("Content-Type: application/json");
error_reporting(0);
include '../includes/db.php';
include '../admin/functions.php'; 

/* =========================
   HELPER RESPONSE
========================= */
function response($status, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        "success" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

/* =========================
   PARSE REQUEST
========================= */
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $uri);
$endpoint = end($segments);

/* =========================
   AUTH FUNCTION
========================= */
function authenticate($pdo) {

    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        response(false, "Unauthorized", null, 401);
    }

    $token = $matches[1];

    $stmt = $pdo->prepare("SELECT id, nama_lengkap, role FROM users WHERE api_token=?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        response(false, "Invalid Token", null, 403);
    }

    return $user;
}

/* =========================
   LOGIN
========================= */
if ($endpoint === "login" && $method === "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (!$username || !$password) {
        response(false, "Username & password wajib diisi", null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        response(false, "Login gagal", null, 401);
    }

    if (empty($user['api_token'])) {
        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE users SET api_token=? WHERE id=?")
            ->execute([$token, $user['id']]);
    } else {
        $token = $user['api_token'];
    }

    response(true, "Login berhasil", [
        "token" => $token,
        "user" => [
            "id" => $user['id'],
            "nama" => $user['nama_lengkap'],
            "role" => $user['role']
        ]
    ]);
}

/* =========================
   PROTECTED ROUTES
========================= */
$user = authenticate($pdo);

/* =========================
   STATS
========================= */
if ($endpoint === "stats" && $method === "GET") {

    $totalFoto = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
    $totalSurvey = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();

    response(true, "OK", [
        "total_portfolio" => (int)$totalFoto,
        "total_survey" => (int)$totalSurvey
    ]);
}

/* =========================
   LIST SURVEYS
========================= */
if ($endpoint === "surveys" && $method === "GET") {

    $data = $pdo->query("SELECT id,nama_klien,lokasi FROM surveys ORDER BY id DESC")->fetchAll();
    response(true, "OK", $data);
}

/* =========================
   CREATE SURVEY (JSON ONLY)
========================= */
if ($endpoint === "surveys" && $method === "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien,lokasi) VALUES (?,?)");
        $stmt->execute([$input['nama_klien'], $input['lokasi']]);
        $survey_id = $pdo->lastInsertId();

        foreach ($input['items'] as $item) {
            $pdo->prepare("INSERT INTO survey_items 
                (survey_id,nama_bagian,p,l,t,qty)
                VALUES (?,?,?,?,?,?)")
                ->execute([
                    $survey_id,
                    $item['nama_bagian'],
                    $item['p'],
                    $item['l'],
                    $item['t'],
                    $item['qty']
                ]);
        }

        $pdo->commit();
        response(true, "Survey berhasil dibuat", ["survey_id"=>$survey_id]);

    } catch (Exception $e) {
        $pdo->rollBack();
        response(false, "Gagal menyimpan survey", null, 500);
    }
}

response(false, "Endpoint tidak ditemukan", null, 404);
