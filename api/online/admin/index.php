<?php
include '../config.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard MGL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #121212;
            color: #e0e0e0;
        }

        .navbar {
            background: #1e1e1e;
            border-bottom: 1px solid #333;
        }

        .card {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .table {
            color: #e0e0e0;
        }

        .btn-success {
            background: #28a745;
        }

        .form-control,
        .form-select {
            background: #2b2b2b;
            border: 1px solid #444;
            color: white;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold"><i class="fas fa-microchip"></i> MGL AUTO PANEL</span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Keluar <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5><i class="fas fa-bolt text-warning"></i> Update Peluru</h5>
                    <form method="POST">
                        <select name="service_name" class="form-select mb-2">
                            <option value="gofood">Gojek Token</option>
                            <option value="grabfood">Grab Token</option>
                            <option value="maps_style">Google Map Style</option>
                        </select>
                        <textarea name="token_value" class="form-control mb-2" rows="3" placeholder="Paste di sini..."></textarea>
                        <button type="submit" name="update_cfg" class="btn btn-warning w-100 fw-bold">UPDATE TOKEN</button>
                    </form>
                </div>

                <div class="card p-3 shadow">
                    <h5><i class="fas fa-user-plus text-success"></i> Aktivasi Driver</h5>
                    <form method="POST">
                        <input type="text" name="hwid" class="form-control mb-2" placeholder="HWID Driver" required>
                        <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Driver">
                        <input type="date" name="exp" class="form-control mb-2" required>
                        <button type="submit" name="add_user" class="btn btn-success w-100 fw-bold">AKTIFKAN!</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-3 shadow">
                    <h5><i class="fas fa-users text-info"></i> Daftar Driver Aktif</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>HWID</th>
                                    <th>Expired</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                                while ($d = mysqli_fetch_assoc($q)) {
                                    echo "<tr>
                                    <td>{$d['nama_driver']}</td>
                                    <td><small class='text-secondary'>{$d['hwid']}</small></td>
                                    <td><span class='badge bg-dark text-info'>{$d['tgl_expired']}</span></td>
                                    <td class='text-center'>
                                        <a href='?hapus={$d['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Hapus driver ini?\")'><i class='fas fa-trash'></i></a>
                                    </td>
                                </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-secondary">
        <small>MGL Stiker Team © 2026 - Versi 2.0 Dark Mode</small>
    </footer>

</body>

</html>