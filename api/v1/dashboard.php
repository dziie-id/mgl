<?php
include 'auth_check.php'; // Panggil pengaman

try {
    $total_gambar = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
    $total_artikel = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $total_survey = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();

    // Ambil 5 survey terbaru untuk ditampilkan di daftar singkat aplikasi
    $stmt = $pdo->query("SELECT id, nama_klien, lokasi, created_at FROM surveys ORDER BY id DESC LIMIT 5");
    $recent_surveys = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'stats' => [
                'total_portfolio' => (int)$total_gambar,
                'total_artikel' => (int)$total_artikel,
                'total_survey' => (int)$total_survey
            ],
            'recent_surveys' => $recent_surveys
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}