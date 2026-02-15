<?php
include 'auth_check.php';
$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';

// --- 1. HAPUS ITEM ---
if (isset($_GET['del_item'])) {
    $item_id = $_GET['del_item'];
    $stmt_f = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
    $stmt_f->execute([$item_id]);
    $img = $stmt_f->fetch();
    if ($img && !empty($img['foto_item'])) { @unlink("../../uploads/survey/" . $img['foto_item']); }
    $pdo->prepare("DELETE FROM survey_items WHERE id = ?")->execute([$item_id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// --- 2. UPDATE / TAMBAH ITEM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($action == 'add_item' || $action == 'update_item')) {
    $nama = $_POST['nama_bagian'];
    $p = $_POST['p']; $l = $_POST['l']; $t = $_POST['t']; $qty = $_POST['qty'];
    
    // Ambil foto lama dari POST
    $foto_final = $_POST['foto_lama'] ?? "";

    // Cek jika ada file baru yang diupload
    if (!empty($_FILES['foto']['name'])) {
        // Hapus foto lama dari folder
        if (!empty($foto_final) && file_exists("../../uploads/survey/" . $foto_final)) {
            @unlink("../../uploads/survey/" . $foto_final);
        }
        // Simpan foto baru
        $foto_final = "API-" . time() . ".jpg";
        move_uploaded_file($_FILES['foto']['tmp_name'], "../../uploads/survey/" . $foto_final);
    }

    if ($action == 'add_item') {
        $stmt = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $nama, $p, $l, $t, $qty, $foto_final]);
    } else {
        $item_id = $_POST['item_id'];
        $stmt = $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=?, foto_item=? WHERE id=?");
        $stmt->execute([$nama, $p, $l, $t, $qty, $foto_final, $item_id]);
    }
    echo json_encode(['status' => 'success']);
    exit;
}

// --- 3. AMBIL DATA ---
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
