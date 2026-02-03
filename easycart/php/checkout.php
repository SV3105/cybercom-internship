<?php
// php/checkout.php
session_start();
require_once '../includes/db.php';
require_once '../data/productsdata.php';

// Redirect to auth if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

// Redirect to cart if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$user = $_SESSION['user'];

// Calculate cart totals
$cart_items = $_SESSION['cart'];
$subtotal = 0;
foreach($cart_items as $p_id => $qty) {
    foreach($products as $p) {
        if($p['id'] == $p_id) {
            $price_val = (float)str_replace(',', '', $p['price']);
            $subtotal += $price_val * $qty;
            break;
        }
    }
}

// Shipping calculation
$shipping_options = [
    'standard' => 40,
    'express' => min(80, $subtotal * 0.10),
    'white_glove' => min(150, $subtotal * 0.05),
    'freight' => max(250, $subtotal * 0.03)
];

$selected_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;
if ($selected_method === null) {
    $selected_method = ($subtotal <= 300) ? 'express' : 'freight';
    $_SESSION['shipping_method'] = $selected_method;
}

$shipping_cost = $shipping_options[$selected_method];

// Discount calculation
$smart_discount = 0;
$item_count = array_sum($cart_items);
if ($item_count > 0) {
    $discount_percent = min($item_count, 100);
    $smart_discount = $subtotal * ($discount_percent / 100);
}

// Tax calculation
$tax = ($subtotal - $smart_discount + $shipping_cost) * 0.18;
$total = ($subtotal - $smart_discount) + $shipping_cost + $tax;

// Pre-fill user data
$prefill = [
    'firstname' => explode(' ', $user['name'])[0] ?? '',
    'lastname' => explode(' ', $user['name'], 2)[1] ?? '',
    'email' => $user['email'] ?? '',
    'phone' => $user['phone'] ?? '',
    'city' => $user['location'] ?? ''
];

$title = "Checkout - EasyCart";
$base_path = "../";
$page = "checkout";
$extra_css = "checkout.css";

include '../includes/header.php';
?>

<?php include '../templates/checkout.php'; ?>

<?php include '../includes/footer.php'; ?>
