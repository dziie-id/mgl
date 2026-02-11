<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['error' => 'Login dulu bang']));
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    die(json_encode(['error' => 'Topik kosong']));
}

$apiKey = GEMINI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Buatkan artikel blog SEO Bahasa Indonesia untuk 'MGL Sticker'. Topik: $topic. Balas HANYA dengan JSON murni: {'judul': '...', 'konten': '... (HTML h2, p, li)', 'meta_desc': '...', 'keywords': '...'}";

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
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => 'API Google Error', 'code' => $http_code]);
    exit;
}

$result = json_decode($response, true);
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo $result['candidates'][0]['content']['parts'][0]['text'];
} else {
    echo json_encode(['error' => 'AI Gagal merespon']);
}
