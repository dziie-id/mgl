<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login MGL - Melek Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0a0a0a; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
        .card { background: #161616; border: 1px solid #333; border-radius: 20px; width: 100%; max-width: 400px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.8); }
        .text-info { color: #00d2ff !important; font-weight: bold; }
        label { color: #ffffff !important; font-weight: 600; margin-bottom: 8px; display: block; }
        .form-control { background: #222 !important; border: 1px solid #444 !important; color: white !important; padding: 12px; border-radius: 10px; }
        .form-control:focus { border-color: #00d2ff !important; box-shadow: 0 0 10px rgba(0, 210, 255, 0.3) !important; }
        .btn-primary { background: #007bff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; transition: 0.3s; }
        .btn-primary:hover { background: #0056b3; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="card">
        <h3 class="text-center text-info mb-4">MGL PANEL</h3>
        <?php
        if (isset($_POST['login'])) {
            $user = aman($_POST['username']);
            $pass = $_POST['password'];
            $res = mysqli_query($conn, "SELECT * FROM admin_users WHERE username='$user'");
            if (mysqli_num_rows($res) > 0) {
                $data = mysqli_fetch_assoc($res);
                if ($pass == $data['password']) {
                    $_SESSION['login'] = true;
                    echo "<script>window.location='index.php';</script>";
                } else { echo "<div class='alert alert-danger py-2 text-center'>Sandi Salah!</div>"; }
            } else { echo "<div class='alert alert-danger py-2 text-center'>User Gak Ada!</div>"; }
        }
        ?>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan ID..." required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Sandi..." required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">MASUK BANG!</button>
        </form>
    </div>
</body>
</html>
