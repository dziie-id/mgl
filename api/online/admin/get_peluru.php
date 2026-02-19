<?php
include '../config.php';
// Cek session biar gak sembarang orang bisa intip peluru
if (!isset($_SESSION['login'])) { exit; }

$service = isset($_GET['service']) ? aman($_GET['service']) : '';

if ($service != '') {
    $q = mysqli_query($conn, "SELECT token_value FROM app_config WHERE service_name = '$service'");
    if (mysqli_num_rows($q) > 0) {
        $d = mysqli_fetch_assoc($q);
        // Kirim datanya aja tanpa tag HTML apapun
        echo $d['token_value'];
    }
}
?>
