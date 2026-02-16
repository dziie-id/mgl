<?php
header("Content-Type: application/json");
require_once "../includes/db.php";

function response($status, $message = "", $data = null) {
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

$module = $_GET['module'] ?? '';
$action = $_GET['action'] ?? '';
$token  = $_GET['token'] ?? '';

// LOGIN gak perlu token
if (!($module == "user" && $action == "login")) {

    if (empty($token)) {
        response(false, "Token diperlukan");
    }

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE api_token='$token'");
    if (mysqli_num_rows($cek) == 0) {
        response(false, "Token tidak valid");
    }
}

switch ($module) {

    case "dashboard":
        require "dashboard.php";
        break;

    case "survey":
        require "survey.php";
        break;

    case "gallery":
        require "gallery.php";
        break;

    case "user":
        require "user.php";
        break;

    default:
        response(false, "Module tidak ditemukan");
}
