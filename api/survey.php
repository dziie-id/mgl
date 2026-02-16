<?php

if ($action == "create") {

    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $koordinat = $_POST['koordinat'];

    $stmt = $conn->prepare("INSERT INTO surveys (nama, alamat, koordinat) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $alamat, $koordinat);
    $stmt->execute();

    response(true, "Survey created");
}

if ($action == "list") {

    $result = $conn->query("SELECT * FROM surveys ORDER BY id DESC");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    response(true, "Survey list", $data);
}

response(false, "Action not found");
