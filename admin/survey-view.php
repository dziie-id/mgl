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

// --- 1. LOGIKA TAMBAH ITEM SUSULAN ---
if (isset($_POST['add_item'])) {
    $foto_name = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = "ITM-" . time() . "-" . rand(10, 99) . ".jpg";
        move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/survey/" . $foto_name);
    }
    $stmt = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id, $_POST['nama_bagian'], $_POST['p'], $_POST['l'], $_POST['t'], $_POST['qty'], $foto_name]);
    $_SESSION['success'] = "Item berhasil ditambahkan!";
    header("Location: survey-view.php?id=$id");
    exit;
}

// --- 2. LOGIKA EDIT ITEM ---
if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $foto_lama = $_POST['foto_lama'];
    $foto_final = $foto_lama;

    if (!empty($_FILES['foto']['name'])) {
        if (!empty($foto_lama) && file_exists("../uploads/survey/$foto_lama")) {
            unlink("../uploads/survey/$foto_lama");
        }
        $foto_final = "ITM-EDIT-" . time() . ".jpg";
        move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/survey/" . $foto_final);
    }

    $stmt = $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=?, foto_item=? WHERE id=?");
    $stmt->execute([$_POST['nama_bagian'], $_POST['p'], $_POST['l'], $_POST['t'], $_POST['qty'], $foto_final, $item_id]);
    $_SESSION['success'] = "Data item diperbarui!";
    header("Location: survey-view.php?id=$id");
    exit;
}

// --- 3. LOGIKA HAPUS ITEM ---
if (isset($_GET['del_item'])) {
    $item_id = $_GET['del_item'];
    $stmt = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $img = $stmt->fetch();
    if ($img && !empty($img['foto_item'])) {
        $path = "../uploads/survey/" . $img['foto_item'];
        if (file_exists($path)) {
            unlink($path);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM survey_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $_SESSION['success'] = "Item berhasil dihapus!";
    header("Location: survey-view.php?id=$id");
    exit;
}

include 'header.php';

// Ambil Data Klien
$stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

// Ambil Rincian Item
$items = $pdo->prepare("SELECT * FROM survey_items WHERE survey_id = ? ORDER BY id DESC");
$items->execute([$id]);
$survey_list = $items->fetchAll();
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item small"><a href="survey.php">Daftar Survey</a></li>
        <li class="breadcrumb-item small active"><?= $client['nama_klien'] ?></li>
    </ol>
</nav>

<div class="card bg-primary text-white border-0 shadow-sm mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0"><?= $client['nama_klien'] ?></h3>
            <p class="mb-0 opacity-75 small"><?= $client['lokasi'] ?></p>
        </div>
        <a href="survey-export.php?id=<?= $id ?>" target="_blank" class="btn btn-light shadow-sm fw-bold">Export PDF</a>
    </div>
</div>

<div class="row g-4">
    <!-- FORM TAMBAH -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-dark text-white fw-bold small">TAMBAH DATA UKURAN</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Bagian / Item</label>
                        <input type="text" name="nama_bagian" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4"><label class="small">P</label><input type="text" name="p" class="form-control"></div>
                        <div class="col-4"><label class="small">L</label><input type="text" name="l" class="form-control"></div>
                        <div class="col-4"><label class="small">T</label><input type="text" name="t" class="form-control"></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">QTY</label>
                        <input type="number" name="qty" class="form-control" value="1">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Ambil Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="add_item" class="btn btn-primary w-100 shadow-sm">SIMPAN ITEM</button>
                </form>
            </div>
        </div>
    </div>

    <!-- LIST ITEM -->
    <div class="col-md-8">
        <div class="row g-3">
            <?php foreach ($survey_list as $itm): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="row g-0">
                            <div class="col-4 col-md-3 bg-dark d-flex align-items-center justify-content-center">
                                <!-- LOGIKA TAMPIL GAMBAR -->
                                <?php if (!empty($itm['foto_item']) && file_exists("../uploads/survey/" . $itm['foto_item'])): ?>
                                    <a href="../uploads/survey/<?= $itm['foto_item'] ?>" data-fancybox="survey">
                                        <img src="../uploads/survey/<?= $itm['foto_item'] ?>" class="img-fluid h-100 w-100" style="object-fit: cover; min-height: 120px;">
                                    </a>
                                <?php else: ?>
                                    <div class="text-center text-white opacity-25">
                                        <i class="fa-solid fa-image fa-3x"></i><br>
                                        <small>No Foto</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-8 col-md-9 p-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold mb-2 text-primary text-uppercase"><?= $itm['nama_bagian'] ?></h6>
                                    <div>
                                        <button class="btn btn-sm text-warning p-0 me-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $itm['id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></button>
                                        <a href="?id=<?= $id ?>&del_item=<?= $itm['id'] ?>" class="text-danger small btn-hapus"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border">P: <?= $itm['p'] ?></span>
                                    <span class="badge bg-light text-dark border">L: <?= $itm['l'] ?></span>
                                    <span class="badge bg-light text-dark border">T: <?= $itm['t'] ?></span>
                                    <span class="badge bg-warning text-dark fw-bold">QTY: <?= $itm['qty'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT ITEM -->
                <div class="modal fade" id="editModal<?= $itm['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title fw-bold">Edit Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="item_id" value="<?= $itm['id'] ?>">
                                    <input type="hidden" name="foto_lama" value="<?= $itm['foto_item'] ?>">
                                    <div class="mb-3">
                                        <label class="small fw-bold">Nama Bagian</label>
                                        <input type="text" name="nama_bagian" class="form-control" value="<?= $itm['nama_bagian'] ?>" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-4"><label class="small">P</label><input type="text" name="p" class="form-control" value="<?= $itm['p'] ?>"></div>
                                        <div class="col-4"><label class="small">L</label><input type="text" name="l" class="form-control" value="<?= $itm['l'] ?>"></div>
                                        <div class="col-4"><label class="small">T</label><input type="text" name="t" class="form-control" value="<?= $itm['t'] ?>"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold">QTY</label>
                                        <input type="number" name="qty" class="form-control" value="<?= $itm['qty'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold">Ganti Foto (Kosongkan jika tidak)</label>
                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update_item" class="btn btn-warning w-100 fw-bold">UPDATE DATA</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>