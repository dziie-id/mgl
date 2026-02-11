<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// --- LOGIKA SIMPAN SEKALIGUS ---
if (isset($_POST['simpan_survey_lengkap'])) {
    $nama_klien = $_POST['nama_klien'];
    $lokasi = $_POST['lokasi'];

    $stmt = $pdo->prepare("INSERT INTO surveys (nama_klien, lokasi) VALUES (?, ?)");
    $stmt->execute([$nama_klien, $lokasi]);
    $survey_id = $pdo->lastInsertId();

    if (isset($_POST['nama_bagian'])) {
        foreach ($_POST['nama_bagian'] as $key => $nama_bagian) {
            if (!empty($nama_bagian)) {
                $p = $_POST['p'][$key] ?? '-';
                $l = $_POST['l'][$key] ?? '-';
                $t = $_POST['t'][$key] ?? '-';
                $qty = $_POST['qty'][$key] ?? 1;

                $foto_name = "";
                // Cek mana yang diisi: Kamera atau Galeri
                $file_input = null;
                if (!empty($_FILES['foto_kamera']['name'][$key])) {
                    $file_input = 'foto_kamera';
                } elseif (!empty($_FILES['foto_galeri']['name'][$key])) {
                    $file_input = 'foto_galeri';
                }

                if ($file_input) {
                    $ext = pathinfo($_FILES[$file_input]['name'][$key], PATHINFO_EXTENSION);
                    $foto_name = "ITM-" . time() . "-" . $key . "." . $ext;
                    move_uploaded_file($_FILES[$file_input]['tmp_name'][$key], "../uploads/survey/" . $foto_name);
                }

                $stmt_item = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_item->execute([$survey_id, $nama_bagian, $p, $l, $t, $qty, $foto_name]);
            }
        }
    }
    $_SESSION['success'] = "Survey berhasil disimpan!";
    header("Location: survey.php");
    exit;
}

// Logika Hapus (Sama)
if (isset($_GET['del'])) {
    $stmt = $pdo->prepare("DELETE FROM surveys WHERE id = ?");
    $stmt->execute([$_GET['del']]);
    $_SESSION['success'] = "Data dihapus!";
    header("Location: survey.php");
    exit;
}

include 'header.php';
$surveys = $pdo->query("SELECT * FROM surveys ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary"><i class="fa-solid fa-clipboard-check me-2"></i>Survey Lapangan</h4>
    <button class="btn btn-primary rounded-pill px-4 shadow" data-bs-toggle="collapse" data-bs-target="#formSurvey">+ Buat Survey</button>
</div>

<div class="collapse mb-5" id="formSurvey">
    <form method="POST" enctype="multipart/form-data">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold">Nama Klien *</label>
                    <input type="text" name="nama_klien" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold small">RINCIAN ITEM</span>
                <button type="button" class="btn btn-sm btn-success" id="addItem">+ Tambah</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="small text-center">
                            <tr>
                                <th>Item</th>
                                <th>P</th>
                                <th>L</th>
                                <th>T</th>
                                <th>Qty</th>
                                <th>Upload Foto</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="itemWrapper">
                            <tr class="item-row">
                                <td><input type="text" name="nama_bagian[]" class="form-control form-control-sm" required></td>
                                <td><input type="text" name="p[]" class="form-control form-control-sm text-center" placeholder="0"></td>
                                <td><input type="text" name="l[]" class="form-control form-control-sm text-center" placeholder="0"></td>
                                <td><input type="text" name="t[]" class="form-control form-control-sm text-center" placeholder="0"></td>
                                <td><input type="number" name="qty[]" class="form-control form-control-sm text-center" value="1"></td>
                                <td>
                                    <div class="btn-group w-100">
                                        <label class="btn btn-sm btn-outline-info" title="Kamera">
                                            <i class="fa fa-camera"></i>
                                            <input type="file" name="foto_kamera[]" accept="image/*" capture="environment" class="d-none">
                                        </label>
                                        <label class="btn btn-sm btn-outline-secondary" title="Galeri">
                                            <i class="fa fa-image"></i>
                                            <input type="file" name="foto_galeri[]" accept="image/*" class="d-none">
                                        </label>
                                    </div>
                                    <small class="text-muted" style="font-size:9px">Pilih salah satu</small>
                                </td>
                                <td><button type="button" class="btn btn-sm btn-danger btn-remove"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" name="simpan_survey_lengkap" class="btn btn-primary px-5 shadow">SIMPAN SEMUA</button>
            </div>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php foreach ($surveys as $s): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0"><?= $s['nama_klien'] ?></h6><small class="text-muted"><?= $s['lokasi'] ?></small>
                    </div>
                    <div>
                        <a href="survey-view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Detail</a>
                        <a href="?del=<?= $s['id'] ?>" class="btn btn-sm btn-link text-danger btn-hapus"><i class="fa-solid fa-trash"></i></a>
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
        row.querySelectorAll('input').forEach(i => {
            if (i.type === 'number') i.value = 1;
            else if (i.type === 'file') i.value = '';
            else i.value = '';
        });
        wrapper.appendChild(row);
    });
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) e.target.closest('.item-row').remove();
        }
    });
</script>
<?php include 'footer.php'; ?>