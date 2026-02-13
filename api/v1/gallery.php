<?php
include 'auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    try {
        $stmt = $pdo->query("SELECT id, file_name, alt_text, kategori, created_at FROM galleries ORDER BY id DESC");
        $data = $stmt->fetchAll();

        $clean_data = [];
        foreach ($data as $item) {
            // Kita rakit URL-nya dengan teliti
            $file = $item['file_name'];
            
            // Hilangkan spasi jika ada di nama file
            $file = trim($file);
            
            // Cek apakah file benar-benar ada di folder
            $check_path = "../../uploads/gallery/" . $file;
            
            if (file_exists($check_path)) {
                $item['url_gambar'] = BASE_URL . "uploads/gallery/" . $file;
                $clean_data[] = $item;
            }
        }

        echo json_encode([
            'status' => 'success',
            'total' => count($clean_data),
            'data' => $clean_data
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($method == 'POST' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak ada']); exit;
    }

    $stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    if ($img) {
        $path = "../../uploads/gallery/" . $img['file_name'];
        if (file_exists($path)) { @unlink($path); }
        $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Terhapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
}