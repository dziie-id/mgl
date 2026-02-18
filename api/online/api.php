<?php
include "config.php";
$hwid = $_GET['hwid'] ?? '';

$getSettings = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();
$checkDriver = $conn->query("SELECT status FROM drivers WHERE hwid = '$hwid'")->fetch_assoc();

$status = $checkDriver ? $checkDriver['status'] : "inactive";

echo json_encode([
    "status" => $status,
    "map_key" => $getSettings['map_key'],
    "gojek_token" => $getSettings['gojek_token'],
    "grab_token" => $getSettings['grab_token']
]);
