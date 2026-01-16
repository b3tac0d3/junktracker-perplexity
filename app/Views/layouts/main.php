<?php
$baseUrl = rtrim($this->config['app']['base_url'], '/');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'JunkTracker') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap 5 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custom styles AFTER Bootstrap -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/app.css?v=2">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/sales.css?v=2">
</head>
<body class="bg-light">
<!-- <nav class="navbar navbar-dark bg-dark navbar-expand-lg mb-3">
    <div class="container main-content">
        <a class="navbar-brand" href="<?= $baseUrl ?>/dashboard">JunkTracker</a>
        <form class="d-flex ms-auto me-3" method="get" action="<?= $baseUrl ?>/search">
            <input class="form-control form-control-sm" type="search" name="q" placeholder="Search clients or jobs..." aria-label="Search">
        </form>
        <?php if (!empty($user)): ?>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <?= htmlspecialchars($user['first_name'] ?? 'User') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= $baseUrl ?>/logout">Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a class="btn btn-sm btn-outline-light" href="<?= $baseUrl ?>/login">Login</a>
        <?php endif; ?>
    </div>
</nav> -->
<nav class="navbar navbar-dark bg-dark navbar-expand-lg mb-3">
    <div class="<div class="main-content">">
        <a class="navbar-brand" href="<?= $baseUrl ?>/dashboard">JunkTracker</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <?php if (!empty($user)): ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/clients">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/jobs">Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/sales">Sales</a>
                    </li>
                    <?php if (isset($user['role']) && (int)$user['role'] >= (int)$this->config['app']['admin_role_min']): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/admin/users">Users</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
            <?php endif; ?>

            <form class="d-flex me-3" method="get" action="<?= $baseUrl ?>/search">
                <input class="form-control form-control-sm" type="search" name="q" placeholder="Search clients or jobs..." aria-label="Search">
            </form>

            <?php if (!empty($user)): ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($user['first_name'] ?? 'User') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>/logout">Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="btn btn-sm btn-outline-light" href="<?= $baseUrl ?>/login">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="main-content">
    <?php include $viewFile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $baseUrl ?>/assets/js/app.js"></script>
</body>
</html>
