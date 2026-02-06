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
    
];
?>
