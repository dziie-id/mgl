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

// --- PAKAI MODEL 2.0 FLASH (TERBARU) ---
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

$prompt = "Tulis artikel SEO blog Bahasa Indonesia untuk website 'MGL Sticker'. Topik: $topic. Balas HANYA dengan format JSON murni: {\"judul\":\"...\",\"konten\":\"... (pake html h2, p)\",\"meta_desc\":\"...\",\"keywords\":\"...\"}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-goog-api-key: ' . $apiKey // CARA BARU KIRIM KEY (LEBIH AMPUH)
]);
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

    // Pembersihan manual kalau AI-nya cerewet
    $start = strpos($raw_text, '{');
    $end = strrpos($raw_text, '}');

    if ($start !== false && $end !== false) {
        echo substr($raw_text, $start, ($end - $start) + 1);
    } else {
        echo json_encode(['error' => 'AI ngasih jawaban tapi bukan format data. Coba lagi bang.']);
    }
} else {
    $res_err = json_decode($response, true);
    $pesan = $res_err['error']['message'] ?? 'Error ' . $http_code;
    echo json_encode(['error' => 'Google Bilang: ' . $pesan]);
}
