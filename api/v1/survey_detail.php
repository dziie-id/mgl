<?php
include 'auth_check.php';
$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';

// --- 1. HAPUS SATU PAKET SURVEY (INDUK & SEMUA FOTONYA) ---
if ($action == 'delete_survey') {
    // Cari semua foto item dulu biar gak nyampah
    $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE survey_id = ?");
    $stmt->execute([$id]);
    $items = $stmt->fetchAll();
    foreach ($items as $itm) {
        if (!empty($itm['foto_item'])) { @unlink("../../uploads/survey/" . $itm['foto_item']); }
    }
    // Hapus Induk (Otomatis anak terhapus karena CASCADE)
    $pdo->prepare("DELETE FROM surveys WHERE id = ?")->execute([$id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// --- 2. TAMBAH / UPDATE ITEM (DENGAN FOTO) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($action == 'add_item' || $action == 'update_item')) {
    $nama = $_POST['nama_bagian'];
    $p = $_POST['p']; $l = $_POST['l']; $t = $_POST['t']; $qty = $_POST['qty'];
    
    $foto_final = $_POST['foto_lama'] ?? "";

    // Cek jika ada upload foto baru (kamera/galeri)
    if (!empty($_FILES['foto']['name'])) {
        if (!empty($foto_final)) { @unlink("../../uploads/survey/" . $foto_final); }
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
