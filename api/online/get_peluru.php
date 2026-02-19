<?php
include '../config.php';
if (!isset($_SESSION['login'])) { exit; }

$service = isset($_GET['service']) ? aman($_GET['service']) : '';

if ($service != '') {
    $q = mysqli_query($conn, "SELECT token_value FROM app_config WHERE service_name = '$service'");
    if (mysqli_num_rows($q) > 0) {
        $d = mysqli_fetch_assoc($q);
        echo $d['token_value'];
    }
}
?>
