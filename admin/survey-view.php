<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

if (isset($_POST['add_item'])) {
    $foto_name = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = "ITM-" . time() . ".jpg";
        move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/survey/" . $foto_name);
    }
    $stmt = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id, $_POST['nama_bagian'], $_POST['p'], $_POST['l'], $_POST['t'], $_POST['qty'], $foto_name]);
    header("Location: survey-view.php?id=$id");
    $_SESSION['success'] = "Item berhasil ditambahkan!";
    exit;
}

if (isset($_GET['del_item'])) {
    $stmt = $pdo->prepare("DELETE FROM survey_items WHERE id = ?");
    $stmt->execute([$_GET['del_item']]);
    header("Location: survey-view.php?id=$id");
    $_SESSION['success'] = "Item berhasil dihapus!";
    exit;
}

include 'header.php';

$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

$items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id DESC");
$items->execute([$id]);
$survey_list = $items->fetchAll();
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item small"><a href="survey.php" class="text-decoration-none text-muted">Daftar Survey</a></li>
        <li class="breadcrumb-item small active text-primary" aria-current="page"><?= $client['nama_klien'] ?></li>
    </ol>
</nav>


<div class="card bg-primary text-white border-0 shadow-sm mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0 text-uppercase"><?= $client['nama_klien'] ?></h3>
            <p class="mb-0 opacity-75 small"><i class="fa-solid fa-map-location-dot me-2"></i><?= $client['lokasi'] ?></p>
        </div>
        <a href="survey-export.php?id=<?= $id ?>" target="_blank" class="btn btn-light shadow-sm">
            <i class="fa-solid fa-file-pdf text-danger me-1"></i> Export PDF
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-dark text-white fw-bold small py-3">TAMBAH DATA UKURAN</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nama Bagian / Item <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bagian" class="form-control" placeholder="Contoh: Kaca Lobby" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4"><label class="x-small fw-bold">P (cm)</label><input type="text" name="p" class="form-control" placeholder="0"></div>
                        <div class="col-4"><label class="x-small fw-bold">L (cm)</label><input type="text" name="l" class="form-control" placeholder="0"></div>
                        <div class="col-4"><label class="x-small fw-bold">T (cm)</label><input type="text" name="t" class="form-control" placeholder="0"></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Jumlah (QTY)</label>
                        <input type="number" name="qty" class="form-control" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Foto Lokasi</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="add_item" class="btn btn-primary w-100 shadow-sm">SIMPAN ITEM</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row g-3">
            <?php foreach ($survey_list as $itm): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="row g-0">
                            <div class="col-4 col-md-3 bg-dark d-flex align-items-center justify-content-center">
                                <?php if ($itm['foto_item']): ?>
                                    <a href="../uploads/survey/<?= $itm['foto_item'] ?>" data-fancybox="survey">
                                        <img src="../uploads/survey/<?= $itm['foto_item'] ?>" class="img-fluid h-100 w-100" style="object-fit: cover; min-height: 100px;">
                                    </a>
                                <?php else: ?>
                                    <i class="fa-solid fa-image fa-2x opacity-25"></i>
                                <?php endif; ?>
                            </div>
                            <div class="col-8 col-md-9 p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0 text-primary text-uppercase"><?= $itm['nama_bagian'] ?></h6>
                                    <a href="?id=<?= $id ?>&del_item=<?= $itm['id'] ?>" class="text-danger small" onclick="return confirm('Hapus item ini?')">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="badge bg-body-tertiary text-body border px-3 py-2 fw-normal">P: <b><?= $itm['p'] ?: '-' ?></b></div>
                                    <div class="badge bg-body-tertiary text-body border px-3 py-2 fw-normal">L: <b><?= $itm['l'] ?: '-' ?></b></div>
                                    <div class="badge bg-body-tertiary text-body border px-3 py-2 fw-normal">T: <b><?= $itm['t'] ?: '-' ?></b></div>
                                    <div class="badge bg-warning text-dark px-3 py-2 fw-bold">QTY: <?= $itm['qty'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($survey_list) == 0): ?>
                <div class="col-12 text-center py-5 rounded border border-dashed">
                    <p class="text-muted mb-0">Belum ada rincian ukuran.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .x-small {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 2px;
        display: block;
    }
</style>

<?php include 'footer.php'; ?>