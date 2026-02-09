<?php
// public/index.php
// Main entry point for the application

// Start session
session_start();

// Define base path
$base_path = '../public/';

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load product data (needed globally)
require_once __DIR__ . '/../data/productsdata.php';

// Load routes
$routes = require_once __DIR__ . '/../routes/web.php';

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query string
$request_uri = strtok($request_uri, '?');

// Remove base directory from URI
$base_dir = '/cybercom-internship-v2/easycart/';
define('BASE_URL', $base_dir); // Define global constant for views

$request_uri = str_replace($base_dir, '', $request_uri);

// Remove leading and trailing slashes
$request_uri = trim($request_uri, '/');

// Default to home if empty
if (empty($request_uri)) {
    $request_uri = '';
}

// Find matching route
if (isset($routes[$request_uri])) {
    $route = $routes[$request_uri];
    $controllerName = $route['controller'];
    $methodName = $route['method'];
    
    // Load the controller
    $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Create controller instance based on controller type
     if ($controllerName === 'AdminController') {
            require_once __DIR__ . '/../models/Product.php';
            require_once __DIR__ . '/../models/Admin.php';
            $productModel = new Product($pdo);
            $adminModel = new Admin($pdo);
            $controller = new $controllerName($productModel, $adminModel);
        } elseif ($controllerName === 'HomeController' || $controllerName === 'ProductController') {
            require_once __DIR__ . '/../models/Product.php';
            $productModel = new Product($pdo);
            $controller = new $controllerName($productModel);
        } else {
            // For other controllers, we'll add proper initialization later
            $controller = new $controllerName();
        }
        
        
        // Call the method
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            http_response_code(404);
            echo "Method not found";
        }
    } else {
        http_response_code(404);
        echo "Controller not found";
    }
} else {
    // 404 - Route not found
    http_response_code(404);
    echo "Page not found";
}
?>
