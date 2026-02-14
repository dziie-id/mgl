<?php
include 'auth_check.php';
$survey_id = $_GET['id'] ?? ''; // ID Induk
$action = $_GET['action'] ?? '';

// --- 1. PROSES HAPUS ITEM (ANAK) ---
if (isset($_GET['del_item'])) {
    $item_id = $_GET['del_item'];
    
    // Cari nama foto biar gak nyampah
    $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $img = $stmt->fetch();
    if ($img && !empty($img['foto_item'])) { @unlink("../../uploads/survey/" . $img['foto_item']); }

    // Hapus data item
    $pdo->prepare("DELETE FROM survey_items WHERE id = ?")->execute([$item_id]);
    echo json_encode(['status' => 'success', 'message' => 'Item berhasil dibuang!']);
    exit;
}

// --- 2. HAPUS SATU PAKET SURVEY (INDUK) ---
if ($action == 'delete_survey') {
    $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE survey_id = ?");
    $stmt->execute([$survey_id]);
    foreach ($stmt->fetchAll() as $itm) {
        if (!empty($itm['foto_item'])) { @unlink("../../uploads/survey/" . $itm['foto_item']); }
    }
    $pdo->prepare("DELETE FROM surveys WHERE id = ?")->execute([$survey_id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// --- 3. TAMBAH / UPDATE ITEM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($action == 'add_item' || $action == 'update_item')) {
    $nama = $_POST['nama_bagian'];
    $p = $_POST['p']; $l = $_POST['l']; $t = $_POST['t']; $qty = $_POST['qty'];
    $foto_final = $_POST['foto_lama'] ?? "";

    if (!empty($_FILES['foto']['name'])) {
        if (!empty($foto_final)) { @unlink("../../uploads/survey/" . $foto_final); }
        $foto_final = "API-" . time() . ".jpg";
        move_uploaded_file($_FILES['foto']['tmp_name'], "../../uploads/survey/" . $foto_final);
    }

    if ($action == 'add_item') {
        $stmt = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$survey_id, $nama, $p, $l, $t, $qty, $foto_final]);
    } else {
        $item_id = $_POST['item_id'];
        $stmt = $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=?, foto_item=? WHERE id=?");
        $stmt->execute([$nama, $p, $l, $t, $qty, $foto_final, $item_id]);
    }
    echo json_encode(['status' => 'success']);
    exit;
}

// --- 4. AMBIL DATA LIST ---
$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$survey_id]);
$client = $stmt->fetch();
$items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id DESC");
$items->execute([$survey_id]);
$survey_list = $items->fetchAll();
foreach ($survey_list as &$itm) {
    $itm['url_foto'] = !empty($itm['foto_item']) ? "https://mglstiker.com/uploads/survey/" . $itm['foto_item'] : "";
}
echo json_encode(['status' => 'success', 'client' => $client, 'items' => $survey_list]);
