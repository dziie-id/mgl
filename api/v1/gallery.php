<?php
include 'auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Ambil list foto
    $stmt = $pdo->query("SELECT * FROM galleries ORDER BY id DESC");
    $data = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $data]);
} 

if ($method == 'POST' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    // Hapus foto via API
    $id = $_POST['id'];
    $stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    
    if ($img) {
        @unlink("../../uploads/gallery/" . $img['file_name']);
        $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Terhapus']);
    }
}