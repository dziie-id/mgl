<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan_survey_lengkap'])) {
    $nama_klien = $_POST['nama_klien'];
    $lokasi = $_POST['lokasi'];

    $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi) VALUES (?, ?)");
    $stmt->execute([$nama_klien, $lokasi]);
    $survey_id = $pdo->lastInsertId();
    $_SESSION['success'] = "Data klien berhasil disimpan!";

    if (isset($_POST['nama_bagian'])) {
        foreach ($_POST['nama_bagian'] as $key => $nama_bagian) {
            if (!empty($nama_bagian)) {
                $p   = $_POST['p'][$key] ?? '-';
                $l   = $_POST['l'][$key] ?? '-';
                $t   = $_POST['t'][$key] ?? '-';
                $qty = $_POST['qty'][$key] ?? 1;

                $foto_name = "";
                if (!empty($_FILES['foto_item']['name'][$key])) {
                    $ext = pathinfo($_FILES['foto_item']['name'][$key], PATHINFO_EXTENSION);
                    $foto_name = "ITEM-" . time() . "-" . $key . "." . $ext;
                    move_uploaded_file($_FILES['foto_item']['tmp_name'][$key], "../uploads/survey/" . $foto_name);
                }

                $stmt_item = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_item->execute([$survey_id, $nama_bagian, $p, $l, $t, $qty, $foto_name]);
            }
        }
    }
    header("Location: survey.php?status=success");
    exit;
}

if (isset($_GET['del_item'])) {
    $id_item = $_GET['del_item'];
    $id_survey = $_GET['id'];
    $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
    $stmt->execute([$id_item]);
    $itm = $stmt->fetch();

    if ($itm && !empty($itm['foto_item'])) {
        $path = "../uploads/survey/" . $itm['foto_item'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    $stmt_del = $pdo->prepare("DELETE FROM survey_items WHERE id = ?");
    $stmt_del->execute([$id_item]);

    $_SESSION['success'] = "Data ukuran dan foto berhasil dibuang!";
    header("Location: survey-view.php?id=" . $id_survey);
    exit;
}

include 'header.php';
$surveys = $pdo->query("SELECT * FROM surveys ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-clipboard-list me-2"></i>Survey Lapangan</h4>
    <button class="btn btn-primary rounded-pill px-4 shadow" data-bs-toggle="collapse" data-bs-target="#formSurvey">
        <i class="fa-solid fa-plus me-1"></i> Buat Survey
    </button>
</div>

<div class="collapse mb-5" id="formSurvey">
    <form method="POST" enctype="multipart/form-data">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white fw-bold">Data Klien & Lokasi</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold">Nama Klien / Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_klien" class="form-control" required placeholder="Contoh: PT. Maju Jaya">
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">Alamat / Keterangan Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" required placeholder="Alamat lengkap">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Rincian Item & Ukuran</span>
                <button type="button" class="btn btn-sm btn-success" id="addItem"><i class="fa-solid fa-plus-circle me-1"></i> Tambah Item</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th style="width: 25%;">Nama Bagian/Item <span class="text-danger">*</span></th>
                                <th>P (cm)</th>
                                <th>L (cm)</th>
                                <th>T (cm)</th>
                                <th style="width: 80px;">QTY</th>
                                <th>Foto Lokasi</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemWrapper">
                            <tr class="item-row">
                                <td><input type="text" name="nama_bagian[]" class="form-control form-control-sm" required></td>
                                <td><input type="text" name="p[]" class="form-control form-control-sm" placeholder="0"></td>
                                <td><input type="text" name="l[]" class="form-control form-control-sm" placeholder="0"></td>
                                <td><input type="text" name="t[]" class="form-control form-control-sm" placeholder="0"></td>
                                <td><input type="number" name="qty[]" class="form-control form-control-sm text-center" value="1" min="1"></td>
                                <td><input type="file" name="foto_item[]" class="form-control form-control-sm" accept="image/*"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white p-3 text-end">
                <button type="submit" name="simpan_survey_lengkap" class="btn btn-primary px-5 shadow">SIMPAN SEMUA DATA</button>
            </div>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php foreach ($surveys as $s): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 align-items-center border-bottom border-light border-opacity-10">
                        <div class="bg-primary text-white rounded p-3 me-3"><i class="fa-solid fa-building"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;"><?= $s['nama_klien'] ?></h6>
                            <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> <?= $s['lokasi'] ?></small>
                        </div>
                        <div class="text-end">
                            <a href="survey-view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Detail</a>
                            <a href="?del=<?= $s['id'] ?>" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="return confirm('Hapus survey ini?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    document.getElementById('addItem').addEventListener('click', function() {
        const wrapper = document.getElementById('itemWrapper');
        const row = document.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => {
            if (input.name.includes('qty')) {
                input.value = 1;
            } else {
                input.value = '';
            }
        });
        wrapper.appendChild(row);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
            }
        }
    });
</script>

<?php include 'footer.php'; ?>