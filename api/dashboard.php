<?php

if ($action == "summary") {

    $data = [];

    $data['total_survey'] = $conn->query("SELECT COUNT(*) as total FROM surveys")
        ->fetch_assoc()['total'];

    $data['total_foto'] = $conn->query("SELECT COUNT(*) as total FROM gallery")
        ->fetch_assoc()['total'];

    $data['total_user'] = $conn->query("SELECT COUNT(*) as total FROM users")
        ->fetch_assoc()['total'];

    $data['total_item'] = $conn->query("SELECT COUNT(*) as total FROM survey_items")
        ->fetch_assoc()['total'];

    response(true, "Dashboard loaded", $data);
}

response(false, "Action not found");
