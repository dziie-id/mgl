<?php
// 1. Bersihkan output buffer agar tidak ada spasi/error yang merusak JSON
ob_start();
error_reporting(0);

include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Header JSON ditaruh di sini
header('Content-Type: application/json');

// 2. Cek Login
if (!isset($_SESSION['admin_logged_in'])) {
    ob_end_clean();
    echo json_encode(['error' => 'Login dulu bang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    ob_end_clean();
    echo json_encode(['error' => 'Topik belum diisi bang']);
    exit;
}

// 3. Ambil API Key
$apiKey = trim(GEMINI_API_KEY);

// --- UPDATE URL KE v1 (JALUR STABIL) ---
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Buat artikel blog SEO Bahasa Indonesia untuk website 'MGL Sticker'. 
Topik: $topic. 
Berikan hasil dalam format JSON murni.
Struktur JSON:
{
  \"judul\": \"Judul Menarik\",
  \"konten\": \"Isi artikel minimal 500 kata, gunakan tag HTML h2, p, li agar rapi\",
  \"meta_desc\": \"Ringkasan 160 karakter\",
  \"keywords\": \"5 kata kunci dipisah koma\"
}";

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

// Ambil hasil buffer dan bersihkan
ob_end_clean();

// 5. Analisis Jawaban
if ($http_code === 200) {
    $result = json_decode($response, true);
    $clean_json = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
    echo $clean_json; // SUKSES KIRIM JSON KE JAVASCRIPT
} else {
    $res_box = json_decode($response, true);
    $pesan_google = $res_box['error']['message'] ?? 'Koneksi Gagal';

    // Kirim pesan error asli dari Google biar ketahuan masalahnya
    echo json_encode(['error' => 'Google Bilang: ' . $pesan_google]);
}
