<?php
session_start();
include "config.php";

// Kalau sudah login, langsung lempar ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    // Ambil data admin dari database
    $query = $conn->query("SELECT * FROM users WHERE username='$user' AND password='$pass'");

    if ($query->num_rows > 0) {
        $_SESSION['admin'] = true;
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau Password Salah, Bang!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login Admin Mall</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: sans-serif;
            background: #1a1a1a;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: #333;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            width: 300px;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #00aa00;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #008800;
        }

        .error {
            color: #ff4444;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h3>Admin Mall GI</h3>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="user" placeholder="Username" required>
            <input type="password" name="pass" placeholder="Password" required>
            <button type="submit" name="login">MASUK JURAGAN</button>
        </form>
    </div>
</body>

</html>