<?php
$host = "localhost";
$user = "mgld3919_online";
$pass = "C@cink3110";
$db   = "mgld3919_online";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi Gagal!");
}
