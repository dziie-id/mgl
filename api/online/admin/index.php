<?php 
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// --- 1. LOGIKA UPDATE DATA (TOKEN / MAPS / API KEY) ---
if (isset($_POST['update_cfg'])) {
    $service = aman($_POST['service_name']);
    $val     = $_POST['token_value']; 
    
    // Pake REPLACE INTO biar ID tetep unik dan gak numpuk
    $sql = "REPLACE INTO app_config (service_name, token_value) VALUES ('$service', '$val')";
    if (mysqli_query($conn, $sql)) {
        $msg = ["success", "GACOR! DATA $service BERHASIL DIUPDATE."];
    } else {
        $msg = ["danger", "DB ERROR: " . mysqli_error($conn)];
    }
}

// --- 2. LOGIKA AKTIVASI DRIVER ---
if (isset($_POST['add_user'])) {
    $hwid = aman($_POST['hwid']);
    $nama = aman($_POST['nama']);
    $exp  = aman($_POST['exp']);
    
    $sql = "INSERT INTO users (hwid, nama_driver, status_aktif, tgl_expired) VALUES ('$hwid', '$nama', '1', '$exp')";
    if (mysqli_query($conn, $sql)) {
        $msg = ["success", "DRIVER $nama SUDAH AKTIF!"];
    } else {
        $msg = ["danger", "GAGAL! HWID SUDAH ADA DI DATABASE."];
    }
}

// --- 3. LOGIKA HAPUS ---
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
        .card { background: #161616; border: 1px solid #333; border-radius: 12px; margin-bottom: 20px; }
        h5 { color: #00d2ff; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        label { color: #888; font-weight: bold; margin-top: 10px; display: block; }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #00d2ff !important; }
        .form-control:focus { background: #252525; border-color: #00d2ff; box-shadow: none; }
        .table { color: #fff; }
        .table thead { background: #00d2ff; color: #000; }
        code { color: #00d2ff; background: #000; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-info"><i class="fas fa-bolt"></i> MGL PANEL <span class="badge bg-info text-dark">V2.6</span></span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">LOGOUT</a>
    </div>
</nav>

<div class="container">
    <?php if(isset($msg)): ?>
        <div class="alert alert-<?= $msg[0] ?> alert-dismissible fade show">
            <?= $msg[1] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4">
                <h5><i class="fas fa-crosshairs"></i> UPDATE PELURU</h5>
                <form method="POST">
                    <label>Pilih Layanan</label>
                    <select name="service_name" id="service_select" class="form-select" onchange="loadPeluru()">
                        <option value="">-- Pilih --</option>
                        <option value="gofood">Gojek Token</option>
                        <option value="grabfood">Grab Token</option>
                        <option value="maps_style">Google Map Style</option>
                        <option value="google_api_key">Google API Key (Pulungan)</option>
                    </select>
                    
                    <label>Isi Data</label>
                    <textarea name="token_value" id="peluru_box" class="form-control" rows="6" placeholder="Pilih layanan..."></textarea>
                    
                    <button type="submit" name="update_cfg" class="btn btn-info w-100 fw-bold mt-3">UPDATE DATA</button>
                    <button type="button" onclick="cekNyawaKey()" class="btn btn-outline-warning w-100 fw-bold mt-2">CEK NYAWA KEY</button>
                </form>
            </div>

            <div class="card p-4">
                <h5><i class="fas fa-user-plus"></i> AKTIVASI DRIVER</h5>
                <form method="POST">
                    <label>HWID HP</label>
                    <input type="text" name="hwid" class="form-control" placeholder="Tempel HWID..." required>
                    <label>Nama Akun</label>
                    <input type="text" name="nama" class="form-control">
                    <label>Masa Aktif</label>
                    <input type="date" name="exp" class="form-control" required>
                    <button type="submit" name="add_user" class="btn btn-success w-100 fw-bold mt-3">AKTIFKAN SEKARANG</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5><i class="fas fa-users"></i> DATA DRIVER AKTIF</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>HWID</th>
                                <th>Expired</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                            while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td class="text-info fw-bold"><?= $row['nama_driver'] ?></td>
                                <td><code><?= $row['hwid'] ?></code></td>
                                <td><span class="badge bg-dark border border-danger"><?= $row['tgl_expired'] ?></span></td>
                                <td>
                                    <a href="?hapus=<?= $row['id'] ?>" class="text-danger" onclick="return confirm('Hapus Driver?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi Ambil Data dari Database ke Textarea
function loadPeluru() {
    var service = document.getElementById("service_select").value;
    var box = document.getElementById("peluru_box");
    if (service == "") { box.value = ""; return; }
    
    fetch('get_peluru.php?service=' + service)
        .then(response => response.text())
        .then(data => { box.value = data.trim(); });
}

// Fungsi Cek Nyawa Google API Key
function cekNyawaKey() {
    var key = document.getElementById("peluru_box").value;
    var service = document.getElementById("service_select").value;

    if (service !== "google_api_key" || key === "") {
        alert("Pilih 'Google API Key' dan pastikan isinya ada Bang!");
        return;
    }

    // Test nembak static map pake key tersebut
    var testUrl = "https://maps.googleapis.com/maps/api/staticmap?center=0,0&zoom=1&size=100x100&key=" + key;
    
    var img = new Image();
    img.onload = function() { alert("GACOR! API Key Masih Idup & Valid."); };
    img.onerror = function() { alert("MODAR! API Key Suspend / Salah."); };
    img.src = testUrl;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
