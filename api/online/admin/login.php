<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Markas Gacor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #121212;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }

        .btn-primary {
            background: #007bff;
            border: none;
        }

        .form-control {
            background: #2b2b2b;
            border: 1px solid #444;
            color: white;
        }

        .form-control:focus {
            background: #333;
            color: white;
            border-color: #007bff;
            box-shadow: none;
        }
    </style>
</head>

<body>
    <div class="card shadow-lg">
        <h3 class="text-center mb-4">MGL PANEL</h3>
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
                } else {
                    echo "<div class='alert alert-danger'>Password Salah!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>User Tak Dikenal!</div>";
            }
        }
        ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Siapa lu?" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Kata sandi..." required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">MASUK BANG!</button>
        </form>
    </div>
</body>

</html>