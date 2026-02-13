<?php
include 'auth_check.php';

$total_gambar = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
$total_artikel = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$total_survey = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();

echo json_encode([
    'status' => 'success',
    'data' => [
        'total_portfolio' => $total_gambar,
        'total_artikel' => $total_artikel,
        'total_survey' => $total_survey
    ]
]);