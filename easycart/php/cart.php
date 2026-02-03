<?php
// php/cart.php
session_start();
require_once '../includes/db.php';
require_once '../data/productsdata.php'; // This now fetches from DB

$title = "Shopping Cart - EasyCart";
$base_path = "../";
$page = "cart";
$extra_css = "cart.css";

// 1. Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['applied_promo'])) {
    $_SESSION['applied_promo'] = null;
}

/**
 * Centralized Cart Calculation Logic.
 * Recalculates Subtotal, Shipping, Tax, and Total based on current cart state.
 */
function calculateCartTotals($cart_items, $products_data) {
    // Note: $products_data is now our DB-driven array
    $subtotal = 0;
    
    // Calculate Subtotal
    foreach($cart_items as $p_id => $qty) {
        foreach($products_data as $p) {
            if($p['id'] == $p_id) {
                // Ensure price is numeric float
                $price_val = (float)str_replace(',', '', $p['price']);
                $subtotal += $price_val * $qty;
                break;
            }
        }
    }

    // --- Shipping Rules ---
    $shipping_options = [
        'standard' => 40,
        'express' => min(80, $subtotal * 0.10),
        'white_glove' => min(150, $subtotal * 0.05),
        'freight' => max(250, $subtotal * 0.03)
    ];

    // Determine Selected Shipping Method
    $selected_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;

    if ($selected_method === null) {
        // Default Logic
        $selected_method = ($subtotal <= 300) ? 'express' : 'freight';
        $_SESSION['shipping_method'] = $selected_method;
    } else {
        // Validation Logic
        if ($subtotal <= 300) {
            if ($selected_method !== 'express') {
                 $selected_method = 'express';
                 $_SESSION['shipping_method'] = $selected_method;
            }
        } else {
            if ($selected_method !== 'white_glove' && $selected_method !== 'freight') {
                $selected_method = 'freight';
                $_SESSION['shipping_method'] = $selected_method;
            }
        }
    }

    $shipping_cost = isset($shipping_options[$selected_method]) ? $shipping_options[$selected_method] : 40;
    
    if (empty($cart_items)) {
        $shipping_cost = 0;
        $shipping_options = array_map(function() { return 0; }, $shipping_options);
    }

    // --- Smart Discount ---
    $smart_discount = 0;
    $reason = "";
    $item_count = array_sum($cart_items);

    if ($item_count > 0) {
        $discount_percent = min($item_count, 100); 
        $smart_discount = $subtotal * ($discount_percent / 100);
        $reason = "Quantity Discount ({$discount_percent}% off)";
    }

    // --- Promo Code ---
    $promo_discount = 0;
    $promo_code = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
    $promo_message = "";

    if ($promo_code) {
        $allowed_codes = ['SAVE5', 'SAVE10', 'SAVE15', 'SAVE20'];
        if (in_array($promo_code, $allowed_codes)) {
             $percent = (int)substr($promo_code, 4);
             $base_for_promo = $subtotal + $shipping_cost;
             $promo_discount = $base_for_promo * ($percent / 100);
             $promo_message = "{$promo_code} Applied ({$percent}% off)";
             
             if ($promo_discount > 0) {
                 $smart_discount = 0;
                 $reason = ""; 
             }
        }
    }

    // Tax (18%)
    $tax = ($subtotal - $smart_discount + $shipping_cost) * 0.18;

    // Grand Total
    $total = ($subtotal - $smart_discount) + $shipping_cost + $tax - $promo_discount;
    $total = max(0, $total);

    return [
        'subtotal' => $subtotal,
        'shipping_cost' => $shipping_cost,
        'tax' => $tax,
        'total' => $total,
        'shipping_options' => $shipping_options,
        'item_count' => $item_count,
        'smart_discount' => $smart_discount,
        'discount_reason' => $reason,
        'selected_method' => $selected_method,
        'promo_discount' => $promo_discount,
        'promo_code' => $promo_code,
        'promo_message' => $promo_message
    ];
}

