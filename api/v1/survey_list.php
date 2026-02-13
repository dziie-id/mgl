<?php
include 'auth_check.php';
$stmt = $pdo->query("SELECT * FROM surveys ORDER BY id DESC");
echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);