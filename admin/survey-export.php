<?php
include '../includes/db.php';
$id = $_GET['id'];

// Ambil data klien
$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) { die("Data tidak ditemukan."); }

// Ambil rincian item
$items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id ASC");
$items->execute([$id]);
$survey_list = $items->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SURVEY_<?= strtoupper(str_replace(' ', '_', $client['nama_klien'])) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Trik Utama Auto-Landscape */
        @page {
            size: landscape;
            margin: 10mm;
        }

        body { 
            background: white; 
            color: black; 
            font-family: 'Arial', sans-serif;
            font-size: 13px; 
        }

        .header-report {
            border-bottom: 4px double #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .table th { 
            background-color: #f2f2f2 !important; 
            color: black !important;
            text-transform: uppercase;
            font-size: 11px;
            vertical-align: middle;
        }

        .img-report {
            width: 180px; /* Lebih lebar karena landscape */
            height: 110px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            display: inline-block;
        }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; }
            .table { width: 100% !important; }
        }
    </style>
</head>
<body "window.print()">

    <div class="container-fluid py-3">
        <!-- HEADER -->
        <div class="header-report d-flex justify-content-between align-items-end">
            <div>
                <h1 class="fw-bold mb-0" style="letter-spacing: -1px;">LAPORAN SURVEY LAPANGAN</h1>
                <p class="mb-0 text-secondary">STICKER MGL JAKARTA - Spesialis Branding & Sticker Custom</p>
            </div>
            <div class="text-end">
                <p class="mb-0 small">Dicetak pada: <?= date('d/m/Y H:i') ?></p>
            </div>
        </div>

        <!-- INFO KLIEN -->
        <div class="row mb-4">
            <div class="col-7">
                <div class="mb-1"><span class="info-label">Nama Klien</span>: <?= hsc($client['nama_klien']) ?></div>
                <div class="mb-1"><span class="info-label">Lokasi / Alamat</span>: <?= hsc($client['lokasi']) ?></div>
            </div>
            <div class="col-5 text-end">
                <div class="p-3 border rounded bg-light">
                    <small class="d-block text-uppercase fw-bold text-muted">Total Item Survey</small>
                    <h3 class="mb-0 fw-bold"><?= count($survey_list) ?> <small class="fs-6">Item</small></h3>
                </div>
            </div>
        </div>

        <!-- TABEL DATA (Lebih Luas) -->
        <table class="table table-bordered align-middle">
            <thead>
                <tr class="text-center">
                    <th style="width: 40px;">No</th>
                    <th style="width: 250px;">Nama Bagian / Item</th>
                    <th>P (m)</th>
                    <th>L (m)</th>
                    <th>T (m)</th>
                    <th style="width: 60px;">Qty</th>
                    <th>Foto Kondisi Lapangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach($survey_list as $itm): 
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="fw-bold text-uppercase"><?= hsc($itm['nama_bagian']) ?></td>
                    <td class="text-center"><?= $itm['p'] ?: '-' ?></td>
                    <td class="text-center"><?= $itm['l'] ?: '-' ?></td>
                    <td class="text-center"><?= $itm['t'] ?: '-' ?></td>
                    <td class="text-center fw-bold"><?= $itm['qty'] ?></td>
                    <td class="text-center py-2">
                        <?php if(!empty($itm['foto_item']) && file_exists("../uploads/survey/".$itm['foto_item'])): ?>
                            <img src="../uploads/survey/<?= $itm['foto_item'] ?>" class="img-report">
                        <?php else: ?>
                            <div class="text-muted small">Tidak ada foto</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="mt-5 pt-3 row text-center">
            <div class="col-4">
                <p class="mb-5">Dibuat Oleh (Admin/Surveyor),</p>
                <br><br>
                <p class="fw-bold border-top d-inline-block px-5 mt-3">Sticker MGL Jakarta</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
                <p class="mb-5">Disetujui Oleh (Klien),</p>
                <br><br>
                <p class="fw-bold border-top d-inline-block px-5 mt-3"><?= hsc($client['nama_klien']) ?></p>
            </div>
        </div>

        <!-- TOMBOL KONTROL (Hanya muncul di layar) -->
        <div class="no-print mt-5 text-center border-top pt-4">
            <button onclick="window.print()" class="btn btn-primary btn-lg px-5 me-2">
                <i class="fa-solid fa-print me-2"></i> CETAK / SIMPAN PDF
            </button>
            <a href="survey-view.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-lg px-4">
                KEMBALI
            </a>
        </div>
    </div>

</body>
</html>
<?php 
// Fungsi helper biar aman dari XSS
function hsc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>