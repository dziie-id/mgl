<?php
session_start();
include "config.php";
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Logika simpan tetep sama biar gak ngerubah database
if (isset($_POST['update_config'])) {
    $map_key = $_POST['map_key'];
    $gojek = $_POST['gojek_token'];
    $grab = $_POST['grab_token'];
    $conn->query("UPDATE settings SET map_key='$map_key', gojek_token='$gojek', grab_token='$grab' WHERE id=1");
    echo "<script>alert('Data Updated!');</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin GI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: arial, sans-serif;
            background: #fff;
            color: #202124;
        }

        .container {
            max-width: 100%;
        }

        .section {
            margin-bottom: 30px;
            border-bottom: 1px solid #dfe1e5;
            padding-bottom: 20px;
        }

        h3 {
            font-size: 16px;
            font-weight: normal;
            color: #70757a;
            margin-bottom: 15px;
        }

        /* Gaya Input Google: Bersih & Minimalis */
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #dfe1e5;
            border-radius: 24px;
            /* Oval ala search bar Google */
            outline: none;
            box-sizing: border-box;
            margin-bottom: 15px;
            font-family: inherit;
        }

        input[type="text"]:focus,
        textarea:focus {
            box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
            border-color: rgba(223, 225, 229, 0);
        }

        textarea {
            border-radius: 12px;
            height: 60px;
        }

        /* Token panjang tetep masuk tanpa ngerusak layout */

        .btn-save {
            background: #1a73e8;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-save:hover {
            background: #1b66c9;
            box-shadow: 0 1px 3px rgba(60, 64, 67, 0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td,
        th {
            padding: 12px;
            border-bottom: 1px solid #ebebeb;
            text-align: left;
            font-size: 14px;
        }

        th {
            color: #70757a;
            font-weight: normal;
        }

        .hwid-code {
            color: #d93025;
            font-family: monospace;
        }
    </style>
</head>

<body>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;" class="section">
            <h2 style="font-size:22px; font-weight:normal;">Settings</h2>
            <a href="?logout=1" style="color:#1a73e8; text-decoration:none; font-size:14px;">Sign out</a>
        </div>

        <form method="POST">
            <div class="section">
                <h3>Global Configuration</h3>
                <input type="text" name="map_key" value="<?= $settings['map_key'] ?>" placeholder="Google Maps API Key">
                <input type="text" name="gojek_token" value="<?= $settings['gojek_token'] ?>" placeholder="Gojek Token (ey...)">
                <input type="text" name="grab_token" value="<?= $settings['grab_token'] ?>" placeholder="Grab Token">
                <button type="submit" name="update_config" class="btn-save">Save changes</button>
            </div>
        </form>

        <div class="section">
            <h3>Registered Drivers</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>HWID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $drivers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['nama'] ?></td>
                            <td class="hwid-code"><?= $row['hwid'] ?></td>
                            <td style="color: <?= $row['status'] == 'active' ? '#188038' : '#d93025' ?>;">
                                <?= $row['status'] ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>