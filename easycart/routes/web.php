<?php
// routes/web.php
// Application routing configuration

return [
    // Home
    '' => ['controller' => 'HomeController', 'method' => 'index'],
    'index.php' => ['controller' => 'HomeController', 'method' => 'index'],
    'sell' => ['controller' => 'HomeController', 'method' => 'sell'],

    // Products
    'products' => ['controller' => 'ProductController', 'method' => 'list'],
    'productdetails' => ['controller' => 'ProductController', 'method' => 'details'],
    'productsearch' => ['controller' => 'ProductController', 'method' => 'ajaxSearch'],

    // Order Routes
    'orders' => ['controller' => 'OrderController', 'method' => 'list'],
    'orderdetails' => ['controller' => 'OrderController', 'method' => 'details'],
    'invoice' => ['controller' => 'OrderController', 'method' => 'invoice'],

    // Profile & Wishlist
    'profile' => ['controller' => 'ProfileController', 'method' => 'index'],
    'profileupdate' => ['controller' => 'ProfileController', 'method' => 'update'],
    'wishlist' => ['controller' => 'ProfileController', 'method' => 'wishlist'],
    'wishlistaction' => ['controller' => 'ProfileController', 'method' => 'wishlistAction'],

    // Authentication
    'auth' => ['controller' => 'AuthController', 'method' => 'showLogin'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'signup' => ['controller' => 'AuthController', 'method' => 'signup'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],

    // Cart
    'cart' => ['controller' => 'CartController', 'method' => 'index'],

    // Checkout
    'checkout' => ['controller' => 'CheckoutController', 'method' => 'index'],
    'placeorder' => ['controller' => 'CheckoutController', 'method' => 'placeOrder'],

    // Admin - Platform Management
    'admin' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/users' => ['controller' => 'AdminController', 'method' => 'users'],
    'admin/vendors' => ['controller' => 'AdminController', 'method' => 'vendors'],
    'admin/settings' => ['controller' => 'AdminController', 'method' => 'settings'],

    // Vendor Routes
    'vendor' => ['controller' => 'VendorController', 'method' => 'dashboard'],
    'vendor/dashboard' => ['controller' => 'VendorController', 'method' => 'dashboard'],
    'vendor/login' => ['controller' => 'VendorAuthController', 'method' => 'showLogin'],
    'vendor/login-process' => ['controller' => 'VendorAuthController', 'method' => 'login'],
    'vendor/register' => ['controller' => 'VendorAuthController', 'method' => 'showRegister'],
    'vendor/register-process' => ['controller' => 'VendorAuthController', 'method' => 'register'],
    'vendor/logout' => ['controller' => 'VendorAuthController', 'method' => 'logout'],
    'vendor/products' => ['controller' => 'VendorController', 'method' => 'products'],
    'vendor/productdelete' => ['controller' => 'VendorController', 'method' => 'deleteProduct'],
    'vendor/productedit' => ['controller' => 'VendorController', 'method' => 'editProduct'],
    'vendor/productsave' => ['controller' => 'VendorController', 'method' => 'saveProduct'],
    'vendor/orders' => ['controller' => 'VendorController', 'method' => 'orders'],
    'vendor/orderview' => ['controller' => 'VendorController', 'method' => 'orderDetails'],
    'vendor/coupons' => ['controller' => 'VendorController', 'method' => 'coupons'],
    'vendor/couponedit' => ['controller' => 'VendorController', 'method' => 'couponForm'],
    'vendor/couponsave' => ['controller' => 'VendorController', 'method' => 'saveCoupon'],
    'vendor/coupondelete' => ['controller' => 'VendorController', 'method' => 'deleteCoupon'],
];
?>