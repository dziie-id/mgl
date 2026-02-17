<?php
header("Content-Type: application/json");
error_reporting(0);
include '../includes/db.php';
// --- JURUS ANTI KERETA JSON ---
ob_start(); // Tampung semua output liar (termasuk dari functions.php)
include '../admin/functions.php'; 
ob_clean(); // Buang tulisan "Action tidak dikenali" yang nyampah tadi
// ------------------------------
/* =========================
   HELPER RESPONSE
========================= */
function response($status, $message, $data = null, $code = 200) {
    // Bersihkan buffer biar nggak ada karakter liar atau JSON dobel
    if (ob_get_length()) ob_clean(); 
    
    http_response_code($code);
    echo json_encode([
        "success" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit; // WAJIB EXIT biar gak bablas ke bawah!
}

/* =========================
   PARSE REQUEST
========================= */
// Ambil action dari POST atau GET (Biar support Android & Browser)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* =========================
   1. LOGIN (NO PROTECTED)
========================= */
if ($action === "login") {
    // Android kirim Form-Urlencoded, tapi kita jagain kalau ada yang kirim JSON
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        response(false, "Username & password wajib diisi Bang!", null, 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        response(false, "Login gagal, cek lagi pass-nya", null, 401);
    }

    // Token Logic
    if (empty($user['api_token'])) {
        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE users SET api_token=? WHERE id=?")->execute([$token, $user['id']]);
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
   PROTECTED ROUTES (Selain Login harus lewat sini)
========================= */
$currentUser = authenticate($pdo);

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
