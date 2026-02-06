<?php
// controllers/ProductController.php
// Product listing and details controller

class ProductController {
    private $productModel;
    
    public function __construct($productModel) {
        $this->productModel = $productModel;
    }
    
    /**
     * Display products listing page with filters and pagination
     */
    public function list() {
        // Get all products
        $products = $this->productModel->getAllProducts();
        
        // Make categories and brands available
        global $categories, $brands;
        
        // 1. Capture Filter Inputs
        $selected_categories = isset($_GET['category']) ? (array)$_GET['category'] : [];
        $selected_brands = isset($_GET['brand']) ? (array)$_GET['brand'] : [];
        $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        // 2. Filter Logic
        $filtered_products = array_filter($products, function($product) use ($selected_categories, $selected_brands, $search_query) {
            // 1. Check Category (Multi-support)
            $productCats = $product['categories'] ?? [$product['category']];
            $cat_match = empty($selected_categories) || !empty(array_intersect((array)$productCats, $selected_categories));
            
            // 2. Check Brand (Multi-support)
            $productBrands = $product['brands'] ?? [$product['brand']];
            $brand_match = empty($selected_brands) || !empty(array_intersect((array)$productBrands, $selected_brands));
            
            // 3. Check Search
            $search_match = true;
            if ($search_query !== '') {
                $search_words = explode(' ', strtolower($search_query));
                
                // Load category synonyms for matching
                $cat_labels = [
                    'electronics' => 'Tech Electronics Technology Gadgets',
                    'fashion' => 'Fashion Clothing Apparel Wear',
                    'home-living' => 'Home Travel Luggage Living Decor',
                    'beauty' => 'Beauty Personal Care Health',
                    'smartphones' => 'Smartphone Mobile Phone Cellphone iPhone Android'
                ];
                
                foreach ($search_words as $word) {
                    $word = trim($word);
                    if ($word === '') continue;
                    
                    $word_found = false;
                    
                    // Check in Title
                    if (stripos($product['title'], $word) !== false) $word_found = true;
                    // Check in all Brands
                    if (!$word_found) {
                        foreach ($productBrands as $bSlug) {
                            if (stripos($bSlug, $word) !== false) { $word_found = true; break; }
                        }
                    }
                    // Check in all Categories
                    if (!$word_found) {
                        foreach ($productCats as $cSlug) {
                            if (stripos($cSlug, $word) !== false) { $word_found = true; break; }
                            if (isset($cat_labels[$cSlug]) && stripos($cat_labels[$cSlug], $word) !== false) { $word_found = true; break; }
                        }
                    }
                    // Check in Description
                    if (!$word_found && isset($product['description']) && stripos($product['description'], $word) !== false) $word_found = true;
                    // Check in Features
                    if (!$word_found && isset($product['features']) && is_array($product['features'])) {
                        foreach ($product['features'] as $feature) {
                            if (stripos($feature, $word) !== false) { $word_found = true; break; }
                        }
                    }
                    
                    if (!$word_found) {
                        $search_match = false;
                        break;
                    }
                }
            }
            
            return $cat_match && $brand_match && $search_match;
        });
        
        // --- PAGINATION LOGIC ---
        $limit = 16; // Products per page
        $page_num = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        
        $total_products = count($filtered_products);
        $total_pages = max(1, (int)ceil($total_products / $limit));
        
        // Ensure page doesn't exceed total pages
        if ($page_num > $total_pages) $page_num = $total_pages;
        
        $offset = ($page_num - 1) * $limit;
        $paginated_products = array_slice($filtered_products, $offset, $limit);
        
        // 4. AJAX HANDLER
        // If this is an AJAX request, ONLY output the grid and exit.
        if(isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            $base_path = ''; // Set for AJAX requests too
            $this->renderGrid($paginated_products, $page_num, $total_pages, $base_path);
            exit;
        }
        
        // Set page variables
        $title = "Shop - EasyCart India";
        $page = "products";
        $extra_css = "products.css";
        $base_path = '';
        
        // Load the view
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/products/list.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Render products grid (for AJAX)
     */
    private function renderGrid($items, $page, $total_pages, $base_path = '') {
        // Include the Grid Template
        require __DIR__ . '/../views/products/grid.php';
    }
    
    /**
     * Display product details page
     */
    public function details() {
        // Get product ID from query string
        $current_product = null;
        if(isset($_GET['id'])) {
            $p_id = (int)$_GET['id'];
            $current_product = $this->productModel->getProductById($p_id);
        }
        
        // Handle Not Found
        if(!$current_product) {
            echo "<h2 style='text-align:center; padding: 5rem;'>Product not found. <a href='products'>Return to Shop</a></h2>";
            exit;
        }
        
        // Set page variables
        $title = $current_product['title'] . " - EasyCart";
        $page = "products";
        $extra_css = "productdetails.css?v=" . time();
        $extra_css_2 = "wishlist.css";
        $base_path = '';
        
        // Load the view
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/products/details.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>
