<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin - Sticker MGL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #000000;
            display: flex;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: #fff;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h3 class="text-center mb-4">Admin Sticker MGL</h3>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Username atau Password salah!</div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="user" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="pass" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login Sekarang</button>
        </form>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'logout'): ?>
        Swal.fire('Logout Berhasil', 'Terima kasih, kerja bagus hari ini!', 'success');
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        Swal.fire('Gagal Login', 'Username atau Password salah bang!', 'error');
    <?php endif; ?>
</script>

</html>