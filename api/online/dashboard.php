<?php
session_start();
include "config.php";
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// 1. Simpan Setting (Token & Maps)
if (isset($_POST['update_config'])) {
    $map_key = $_POST['map_key'];
    $gojek = $_POST['gojek_token'];
    $grab = $_POST['grab_token'];
    $conn->query("UPDATE settings SET map_key='$map_key', gojek_token='$gojek', grab_token='$grab' WHERE id=1");
    echo "<script>alert('Setting Tersimpan!');</script>";
}

// 2. Tambah Driver Manual
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
    <title>Dashboard Juragan GI</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            height: 80px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .btn-save {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>Panel Control Grand Indonesia</h2>

    <div class="card">
        <h3>Settings Global (Maps & API)</h3>
        <form method="POST">
            <label><b>Google Maps API Key:</b></label>
            <input type="text" name="map_key" value="<?= $settings['map_key'] ?>">

            <label><br>Token Gojek (ey...):</b></label>
            <textarea name="gojek_token"><?= $settings['gojek_token'] ?></textarea>

            <label><br>Token Grab:</b></label>
            <textarea name="grab_token"><?= $settings['grab_token'] ?></textarea>

            <button type="submit" name="update_config" class="btn-save">Simpan Semua Setting</button>
        </form>
    </div>

    <div class="card">
        <h3>Tambah Driver Manual</h3>
        <form method="POST" style="display: flex; gap: 10px;">
            <input type="text" name="nama" placeholder="Nama Driver" required>
            <input type="text" name="hwid" placeholder="HWID (Contoh: ec8369...)" required>
            <button type="submit" name="add_driver" class="btn-save" style="margin: 0;">Tambah & Aktifkan</button>
        </form>
    </div>

    <div class="card">
        <h3>Daftar Driver Aktif</h3>
        <table>
            <tr style="background: #f8f9fa;">
                <th>Nama</th>
                <th>HWID</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $drivers->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['nama'] ?></td>
                    <td><code><?= $row['hwid'] ?></code></td>
                    <td><b style="color: <?= $row['status'] == 'active' ? 'green' : 'red' ?>;"><?= strtoupper($row['status']) ?></b></td>
                    <td><a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus Driver?')" style="color:red;">Hapus</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>