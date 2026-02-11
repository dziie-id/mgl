<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    die("Akses ditolak");
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    die(json_encode(['error' => 'Topik kosong']));
}

$apiKey = GEMINI_API_KEY;
// Gunakan endpoint v1beta dengan parameter tambahan
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// Prompt lebih tegas
$prompt = "Buat artikel SEO website 'MGL Sticker Jakarta'. Topik: $topic. Judul, Konten (HTML h2, p, li), Meta Desc (160 char), Keywords (5 kata).";

// Data dengan instruksi format JSON murni
$data = [
    "contents" => [["parts" => [["text" => $prompt]]]],
    "generationConfig" => [
        "response_mime_type" => "application/json", // JURUS BIAR HASILNYA BUKAN TEXT TAPI JSON
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Abaikan SSL error di hosting
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Kasih waktu 1 menit (karena nulis artikel butuh waktu)

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

// 1. Cek kalau server hosting matiin cURL
if ($err) {
    die(json_encode(['error' => 'Koneksi Server Hosting Error (cURL): ' . $err]));
}

// 2. Cek kalau API Key salah atau Limit
if ($http_code !== 200) {
    die(json_encode(['error' => 'API Google Error Code: ' . $http_code, 'detail' => $response]));
}

$result = json_decode($response, true);

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    // Kirim langsung responnya karena sudah pasti format JSON dari sononya
    echo $result['candidates'][0]['content']['parts'][0]['text'];
} else {
    echo json_encode(['error' => 'Format Balasan AI Salah', 'raw' => $result]);
}
