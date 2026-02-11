<?php
// 1. Matikan output lain agar JSON bersih
ob_clean();
error_reporting(0);
header('Content-Type: application/json');

include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cek Login
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Sesi habis, silakan login ulang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    echo json_encode(['error' => 'Topik belum diisi bang']);
    exit;
}

// 3. Ambil API Key & Bersihkan
$apiKey = trim(GEMINI_API_KEY);

// Jalur v1beta biasanya lebih stabil buat key baru
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Tulis artikel blog SEO Bahasa Indonesia untuk 'MGL Sticker'. Topik: $topic. Balas HANYA JSON: {'judul': '...', 'konten': '... (pake html h2, p, li)', 'meta_desc': '...', 'keywords': '...'}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]],
    "generationConfig" => [
        "response_mime_type" => "application/json",
        "temperature" => 0.7
    ]
];

// 4. Eksekusi cURL
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

// 5. Analisis Jawaban
if ($http_code === 200) {
    $result = json_decode($response, true);
    $clean_json = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
    echo $clean_json; // SUKSES
} else {
    $res_box = json_decode($response, true);
    $pesan = $res_box['error']['message'] ?? 'Koneksi ke Google Gagal';

    // Jika masih "API key not valid", berarti Google butuh waktu propagasi
    if (strpos($pesan, 'not valid') !== false) {
        echo json_encode(['error' => 'Google sedang memproses API Key baru abang. Tunggu 1-2 menit terus coba lagi bang!']);
    } else {
        echo json_encode(['error' => 'Google Bilang: ' . $pesan]);
    }
}
