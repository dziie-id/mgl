<?php
include 'auth_check.php';
$stmt = $pdo->query("SELECT * FROM surveys ORDER BY id DESC");
echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);

// Tambahkan ini di survey_list.phpif (isset($_GET['del'])) {
    $id = $_GET['del'];
    // Hapus item-itemnya dulu (agar tidak jadi data sampah)
    $pdo->prepare("DELETE FROM survey_items WHERE survey_id = ?")->execute([$id]);
    // Hapus induk surveynya
    $pdo->prepare("DELETE FROM surveys WHERE id = ?")->execute([$id]);
    
    echo json_encode(['status' => 'success', 'message' => 'Survey berhasil dihapus']);
    exit;
}
