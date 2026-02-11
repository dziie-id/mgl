<?php
include '../includes/db.php';
$apiKey = GEMINI_API_KEY;
echo "Testing Koneksi ke Google AI...<br>";

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash?key=" . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$res = curl_exec($ch);
$info = curl_getinfo($ch);

if (curl_errno($ch)) {
    echo "ERROR: " . curl_error($ch);
} else {
    echo "Koneksi Berhasil! HTTP CODE: " . $info['http_code'];
}
curl_close($ch);
