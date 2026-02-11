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
    echo json_encode(['error' => 'Sesi habis, login ulang bang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    ob_end_clean();
    echo json_encode(['error' => 'Topik kosong']);
    exit;
}

$apiKey = trim(GEMINI_API_KEY);

// KITA PAKAI v1beta LAGI (Karena paling lengkap fiturnya)
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Buat artikel blog SEO Bahasa Indonesia untuk website 'MGL Sticker'. 
Topik: $topic. 
Output WAJIB JSON murni.
Struktur:
{
  \"judul\": \"...\",
  \"konten\": \"... (pake html h2, p, li)\",
  \"meta_desc\": \"...\",
  \"keywords\": \"...\"
}";

$data = [
    "contents" => [["parts" => [["text" => $prompt]]]]
    // response_mime_type DIHAPUS karena bikin error di beberapa akun
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

    // --- JURUS SARINGAN SUPER ---
    // Mencari bagian awal { dan bagian akhir } biar gak keder sama teks tambahan AI
    $start_pos = strpos($raw_text, '{');
    $end_pos = strrpos($raw_text, '}');

    if ($start_pos !== false && $end_pos !== false) {
        $json_only = substr($raw_text, $start_pos, ($end_pos - $start_pos) + 1);
        echo $json_only; // KIRIM KE JAVASCRIPT
    } else {
        echo json_encode(['error' => 'Format AI kacau, coba lagi bang']);
    }
} else {
    $res_box = json_decode($response, true);
    $pesan_google = $res_box['error']['message'] ?? 'Koneksi Gagal';
    echo json_encode(['error' => 'Google Bilang: ' . $pesan_google]);
}
