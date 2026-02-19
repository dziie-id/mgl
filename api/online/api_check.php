<?php
include 'config.php';

header('Content-Type: application/json');

// 1. Proteksi Header
$headers = apache_request_headers();
$apiKeySent = isset($headers['X-API-KEY']) ? $headers['X-API-KEY'] : (isset($headers['x-api-key']) ? $headers['x-api-key'] : '');

if ($apiKeySent !== $SECRET_KEY) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Akses Ilegal!"]);
    exit;
}

// 2. Ambil HWID
$hwid = isset($_GET['hwid']) ? aman($_GET['hwid']) : '';

if (empty($hwid)) {
    echo json_encode(["status" => "error", "message" => "HWID Kosong"]);
    exit;
}

// 3. Cek Status Driver & Expired
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE hwid = '$hwid' AND status_aktif = '1' AND tgl_expired > NOW()");

if (mysqli_num_rows($queryUser) > 0) {
    $driver = mysqli_fetch_assoc($queryUser);
    
    // 4. Ambil Semua Config (Termasuk google_api_key yang lu tambah tadi)
    $configs = [];
    $queryCfg = mysqli_query($conn, "SELECT * FROM app_config");
    while($row = mysqli_fetch_assoc($queryCfg)) {
        $configs[$row['service_name']] = $row['token_value'];
    }

    echo json_encode([
        "status" => "success",
        "melek"  => true,
        "driver" => $driver['nama_driver'],
        "exp"    => $driver['tgl_expired'],
        "peluru" => $configs // Isinya: gofood, grabfood, maps_style, google_api_key
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "melek"  => false,
        "message" => "AKSES DITOLAK: HWID BELUM AKTIF / EXPIRED"
    ]);
}
?>
