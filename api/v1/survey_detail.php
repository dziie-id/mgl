<?php
include 'auth_check.php';
$id = $_GET['id'] ?? '';
if (empty($id)) { echo json_encode(['status' => 'error', 'message' => 'ID missing']); exit; }

$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

$stmt_items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id ASC");
$stmt_items->execute([$id]);
$items = $stmt_items->fetchAll();

foreach ($items as &$itm) {
    // KUNCINYA DISINI: Gunakan path absolut yang benar
    if (!empty($itm['foto_item'])) {
        $itm['url_foto'] = "https://mglstiker.com/uploads/survey/" . $itm['foto_item'];
    } else {
        $itm['url_foto'] = "";
    }
}

echo json_encode(['status' => 'success', 'client' => $client, 'items' => $items]);
