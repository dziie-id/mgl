<?php
include 'auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// --- 1. LIST ARTIKEL ---
if ($method == 'GET' && $action == 'list') {
    $stmt = $pdo->query("SELECT id, judul, slug, thumbnail, meta_desc, created_at FROM articles ORDER BY id DESC");
    $data = $stmt->fetchAll();

    foreach ($data as &$art) {
        // Cek path gambar agar Android gak nampilin ikon pecah
        $path = "../../uploads/articles/" . $art['thumbnail'];
        if (!file_exists($path) || empty($art['thumbnail'])) {
            $art['url_thumbnail'] = BASE_URL . "uploads/gallery/" . $art['thumbnail'];
        } else {
            $art['url_thumbnail'] = BASE_URL . "uploads/articles/" . $art['thumbnail'];
        }
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
}

// --- 2. HAPUS ARTIKEL ---
if ($method == 'POST' && $action == 'delete') {
    $id = $_POST['id'] ?? '';
    $stmt = $pdo->prepare("SELECT thumbnail FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $art = $stmt->fetch();

    if ($art) {
        // Hanya hapus jika gambarnya bukan dari galeri (awalan blog-)
        if (strpos($art['thumbnail'], 'blog-') !== false) {
            @unlink("../../uploads/articles/" . $art['thumbnail']);
        }
        $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Artikel terhapus!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan']);
    }
}