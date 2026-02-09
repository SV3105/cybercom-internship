<?php
// controllers/HomeController.php
// Home page controller

class HomeController {
    private $productModel;
    
    public function __construct($productModel) {
        $this->productModel = $productModel;
    }
    
    /**
     * Display home page
     */
    public function index() {
        // Get all products for home page display
        $products = $this->productModel->getAllProducts();
        
        // Make categories available
        global $categories;
        
        // Set page variables
        $title = "EasyCart India - The Big Sale is Live!";
        $page = "home";
        $base_path = ''; // Empty since we're serving from public/
        
        // Load the view
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/home/home.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>
