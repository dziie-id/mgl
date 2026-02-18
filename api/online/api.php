<?php
include "config.php";

$hwid = $_GET['hwid'] ?? '';

if (empty($hwid)) {
    die(json_encode(["status" => "error", "message" => "HWID Kosong"]));
}

// 1. Cek apakah HWID terdaftar dan aktif
$checkDriver = $conn->query("SELECT status FROM drivers WHERE hwid = '$hwid'");
$driverData = $checkDriver->fetch_assoc();

// 2. Ambil Global Setting (Token & Map Key)
$getSettings = $conn->query("SELECT * FROM settings WHERE id = 1");
$settings = $getSettings->fetch_assoc();

if ($driverData) {
    // Balikin data ke APK dalem bentuk JSON
    echo json_encode([
        "status" => $driverData['status'], // 'active' atau 'inactive'
        "map_key" => $settings['map_key'],
        "gojek_token" => $settings['gojek_token'],
        "grab_token" => $settings['grab_token']
    ]);
} else {
    // Kalau HWID belum ada di DB, otomatis masukin sebagai 'inactive' biar lu tinggal acc di dashboard
    $conn->query("INSERT INTO drivers (hwid, status) VALUES ('$hwid', 'inactive')");
    echo json_encode([
        "status" => "inactive",
        "map_key" => $settings['map_key'],
        "message" => "HWID Baru Terdaftar, Hubungi Admin"
    ]);
}
