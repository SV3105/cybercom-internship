<?php
// routes/web.php
// Application routing configuration

return [
    // Home
    '' => ['controller' => 'HomeController', 'method' => 'index'],
    'index.php' => ['controller' => 'HomeController', 'method' => 'index'],
    
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
    
    // Admin - Dashboard & CSV Import/Export
    'admin' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/export' => ['controller' => 'AdminController', 'method' => 'exportPage'],
    'admin/exportdownload' => ['controller' => 'AdminController', 'method' => 'exportProducts'],
    'admin/import' => ['controller' => 'AdminController', 'method' => 'importPage'],
    'admin/importprocess' => ['controller' => 'AdminController', 'method' => 'handleImport'],
    'admin/downloadfailed' => ['controller' => 'AdminController', 'method' => 'downloadFailed'],
    'admin/products' => ['controller' => 'AdminController', 'method' => 'products'],
    'admin/orders' => ['controller' => 'AdminController', 'method' => 'orders'],
    'admin/productdelete' => ['controller' => 'AdminController', 'method' => 'deleteProduct'],
    'admin/productedit' => ['controller' => 'AdminController', 'method' => 'editProduct'],
    'admin/productsave' => ['controller' => 'AdminController', 'method' => 'saveProduct'],
    'admin/orderview' => ['controller' => 'AdminController', 'method' => 'orderDetails'],
    'admin/orderstatusupdate' => ['controller' => 'AdminController', 'method' => 'updateOrderStatus'],
    'admin/ordersavenotes' => ['controller' => 'AdminController', 'method' => 'saveOrderNotes'],
    'admin/profile' => ['controller' => 'AdminController', 'method' => 'profile'],
    'admin/createadmin' => ['controller' => 'AdminController', 'method' => 'showCreateAdmin'],
    'admin/processcreateadmin' => ['controller' => 'AdminController', 'method' => 'handleCreateAdmin'],
];
?>
