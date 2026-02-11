<?php
include '../includes/db.php';
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

if (isset($_POST['add_user'])) {
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['username'], $pass, $_POST['nama'], $_POST['role']]);
    echo "<script>window.location.href='user-manager.php';</script>";
    $_SESSION['success'] = "User baru berhasil didaftarkan!";
    exit;
}

if (isset($_POST['change_pass'])) {
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$new_pass, $_POST['user_id']]);
    echo "<script>window.location.href='user-manager.php';</script>";
    $_SESSION['success'] = "Password user berhasil diubah!";
    exit;
}

if (isset($_GET['delete'])) {
    if ($_GET['delete'] != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        echo "<script>window.location.href='user-manager.php';</script>";
        $_SESSION['success'] = "User telah dihapus selamanya!";
    }
    header("Location: user-manager.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY role ASC")->fetchAll();
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent fw-bold">Tambah User Baru</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Role</label>
                        <select name="role" class="form-select">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary w-100">Simpan User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th>User / Nama</th>
                            <th>Role</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= $u['username'] ?></div>
                                    <div class="small text-muted"><?= $u['nama_lengkap'] ?></div>
                                </td>
                                <td><span class="badge <?= $u['role'] == 'admin' ? 'bg-danger' : 'bg-info' ?>"><?= $u['role'] ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#passModal<?= $u['id'] ?>">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?= $u['id'] ?>"
                                            class="btn btn-sm btn-outline-danger btn-hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <div class="modal fade" id="passModal<?= $u['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-body">
                                                <h6 class="fw-bold mb-3">Ganti Password: <?= $u['username'] ?></h6>
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
                                            </div>
                                            <div class="modal-footer p-1 border-0">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="change_pass" class="btn btn-sm btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>