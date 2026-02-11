<?php
include '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['update_pass'])) {
    $id = $_SESSION['user_id'];
    $pass_baru = $_POST['password'];
    $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $id]);

    $_SESSION['success'] = "Password berhasil diperbarui!";
    header("Location: profil.php");
    exit;
}

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-user-shield me-2"></i> Profil & Keamanan</h5>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-user fa-3x text-secondary"></i>
                    </div>
                    <h4 class="fw-bold mt-3"><?= $_SESSION['user_nama'] ?></h4>
                    <span class="badge bg-info text-dark text-uppercase"><?= $_SESSION['role'] ?></span>
                </div>

                <hr>

                <form method="POST">
                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Username</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['user_nama'] ?>" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold small mb-1">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password baru..." required minlength="5">
                        <div class="form-text text-muted">Minimal 5 karakter.</div>
                    </div>

                    <button type="submit" name="update_pass" class="btn btn-primary w-100 shadow" onclick="return confirm('Yakin ingin mengubah password?')">
                        <i class="fa-solid fa-save me-1"></i> Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>