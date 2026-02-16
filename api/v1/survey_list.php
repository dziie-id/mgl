<?php
// Matikan error reporting agar tidak merusak format JSON
error_reporting(0); 

include 'auth_check.php';

try {
    // Ambil data klien
    $stmt = $pdo->query("SELECT id, nama_klien, lokasi, koordinat FROM surveys ORDER BY id DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
