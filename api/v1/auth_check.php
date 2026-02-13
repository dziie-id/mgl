<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// Cara universal ambil header di hosting
$api_token = '';

if (isset($_SERVER['HTTP_X_API_KEY'])) {
    $api_token = $_SERVER['HTTP_X_API_KEY'];
} elseif (isset($_SERVER['X_API_KEY'])) {
    $api_token = $_SERVER['X_API_KEY'];
}

if (empty($api_token)) {
    // Jika masih kosong, coba cek pakai fungsi getallheaders
    $all_headers = function_exists('getallheaders') ? getallheaders() : [];
    // Cek manual (case insensitive)
    foreach ($all_headers as $name => $value) {
        if (strtolower($name) == 'x-api-key') {
            $api_token = $value;
            break;
        }
    }
}

// JIKA TETAP KOSONG
if (empty($api_token)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: X-API-KEY tidak ditemukan di Header']);
    exit;
}

// Cek di database
$stmt = $pdo->prepare("SELECT id, nama_lengkap, role FROM users WHERE api_token = ?");
$stmt->execute([$api_token]);
$user_api = $stmt->fetch();

if (!$user_api) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: API Key tidak valid']);
    exit;
}