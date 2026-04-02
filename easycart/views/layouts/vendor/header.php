<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Vendor - EasyCart' ?></title>
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
                <a href="<?= BASE_URL ?>vendor/dashboard" style="text-decoration: none; color: inherit;">
                    <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 800; letter-spacing: -0.5px; margin: 0;">EasyCart</h2>
                </a>
            </div>
            
            <nav class="admin-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Vendor Central</div>
                    <a href="<?= BASE_URL ?>vendor/dashboard" class="nav-link <?= ($page ?? '') === 'vendor_dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">My Store</div>
                    <a href="<?= BASE_URL ?>vendor/products" class="nav-link <?= ($page ?? '') === 'vendor_products' ? 'active' : '' ?>">
                        <i class="fas fa-box-open"></i>
                        <span>Products</span>
                    </a>
                    <a href="<?= BASE_URL ?>vendor/orders" class="nav-link <?= ($page ?? '') === 'vendor_orders' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                    <a href="<?= BASE_URL ?>vendor/coupons" class="nav-link <?= ($page ?? '') === 'vendor_coupons' ? 'active' : '' ?>">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Coupons</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="<?= BASE_URL ?>vendor/logout" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    <a href="<?= BASE_URL ?>" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Back to Storefront</span>
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
                    <span style="margin-right: 15px; font-weight: 600;">
                        <?php echo htmlspecialchars($_SESSION['vendor_user']['store_name'] ?? 'Vendor'); ?>
                    </span>
                    <a href="<?= BASE_URL ?>" class="topbar-btn" title="View Store">
                        <i class="fas fa-store"></i>
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
