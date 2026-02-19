<?php 
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// --- LOGIKA 1: UPDATE PELURU (TOKEN/MAPS) ---
if (isset($_POST['update_cfg'])) {
    $service = aman($_POST['service_name']);
    $val     = $_POST['token_value']; // Tanpa 'aman' agar karakter spesial JSON/Style Maps tidak rusak
    
    // Gunakan REPLACE INTO agar jika service_name sudah ada, dia meniban. Jika belum, dia menambah.
    $sql = "REPLACE INTO app_config (service_name, token_value) VALUES ('$service', '$val')";
    if (mysqli_query($conn, $sql)) {
        $msg = ["success", "PELURU $service BERHASIL DIISI!"];
    } else {
        $msg = ["danger", "GAGAL ISI PELURU: " . mysqli_error($conn)];
    }
}

// --- LOGIKA 2: TAMBAH/AKTIVASI DRIVER ---
if (isset($_POST['add_user'])) {
    $hwid = aman($_POST['hwid']);
    $nama = aman($_POST['nama']);
    $exp  = aman($_POST['exp']);
    
    $sql = "INSERT INTO users (hwid, nama_driver, status_aktif, tgl_expired) VALUES ('$hwid', '$nama', '1', '$exp')";
    if (mysqli_query($conn, $sql)) {
        $msg = ["success", "DRIVER $nama BERHASIL DIAKTIFKAN!"];
    } else {
        $msg = ["danger", "GAGAL AKTIVASI: HWID Mungkin Sudah Ada!"];
    }
}

// --- LOGIKA 3: HAPUS DRIVER ---
if (isset($_GET['hapus'])) {
    $id = aman($_GET['hapus']);
    if (mysqli_query($conn, "DELETE FROM users WHERE id = '$id'")) {
        echo "<script>window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGL AUTO PANEL - GACOR MODE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0a0a0a; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #161616; border-bottom: 2px solid #00d2ff; box-shadow: 0 0 15px rgba(0,210,255,0.3); }
        .card { background: #161616; border: 1px solid #333; border-radius: 12px; transition: 0.3s; }
        .card:hover { border-color: #00d2ff; }
        h5 { color: #00d2ff; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        label { color: #f8f9fa !important; font-weight: bold; margin-bottom: 5px; }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff !important; }
        .form-control:focus { background: #282828; border-color: #00d2ff; box-shadow: none; }
        .table { color: #fff; border-color: #333; }
        .table thead { background: #00d2ff; color: #000; }
        .btn-info { background: #00d2ff; border: none; color: #000; }
        .btn-info:hover { background: #0099bb; }
        code { color: #00d2ff; background: #1a1a1a; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 p-3">
    <div class="container">
        <span class="navbar-brand fw-bold text-info">
            <i class="fas fa-bolt text-warning"></i> MGL AUTO PANEL <span class="badge bg-info text-dark ms-2">v2.5</span>
        </span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold">KELUAR <i class="fas fa-sign-out-alt"></i></a>
    </div>
</nav>

<div class="container">
    <?php if(isset($msg)): ?>
        <div class="alert alert-<?= $msg[0] ?> alert-dismissible fade show fw-bold mb-4">
            <?= $msg[1] ?>
            <button type="button" class="btn-close" data-bs-dismiss='alert'></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 mb-4 shadow-lg">
    <h5><i class="fas fa-crosshairs text-warning"></i> UPDATE PELURU</h5>
    <hr class="border-secondary">
    <form method="POST">
        <div class="mb-3">
            <label>Layanan</label>
            <select name="service_name" id="service_select" class="form-select" onchange="loadPeluru()">
                <option value="">-- Pilih Layanan --</option>
                <option value="gofood">Gojek Token</option>
                <option value="grabfood">Grab Token</option>
                <option value="maps_style">Google Map Style</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Isi Peluru (Token/Style)</label>
            <textarea name="token_value" id="peluru_box" class="form-control text-info" rows="5" placeholder="Pilih layanan dulu bang..."></textarea>
        </div>
        <button type="submit" name="update_cfg" class="btn btn-info w-100 fw-bold">UPDATE DATA</button>
    </form>
</div>

<script>
function loadPeluru() {
    var service = document.getElementById("service_select").value;
    var box = document.getElementById("peluru_box");
    
    if (service == "") {
        box.value = "";
        return;
    }

    // Kita minta data ke file helper (get_peluru.php)
    fetch('get_peluru.php?service=' + service)
        .then(response => response.text())
        .then(data => {
            box.value = data;
        });
}
</script>

            <div class="card p-4 shadow-lg">
                <h5><i class="fas fa-user-plus text-success"></i> AKTIVASI DRIVER</h5>
                <hr class="border-secondary">
                <form method="POST">
                    <div class="mb-3">
                        <label>Hardware ID (HWID)</label>
                        <input type="text" name="hwid" class="form-control" placeholder="Contoh: XYZ-123" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Driver</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Akun">
                    </div>
                    <div class="mb-3">
                        <label>Expired Date</label>
                        <input type="date" name="exp" class="form-control" required>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-success w-100 fw-bold">AKTIFKAN SEKARANG</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4 shadow-lg">
                <h5><i class="fas fa-database text-info"></i> DRIVER TERDAFTAR</h5>
                <div class="table-responsive mt-3">
                    <table class="table table-dark table-hover align-middle">
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
                            if(mysqli_num_rows($q) == 0) echo "<tr><td colspan='4' class='text-center'>Belum ada driver aktif.</td></tr>";
                            while($d = mysqli_fetch_assoc($q)) {
                                echo "<tr>
                                    <td class='fw-bold text-info'><i class='fas fa-user-circle'></i> {$d['nama_driver']}</td>
                                    <td><code>{$d['hwid']}</code></td>
                                    <td class='text-center'><span class='badge bg-dark border border-info text-info'>{$d['tgl_expired']}</span></td>
                                    <td class='text-center'>
                                        <a href='?hapus={$d['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus driver ini?\")'>
                                            <i class='fas fa-trash'></i>
                                        </a>
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
    <small style="letter-spacing: 2px;">MGL STIKER TEAM &copy; 2026 | BUILT FOR GACOR</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
