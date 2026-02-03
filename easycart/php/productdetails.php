<?php
session_start();
$title = "Product Details - EasyCart";
$base_path = "../";
$page = "products";
$extra_css = "productdetails.css?v=" . time();
$extra_css_2 = "wishlist.css";
include '../data/productsdata.php';


// Logic to find product
$current_product = null;
if(isset($_GET['id'])) {
    $p_id = (int)$_GET['id'];
    foreach($products as $p) {
        if($p['id'] == $p_id) {
            $current_product = $p;
            break;
        }
    }
}

// Handle Not Found
if(!$current_product) {
    echo "<h2 style='text-align:center; padding: 5rem;'>Product not found. <a href='products.php'>Return to Shop</a></h2>";
    exit;
}

// Update Title to Product Name
$title = $current_product['title'] . " - EasyCart";

include '../includes/header.php';
?>


<?php include '../templates/productdetails.php'; ?>

<?php include '../includes/footer.php'; ?>
