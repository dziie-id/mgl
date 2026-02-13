<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// Ambil API Key dari Header permintaan Android
$headers = apache_request_headers();
$api_token = $headers['X-API-KEY'] ?? '';

if (empty($api_token)) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: API Key diperlukan']);
    exit;
}

// Cek di database apakah tokennya valid
$stmt = $pdo->prepare("SELECT id, nama_lengkap, role FROM users WHERE api_token = ?");
$stmt->execute([$api_token]);
$user_api = $stmt->fetch();

if (!$user_api) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: API Key tidak valid']);
    exit;
}
// Jika berhasil, variabel $user_api bisa dipakai di file selanjutnya