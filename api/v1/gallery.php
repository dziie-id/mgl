<?php
include 'auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

// --- 1. AMBIL DAFTAR FOTO ---
if ($method == 'GET') {
    try {
        $stmt = $pdo->query("SELECT id, file_name, alt_text, kategori, created_at FROM galleries ORDER BY id DESC");
        $data = $stmt->fetchAll();

        // Tambahkan Full URL agar Android gampang nampilin gambarnya
        foreach ($data as &$item) {
            $item['url_gambar'] = BASE_URL . "uploads/gallery/" . $item['file_name'];
        }

        echo json_encode([
            'status' => 'success',
            'total' => count($data),
            'data' => $data
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// --- 2. HAPUS FOTO ---
if ($method == 'POST') {
    // Kita cek apakah ada aksi hapus
    if (isset($_GET['action']) && $_GET['action'] == 'delete') {
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID Gambar diperlukan']);
            exit;
        }

        // Cari nama filenya dulu buat dihapus di folder
        $stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetch();

        if ($img) {
            $path = "../../uploads/gallery/" . $img['file_name'];
            
            // Hapus File Fisik
            if (file_exists($path)) {
                @unlink($path);
            }

            // Hapus Data Database
            $delete = $pdo->prepare("DELETE FROM galleries WHERE id = ?");
            $delete->execute([$id]);

            echo json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus selamanya!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Foto tidak ditemukan']);
        }
    }
}