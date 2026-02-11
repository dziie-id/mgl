<?php
include '../includes/db.php';

$passwordBaru = 'admin123';
$hashBaru = password_hash($passwordBaru, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
if ($stmt->execute([$hashBaru])) {
    echo "Password admin berhasil direset menjadi: <b>admin123</b><br>";
    echo "Silakan hapus file ini dan coba login kembali.";
} else {
    echo "Gagal mereset password.";
}
?>