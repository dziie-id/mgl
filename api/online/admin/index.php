<?php
include '../config.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

if (isset($_POST['add_driver'])) {
    $hwid = $_POST['hwid'];
    $nama = $_POST['nama'];
    $expired = $_POST['expired'];
    mysqli_query($conn, "INSERT INTO users (hwid, nama_driver, status_aktif, tgl_expired) VALUES ('$hwid', '$nama', '1', '$expired')");
}

if (isset($_POST['update_token'])) {
    $token = $_POST['token_value'];
    mysqli_query($conn, "UPDATE app_config SET token_value='$token' WHERE service_name='gofood'");
}
?>

<h3>Panel Kendali</h3>
<a href="logout.php">Keluar</a>

<hr>
<h4>Update Peluru (Token/Maps)</h4>
<form method="POST">
    <select name="service_name">
        <option value="gofood">Token Gojek</option>
        <option value="grabfood">Token Grab</option>
        <option value="maps_style">Style Peta (Google)</option>
    </select><br>
    <textarea name="token_value" rows="5" cols="50" placeholder="Paste peluru di sini..."></textarea><br>
    <button type="submit" name="update_config">Update Sekarang</button>
</form>

<?php
if (isset($_POST['update_config'])) {
    $name = $_POST['service_name'];
    $val  = $_POST['token_value'];

    // Cek dulu apakah row sudah ada
    $check = mysqli_query($conn, "SELECT * FROM app_config WHERE service_name='$name'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE app_config SET token_value='$val' WHERE service_name='$name'");
    } else {
        mysqli_query($conn, "INSERT INTO app_config (service_name, token_value) VALUES ('$name', '$val')");
    }
    echo "Peluru $name berhasil diupdate!";
}
?>

<hr>
<h4>Tambah Driver Baru</h4>
<form method="POST">
    <input type="text" name="hwid" placeholder="HWID HP Driver" required>
    <input type="text" name="nama" placeholder="Nama Driver">
    <input type="date" name="expired" required>
    <button type="submit" name="add_driver">Aktifkan Driver</button>
</form>