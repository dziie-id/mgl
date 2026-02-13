<?php
// Tampilkan error biar gak menebak-nebak
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'auth_check.php'; // Panggil pengaman

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    // --- 1. AMBIL DAFTAR USER ---
    if ($method == 'GET' && $action == 'list') {
        if ($user_api['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Hanya Admin yang bisa lihat daftar user']);
            exit;
        }

        // Ambil data (Saya hapus created_at sementara buat jaga-jaga kalau abang belum eksekusi SQL)
        $stmt = $pdo->query("SELECT id, username, nama_lengkap, role FROM users ORDER BY role ASC");
        $users = $stmt->fetchAll();
        
        echo json_encode(['status' => 'success', 'data' => $users]);
    }

    // --- 2. TAMBAH USER BARU ---
    if ($method == 'POST' && $action == 'add') {
        if ($user_api['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']); exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $nama     = $_POST['nama_lengkap'] ?? '';
        $role     = $_POST['role'] ?? 'staff';

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Lengkapi data bang']); exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hash, $nama, $role]);
        
        echo json_encode(['status' => 'success', 'message' => 'User berhasil didaftarkan']);
    }

    // --- 3. UPDATE USER ---
    if ($method == 'POST' && $action == 'update') {
        $target_id = $_POST['user_id'] ?? '';
        $new_nama  = $_POST['nama_lengkap'] ?? '';
        $new_pass  = $_POST['password'] ?? '';

        if ($user_api['role'] !== 'admin' && $user_api['id'] != $target_id) {
            echo json_encode(['status' => 'error', 'message' => 'Hanya boleh ubah profil sendiri']); exit;
        }

        if (!empty($new_pass)) {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, password = ? WHERE id = ?");
            $stmt->execute([$new_nama, $hash, $target_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ? WHERE id = ?");
            $stmt->execute([$new_nama, $target_id]);
        }
        echo json_encode(['status' => 'success', 'message' => 'Data diperbarui']);
    }

    // --- 4. HAPUS USER ---
    if ($method == 'POST' && $action == 'delete') {
        if ($user_api['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']); exit;
        }

        $target_id = $_POST['user_id'] ?? '';
        if ($target_id == $user_api['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Gak bisa hapus diri sendiri']); exit;
        }

        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$target_id]);
        echo json_encode(['status' => 'success', 'message' => 'User dihapus']);
    }

} catch (PDOException $e) {
    // Kalau ada error database, dia bakal ngasih tau kolom mana yang bermasalah
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}