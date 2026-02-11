<?php
// 1. Hidupkan error reporting untuk debugging jika error 500 muncul lagi
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php';

// Fungsi helper ditaruh di ATAS agar tidak error 500
if (!function_exists('hsc')) {
    function hsc($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

if (!$id) {
    die("ID Survey tidak ditemukan.");
}

try {
    // Ambil data klien
    $stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch();

    if (!$client) {
        die("Data klien tidak ditemukan di database.");
    }

    // Ambil rincian item
    $items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id ASC");
    $items->execute([$id]);
    $survey_list = $items->fetchAll();

} catch (PDOException $e) {
    die("Kesalahan Database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SURVEY_<?= strtoupper(str_replace(' ', '_', $client['nama_klien'])) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Paksa Landscape */
        @page { size: landscape; margin: 10mm; }
        
        body { background: white; color: black; font-family: 'Arial', sans-serif; font-size: 13px; }
        .header-report { border-bottom: 4px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .table th { background-color: #f2f2f2 !important; color: black !important; text-transform: uppercase; font-size: 11px; }
        .img-report { width: 160px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; }
        
        /* Trik agar tabel tidak hancur di HP */
        @media screen and (max-width: 768px) {
            body { min-width: 1000px; padding: 20px; }
            .no-print-peringatan { 
                background: #fff3cd; padding: 15px; border: 1px solid #ffeeba; 
                margin-bottom: 20px; border-radius: 10px; text-align: center;
            }
        }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; padding: 0; min-width: 100% !important; }
        }
    </style>
</head>
<body>

    <div class="container-fluid py-3">
        
        <!-- Peringatan Khusus Mobile (Hanya muncul di layar HP) -->
        <div class="no-print d-md-none no-print-peringatan">
            <strong><i class="fa fa-info-circle"></i> TIPS ANDROID:</strong><br>
            Saat menu print muncul, klik <b>"Tanda Panah Bawah"</b> -> pilih <b>"Orientasi"</b> -> ubah ke <b>"Landscape"</b>.
        </div>

        <!-- HEADER -->
        <div class="header-report d-flex justify-content-between align-items-end">
            <div>
                <h1 class="fw-bold mb-0">LAPORAN SURVEY LAPANGAN</h1>
                <p class="mb-0 text-secondary">STICKER MGL JAKARTA - Premium Vehicle Branding</p>
            </div>
            <div class="text-end small">
                Dicetak: <?= date('d/m/Y H:i') ?>
            </div>
        </div>

        <!-- INFO KLIEN -->
        <div class="row mb-4">
            <div class="col-8">
                <table class="table table-borderless table-sm w-auto">
                    <tr><td width="120" class="fw-bold">Nama Klien</td><td>: <?= hsc($client['nama_klien']) ?></td></tr>
                    <tr><td class="fw-bold">Lokasi/Alamat</td><td>: <?= hsc($client['lokasi']) ?></td></tr>
                </table>
            </div>
            <div class="col-4 text-end">
                <div class="p-2 border rounded bg-light d-inline-block px-4">
                    <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 10px;">Total Item</small>
                    <h4 class="mb-0 fw-bold"><?= count($survey_list) ?></h4>
                </div>
            </div>
        </div>

        <!-- TABEL DATA -->
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th width="40">No</th>
                    <th>Nama Bagian / Item</th>
                    <th width="80">P (m)</th>
                    <th width="80">L (m)</th>
                    <th width="80">T (m)</th>
                    <th width="60">Qty</th>
                    <th width="200">Foto Lapangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($survey_list as $itm): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="fw-bold text-uppercase"><?= hsc($itm['nama_bagian']) ?></td>
                    <td class="text-center"><?= $itm['p'] ?: '-' ?></td>
                    <td class="text-center"><?= $itm['l'] ?: '-' ?></td>
                    <td class="text-center"><?= $itm['t'] ?: '-' ?></td>
                    <td class="text-center fw-bold"><?= $itm['qty'] ?></td>
                    <td class="text-center">
                        <?php 
                        $img_path = "../uploads/survey/" . $itm['foto_item'];
                        if(!empty($itm['foto_item']) && file_exists($img_path)): ?>
                            <img src="<?= $img_path ?>" class="img-report">
                        <?php else: ?>
                            <span class="text-muted small">Tanpa Foto</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- FOOTER TANDA TANGAN -->
        <div class="mt-5 row text-center">
            <div class="col-4">
                <p class="mb-5">Surveyor,</p>
                <br><br>
                <p class="fw-bold border-top d-inline-block px-4 pt-1">Sticker MGL Jakarta</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
                <p class="mb-5">Klien,</p>
                <br><br>
                <p class="fw-bold border-top d-inline-block px-4 pt-1"><?= hsc($client['nama_klien']) ?></p>
            </div>
        </div>

        <!-- BUTTONS -->
        <div class="no-print mt-5 text-center border-top pt-4">
            <button onclick="window.print()" class="btn btn-primary px-5 py-2 fw-bold">
                <i class="fa fa-print me-2"></i> PRINT / SAVE PDF
            </button>
            <a href="survey-view.php?id=<?= $id ?>" class="btn btn-outline-secondary px-4 py-2 ms-2">KEMBALI</a>
        </div>
    </div>

</body>
</html>