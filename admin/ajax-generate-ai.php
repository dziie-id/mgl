<?php
error_reporting(0);
ob_start();

include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Login ulang bang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
$apiKey = trim(GEMINI_API_KEY);

// --- PAKAI MODEL 1.5 FLASH & JALUR v1 (PALING STABIL & JATAH BANYAK) ---
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Buat artikel SEO blog Bahasa Indonesia untuk 'MGL Sticker'. Topik: $topic. Balas HANYA JSON murni: {\"judul\":\"...\",\"konten\":\"... (HTML h2, p, li)\",\"meta_desc\":\"...\",\"keywords\":\"...\"}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]]
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

ob_end_clean();

if ($http_code === 200) {
    $result = json_decode($response, true);
    $raw_text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

    // Saringan JSON
    $start = strpos($raw_text, '{');
    $end = strrpos($raw_text, '}');

    if ($start !== false && $end !== false) {
        echo substr($raw_text, $start, ($end - $start) + 1);
    } else {
        echo json_encode(['error' => 'AI ngirim format salah. Coba lagi bang.']);
    }
} else {
    $res_err = json_decode($response, true);
    $pesan = $res_err['error']['message'] ?? 'Quota Habis/Error';

    // Kasih solusi kalau kuota habis
    if (strpos($pesan, 'quota') !== false || $http_code == 429) {
        echo json_encode(['error' => 'Google lagi pelit kuota di akun ini. Coba pake Akun Google lain buat bikin API Key baru, atau tunggu 1 jam lagi bang.']);
    } else {
        echo json_encode(['error' => 'Google Bilang: ' . $pesan]);
    }
}
