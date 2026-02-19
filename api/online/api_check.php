<?php
include 'config.php';

// Cek Header Rahasia
$headers = apache_request_headers();
$secret_key = "GueDriverGacor2026"; // Lu bebas ganti apa aja

if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== $secret_key) {
    header('HTTP/1.0 404 Not Found'); // Pura-pura file gak ada
    exit;
}

$hwid = $_GET['hwid'];

// 1. Validasi Driver
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE hwid = '$hwid' AND status_aktif = '1' AND tgl_expired > NOW()");

if (mysqli_num_rows($queryUser) > 0) {
    // 2. Ambil SEMUA Config (Gojek, Grab, Maps)
    $configs = [];
    $queryCfg = mysqli_query($conn, "SELECT * FROM app_config");
    while ($row = mysqli_fetch_assoc($resCfg)) {
        // Kita simpan pake nama service sebagai Key-nya
        $configs[$row['service_name']] = $row['token_value'];
    }

    echo json_encode([
        "status" => "success",
        "melek"  => true,
        "config" => $configs // Isinya otomatis ada gofood, grabfood, maps_style, dll
    ]);
} else {
    echo json_encode(["status" => "error", "melek" => false]);
}
