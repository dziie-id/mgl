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
    die("Topik kosong");
}

$apiKey = GEMINI_API_KEY;
// Gunakan model flash terbaru
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Buatkan artikel blog profesional SEO-friendly Bahasa Indonesia untuk website 'MGL Sticker'. Topik: '$topic'. 
Berikan hasil dalam format JSON murni TANPA markdown, TANPA kata pembuka. 
Struktur: {'judul': '...', 'konten': '... (pake html h2, p, li)', 'meta_desc': '...', 'keywords': '...'}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// PENTING UNTUK HOSTING:
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 40); // Kasih waktu lebih lama (40 detik)

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['error' => 'cURL Error: ' . $err]);
    exit;
}

$result = json_decode($response, true);

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $raw_text = $result['candidates'][0]['content']['parts'][0]['text'];

    // Pembersihan Karakter Markdown yang sering bikin JSON Error
    $clean_json = trim($raw_text);
    $clean_json = str_replace(['```json', '```'], '', $clean_json);

    // Kirim hasil akhir
    echo $clean_json;
} else {
    echo json_encode(['error' => 'AI Gagal Merespon', 'debug' => $result]);
}
