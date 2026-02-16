<?php
// 1. HIDUPKAN PENDEKTEKSI ERROR (Hanya untuk nyari masalah)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'auth_check.php';

header('Content-Type: application/json');

try {
    // 2. CEK TABEL DAN AMBIL DATA
    // Kita panggil satu-satu biar tau mana yang bikin error
    $stmt = $pdo->query("SELECT id, nama_klien, lokasi FROM surveys ORDER BY id DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kirim hasil
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);

} catch (Exception $e) {
    // 3. TANGKAP ERROR DAN KIRIM SEBAGAI JSON (Biar gak 500)
    http_response_code(200); // Paksa 200 biar Android bisa baca pesannya
    echo json_encode([
        'status' => 'error',
        'message' => 'Penyakitnya: ' . $e->getMessage()
    ]);
}
