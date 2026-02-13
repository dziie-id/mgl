<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// Tangkap data dari Aplikasi Android
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if (empty($user) || empty($pass)) {
    echo json_encode(['status' => 'error', 'message' => 'Username & Password wajib diisi']);
    exit;
}

// Cari user di database
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$user]);
$admin = $stmt->fetch();

if ($admin && password_verify($pass, $admin['password'])) {
    // Jika login benar, buatkan Token unik jika belum ada
    if (empty($admin['api_token'])) {
        $token = bin2hex(random_bytes(32)); // Buat kunci acak
        $update = $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?");
        $update->execute([$token, $admin['id']]);
    } else {
        $token = $admin['api_token'];
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Login Berhasil',
        'api_key' => $token,
        'user_data' => [
            'nama' => $admin['nama_lengkap'],
            'role' => $admin['role']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Username atau Password salah']);
}