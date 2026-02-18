<?php
session_start();
include "config.php";
if (!$_SESSION['admin']) {
    header("Location: login.php");
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Logika Simpan Token & API Key
if (isset($_POST['update_config'])) {
    $map_key = $_POST['map_key'];
    $token = $_POST['gojek_token'];
    $conn->query("UPDATE settings SET map_key='$map_key', gojek_token='$token' WHERE id=1");
}

// Logika Aktifkan Driver (Ijo-in HWID)
if (isset($_GET['aktifkan'])) {
    $id = $_GET['aktifkan'];
    $conn->query("UPDATE drivers SET status='active' WHERE id=$id");
}

$settings = $conn->query("SELECT * FROM settings WHERE id=1")->fetch_assoc();
$drivers = $conn->query("SELECT * FROM drivers");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Mall GI</title>
</head>

<body style="font-family:sans-serif; padding:20px; background:#f4f4f4;">
    <h2>Pusat Kendali Grand Indonesia</h2>

    <div style="background:white; padding:15px; border-radius:10px; margin-bottom:20px;">
        <h3>Setelan Utama (Global)</h3>
        <form method="POST">
            <label>Google Map API Key (Biar Gak Blank):</label><br>
            <input type="text" name="map_key" value="<?= $settings['map_key'] ?>" style="width:100%"><br><br>

            <label>Token Gojek (Yang awalan ey...):</label><br>
            <textarea name="gojek_token" style="width:100%; height:100px;"><?= $settings['gojek_token'] ?></textarea><br><br>

            <button name="update_config" style="padding:10px 20px; background:green; color:white;">Simpan Perubahan</button>
        </form>
    </div>

    <div style="background:white; padding:15px; border-radius:10px;">
        <h3>Daftar Driver (HWID)</h3>
        <table border="1" width="100%" cellpadding="10" style="border-collapse:collapse;">
            <tr style="background:#eee;">
                <th>Nama</th>
                <th>HWID</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php while ($d = $drivers->fetch_assoc()): ?>
                <tr>
                    <td><?= $d['nama'] ?></td>
                    <td><code><?= $d['hwid'] ?></code></td>
                    <td style="color: <?= $d['status'] == 'active' ? 'green' : 'red' ?>; font-weight:bold;">
                        <?= $d['status'] ?>
                    </td>
                    <td>
                        <a href="?aktifkan=<?= $d['id'] ?>">Aktifkan (Ijo-in)</a> |
                        <a href="?hapus=<?= $d['id'] ?>" style="color:red;">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <a href="?logout=1" style="float:right; color:red; text-decoration:none; font-weight:bold;">LOGOUT</a>
</body>

</html>