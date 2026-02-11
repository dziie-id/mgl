<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pass, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_nama'] = $admin['nama_lengkap'];
        $_SESSION['role'] = $admin['role'];

        $_SESSION['success'] = "Selamat datang kembali, " . $admin['nama_lengkap'];
        header("Location: index.php");
    } else {
        header("Location: login.php?error=1");
    }
}
