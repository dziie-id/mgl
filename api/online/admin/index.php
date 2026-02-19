<?php 
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// --- 1. LOGIKA UPDATE PELURU (TOKEN/MAPS) ---
if (isset($_POST['update_cfg'])) {
    $service = aman($_POST['service_name']);
    $val     = $_POST['token_value']; // Tanpa 'aman' agar JSON/Style Maps gak rusak
    
    // REPLACE INTO memastikan data TIDAK NUMPUK di database
    $sql = "REPLACE INTO app_config (service_name, token_value) VALUES ('$service', '$val')";
    if (mysqli_query($conn, $sql)) {
        $msg = ["success", "GACOR! DATA $service BERHASIL DIPERBAHARUI."];
    } else {
        $msg = ["danger", "ERROR DB: " . mysqli_error($conn)];
    }
}

// --- 2. LOGIKA AKTIVASI DRIVER ---
if (isset($_POST['add_user'])) {
    $hwid = aman($_POST['hwid']);
    $nama = aman($_POST['nama']);
    $exp  = aman($_POST['exp']);
    
    $sql = "INSERT INTO users (hwid, nama_driver, status_aktif, tgl_expired) VALUES ('$hwid', '$nama', '1', '$exp')";
    if (mysqli_query($conn, $sql)) {
        $msg = ["success", "DRIVER $nama AKTIF SAMPAI $exp!"];
    } else {
        $msg = ["danger", "GAGAL! HWID SUDAH TERDAFTAR."];
    }
}

// --- 3. LOGIKA HAPUS DRIVER ---
if (isset($_GET['hapus'])) {
    $id = aman($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGL AUTO PANEL v2.6</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0a0a0a; color: #fff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #161616; border-bottom: 2px solid #00d2ff; }
        .card { background: #161616; border: 1px solid #333; border-radius: 12px; }
        h5 { color: #00d2ff; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 10px; }
        label { color: #fff !important; font-weight: bold; margin-top: 10px; display: block; }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #00d2ff !important; font-weight: 500; }
        .form-control:focus { background: #252525; border-color: #00d2ff; box-shadow: none; }
        .table { color: #fff; }
        .table thead { background: #00d2ff; color: #000; }
        .badge-exp { background: #ff4757; }
        code { color: #00d2ff; background: #000; padding: 3px 6px; border-radius: 5px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-info"><i class="fas fa-bolt"></i> MGL AUTO PANEL <span class="badge bg-info text-dark">v2.6</span></span>
        <a href="logout.php" class="btn btn-danger btn-sm fw-bold">KELUAR <i class="fas fa-sign-out-alt"></i></a>
    </div>
</nav>

<div class="container">
    <?php if(isset($msg)): ?>
        <div class="alert alert-<?= $msg[0] ?> alert-dismissible fade show fw-bold">
            <?= $msg[1] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 mb-4">
                <h5><i class="fas fa-crosshairs text-warning"></i> UPDATE PELURU</h5>
                <form method="POST">
                    <label>Pilih Layanan</label>
                    <select name="service_name" id="service_select" class="form-select" onchange="loadPeluru()">
                        <option value="">-- Pilih --</option>
                        <option value="gofood">Gojek Token</option>
                        <option value="grabfood">Grab Token</option>
                        <option value="maps_style">Google Map Style</option>
                    </select>
                    
                    <label>Isi Peluru</label>
                    <textarea name="token_value" id="peluru_box" class="form-control" rows="5" placeholder="Pilih layanan dulu..."></textarea>
                    
                    <button type="submit" name="update_cfg" class="btn btn-info w-100 fw-bold mt-3">UPDATE DATA</button>
                </form>
            </div>

            <div class="card p-4">
                <h5><i class="fas fa-key text-success"></i> AKTIVASI DRIVER</h5>
                <form method="POST">
                    <label>HWID HP</label>
                    <input type="text" name="hwid" class="form-control" placeholder="Tempel HWID..." required>
                    <label>Nama Akun</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Panggilan">
                    <label>Berlaku Sampai</label>
                    <input type="date" name="exp" class="form-control" required>
                    <button type="submit" name="add_user" class="btn btn-success w-100 fw-bold mt-3">AKTIFKAN!</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5><i class="fas fa-users"></i> DRIVER TERDAFTAR</h5>
                <div class="table-responsive">
                    <table class="table table-hover mt-3">
                        <thead>
                            <tr class="text-center">
                                <th>Driver</th>
                                <th>HWID</th>
                                <th>Expired</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                            while($d = mysqli_fetch_assoc($q)) {
                                echo "<tr>
                                    <td class='text-info fw-bold'>{$d['nama_driver']}</td>
                                    <td><code>{$d['hwid']}</code></td>
                                    <td class='text-center'><span class='badge bg-dark border border-danger'>{$d['tgl_expired']}</span></td>
                                    <td class='text-center'>
                                        <a href='?hapus={$d['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus?\")'><i class='fas fa-trash'></i></a>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 mb-4 text-secondary">
    <small>MGL STIKER TEAM © 2026 | GACOR FOREVER</small>
</footer>

<script>
function loadPeluru() {
    var service = document.getElementById("service_select").value;
    var box = document.getElementById("peluru_box");
    
    if (service == "") {
        box.value = "";
        return;
    }

    // Kasih placeholder biar user tau lagi loading
    box.value = "Sedang mengambil data...";

    fetch('get_peluru.php?service=' + service)
        .then(response => {
            if (!response.ok) throw new Error('File get_peluru.php tidak ditemukan!');
            return response.text();
        })
        .then(data => {
            // Bersihin data kalau ada spasi gak jelas
            box.value = data.trim();
        })
        .catch(err => {
            box.value = "ERROR: " + err.message;
        });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
