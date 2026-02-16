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
