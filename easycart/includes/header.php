<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'EasyCart India'; ?></title>
    <?php $v = time(); ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/styles.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if(isset($extra_css)): ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/<?php echo $extra_css; ?>?v=<?php echo $v; ?>">
    <?php endif; ?>
    <?php if(isset($extra_css_2)): ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/<?php echo $extra_css_2; ?>?v=<?php echo $v; ?>">
    <?php endif; ?>
</head>
<body>
    <header>
        <nav>
            <!-- Logo (Left) -->
            <a href="<?php echo $base_path; ?>index.php" class="logo">
                <h1>EasyCart</h1>
            </a>

            <!-- Main Navigation Links (Center-Left) -->
            <ul class="nav-links">
                <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo ($page === 'home') ? 'active-nav' : ''; ?>">HOME</a></li>
                <li><a href="<?php echo $base_path; ?>php/products.php" class="<?php echo ($page === 'products') ? 'active-nav' : ''; ?>">COLLECTIONS</a></li>
                <li><a href="<?php echo $base_path; ?>php/orders.php" class="<?php echo ($page === 'orders') ? 'active-nav' : ''; ?>">ORDERS</a></li>
            </ul>

            <!-- Search Bar (Center-Right) -->
            <form action="<?php echo $base_path; ?>php/products.php" method="GET" class="nav-search">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" placeholder="Search for products, brands and more" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </form>

            <!-- Icon Links (Right) -->
            <ul class="nav-icons">
                <!-- Profile Icon -->
                <li>
                    <?php if(isset($_SESSION['user'])): ?>
                        <a href="<?php echo $base_path; ?>php/profile.php" class="nav-icon-link <?php echo ($page === 'profile') ? 'active-nav' : ''; ?>" title="Profile">
                            <i class="far fa-user"></i>
                            <span>Profile</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>php/auth.php" class="nav-icon-link <?php echo ($page === 'auth') ? 'active-nav' : ''; ?>" title="Login">
                            <i class="far fa-user"></i>
                            <span>Profile</span>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Wishlist Icon -->
                <li>
                    <a href="<?php echo $base_path; ?>php/wishlist.php" class="nav-icon-link <?php echo ($page === 'wishlist') ? 'active-nav' : ''; ?>" title="Wishlist">
                        <i class="far fa-heart"></i>
                        <span>Wishlist</span>
                        <?php 
                        $wishlist_count = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
                        ?>
                        <span class="cart-count-badge" id="wishlist-count" style="display: <?php echo ($wishlist_count > 0) ? 'flex' : 'none'; ?>">
                            <?php echo $wishlist_count; ?>
                        </span>
                    </a>
                </li>

                <!-- Cart/Bag Icon -->
                <li>
                    <a href="<?php echo $base_path; ?>php/cart.php" class="nav-icon-link <?php echo ($page === 'cart') ? 'active-nav' : ''; ?>" title="Shopping Bag">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Bag</span>
                        <?php 
                        $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                        ?>
                        <span class="cart-count-badge" id="cart-count" style="display: <?php echo ($cart_count > 0) ? 'flex' : 'none'; ?>">
                            <?php echo $cart_count; ?>
                        </span>
                    </a>
                </li>

                <!-- Login Icon (only for logged-out users) -->
                <?php if(!isset($_SESSION['user'])): ?>
                    <li>
                        <a href="<?php echo $base_path; ?>php/auth.php" class="nav-icon-link nav-login-btn" title="Login">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Logout Icon (only for logged-in users) -->
                <?php if(isset($_SESSION['user'])): ?>
                    <li>
                        <a href="<?php echo $base_path; ?>php/logout.php" class="nav-icon-link nav-logout-btn" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <script src="<?php echo $base_path; ?>js/wishlist.js?v=<?php echo $v; ?>"></script>

