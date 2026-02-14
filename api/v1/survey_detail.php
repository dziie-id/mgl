<?php
include 'auth_check.php';
$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';

// --- 1. PROSES HAPUS ITEM ---
if (isset($_GET['del_item'])) {
    $item_id = $_GET['del_item'];
    $stmt_f = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
    $stmt_f->execute([$item_id]);
    $img = $stmt_f->fetch();
    if ($img && !empty($img['foto_item'])) { @unlink("../../uploads/survey/" . $img['foto_item']); }
    $pdo->prepare("DELETE FROM survey_items WHERE id = ?")->execute([$item_id]);
    echo json_encode(['status' => 'success', 'message' => 'Item dihapus']);
    exit;
}

// --- 2. PROSES UPDATE ITEM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'update_item') {
    $item_id = $_POST['item_id'];
    $stmt = $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=? WHERE id=?");
    $stmt->execute([$_POST['nama_bagian'], $_POST['p'], $_POST['l'], $_POST['t'], $_POST['qty'], $item_id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// --- 3. AMBIL DATA DETAIL ---
$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

$stmt_items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id DESC");
$stmt_items->execute([$id]);
$items = $stmt_items->fetchAll();

foreach ($items as &$itm) {
    $itm['url_foto'] = !empty($itm['foto_item']) ? "https://mglstiker.com/uploads/survey/" . $itm['foto_item'] : "";
}

echo json_encode(['status' => 'success', 'client' => $client, 'items' => $items]);
