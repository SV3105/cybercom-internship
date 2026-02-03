<?php
session_start();

$title = "Shop - EasyCart India";
$base_path = "../";
$page = "products";
$extra_css = "products.css";
include '../data/productsdata.php';

// --- SERVER SIDE LOGIC ---

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

// 3. Render Function (HTML Output for Grid Items + Pagination)
function renderProductsGrid($items, $page, $total_pages) {
    // Include the Grid Template
    include '../templates/productsgrid.php';
}

// 4. AJAX HANDLER
// If this is an AJAX request, ONLY output the grid and exit.
// If this is an AJAX request, ONLY output the grid and exit.
if(isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    renderProductsGrid($paginated_products, $page_num, $total_pages);
    exit;
}

// --- NORMAL PAGE LOAD ---
include '../includes/header.php';
?>


<?php include '../templates/products.php'; ?>

<?php include '../includes/footer.php'; ?>