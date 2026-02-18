<?php
session_start();
include "config.php";
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
    echo "<script>alert('Semua Data Berhasil Disimpan!');</script>";
}

// 2. Tambah Driver Manual (Input ID Berapa aja)
if (isset($_POST['add_driver'])) {
    $nama = $_POST['nama'];
    $hwid = $_POST['hwid'];
    $conn->query("INSERT INTO drivers (nama, hwid, status) VALUES ('$nama', '$hwid', 'active')");
}

$settings = $conn->query("SELECT * FROM settings WHERE id=1")->fetch_assoc();
$drivers = $conn->query("SELECT * FROM drivers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pusat Kendali GI</title>
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
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: monospace;
            font-size: 14px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
            width: 100%;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
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
        }

        code {
            background: #f1f1f1;
            padding: 3px 6px;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>DASHBOARD JURAGAN MALL</h2>
        <a href="?logout=1" style="color:red; text-decoration:none; font-weight:bold;">KELUAR</a>

        <div class="card">
            <h3>Setelan API & Token (Full Width)</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Google Maps API Key (Biar Peta Gak Blank):</label>
                    <input type="text" name="map_key" value="<?= $settings['map_key'] ?>" placeholder="AIzaSy...">
                </div>

                <div class="form-group">
                    <label>Token Gojek (JWT - ey...):</label>
                    <textarea name="gojek_token" placeholder="Paste Token Gojek Disini..."><?= $settings['gojek_token'] ?></textarea>
                </div>

                <div class="form-group">
                    <label>Token Grab:</label>
                    <textarea name="grab_token" placeholder="Paste Token Grab Disini..."><?= $settings['grab_token'] ?></textarea>
                </div>

                <button type="submit" name="update_config" class="btn btn-primary">SIMPAN SEMUA PERUBAHAN</button>
            </form>
        </div>

        <div class="card">
            <h3>Tambah ID Manual</h3>
            <form method="POST" style="display: flex; gap: 15px;">
                <input type="text" name="nama" placeholder="Nama Driver" required style="flex: 1;">
                <input type="text" name="hwid" placeholder="HWID (ec8369...)" required style="flex: 2;">
                <button type="submit" name="add_driver" class="btn btn-success" style="flex: 1;">TAMBAH & IJO-IN</button>
            </form>
        </div>

        <div class="card" style="padding: 0;">
            <table style="margin: 0;">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>HWID Driver</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $drivers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['nama'] ?></td>
                            <td><code><?= $row['hwid'] ?></code></td>
                            <td><b style="color: <?= $row['status'] == 'active' ? 'green' : 'red' ?>;"><?= strtoupper($row['status']) ?></b></td>
                            <td><a href="?hapus=<?= $row['id'] ?>" style="color:red; font-size: 12px;">Hapus</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>