// 2. Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Calculate current totals first
    $totals = calculateCartTotals($_SESSION['cart'], $products);
    $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    // --- CHECKOUT LOGIC ---
    if ($_POST['action'] === 'checkout') {
        
        // 1. Validate User
        if (!isset($_SESSION['user'])) {
             // Return JSON error or redirect
             if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                 echo json_encode(['success' => false, 'message' => 'Please login to checkout.']);
                 exit;
             } else {
                 header("Location: auth.php");
                 exit;
             }
        }
        
        $user_id = $_SESSION['user']['id'];
        
        // 2. Validate Cart
        if(empty($_SESSION['cart'])) {
             if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                 echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
                 exit;
             }
             header("Location: cart.php"); 
             exit;
        }

        try {
            $pdo->beginTransaction();

            // 3. Create Order
            // Use time() as simple increment_id for now, or use UUID
            $increment_id = time() . '-' . $user_id; 
            
            $stmtOrder = $pdo->prepare("
                INSERT INTO sales_order 
                (increment_id, user_id, status, subtotal, shipping_amount, tax_amount, grand_total, customer_email, created_at)
                VALUES (?, ?, 'processing', ?, ?, ?, ?, ?, NOW())
                RETURNING order_id
            ");
            
            // Assuming customer email from session
            $customer_email = $_SESSION['user']['email'];
            
            $stmtOrder->execute([
                $increment_id,
                $user_id,
                $totals['subtotal'],
                $totals['shipping_cost'],
                $totals['tax'],
                $totals['total'],
                $customer_email
            ]);
            
            $order_id = $stmtOrder->fetchColumn();

            // 4. Create Order Items
            $stmtItem = $pdo->prepare("
                INSERT INTO sales_order_products 
                (order_id, product_id, sku, name, price, quantity, total_price)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            foreach($_SESSION['cart'] as $pid => $qty) {
                 // Find product data
                 $product = null;
                 foreach($products as $p) {
                     if($p['id'] == $pid) {
                         $product = $p;
                         break;
                     }
                 }
                 
                 if ($product) {
                     $price = (float)str_replace(',', '', $product['price']);
                     $total_price = $price * $qty;
                     // Generate dummy SKU if not present or DB has it
                     $sku = 'SKU-' . $pid; 
                     
                     $stmtItem->execute([
                         $order_id,
                         $pid,
                         $sku,
                         $product['title'],
                         $price,
                         $qty,
                         $total_price
                     ]);
                 }
            }
            
            $pdo->commit();
            
            // 5. Clear Cart
            $_SESSION['cart'] = [];
            unset($_SESSION['shipping_method']);
            unset($_SESSION['applied_promo']);
            
            // 6. Response
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => true, 'redirect' => 'orders.php']);
                exit;
            } else {
                header("Location: orders.php");
                exit;
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
                exit;
            }
            // Log error
            die("Order creation failed");
        }
    }
    
    // --- OTHER ACTIONS (QTY, REMOVE, PROMO) ---
    // (Existing Logic with Session Only for now)
    elseif ($_POST['action'] === 'update_qty' && $p_id > 0) {
        if (isset($_POST['qty'])) {
            $new_qty = (int)$_POST['qty'];
            $_SESSION['cart'][$p_id] = $new_qty;
        } elseif (isset($_POST['change'])) {
            $change = (int)$_POST['change'];
            if (!isset($_SESSION['cart'][$p_id])) {
                $_SESSION['cart'][$p_id] = 0;
            }
            $_SESSION['cart'][$p_id] += $change;
        }
        
        if (isset($_SESSION['cart'][$p_id]) && $_SESSION['cart'][$p_id] <= 0) {
            unset($_SESSION['cart'][$p_id]);
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['shipping_method']);
            }
        }
    } elseif ($_POST['action'] === 'remove' && $p_id > 0) {
        if (isset($_SESSION['cart'][$p_id])) {
            unset($_SESSION['cart'][$p_id]);
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['shipping_method']);
            }
        }
    } elseif ($_POST['action'] === 'set_shipping' && isset($_POST['method'])) {
        $_SESSION['shipping_method'] = $_POST['method'];
    } elseif ($_POST['action'] === 'clear') {
        $_SESSION['cart'] = [];
        unset($_SESSION['shipping_method']);
        unset($_SESSION['applied_promo']);
    } elseif ($_POST['action'] === 'apply_promo' && isset($_POST['code'])) {
        $code = trim($_POST['code']);
        $allowed_codes = ['SAVE5', 'SAVE10', 'SAVE15', 'SAVE20'];
        if (in_array($code, $allowed_codes)) {
            $_SESSION['applied_promo'] = $code;
        } else {
             $_SESSION['applied_promo'] = null;
        }
    }

    // AJAX Response for Updates
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        $totals = calculateCartTotals($_SESSION['cart'], $products); // Recalculate
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'summary' => [
                'subtotal' => $totals['subtotal'],
                'shipping' => $totals['shipping_cost'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'count' => $totals['item_count'],
                'smart_discount' => $totals['smart_discount'],
                'reason' => $totals['discount_reason'],
                'shipping_options' => $totals['shipping_options'],
                'promo_discount' => $totals['promo_discount'],
                'promo_code' => $totals['promo_code'],
                'promo_message' => $totals['promo_message']
            ]
        ]);
        exit;
    }
    
    header("Location: cart.php");
    exit;
}

include '../includes/header.php';
?>

<?php include '../templates/cart.php'; ?>

<?php include '../includes/footer.php'; ?>