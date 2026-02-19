<?php
include 'config.php';

// 1. Setting Header Agar Responnya JSON
header('Content-Type: application/json');

// 2. Proteksi Header: Cek apakah request bawa X-API-KEY rahasia
$headers = apache_request_headers();
if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== $SECRET_KEY) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Akses Ilegal!"]);
    exit;
}

// 3. Ambil HWID dari Parameter GET
$hwid = isset($_GET['hwid']) ? aman($_GET['hwid']) : '';

if (empty($hwid)) {
    echo json_encode(["status" => "error", "message" => "HWID Tidak Ditemukan"]);
    exit;
}

// 4. Cek Status Driver di Database
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE hwid = '$hwid' AND status_aktif = '1' AND tgl_expired > NOW()");

if (mysqli_num_rows($queryUser) > 0) {
    $driver = mysqli_fetch_assoc($queryUser);
    
    // 5. Ambil Semua Peluru (Token & Maps Style)
    $configs = [];
    $queryCfg = mysqli_query($conn, "SELECT * FROM app_config");
    while($row = mysqli_fetch_assoc($queryCfg)) {
        $configs[$row['service_name']] = $row['token_value'];
    }

    // 6. Respon Sukses (Aplikasi Jadi Melek)
    echo json_encode([
        "status" => "success",
        "melek"  => true,
        "driver" => $driver['nama_driver'],
        "exp"    => $driver['tgl_expired'],
        "peluru" => $configs
    ]);
} else {
    // 7. Respon Gagal (Aplikasi Tetap Butek)
    echo json_encode([
        "status" => "error",
        "melek"  => false,
        "message" => "HWID Belum Aktif atau Expired!"
    ]);
}
?>
