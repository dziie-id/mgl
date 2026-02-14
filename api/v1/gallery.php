<?php
include 'auth_check.php';
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method == 'POST' && $action == 'delete_batch') {
    // Android kirim list ID: "60,61,62"
    $ids_raw = $_POST['ids'] ?? '';
    if (empty($ids_raw)) {
        die(json_encode(['status' => 'error', 'message' => 'Pilih foto dulu!']));
    }

    $ids = explode(',', $ids_raw);
    foreach ($ids as $id) {
        $stmt = $pdo->prepare("SELECT file_name FROM galleries WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetch();
        if ($img) {
            @unlink("../../uploads/gallery/" . $img['file_name']);
            $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]);
        }
    }
    echo json_encode(['status' => 'success', 'message' => count($ids) . ' Foto dihapus!']);
    exit;
}

// ... Siswa kodingan GET (List) tetap sama seperti sebelumnya ...
if ($method == 'GET') {
    $stmt = $pdo->query("SELECT id, file_name, alt_text, kategori FROM galleries ORDER BY id DESC");
    $data = $stmt->fetchAll();
    foreach ($data as &$item) {
        $item['url_gambar'] = "https://mglstiker.com/uploads/gallery/" . $item['file_name'];
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
}
