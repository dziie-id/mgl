<?php
// Matikan error reporting agar tidak merusak format JSON
error_reporting(0); 

include 'auth_check.php'; // Panggil satpam API (X-API-KEY)

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    // --- 1. AMBIL DAFTAR USER (Hanya Admin) ---
    if ($method == 'GET' && $action == 'list') {
        if ($user_api['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: Hanya Admin yang bisa melihat tim.']);
            exit;
        }

        // Ambil semua data kecuali password
        $stmt = $pdo->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY role ASC");
        $users = $stmt->fetchAll();
        
        echo json_encode([
            'status' => 'success', 
            'total' => count($users),
            'data' => $users
        ]);
    }

    // --- 2. TAMBAH USER BARU (Hanya Admin) ---
    if ($method == 'POST' && $action == 'add') {
        if ($user_api['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']); exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $nama     = $_POST['nama_lengkap'] ?? '';
        $role     = $_POST['role'] ?? 'staff';

        if (empty($username) || empty($password) || empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Lengkapi data: Username, Nama, & Password wajib diisi.']); 
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hash, $nama, $role]);
        
        echo json_encode(['status' => 'success', 'message' => 'User ' . $nama . ' berhasil didaftarkan!']);
    }

    // --- 3. UPDATE USER / GANTI PASSWORD ---
    if ($method == 'POST' && $action == 'update') {
        $target_id = $_POST['user_id'] ?? '';
        $new_nama  = $_POST['nama_lengkap'] ?? '';
        $new_pass  = $_POST['password'] ?? '';

        // PROTEKSI: Jika bukan admin, pastikan dia cuma ganti profilnya sendiri
        if ($user_api['role'] !== 'admin' && $user_api['id'] != $target_id) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: Anda hanya boleh mengubah akun sendiri.']);
            exit;
        }

        // AMBIL DATA LAMA BIAR NAMA GAK HILANG
        $stmt_old = $pdo->prepare("SELECT nama_lengkap FROM users WHERE id = ?");
        $stmt_old->execute([$target_id]);
        $old_user = $stmt_old->fetch();

        if (!$old_user) {
            echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan.']); exit;
        }

        // Jika input nama kosong, pakai nama yang lama di database
        $final_nama = (!empty($new_nama)) ? $new_nama : $old_user['nama_lengkap'];

        if (!empty($new_pass)) {
            // Update Nama & Password baru
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, password = ? WHERE id = ?");
            $stmt->execute([$final_nama, $hash, $target_id]);
        } else {
            // Update Nama saja
            $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ? WHERE id = ?");
            $stmt->execute([$final_nama, $target_id]);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Data user berhasil diperbarui!']);
    }

    // --- 4. HAPUS USER (Hanya Admin) ---
    if ($method == 'POST' && $action == 'delete') {
        if ($user_api['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Hanya admin yang bisa menghapus user.']); exit;
        }

        $target_id = $_POST['user_id'] ?? '';
        
        // Gak boleh hapus diri sendiri pas login
        if ($target_id == $user_api['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Gak bisa hapus akun sendiri pas lagi dipake bang!']);
            exit;
        }

        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$target_id]);
        echo json_encode(['status' => 'success', 'message' => 'User berhasil dihapus selamanya.']);
    }

} catch (PDOException $e) {
    // Tangkap error database (misal: kolom kurang)
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan Database: ' . $e->getMessage()]);
}
