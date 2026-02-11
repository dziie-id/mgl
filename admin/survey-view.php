<?php 
include '../includes/db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }

$id = $_GET['id'];

// --- LOGIKA TAMBAH ITEM ---
if (isset($_POST['add_item'])) {
    $foto_name = "";
    $file_input = !empty($_FILES['foto_kamera']['name']) ? 'foto_kamera' : (!empty($_FILES['foto_galeri']['name']) ? 'foto_galeri' : null);

    if ($file_input) {
        $foto_name = "ITM-" . time() . ".jpg";
        move_uploaded_file($_FILES[$file_input]['tmp_name'], "../uploads/survey/" . $foto_name);
    }
    $stmt = $pdo->prepare("INSERT INTO survey_items (survey_id, nama_bagian, p, l, t, qty, foto_item) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id, $_POST['nama_bagian'], $_POST['p'], $_POST['l'], $_POST['t'], $_POST['qty'], $foto_name]);
    $_SESSION['success'] = "Item ditambahkan!";
    header("Location: survey-view.php?id=$id"); exit;
}

// --- LOGIKA EDIT ITEM ---
if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $foto_final = $_POST['foto_lama'];
    $file_input = !empty($_FILES['foto_kamera']['name']) ? 'foto_kamera' : (!empty($_FILES['foto_galeri']['name']) ? 'foto_galeri' : null);

    if ($file_input) {
        if (!empty($foto_final) && file_exists("../uploads/survey/$foto_final")) { unlink("../uploads/survey/$foto_final"); }
        $foto_final = "ITM-EDIT-" . time() . ".jpg";
        move_uploaded_file($_FILES[$file_input]['tmp_name'], "../uploads/survey/" . $foto_final);
    }

    $stmt = $pdo->prepare("UPDATE survey_items SET nama_bagian=?, p=?, l=?, t=?, qty=?, foto_item=? WHERE id=?");
    $stmt->execute([$_POST['nama_bagian'], $_POST['p'], $_POST['l'], $_POST['t'], $_POST['qty'], $foto_final, $item_id]);
    $_SESSION['success'] = "Data diperbarui!";
    header("Location: survey-view.php?id=$id"); exit;
}

// --- LOGIKA HAPUS ---
if (isset($_GET['del_item'])) {
    $stmt_f = $pdo->prepare("SELECT foto_item FROM survey_items WHERE id = ?");
    $stmt_f->execute([$_GET['del_item']]);
    $img = $stmt_f->fetch();
    if ($img && !empty($img['foto_item'])) { @unlink("../uploads/survey/" . $img['foto_item']); }
    $pdo->prepare("DELETE FROM survey_items WHERE id = ?")->execute([$_GET['del_item']]);
    $_SESSION['success'] = "Item dihapus!";
    header("Location: survey-view.php?id=$id"); exit;
}

include 'header.php'; 
$client = $pdo->query("SELECT * FROM surveys WHERE id = $id")->fetch();
$survey_list = $pdo->query("SELECT * FROM survey_items WHERE survey_id = $id ORDER BY id DESC")->fetchAll();
?>

<nav class="mb-3"><a href="survey.php" class="small text-muted text-decoration-none"><i class="fa fa-arrow-left"></i> Daftar Survey</a></nav>

<div class="card bg-primary text-white border-0 shadow-sm mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div><h3 class="fw-bold mb-0 text-uppercase"><?= $client['nama_klien'] ?></h3><p class="mb-0 opacity-75 small"><?= $client['lokasi'] ?></p></div>
        <a href="survey-export.php?id=<?= $id ?>" target="_blank" class="btn btn-light shadow-sm fw-bold btn-sm">EXPORT PDF</a>
    </div>
</div>

