<?php
include 'auth_check.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID Survey diperlukan']);
    exit;
}

try {
    // 1. Ambil Data Klien
    $stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch();

    if (!$client) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        exit;
    }

    // 2. Ambil Rincian Barang/Item
    $stmt_items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id ASC");
    $stmt_items->execute([$id]);
    $items = $stmt_items->fetchAll();

    // 3. Rapikan URL Foto agar bisa dibuka di Android
    foreach ($items as &$itm) {
        $itm['url_foto'] = !empty($itm['foto_item']) ? BASE_URL . "uploads/survey/" . $itm['foto_item'] : "";
    }

    echo json_encode([
        'status' => 'success',
        'client' => $client,
        'items' => $items
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}