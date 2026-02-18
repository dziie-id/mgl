<?php
session_start();
include "config.php";
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// ... logika simpan tetap sama ...
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pusat Kendali GI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* RESET CSS: BIAR GAK ADA SELA SAMA SEKALI */
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
        }

        /* HEADER FULL WIDTH */
        .header {
            background: #333;
            color: white;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        /* CONTAINER DIBIKIN 100% BIAR SAMA KAYA MAP */
        .full-container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* CARD TANPA MARGIN SAMPING */
        .card {
            background: white;
            padding: 30px;
            margin-bottom: 2px;
            /* Sela tipis antar section */
            width: 100%;
            box-sizing: border-box;
            border-bottom: 1px solid #ddd;
        }

        h3 {
            margin-top: 0;
            color: #444;
            border-left: 5px solid #007bff;
            padding-left: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #666;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background: #fafafa;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        textarea {
            height: 120px;
        }

        .btn-blue {
            background: #007bff;
            color: white;
            border: none;
            padding: 20px;
            width: 100%;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-blue:hover {
            background: #0056b3;
        }

        /* TABEL DRIVER JUGA FULL WIDTH */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #eee;
        }

        code {
            background: #fff3cd;
            padding: 5px;
            border-radius: 3px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2 style="margin:0;">JURAGAN MALL - PUSAT KENDALI</h2>
        <a href="?logout=1" style="color:#ff4444; text-decoration:none;">LOGOUT</a>
    </div>

    <div class="full-container">
        <form method="POST">
            <div class="card">
                <h3>SETELAN GLOBAL (Google Maps & API)</h3>
                <div class="form-group">
                    <label>Google Maps API Key:</label>
                    <input type="text" name="map_key" value="<?= $settings['map_key'] ?>" placeholder="Masukkan API Key Google Maps...">
                </div>

                <div class="form-group">
                    <label>Token Gojek (JWT):</label>
                    <textarea name="gojek_token" placeholder="Paste Token Gojek..."><?= $settings['gojek_token'] ?></textarea>
                </div>

                <div class="form-group">
                    <label>Token Grab:</label>
                    <textarea name="grab_token" placeholder="Paste Token Grab..."><?= $settings['grab_token'] ?></textarea>
                </div>

                <button type="submit" name="update_config" class="btn-blue">SIMPAN SEMUA PERUBAHAN</button>
            </div>
        </form>

        <div class="card" style="background: #e9ecef;">
            <h3>TAMBAH ID MANUAL</h3>
            <form method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                <input type="text" name="nama" placeholder="Nama Driver" required>
                <input type="text" name="hwid" placeholder="HWID (Contoh: ec8369...)" required>
                <button type="submit" name="add_driver" style="padding: 15px; background: #28a745; color: white; border: none; font-weight: bold; cursor:pointer;">AKTIFKAN DRIVER</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>DRIVER</th>
                        <th>HWID</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $drivers->fetch_assoc()): ?>
                        <tr>
                            <td><b><?= $row['nama'] ?></b></td>
                            <td><code><?= $row['hwid'] ?></code></td>
                            <td><span style="color: <?= $row['status'] == 'active' ? 'green' : 'red' ?>; font-weight:bold;"><?= strtoupper($row['status']) ?></span></td>
                            <td><a href="?hapus=<?= $row['id'] ?>" style="color:red;">Hapus</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>