<div class="row g-4">
    <!-- TAMBAH ITEM -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-dark text-white fw-bold small">TAMBAH DATA</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3"><label class="small fw-bold">Nama Bagian *</label><input type="text" name="nama_bagian" class="form-control" required></div>
                    <div class="row g-2 mb-3">
                        <div class="col-4"><label class="small">P</label><input type="text" name="p" class="form-control"></div>
                        <div class="col-4"><label class="small">L</label><input type="text" name="l" class="form-control"></div>
                        <div class="col-4"><label class="small">T</label><input type="text" name="t" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="small fw-bold">QTY</label><input type="number" name="qty" class="form-control" value="1"></div>
                    <div class="mb-3">
                        <label class="small fw-bold">Foto Item</label>
                        <div class="btn-group w-100 mt-1">
                            <label class="btn btn-outline-info btn-sm"><i class="fa fa-camera"></i> Kamera<input type="file" name="foto_kamera" accept="image/*" capture="environment" class="d-none"></label>
                            <label class="btn btn-outline-secondary btn-sm"><i class="fa fa-image"></i> Galeri<input type="file" name="foto_galeri" accept="image/*" class="d-none"></label>
                        </div>
                    </div>
                    <button type="submit" name="add_item" class="btn btn-primary w-100 shadow-sm">SIMPAN ITEM</button>
                </form>
            </div>
        </div>
    </div>

    <!-- LIST ITEM -->
    <div class="col-md-8">
        <div class="row g-3">
            <?php foreach($survey_list as $itm): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="row g-0">
                        <div class="col-4 bg-dark d-flex align-items-center justify-content-center">
                            <?php if(!empty($itm['foto_item']) && file_exists("../uploads/survey/".$itm['foto_item'])): ?>
                                <a href="../uploads/survey/<?= $itm['foto_item'] ?>" data-fancybox="survey"><img src="../uploads/survey/<?= $itm['foto_item'] ?>" class="img-fluid h-100 w-100" style="object-fit: cover; min-height: 100px;"></a>
                            <?php else: ?><i class="fa-solid fa-image fa-2x opacity-25 text-white"></i><?php endif; ?>
                        </div>
                        <div class="col-8 p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold mb-0 text-primary text-uppercase"><?= $itm['nama_bagian'] ?></h6>
                                <div>
                                    <button class="btn btn-sm text-warning p-0 me-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $itm['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <a href="?id=<?= $id ?>&del_item=<?= $itm['id'] ?>" class="text-danger small btn-hapus"><i class="fa-solid fa-xmark fa-lg"></i></a>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border">P:<?= $itm['p'] ?></span><span class="badge bg-light text-dark border">L:<?= $itm['l'] ?></span><span class="badge bg-light text-dark border">T:<?= $itm['t'] ?></span><span class="badge bg-warning text-dark">QTY:<?= $itm['qty'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL EDIT -->
            <div class="modal fade" id="editModal<?= $itm['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                    <div class="modal-header bg-warning py-2"><h6 class="modal-title fw-bold">Edit Item</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <form method="POST" enctype="multipart/form-data"><div class="modal-body">
                        <input type="hidden" name="item_id" value="<?= $itm['id'] ?>"><input type="hidden" name="foto_lama" value="<?= $itm['foto_item'] ?>">
                        <div class="mb-3"><label class="small fw-bold">Nama Bagian</label><input type="text" name="nama_bagian" class="form-control" value="<?= $itm['nama_bagian'] ?>" required></div>
                        <div class="row g-2 mb-3"><div class="col-4"><label class="small">P</label><input type="text" name="p" class="form-control" value="<?= $itm['p'] ?>"></div><div class="col-4"><label class="small">L</label><input type="text" name="l" class="form-control" value="<?= $itm['l'] ?>"></div><div class="col-4"><label class="small">T</label><input type="text" name="t" class="form-control" value="<?= $itm['t'] ?>"></div></div>
                        <div class="mb-3"><label class="small fw-bold">QTY</label><input type="number" name="qty" class="form-control" value="<?= $itm['qty'] ?>"></div>
                        <div class="mb-3"><label class="small fw-bold">Ganti Foto</label>
                            <div class="btn-group w-100 mt-1">
                                <label class="btn btn-outline-info btn-sm"><i class="fa fa-camera"></i> Kamera<input type="file" name="foto_kamera" accept="image/*" capture="environment" class="d-none"></label>
                                <label class="btn btn-outline-secondary btn-sm"><i class="fa fa-image"></i> Galeri<input type="file" name="foto_galeri" accept="image/*" class="d-none"></label>
                            </div>
                        </div>
                    </div><div class="modal-footer"><button type="submit" name="update_item" class="btn btn-warning w-100 fw-bold">UPDATE</button></div></form>
                </div></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>