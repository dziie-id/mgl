<?php 
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard MGL - Melek Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0f0f0f; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: #1a1a1a; border-bottom: 2px solid #00d2ff; }
        .card { background: #1a1a1a; border: 1px solid #333; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        h5 { color: #00d2ff; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 10px; }
        
        /* Teks Label biar gak remang-remang */
        label, .form-label { color: #f8f9fa !important; font-weight: 500; margin-bottom: 5px; }
        .table thead { background: #00d2ff; color: #000; }
        .table { color: #ffffff !important; }
        
        .form-control, .form-select { 
            background: #252525; border: 1px solid #444; color: #fff !important; 
        }
        .form-control::placeholder { color: #777; }
        .form-control:focus { background: #303030; border-color: #00d2ff; box-shadow: none; color: #fff; }
        
        .badge-expired { background: #ff4757; color: white; }
        .badge-active { background: #2ed573; color: black; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-info"><i class="fas fa-microchip"></i> MGL AUTO PANEL <span class="badge bg-info text-dark" style="font-size: 0.5em;">v2.1</span></span>
        <a href="logout.php" class="btn btn-danger btn-sm fw-bold">KELUAR <i class="fas fa-sign-out-alt"></i></a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 mb-4">
                <h5><i class="fas fa-crosshairs text-warning"></i> UPDATE PELURU</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Pilih Layanan</label>
                        <select name="service_name" class="form-select">
                            <option value="gofood">Gojek Token</option>
                            <option value="grabfood">Grab Token</option>
                            <option value="maps_style">Map Style (Google)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Isi Peluru</label>
                        <textarea name="token_value" class="form-control" rows="4" placeholder="Paste token atau style di sini..."></textarea>
                    </div>
                    <button type="submit" name="update_cfg" class="btn btn-info w-100 fw-bold text-dark">ISI ULANG PELURU</button>
                </form>
            </div>

            <div class="card p-4">
                <h5><i class="fas fa-key text-success"></i> AKTIVASI HWID</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Hardware ID (HWID)</label>
                        <input type="text" name="hwid" class="form-control" placeholder="Contoh: 8899-AABB-CC" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Driver</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Panggilan">
                    </div>
                    <div class="mb-3">
                        <label>Masa Berlaku</label>
                        <input type="date" name="exp" class="form-control" required>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-success w-100 fw-bold">AKTIFKAN SEKARANG</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5><i class="fas fa-list-ul"></i> DATABASE DRIVER AKTIF</h5>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr class="text-center">
                                <th width="20%">Nama</th>
                                <th>HWID</th>
                                <th width="25%">Expired</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                            while($d = mysqli_fetch_assoc($q)) {
                                echo "<tr>
                                    <td class='fw-bold text-info'>{$d['nama_driver']}</td>
                                    <td><code class='text-light'>{$d['hwid']}</code></td>
                                    <td class='text-center'><span class='badge badge-active'>{$d['tgl_expired']}</span></td>
                                    <td class='text-center'>
                                        <a href='?hapus={$d['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus driver ini?\")'><i class='fas fa-trash'></i></a>
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

<footer class="text-center mt-5 mb-4 text-secondary" style="font-size: 0.8em; letter-spacing: 1px;">
    DEVELOPED BY <span class="text-info">MGL STIKER TEAM</span> &copy; 2026
</footer>

</body>
</html>
