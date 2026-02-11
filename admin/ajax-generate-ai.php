<?php
ob_start();
error_reporting(0);

include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    ob_end_clean();
    echo json_encode(['error' => 'Login dulu bang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    ob_end_clean();
    echo json_encode(['error' => 'Topik kosong']);
    exit;
}

$apiKey = trim(GEMINI_API_KEY);

// Tetap di v1 biar gak "Model Not Found"
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// Prompt dipertegas agar hasilnya rapi
$prompt = "Buatkan artikel blog SEO Bahasa Indonesia untuk website 'MGL Sticker'. 
Topik: $topic. 
Berikan hasil dalam format JSON murni dengan struktur:
{
  \"judul\": \"...\",
  \"konten\": \"... (pake html h2, p, li)\",
  \"meta_desc\": \"...\",
  \"keywords\": \"...\"
}
PENTING: Jangan berikan kata pembuka, langsung JSON saja.";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]],
    "generationConfig" => [
        "temperature" => 0.7
        // "response_mime_type" DIHAPUS karena bikin error di v1
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
curl_close($ch);

ob_end_clean();

if ($http_code === 200) {
    $result = json_decode($response, true);
    $raw_text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

    // --- JURUS SAPU BERSIH MARKDOWN ---
    // AI sering membungkus JSON pake ```json ... ```, kita buang manual
    $clean_json = str_replace(['```json', '```'], '', $raw_text);
    $clean_json = trim($clean_json);

    // Kirim hasil ke Javascript
    echo $clean_json;
} else {
    $res_box = json_decode($response, true);
    $pesan_google = $res_box['error']['message'] ?? 'Koneksi Gagal';
    echo json_encode(['error' => 'Google Bilang: ' . $pesan_google]);
}
