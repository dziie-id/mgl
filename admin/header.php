<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker MGL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [data-bs-theme="dark"] body {
            background-color: #0b0c10;
        }

        [data-bs-theme="light"] body {
            background-color: #f4f6f9;
        }

        .sidebar {
            min-height: 100vh;
            transition: all 0.3s;
            background: var(--bs-body-bg);
            border-right: 1px solid var(--bs-border-color);
        }

        .nav-link {
            margin: 5px 15px;
            border-radius: 8px;
            padding: 10px 15px;
            color: var(--bs-body-color);
        }

        .nav-link:hover,
        .nav-link.active {
            background: #0d6efd;
            color: white !important;
        }

        @media (max-width: 768px) {
            .sidebar-desktop {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg border-bottom d-md-none sticky-top bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-brand fw-bold">STICKER MGL</span>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary theme-toggle">
                    <i class="fa-solid fa-moon-stars"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">STICKER MGL</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="nav flex-column mt-3">
                <?php include 'menu-list.php'; ?>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0 sidebar sidebar-desktop sticky-top">
                <div class="p-4 text-center border-bottom mb-3">
                    <h5 class="fw-bold mb-0">STICKER MGL</h5>
                </div>
                <div class="nav flex-column">
                    <?php include 'menu-list.php'; ?>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 px-0">
                <div class="navbar navbar-expand-lg border-bottom d-none d-md-flex px-4 py-3 bg-body-tertiary">
                    <span class="navbar-text fw-bold">Panel Administrasi</span>
                    <div class="ms-auto d-flex align-items-center">
                        <button class="btn btn-outline-secondary btn-sm me-3 theme-toggle" id="themeBtn">
                            <i class="fa-solid fa-sun me-1"></i> Mode
                        </button>
                        <span class="small text-muted me-3"><i class="fa-solid fa-circle-user me-1"></i> <?= $_SESSION['user_nama'] ?></span>
                    </div>
                </div>
                <div class="p-4">