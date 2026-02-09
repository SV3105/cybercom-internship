<?php
// routes/web.php
// Application routing configuration

return [
    // Home
    '' => ['controller' => 'HomeController', 'method' => 'index'],
    'index.php' => ['controller' => 'HomeController', 'method' => 'index'],
    
    // Products
    'products' => ['controller' => 'ProductController', 'method' => 'list'],
    'product-details' => ['controller' => 'ProductController', 'method' => 'details'],
    'product-search' => ['controller' => 'ProductController', 'method' => 'ajaxSearch'],
    
    // Order Routes
    'orders' => ['controller' => 'OrderController', 'method' => 'list'],
    'order-details' => ['controller' => 'OrderController', 'method' => 'details'],
    'invoice' => ['controller' => 'OrderController', 'method' => 'invoice'],
    
    // Profile & Wishlist
    'profile' => ['controller' => 'ProfileController', 'method' => 'index'],
    'profile-update' => ['controller' => 'ProfileController', 'method' => 'update'],
    'wishlist' => ['controller' => 'ProfileController', 'method' => 'wishlist'],
    'wishlist-action' => ['controller' => 'ProfileController', 'method' => 'wishlistAction'],
    
    // Authentication
    'auth' => ['controller' => 'AuthController', 'method' => 'showLogin'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'signup' => ['controller' => 'AuthController', 'method' => 'signup'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    
    // Cart
    'cart' => ['controller' => 'CartController', 'method' => 'index'],
    
    // Checkout
    'checkout' => ['controller' => 'CheckoutController', 'method' => 'index'],
    'place-order' => ['controller' => 'CheckoutController', 'method' => 'placeOrder'],
    
    // Admin - Dashboard & CSV Import/Export
    'admin' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/export' => ['controller' => 'AdminController', 'method' => 'exportPage'],
    'admin/export-download' => ['controller' => 'AdminController', 'method' => 'exportProducts'],
    'admin/import' => ['controller' => 'AdminController', 'method' => 'importPage'],
    'admin/import-process' => ['controller' => 'AdminController', 'method' => 'handleImport'],
    'admin/download-failed' => ['controller' => 'AdminController', 'method' => 'downloadFailed'],
    'admin/products' => ['controller' => 'AdminController', 'method' => 'products'],
    'admin/orders' => ['controller' => 'AdminController', 'method' => 'orders'],
    'admin/product-delete' => ['controller' => 'AdminController', 'method' => 'deleteProduct'],
    'admin/product-edit' => ['controller' => 'AdminController', 'method' => 'editProduct'],
    'admin/product-save' => ['controller' => 'AdminController', 'method' => 'saveProduct'],
    'admin/order-view' => ['controller' => 'AdminController', 'method' => 'orderDetails'],
    'admin/order-status-update' => ['controller' => 'AdminController', 'method' => 'updateOrderStatus'],
    'admin/order-save-notes' => ['controller' => 'AdminController', 'method' => 'saveOrderNotes'],
    'admin/profile' => ['controller' => 'AdminController', 'method' => 'profile'],
    'admin/create-admin' => ['controller' => 'AdminController', 'method' => 'showCreateAdmin'],
    'admin/process-create-admin' => ['controller' => 'AdminController', 'method' => 'handleCreateAdmin'],
];
?>
