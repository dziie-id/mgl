<?php
include 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_klien = $_POST['nama_klien'];
    $lokasi = $_POST['lokasi'];
    $items = json_decode($_POST['items'], true); // Android kirim data barang format JSON

    // 1. Simpan Induk
    $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi) VALUES (?, ?)");
    $stmt->execute([$nama_klien, $lokasi]);
    $survey_id = $pdo->lastInsertId();

    // 2. Simpan Items (Looping)
    foreach ($items as $index => $item) {
        $foto_name = "";
        // Cek jika ada upload file foto untuk item ini
        if (isset($_FILES['foto_'.$index])) {
            $ext = pathinfo($_FILES['foto_'.$index]['name'], PATHINFO_EXTENSION);
            $foto_name = "API-ITEM-" . time() . "-$index." . $ext;
            move_uploaded_file($_FILES['foto_'.$index]['tmp_name'], "../../uploads/survey/" . $foto_name);
        }

        $stmt_item = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_item->execute([$survey_id, $item['nama'], $item['p'], $item['l'], $item['t'], $item['qty'], $foto_name]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Survey Berhasil Masuk!']);
}