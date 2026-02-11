<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// --- 1. LOGIKA HAPUS SURVEY ---
if (isset($_GET['del'])) {
    $id_survey = $_GET['del'];
    $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE survey_id = ?");
    $stmt->execute([$id_survey]);
    $items = $stmt->fetchAll();
    foreach ($items as $item) {
        if (!empty($item['foto_item'])) {
            $path = "../uploads/survey/" . $item['foto_item'];
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
    $stmt = $pdo->prepare("DELETE FROM surveys WHERE id = ?");
    $stmt->execute([$id_survey]);
    $_SESSION['success'] = "Data survey berhasil dihapus!";
    header("Location: survey.php");
    exit;
}

// --- 2. LOGIKA SIMPAN SEKALIGUS (INDUK & ITEM) ---
if (isset($_POST['simpan_survey_lengkap'])) {
    $nama_klien = $_POST['nama_klien'];
    $lokasi = $_POST['lokasi'];

    // Simpan Induk
    $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi) VALUES (?, ?)");
    $stmt->execute([$nama_klien, $lokasi]);
    $survey_id = $pdo->lastInsertId();

    // Simpan Item-itemnya
    if (isset($_POST['nama_bagian'])) {
        foreach ($_POST['nama_bagian'] as $key => $nama_bagian) {
            if (!empty($nama_bagian)) {
                $p   = $_POST['p'][$key] ?? '-';
                $l   = $_POST['l'][$key] ?? '-';
                $t   = $_POST['t'][$key] ?? '-';
                $qty = $_POST['qty'][$key] ?? 1;

                // Handle Foto Item dalam Loop
                $foto_name = "";
                if (!empty($_FILES['foto_item']['name'][$key])) {
                    $ext = pathinfo($_FILES['foto_item']['name'][$key], PATHINFO_EXTENSION);
                    $foto_name = "ITEM-" . time() . "-" . $key . "-" . rand(10, 99) . "." . $ext;
                    $target_file = "../uploads/survey/" . $foto_name;
                    move_uploaded_file($_FILES['foto_item']['tmp_name'][$key], $target_file);
                }

                $stmt_item = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_item->execute([$survey_id, $nama_bagian, $p, $l, $t, $qty, $foto_name]);
            }
        }
    }
    $_SESSION['success'] = "Survey Lengkap Berhasil Disimpan!";
    header("Location: survey.php");
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

<!-- FORM SURVEY SEKALIGUS -->
<div class="collapse mb-5" id="formSurvey">
    <!-- PASTIKAN ADA enctype="multipart/form-data" -->
    <form method="POST" enctype="multipart/form-data">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white fw-bold">Data Klien & Lokasi</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold">Nama Klien / Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_klien" class="form-control" required placeholder="PT. ABC">
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">Alamat / Keterangan Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" required placeholder="Jakarta">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Rincian Item</span>
                <button type="button" class="btn btn-sm btn-success" id="addItem"><i class="fa-solid fa-plus-circle me-1"></i> Tambah Baris</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="table">
                            <tr class="small text-center">
                                <th style="width: 25%;">Item <span class="text-danger">*</span></th>
                                <th>P</th>
                                <th>L</th>
                                <th>T</th>
                                <th style="width: 80px;">Qty</th>
                                <th>Foto / Kamera</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemWrapper">
                            <tr class="item-row">
                                <td><input type="text" name="nama_bagian[]" class="form-control form-control-sm" required></td>
                                <td><input type="text" name="p[]" class="form-control form-control-sm text-center" placeholder="0"></td>
                                <td><input type="text" name="l[]" class="form-control form-control-sm text-center" placeholder="0"></td>
                                <td><input type="text" name="t[]" class="form-control form-control-sm text-center" placeholder="0"></td>
                                <td><input type="number" name="qty[]" class="form-control form-control-sm text-center" value="1"></td>
                                <td><input type="file" name="foto_item[]" class="form-control form-control-sm" accept="image/*"></td>
                                <td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer p-3 text-end">
                <button type="submit" name="simpan_survey_lengkap" class="btn btn-primary px-5 shadow">SIMPAN SEMUA DATA</button>
            </div>
        </div>
    </form>
</div>

<!-- LIST SURVEY -->
<div class="row g-3">
    <?php foreach ($surveys as $s): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 align-items-center border-bottom border-light border-opacity-10">
                        <div class="bg-primary text-white rounded p-3 me-3"><i class="fa-solid fa-building"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0"><?= $s['nama_klien'] ?></h6>
                            <small class="text-muted"><?= $s['lokasi'] ?></small>
                        </div>
                        <div>
                            <a href="survey-view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Detail</a>
                            <a href="?del=<?= $s['id'] ?>" class="btn btn-sm btn-link text-danger btn-hapus ms-1"><i class="fa-solid fa-trash"></i></a>
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
            if (input.type === 'number') {
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