<?php
include '../includes/db.php';
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Login dulu bang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
$apiKey = GEMINI_API_KEY;

// PAKAI URL v1 (Bukan v1beta) biar lebih stabil
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Buatkan artikel blog SEO Bahasa Indonesia tentang: $topic. Website: MGL Sticker Jakarta. Balas HANYA JSON murni: {'judul': '...', 'konten': '... (HTML h2, p)', 'meta_desc': '...', 'keywords': '...'}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]],
    "generationConfig" => ["response_mime_type" => "application/json"]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($http_code !== 200) {
    // AMBIL PESAN ERROR ASLI DARI GOOGLE
    $pesan_google = isset($result['error']['message']) ? $result['error']['message'] : 'Gak tau kenapa';
    echo json_encode(['error' => 'Google Bilang: ' . $pesan_google]);
    exit;
}

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo $result['candidates'][0]['content']['parts'][0]['text'];
} else {
    echo json_encode(['error' => 'Jawaban AI kosong']);
}
