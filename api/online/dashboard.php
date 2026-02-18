<?php
session_start();
include "config.php";
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Logika Simpan
if (isset($_POST['update_config'])) {
    $map_key = $_POST['map_key'];
    $gojek = $_POST['gojek_token'];
    $grab = $_POST['grab_token'];
    $conn->query("UPDATE settings SET map_key='$map_key', gojek_token='$gojek', grab_token='$grab' WHERE id=1");
    echo "<script>alert('Updated!'); window.location='dashboard.php';</script>";
}
// Hapus Driver
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
    <title>Admin GI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 10px;
            background: #fff;
        }

        .wrapper {
            width: 100%;
            box-sizing: border-box;
        }

        /* Tabel Input: Biar Sejajar & Gak Numpuk */
        .table-input {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-input td {
            padding: 10px;
            border: 1px solid #eee;
        }

        .label {
            background: #f8f9fa;
            width: 150px;
            font-weight: bold;
            font-size: 13px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-family: monospace;
        }

        .btn-save {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 15px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
        }

        /* Tabel Driver */
        .table-driver {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
        }

        .table-driver th {
            background: #333;
            color: #fff;
            padding: 10px;
            text-align: left;
        }

        .table-driver td {
            padding: 10px;
            border: 1px solid #eee;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-off {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <b>PANEL SETTING</b>
            <a href="?logout=1" style="color:red; font-size:12px;">LOGOUT</a>
        </div>

        <form method="POST">
            <table class="table-input">
                <tr>
                    <td class="label">MAPS KEY</td>
                    <td><input type="text" name="map_key" value="<?= $settings['map_key'] ?>"></td>
                </tr>
                <tr>
                    <td class="label">TOKEN GOJEK</td>
                    <td><input type="text" name="gojek_token" value="<?= $settings['gojek_token'] ?>"></td>
                </tr>
                <tr>
                    <td class="label">TOKEN GRAB</td>
                    <td><input type="text" name="grab_token" value="<?= $settings['grab_token'] ?>"></td>
                </tr>
            </table>
            <button type="submit" name="update_config" class="btn-save">SIMPAN SEMUA DATA</button>
        </form>

        <hr>

        <b>DAFTAR DRIVER</b>
        <table class="table-driver">
            <thead>
                <tr>
                    <th>NAMA</th>
                    <th>HWID</th>
                    <th>STATUS</th>
                    <th>OPSI</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $drivers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><small><code><?= $row['hwid'] ?></code></small></td>
                        <td class="<?= $row['status'] == 'active' ? 'status-active' : 'status-off' ?>">
                            <?= strtoupper($row['status']) ?>
                        </td>
                        <td><a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus?')" style="color:red;">Hapus</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>