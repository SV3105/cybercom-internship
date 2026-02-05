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
            // Check Category
            $cat_match = empty($selected_categories) || in_array($product['category'], $selected_categories);
            // Check Brand
            $brand_match = empty($selected_brands) || in_array($product['brand'], $selected_brands);
            // Check Search
            $search_match = true;
            if ($search_query !== '') {
                $search_words = explode(' ', strtolower($search_query));
                
                // Load category synonyms for matching (generalized names only)
                $cat_labels = [
                    'electronics' => 'Tech Electronics Technology Gadgets',
                    'fashion' => 'Fashion Clothing Apparel Wear',
                    'home' => 'Home Travel Luggage Living',
                    'beauty' => 'Beauty Personal Care Health'
                ];
                
                foreach ($search_words as $word) {
                    $word = trim($word);
                    if ($word === '') continue;
                    
                    $word_found = false;
                    
                    // 1. Check in Title
                    if (stripos($product['title'], $word) !== false) $word_found = true;
                    // 2. Check in Brand
                    elseif (stripos($product['brand'], $word) !== false) $word_found = true;
                    // 3. Check in Category Slug OR Display Label
                    elseif (stripos($product['category'], $word) !== false) $word_found = true;
                    elseif (isset($cat_labels[$product['category']]) && stripos($cat_labels[$product['category']], $word) !== false) $word_found = true;
                    // 4. Check in Description
                    elseif (isset($product['description']) && stripos($product['description'], $word) !== false) $word_found = true;
                    // 5. Check in Image Filename (removes .png/.jpg etc)
                    elseif (stripos($product['image'], $word) !== false) $word_found = true;
                    // 6. Check in Features
                    elseif (isset($product['features']) && is_array($product['features'])) {
                        foreach ($product['features'] as $feature) {
                            if (stripos($feature, $word) !== false) {
                                $word_found = true;
                                break;
                            }
                        }
                    }
                    
                    // If THIS word was not found in ANY field, then the product is not a match
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
        $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page_num < 1) $page_num = 1;
        
        $total_products = count($filtered_products);
        $total_pages = ceil($total_products / $limit);
        
        // Ensure page doesn't exceed total pages
        if ($page_num > $total_pages && $total_pages > 0) $page_num = $total_pages;
        
        $offset = ($page_num - 1) * $limit;
        $paginated_products = array_slice($filtered_products, $offset, $limit);
        
        // 4. AJAX HANDLER
        // If this is an AJAX request, ONLY output the grid and exit.
        if(isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            $base_path = ''; // Set for AJAX requests too
            $this->renderProductsGrid($paginated_products, $page_num, $total_pages, $base_path);
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
    private function renderProductsGrid($items, $page, $total_pages, $base_path = '') {
        // Include the Grid Template
        require __DIR__ . '/../views/products/productsgrid.php';
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
