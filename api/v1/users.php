<?php
include 'auth_check.php';
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method == 'POST' && $action == 'update') {
    $target_id = $_POST['user_id'];
    $new_pass  = $_POST['password'] ?? '';
    $new_nama  = $_POST['nama_lengkap'] ?? '';

    // Ambil data lama dulu
    $stmt_old = $pdo->prepare("SELECT nama_lengkap FROM users WHERE id = ?");
    $stmt_old->execute([$target_id]);
    $old_user = $stmt_old->fetch();

    // Jika input nama kosong, pakai nama lama
    $final_nama = (!empty($new_nama)) ? $new_nama : $old_user['nama_lengkap'];

    if (!empty($new_pass)) {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, password = ? WHERE id = ?");
        $stmt->execute([$final_nama, $hash, $target_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ? WHERE id = ?");
        $stmt->execute([$final_nama, $target_id]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Profil diperbarui']);
}
?>