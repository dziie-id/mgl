<?php
include 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method harus POST']);
    exit;
}

try {
    // 1. Tangkap Data Induk
    $nama_klien = $_POST['nama_klien'] ?? '';
    $lokasi     = $_POST['lokasi'] ?? '';
    
    // Data rincian barang dikirim Android dalam bentuk JSON String
    $items_json = $_POST['items'] ?? '[]';
    $items      = json_decode($items_json, true);

    if (empty($nama_klien) || empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Data klien atau item tidak boleh kosong']);
        exit;
    }

    // 2. Simpan ke Tabel Induk (surveys)
    $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi) VALUES (?, ?)");
    $stmt->execute([$nama_klien, $lokasi]);
    $survey_id = $pdo->lastInsertId();

    // 3. Simpan Rincian Item (Looping)
    $upload_dir = "../../uploads/survey/";
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

    $success_items = 0;

    foreach ($items as $index => $item) {
        $foto_name = "";
        
        // Android harus mengirim file dengan key: foto_0, foto_1, dst sesuai index array
        $file_key = "foto_" . $index;

        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
            $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
            $foto_name = "API-SRV-" . time() . "-" . $index . "." . $ext;
            move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $foto_name);
        }

        // Simpan ke Tabel survey_items
        $stmt_item = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_item->execute([
            $survey_id, 
            $item['nama_bagian'], 
            $item['p'] ?? '-', 
            $item['l'] ?? '-', 
            $item['t'] ?? '-', 
            $item['qty'] ?? 1, 
            $foto_name
        ]);
        $success_items++;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Survey berhasil disimpan!',
        'survey_id' => $survey_id,
        'item_tersimpan' => $success_items
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}