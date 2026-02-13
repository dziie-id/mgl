<?php
include '../../includes/db.php';
header('Content-Type: application/json');

$headers = apache_request_headers();
$api_token = $headers['X-API-KEY'] ?? '';

if (empty($api_token)) {
    echo json_encode(['status' => 'error', 'message' => 'API Key required']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ?");
$stmt->execute([$api_token]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API Key']);
    exit;
}
// Jika lewat sini, berarti aman