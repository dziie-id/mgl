<?php
session_start();
session_destroy(); // Hapus semua jejak login
header("Location: login.php"); // Tendang balik ke halaman login
exit;
?>
