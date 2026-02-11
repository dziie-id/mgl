<?php
// 1. Matikan semua gangguan (Notice/Warning) biar gak ngerusak JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 2. Proteksi
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Sesi habis, login ulang bang']);
    exit;
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    echo json_encode(['error' => 'Isi topiknya dulu bang']);
    exit;
}

$apiKey = trim(GEMINI_API_KEY);

// --- GUNAKAN MODEL GEMINI-PRO (BIASANYA LEBIH STABIL DI HOSTING) ---
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;

$prompt = "Buatkan artikel SEO website jasa sticker 'MGL Sticker' tentang: $topic. Balas HANYA dengan format JSON seperti ini: {\"judul\":\"...\",\"konten\":\"... (pake html h2, p)\",\"meta_desc\":\"...\",\"keywords\":\"...\"}";

$data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
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

// Ambil semua output yang tertahan (kalau ada error nyelip)
ob_end_clean();

if ($http_code === 200) {
    $result = json_decode($response, true);
    $raw_text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

    // MEMBERSIHKAN HASIL (Cari kurung kurawal pertama dan terakhir)
    $start = strpos($raw_text, '{');
    $end = strrpos($raw_text, '}');

    if ($start !== false && $end !== false) {
        $json_final = substr($raw_text, $start, ($end - $start) + 1);
        echo $json_final; // SUKSES
    } else {
        // Jika Google gak ngasih JSON, kita paksa bikin JSON sendiri dari teksnya
        echo json_encode([
            'judul' => $topic,
            'konten' => '<p>' . nl2br($raw_text) . '</p>',
            'meta_desc' => substr(strip_tags($raw_text), 0, 150),
            'keywords' => str_replace(' ', ',', $topic)
        ]);
    }
} else {
    $res_error = json_decode($response, true);
    $pesan = $res_error['error']['message'] ?? 'Gagal koneksi ke Google';
    echo json_encode(['error' => 'Google Bilang: ' . $pesan]);
}
