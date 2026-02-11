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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sticker MGL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: #0a0a0a; /* Hitam pekat biar sangar */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* Efek cahaya di background biar gak sepi */
        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: #007bff;
            filter: blur(150px);
            opacity: 0.2;
            top: 10%;
            left: 10%;
        }

        .login-card {
            width: 90%;
            max-width: 400px;
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.05); /* Transparan gelap */
            backdrop-filter: blur(10px); /* Efek kaca */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .login-card h3 {
            color: #fff;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .form-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 10px;
            padding: 12px;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #007bff;
            color: #fff;
            box-shadow: none;
        }

        .btn-login {
            background: #007bff;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        .alert {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff858d;
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h3 class="mb-1">STICKER<span class="text-primary">MGL</span></h3>
            <p class="text-secondary small">Panel Kendali Admin</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> Username/Password Salah!
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <input type="text" name="user" class="form-control" placeholder="Masukkan username..." required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="pass" class="form-control" placeholder="Masukkan password..." required>
            </div>
            <button type="submit" class="btn btn-primary btn-login w-100">
                Masuk Sekarang <i class="fas fa-sign-in-alt ms-2"></i>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Alert Logout dari file asli lu
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'logout'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Logout Berhasil',
                text: 'Sampai jumpa kembali, Admin!',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#007bff'
            });
        <?php endif; ?>
    </script>
</body>
</html>
