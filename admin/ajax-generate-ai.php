<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek keamanan
if (!isset($_SESSION['admin_logged_in'])) {
    die("Akses ditolak");
}

$topic = $_POST['topic'] ?? '';
if (empty($topic)) {
    die("Topik tidak boleh kosong");
}

$apiKey = GEMINI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// INSTRUKSI KHUSUS UNTUK AI (PROMPT)
$prompt = "Tolong buatkan artikel blog profesional SEO-friendly dalam Bahasa Indonesia untuk website jasa sticker 'Sticker MGL Jakarta'. 
Topik: '$topic'. 
Struktur artikel harus dalam format JSON murni tanpa teks pembuka/penutup lainnya. 
Isi JSON harus memiliki key:
- 'judul': Judul artikel yang menarik.
- 'konten': Minimal 400 kata, gunakan tag HTML seperti <h2>, <p>, dan <ul> <li> agar rapi. Berikan tips pemasangan sticker yang relevan.
- 'meta_desc': Penjelasan singkat artikel maksimal 160 karakter.
- 'keywords': 5 kata kunci yang dipisahkan dengan koma.

Harap berikan hasil JSON yang valid.";

$data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ]
];

// Kirim ke Google via CURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $raw_text = $result['candidates'][0]['content']['parts'][0]['text'];

    // Bersihkan hasil jika AI membungkus dengan tag ```json
    $clean_json = str_replace(['```json', '```'], '', $raw_text);
    echo trim($clean_json);
} else {
    echo json_encode(['error' => 'Gagal mengambil data dari AI']);
}
