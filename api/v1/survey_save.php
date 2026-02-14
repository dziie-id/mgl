<?php
// 1. Matikan error reporting agar tidak merusak format JSON
error_reporting(0); 

include 'auth_check.php'; // Panggil satpam API (X-API-KEY)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method harus POST']);
    exit;
}

try {
    // 2. Tangkap Data Induk
    $nama_klien = $_POST['nama_klien'] ?? '';
    $lokasi     = $_POST['lokasi'] ?? '';
    $koordinat  = $_POST['koordinat'] ?? ''; // DATA KOORDINAT DARI APP
    
    // Data rincian barang dikirim Android dalam bentuk JSON String
    $items_json = $_POST['items'] ?? '[]';
    $items      = json_decode($items_json, true);

    if (empty($nama_klien) || empty($items)) {
        echo json_encode(['status' => 'error', 'message' => 'Data klien atau rincian barang tidak boleh kosong']);
        exit;
    }

    // 3. Simpan ke Tabel Induk (surveys)
    // Pastikan kolom koordinat sudah dibuat di database
    $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi, koordinat) VALUES (?, ?, ?)");
    $stmt->execute([$nama_klien, $lokasi, $koordinat]);
    $survey_id = $pdo->lastInsertId();

    // 4. Proses Simpan Rincian Item (Looping)
    $upload_dir = "../../uploads/survey/";
    // Buat folder jika belum ada
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

    $success_items = 0;

    foreach ($items as $index => $item) {
        $foto_name = "";
        
        // Android mengirim file dengan key: foto_0, foto_1, dst sesuai index array
        $file_key = "foto_" . $index;

        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
            $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
            // Nama file unik biar gak bentrok
            $foto_name = "API-SRV-" . time() . "-" . $index . "-" . rand(100,999) . "." . $ext;
            
            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $foto_name)) {
                // File berhasil dipindah
            } else {
                $foto_name = ""; // Reset jika gagal upload
            }
        }

        // 5. Simpan ke Tabel survey_items
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

    // 6. Beri Jawaban Sukses ke Aplikasi Android
    echo json_encode([
        'status' => 'success',
        'message' => 'Survey klien ' . $nama_klien . ' berhasil disimpan!',
        'survey_id' => $survey_id,
        'jumlah_item' => $success_items
    ]);

} catch (PDOException $e) {
    // Jika ada error database
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan Database: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Jika ada error lainnya
    echo json_encode(['status' => 'error', 'message' => 'Sistem Error: ' . $e->getMessage()]);
}
