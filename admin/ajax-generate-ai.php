<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CEK LOGIN
if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['error' => 'Akses ditolak. Silakan login ulang.']));
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    die(json_encode(['error' => 'Isi topiknya dulu bang!']));
}

$apiKey = GEMINI_API_KEY;
// End-point resmi untuk generate konten
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// PROMPT: Instruksi super ketat biar Google gak ngaco
$prompt = "Buatkan artikel SEO Bahasa Indonesia untuk website 'MGL Sticker'. 
Topik: $topic. 
Output WAJIB JSON murni tanpa markdown, tanpa teks lain. 
Format JSON: {
  \"judul\": \"Judul Artikel\",
  \"konten\": \"Isi minimal 400 kata dengan HTML h2, p, li\",
  \"meta_desc\": \"Ringkasan 160 karakter\",
  \"keywords\": \"5 kata kunci\"
}";

$data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ]
];

// PROSES CURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

// 2. CEK KONEKSI
if ($err) {
    die(json_encode(['error' => 'Gagal kontak Google: ' . $err]));
}

// 3. CEK RESPON GOOGLE
$result = json_decode($response, true);

if ($http_code !== 200) {
    $msg = $result['error']['message'] ?? 'Error tidak diketahui';
    die(json_encode(['error' => 'Google API Error: ' . $msg]));
}

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $raw_text = $result['candidates'][0]['content']['parts'][0]['text'];

    // MEMBERSIHKAN TANDA ```json atau ``` yang sering bikin error parse
    $clean_json = str_replace(['```json', '```'], '', $raw_text);
    $clean_json = trim($clean_json);

    // Pastikan ini adalah JSON valid sebelum dikirim balik ke Javascript
    $test_json = json_decode($clean_json);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $clean_json; // BERHASIL
    } else {
        die(json_encode(['error' => 'AI memberikan format salah, coba klik tombol sekali lagi bang.']));
    }
} else {
    die(json_encode(['error' => 'AI tidak memberikan jawaban. Coba topik lain.']));
}
