<?php
// Matikan semua output error agar tidak merusak JSON
error_reporting(0);
include '../includes/db.php';

// Pastikan respon selalu JSON
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Sesi habis, silakan login ulang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    echo json_encode(['error' => 'Topik belum diisi']);
    exit;
}

$apiKey = GEMINI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// Prompt diperketat
$prompt = "Buatkan artikel blog SEO Bahasa Indonesia untuk website 'MGL Sticker'. Topik: $topic. Balas HANYA dengan JSON murni: {'judul': '...', 'konten': '... (HTML)', 'meta_desc': '...', 'keywords': '...'}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]],
    "generationConfig" => [
        "response_mime_type" => "application/json",
        "temperature" => 0.7
    ]
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
$curl_err = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    echo json_encode(['error' => 'Server Hosting Gagal Koneksi: ' . $curl_err]);
    exit;
}

if ($http_code !== 200) {
    echo json_encode(['error' => 'Google API Error', 'code' => $http_code]);
    exit;
}

$result = json_decode($response, true);
$raw_content = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

if (!empty($raw_content)) {
    // Pastikan respon adalah JSON valid sebelum dikirim
    $clean_json = trim($raw_content);
    // Jika Google ngasih markdown, kita bersihkan
    $clean_json = str_replace(['```json', '```'], '', $clean_json);
    echo $clean_json;
} else {
    echo json_encode(['error' => 'AI Tidak memberikan jawaban']);
}
