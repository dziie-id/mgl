<?php
header("Content-Type: application/json");

require "config.php";
require "response.php";

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
        response(false, "Module not found");
}
