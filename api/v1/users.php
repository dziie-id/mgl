<?php
include 'auth_check.php'; // Panggil pengaman API Key

// $user_api didapat dari auth_check.php (berisi data user yang sedang login)
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// --- 1. AMBIL DAFTAR USER (Hanya Admin) ---
if ($method == 'GET' && $action == 'list') {
    if ($user_api['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Hanya Admin yang bisa lihat daftar user']);
        exit;
    }

    $stmt = $pdo->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY role ASC");
    $users = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $users]);
}

// --- 2. TAMBAH USER BARU (Hanya Admin) ---
if ($method == 'POST' && $action == 'add') {
    if ($user_api['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
        exit;
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nama     = $_POST['nama_lengkap'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username & Password wajib diisi']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hash, $nama, $role]);
        echo json_encode(['status' => 'success', 'message' => 'User berhasil didaftarkan']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah terdaftar']);
    }
}

// --- 3. UPDATE / GANTI PASSWORD (Admin bisa semua, Staff cuma bisa diri sendiri) ---
if ($method == 'POST' && $action == 'update') {
    $target_id = $_POST['user_id'] ?? '';
    $new_nama  = $_POST['nama_lengkap'] ?? '';
    $new_pass  = $_POST['password'] ?? '';

    // Proteksi: Staff gak boleh ganti data orang lain
    if ($user_api['role'] !== 'admin' && $user_api['id'] != $target_id) {
        echo json_encode(['status' => 'error', 'message' => 'Anda hanya boleh mengubah profil sendiri']);
        exit;
    }

    if (!empty($new_pass)) {
        // Update dengan password baru
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, password = ? WHERE id = ?");
        $stmt->execute([$new_nama, $hash, $target_id]);
    } else {
        // Update nama saja
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ? WHERE id = ?");
        $stmt->execute([$new_nama, $target_id]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Data user diperbarui']);
}

// --- 4. HAPUS USER (Hanya Admin) ---
if ($method == 'POST' && $action == 'delete') {
    if ($user_api['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
        exit;
    }

    $target_id = $_POST['user_id'] ?? '';
    if ($target_id == $user_api['id']) {
        echo json_encode(['status' => 'error', 'message' => 'Gak bisa hapus diri sendiri bang!']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$target_id]);
    echo json_encode(['status' => 'success', 'message' => 'User berhasil dihapus']);
}