<?php
session_start();
include "config.php";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// 1. Simpan Setting (Maps, Gojek, Grab)
if (isset($_POST['update_config'])) {
    $map_key = $_POST['map_key'];
    $gojek = $_POST['gojek_token'];
    $grab = $_POST['grab_token'];
    $conn->query("UPDATE settings SET map_key='$map_key', gojek_token='$gojek', grab_token='$grab' WHERE id=1");
    echo "<script>alert('Semua Data Berhasil Disimpan!'); window.location='dashboard.php';</script>";
}

// 2. Tambah Driver Manual
if (isset($_POST['add_driver'])) {
    $nama = $_POST['nama'];
    $hwid = $_POST['hwid'];
    $conn->query("INSERT INTO drivers (nama, hwid, status) VALUES ('$nama', '$hwid', 'active')");
}

// 3. Hapus Driver
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM drivers WHERE id=$id");
    header("Location: dashboard.php");
}

$settings = $conn->query("SELECT * FROM settings WHERE id=1")->fetch_assoc();
$drivers = $conn->query("SELECT * FROM drivers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Juragan GI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        h2,
        h3 {
            color: #333;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }

        /* INPUT STYLE GOOGLE: FIXED SATU BARIS */
        .google-input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #dfe1e5;
            border-radius: 24px;
            box-sizing: border-box;
            font-family: monospace;
            font-size: 14px;
            outline: none;
            white-space: nowrap;
            /* Paksa teks satu baris */
            overflow-x: auto;
            /* Kalau panjang bisa di-scroll ke samping */
            background: #fff;
        }

        .google-input:focus {
            box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
            border-color: rgba(0, 0, 0, 0);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 24px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary {
            background: #1a73e8;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .btn-success {
            background: #28a745;
            color: white;
            border-radius: 8px;
        }

        .btn:hover {
            opacity: 0.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            color: #666;
            font-size: 13px;
        }

        code {
            background: #f1f1f1;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
            color: #d93025;
        }
    </style>
</head>

<body>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>DASHBOARD GI</h2>
            <a href="?logout=1" style="color:#1a73e8; text-decoration:none; font-weight:bold;">Sign Out</a>
        </div>

        <div class="card">
            <h3>API Configuration</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Google Maps Key:</label>
                    <input type="text" name="map_key" value="<?= $settings['map_key'] ?>" class="google-input">
                </div>

                <div class="form-group">
                    <label>Gojek Token (JWT):</label>
                    <input type="text" name="gojek_token" value="<?= $settings['gojek_token'] ?>" class="google-input">
                </div>

                <div class="form-group">
                    <label>Grab Token:</label>
                    <input type="text" name="grab_token" value="<?= $settings['grab_token'] ?>" class="google-input">
                </div>

                <button type="submit" name="update_config" class="btn btn-primary">SAVE ALL CHANGES</button>
            </form>
        </div>

        <div class="card">
            <h3>Add Driver Manual</h3>
            <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="nama" placeholder="Name" required style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="text" name="hwid" placeholder="HWID (ec8369...)" required style="flex: 2; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                <button type="submit" name="add_driver" class="btn btn-success">Add & Activate</button>
            </form>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="margin: 0;">
                <thead>
                    <tr>
                        <th>Driver Name</th>
                        <th>HWID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $drivers->fetch_assoc()): ?>
                        <tr>
                            <td><b><?= $row['nama'] ?></b></td>
                            <td><code><?= $row['hwid'] ?></code></td>
                            <td><span style="color: <?= $row['status'] == 'active' ? '#28a745' : '#d93025' ?>; font-weight:bold;"><?= strtoupper($row['status']) ?></span></td>
                            <td><a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus?')" style="color:#d93025; text-decoration:none; font-size: 13px;">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>