<?php
include '../config.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM admin_users WHERE username='$user'");
    if (mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
        if ($pass == $data['password']) {
            $_SESSION['login'] = true;
            header("Location: index.php");
        }
    }
    $error = "Login Gagal, Bang!";
}
?>

<form method="POST">
    <h2>Login</h2>
    <?php if (isset($error)) echo $error; ?>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Masuk</button>
</form>