<?php
include '../../includes/db.php';
header('Content-Type: application/json');

$api_token = '';

// 1. Coba ambil dari Header (Standar Aplikasi Android)
if (isset($_SERVER['HTTP_X_API_KEY'])) {
    $api_token = $_SERVER['HTTP_X_API_KEY'];
} elseif (isset($_SERVER['X_API_KEY'])) {
    $api_token = $_SERVER['X_API_KEY'];
}

// 2. Kalau di Header gak ada, coba ambil dari URL (Khusus buat ngetes di Chrome)
if (empty($api_token) && isset($_GET['key'])) {
    $api_token = $_GET['key'];
}

// 3. Kalau tetep kosong, baru ditolak
if (empty($api_token)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'API Key diperlukan (Header X-API-KEY atau param ?key=)']);
    exit;
}

// 4. Cek ke Database
$stmt = $pdo->prepare("SELECT id, nama_lengkap, role FROM users WHERE api_token = ?");
$stmt->execute([$api_token]);
$user_api = $stmt->fetch();

if (!$user_api) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'API Key salah bang!']);
    exit;
}
// Berhasil, $user_api bisa lanjut dipake