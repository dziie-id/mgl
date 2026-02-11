<?php
include '../includes/db.php';
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

$items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id ASC");
$items->execute([$id]);
$survey_list = $items->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>LAPORAN_SURVEY_<?= strtoupper($client['nama_klien']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: white;
            color: black;
            font-size: 12px;
        }

        .table th {
            background-color: #f8f9fa !important;
            color: black !important;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }

        .header-box {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body window.print()">
    <div class="container my-4">
        <div class="header-box d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-0">LAPORAN SURVEY LAPANGAN</h2>
                <p class="mb-0 text-muted">Sticker MGL - Jasa Branding & Sticker Professional</p>
            </div>
            <div class="text-end">
                <h5 class="fw-bold mb-0"><?= $client['nama_klien'] ?></h5>
                <p class="small mb-0"><?= date('d F Y') ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-decoration-underline">DETAIL LOKASI:</h6>
                <p><?= nl2br($client['lokasi']) ?></p>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th style="width: 50px;">NO</th>
                    <th>NAMA BAGIAN / ITEM</th>
                    <th style="width: 80px;">P (cm)</th>
                    <th style="width: 80px;">L (cm)</th>
                    <th style="width: 80px;">T (cm)</th>
                    <th style="width: 80px;">QTY</th>
                    <th>FOTO LOKASI</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($survey_list as $itm): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="fw-bold"><?= strtoupper($itm['nama_bagian']) ?></td>
                        <td class="text-center"><?= $itm['p'] ?></td>
                        <td class="text-center"><?= $itm['l'] ?></td>
                        <td class="text-center"><?= $itm['t'] ?></td>
                        <td class="text-center fw-bold"><?= $itm['qty'] ?></td>
                        <td class="text-center">
                            <?php if ($itm['foto_item']): ?>
                                <img src="../uploads/survey/<?= $itm['foto_item'] ?>" style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd;">
                            <?php else: ?>
                                <small class="text-muted">Tidak ada foto</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-5 row">
            <div class="col-4 text-center">
                <p class="mb-5 small">Petugas Lapangan,</p>
                <br><br>
                <p class="fw-bold border-top d-inline-block px-4">Admin Sticker MGL</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="mb-5 small">Mengetahui (Klien),</p>
                <br><br>
                <p class="fw-bold border-top d-inline-block px-4"><?= $client['nama_klien'] ?></p>
            </div>
        </div>

        <div class="mt-5 no-print text-center">
            <hr>
            <p class="text-muted">Gunakan fitur <b>"Save as PDF"</b> pada jendela print browser Anda.</p>
            <button class="btn btn-primary btn-lg" onclick="window.print()">PRINT / DOWNLOAD PDF</button>
            <a href="survey-view.php?id=<?= $id ?>" class="btn btn-secondary btn-lg">KEMBALI</a>
        </div>
    </div>
</body>

</html>