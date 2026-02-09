<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - EasyCart' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/adminauth.css">
    <?php if (isset($extra_css)): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>public/css/<?= $extra_css ?>">
    <?php endif; ?>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <a href="<?= BASE_URL ?>admin" style="text-decoration: none; color: inherit;">
                    <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 800; letter-spacing: -0.5px; margin: 0;">EasyCart</h2>
                </a>
            </div>
            
            <nav class="admin-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <a href="<?= BASE_URL ?>admin" class="nav-link <?= ($page ?? '') === 'admin' ? 'active' : '' ?>">
                        <i class="fas fa-th-large"></i>
                        <span>Overview</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Products</div>
                    <a href="<?= BASE_URL ?>admin/import" class="nav-link <?= ($page ?? '') === 'import' ? 'active' : '' ?>">
                        <i class="fas fa-file-import"></i>
                        <span>Import Products</span>
                    </a>
                    <a href="<?= BASE_URL ?>admin/export" class="nav-link <?= ($page ?? '') === 'export' ? 'active' : '' ?>">
                        <i class="fas fa-file-export"></i>
                        <span>Export Products</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Quick Links</div>
                    <a href="<?= BASE_URL ?>admin/products" class="nav-link <?= ($page ?? '') === 'products' ? 'active' : '' ?>">
                        <i class="fas fa-box-open"></i>
                        <span>Products</span>
                    </a>
                    <a href="<?= BASE_URL ?>admin/orders" class="nav-link <?= ($page ?? '') === 'orders' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="<?= BASE_URL ?>admin/createadmin" class="nav-link <?= ($page ?? '') === 'createadmin' ? 'active' : '' ?>">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Admin</span>
                    </a>
                    <a href="<?= BASE_URL ?>" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Back to Store</span>
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <h1><?= $page_title ?? 'Dashboard' ?></h1>
                </div>
                <div class="topbar-right">
                    <a href="<?= BASE_URL ?>" class="topbar-btn" title="View Store">
                        <i class="fas fa-store"></i>
                    </a>
                    <a href="<?= BASE_URL ?>admin/profile" class="topbar-btn" title="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </header>
            
            <!-- Content -->
            <div class="admin-content">
                <!-- Flash Messages -->
                <?php $flash = getFlash(); if ($flash): ?>
                    <div class="flash-container">
                        <div class="flash-message <?= $flash['type'] ?>">
                            <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?>"></i>
                            <span><?= htmlspecialchars($flash['message']) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
