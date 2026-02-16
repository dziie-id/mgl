<?php

if ($action == "summary") {

    $data = [];

    $data['total_survey'] = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) as total FROM surveys")
    )['total'];

    $data['total_foto'] = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) as total FROM gallery")
    )['total'];

    $data['total_user'] = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) as total FROM users")
    )['total'];

    $data['total_item'] = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) as total FROM survey_items")
    )['total'];

    response(true, "Dashboard loaded", $data);
}

response(false, "Action tidak ditemukan");
