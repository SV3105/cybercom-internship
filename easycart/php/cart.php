<?php
// php/cart.php
session_start();
require_once '../includes/db.php';
require_once '../data/productsdata.php';

// Restrict cart PAGE access to logged-in users only
// But allow AJAX requests (add to cart) for guest users
$isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!isset($_SESSION['user']) && !$isAjaxRequest) {
    // Guest user trying to access cart page - redirect to login
    header("Location: auth.php");
    exit;
}

$title = "Shopping Cart - EasyCart";
$base_path = "../";
$page = "cart";
$extra_css = "cart.css";

function syncCartToDb($user_id, $cart, $session_id_val) {
    global $pdo;
    try {
        $cart_id = false;
        
        // 1. Get or Create Active Cart
        if ($user_id) {
            // Logged in: Match by User ID
            $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE");
            $stmt->execute([$user_id]);
            $cart_id = $stmt->fetchColumn();
            
            // If we found a cart, update its session_id to current one (handling device switches)
            if ($cart_id) {
                $pdo->prepare("UPDATE sales_cart SET session_id = ? WHERE id = ?")->execute([$session_id_val, $cart_id]);
            }
        } else {
            // Guest: Match by Session ID
            $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE session_id = ? AND is_active = TRUE AND user_id IS NULL");
            $stmt->execute([$session_id_val]);
            $cart_id = $stmt->fetchColumn();
        }

        if (!$cart_id) {
            if (empty($cart)) return; // Don't create empty cart record
            
            if ($user_id) {
                $stmtCreate = $pdo->prepare("INSERT INTO sales_cart (user_id, session_id, is_active, created_at) VALUES (?, ?, TRUE, NOW()) RETURNING id");
                $stmtCreate->execute([$user_id, $session_id_val]);
            } else {
                $stmtCreate = $pdo->prepare("INSERT INTO sales_cart (session_id, is_active, created_at) VALUES (?, TRUE, NOW()) RETURNING id");
                $stmtCreate->execute([$session_id_val]);
            }
            $cart_id = $stmtCreate->fetchColumn();
        } 

        // 2. Sync Items & Calculate Total
        $pdo->prepare("DELETE FROM sales_cart_products WHERE cart_id = ?")->execute([$cart_id]);

        $calculated_total = 0.00;

        if (!empty($cart)) {
            $stmtInsert = $pdo->prepare("INSERT INTO sales_cart_products (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmtPrice = $pdo->prepare("SELECT price FROM catalog_product_entity WHERE entity_id = ?");
            
            foreach ($cart as $p_id => $qty) {
                 if ($qty > 0) {
                    $stmtPrice->execute([$p_id]);
                    $price = $stmtPrice->fetchColumn();
                    if ($price !== false) {
                        $stmtInsert->execute([$cart_id, $p_id, $qty, $price]);
                        $calculated_total += ($price * $qty);
                    }
                 }
            }
        }
        

        $pdo->prepare("UPDATE sales_cart SET grand_total = ? WHERE id = ?")->execute([$calculated_total, $cart_id]);

    } catch (PDOException $e) {
        error_log("Cart Sync Error: " . $e->getMessage());
    }
}

function loadCartFromDb($user_id) {
    global $pdo;
    $db_cart = [];
    try {
        $stmt = $pdo->prepare("
            SELECT p.product_id, p.quantity 
            FROM sales_cart_products p
            JOIN sales_cart c ON p.cart_id = c.id
            WHERE c.user_id = ? AND c.is_active = TRUE
        ");
        $stmt->execute([$user_id]);
        while ($row = $stmt->fetch()) {
            $db_cart[$row['product_id']] = $row['quantity'];
        }
    } catch (PDOException $e) {
        error_log("Cart Load Error: " . $e->getMessage());
    }
    return $db_cart;
}

function getActiveCartId($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Get Cart ID Error: " . $e->getMessage());
        return false;
    }
}

// 1. Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- SYNC ON LOAD (If Logged In) ---
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    
    // Load DB cart
    $db_cart = loadCartFromDb($user_id);
    
    $merged = $db_cart;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (isset($merged[$pid])) {
             $merged[$pid] = $qty; // Overwrite DB with Session (current active)
        } else {
             $merged[$pid] = $qty;
        }
    }
   
    if (empty($_SESSION['cart']) && !empty($db_cart)) {
        $_SESSION['cart'] = $db_cart;
    }

}

/**
 * [calculateCartTotals function remains same - omitted for brevity in thought process, but included in file write]
 */
function calculateCartTotals($cart_items, $products_data) {
     $subtotal = 0;
    foreach($cart_items as $p_id => $qty) {
        foreach($products_data as $p) {
            if($p['id'] == $p_id) {
                $price_val = (float)str_replace(',', '', $p['price']);
                $subtotal += $price_val * $qty;
                break;
            }
        }
    }
    // ... Shipping & Discount Logic Copy ...
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
    } else {
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
    $smart_discount = 0;
    $reason = "";
    $item_count = array_sum($cart_items);
    if ($item_count > 0) {
        $discount_percent = min($item_count, 100); 
        $smart_discount = $subtotal * ($discount_percent / 100);
        $reason = "Quantity Discount ({$discount_percent}% off)";
    }
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
    $tax = ($subtotal - $smart_discount + $shipping_cost) * 0.18;
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
    
    $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    // --- CHECKOUT LOGIC ---
    if ($_POST['action'] === 'checkout') {
        if (!isset($_SESSION['user'])) {
             if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                 echo json_encode(['success' => false, 'message' => 'Please login to checkout.']);
                 exit;
             } else {
                 header("Location: auth.php");
                 exit;
             }
        }
        $user_id = $_SESSION['user']['id'];
        if(empty($_SESSION['cart'])) {
             if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                 echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
                 exit;
             }
             header("Location: cart.php"); 
             exit;
        }

        // Get address data from POST
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $street = $_POST['street'] ?? '';
        $city = $_POST['city'] ?? '';
        $postcode = $_POST['postcode'] ?? '';
        $payment_method = $_POST['payment_method'] ?? 'cod';
        $payment_info = $_POST['payment_info'] ?? null; // Capture payment info JSON

        $totals = calculateCartTotals($_SESSION['cart'], $products);

        try {
            $pdo->beginTransaction();
            
            // Get or create active cart
            $cart_id = getActiveCartId($user_id);
            
            // If no ACTIVE cart found by user_id, check by session_id (active or inactive)
            if (!$cart_id) {
                $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE session_id = ? AND is_active = TRUE ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([session_id()]);
                $cart_id = $stmt->fetchColumn();
                
                if ($cart_id) {
                    // Link to user if needed
                    $pdo->prepare("UPDATE sales_cart SET user_id = ? WHERE id = ?")->execute([$user_id, $cart_id]);
                }
            }
            
            // Only create if still no cart found
            if (!$cart_id) {
                $stmtCart = $pdo->prepare("INSERT INTO sales_cart (user_id, session_id, is_active, created_at) VALUES (?, ?, TRUE, NOW()) RETURNING id");
                $stmtCart->execute([$user_id, session_id()]);
                $cart_id = $stmtCart->fetchColumn();
            }
            
            // 1. Save Address to sales_cart_address (without telephone - not in schema)
            $pdo->prepare("DELETE FROM sales_cart_address WHERE cart_id = ?")->execute([$cart_id]);
            $stmtAddr = $pdo->prepare("
                INSERT INTO sales_cart_address 
                (cart_id, address_type, firstname, lastname, email, street, city, postcode) 
                VALUES (?, 'shipping', ?, ?, ?, ?, ?, ?)
            ");
            $stmtAddr->execute([$cart_id, $firstname, $lastname, $email, $street, $city, $postcode]);
            
            // 2. Save Shipping to sales_cart_shipping
            $pdo->prepare("DELETE FROM sales_cart_shipping WHERE cart_id = ?")->execute([$cart_id]);
            $stmtShip = $pdo->prepare("
                INSERT INTO sales_cart_shipping (cart_id, method_code, price) 
                VALUES (?, ?, ?)
            ");
            $stmtShip->execute([$cart_id, $totals['selected_method'], $totals['shipping_cost']]);
            
            // 3. Save Payment to sales_cart_payment
            $pdo->prepare("DELETE FROM sales_cart_payment WHERE cart_id = ?")->execute([$cart_id]);
            $stmtPay = $pdo->prepare("
                INSERT INTO sales_cart_payment (cart_id, method_code, payment_info) 
                VALUES (?, ?, ?)
            ");
            $stmtPay->execute([$cart_id, $payment_method, $payment_info]);
            
            // 4. Create Order
            $increment_id = time() . '-' . $user_id; 
            
            // Calculate total discount (Smart + Promo)
            $total_discount = $totals['smart_discount'] + $totals['promo_discount'];
            $coupon_code = $totals['promo_code'];

            $stmtOrder = $pdo->prepare("
                INSERT INTO sales_order 
                (increment_id, user_id, status, subtotal, shipping_amount, tax_amount, discount_amount, coupon_code, grand_total, customer_email, created_at) 
                VALUES (?, ?, 'processing', ?, ?, ?, ?, ?, ?, ?, NOW()) 
                RETURNING order_id
            ");
            $customer_email = $_SESSION['user']['email'];
            $stmtOrder->execute([
                $increment_id, 
                $user_id, 
                $totals['subtotal'], 
                $totals['shipping_cost'], 
                $totals['tax'], 
                $total_discount,
                $coupon_code,
                $totals['total'], 
                $customer_email
            ]);
            $order_id = $stmtOrder->fetchColumn();

            // 5. Insert Order Items
            $stmtItem = $pdo->prepare("
                INSERT INTO sales_order_products 
                (order_id, product_id, name, price, quantity, total_price) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach($_SESSION['cart'] as $pid => $qty) {
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
                     $stmtItem->execute([$order_id, $pid, $product['title'], $price, $qty, $total_price]);
                 }
            }
            
            // 6. Insert Address to sales_order_address (with telephone from POST)
            $stmtOrderAddr = $pdo->prepare("
                INSERT INTO sales_order_address 
                (order_id, address_type, firstname, lastname, street, city, postcode, telephone)
                VALUES (?, 'shipping', ?, ?, ?, ?, ?, ?)
            ");
            $stmtOrderAddr->execute([$order_id, $firstname, $lastname, $street, $city, $postcode, $phone]);
            
            // 7. Copy Payment to sales_order_payment
            $stmtOrderPay = $pdo->prepare("
                INSERT INTO sales_order_payment (order_id, method, payment_info)
                SELECT ?, method_code, payment_info 
                FROM sales_cart_payment 
                WHERE cart_id = ?
            ");
            $stmtOrderPay->execute([$order_id, $cart_id]);
            
            // 7.5. Copy Shipping Method to sales_order_payment
            $stmtShipping = $pdo->prepare("
                UPDATE sales_order_payment 
                SET shipping_method = (
                    SELECT method_code FROM sales_cart_shipping WHERE cart_id = ?
                )
                WHERE order_id = ?
            ");
            $stmtShipping->execute([$cart_id, $order_id]);
            
            // 8. Deactivate cart (keep for history)
            $pdo->prepare("UPDATE sales_cart SET is_active = FALSE WHERE id = ?")->execute([$cart_id]);

            $pdo->commit();
            
            $_SESSION['cart'] = [];
            unset($_SESSION['shipping_method']);
            unset($_SESSION['applied_promo']);
            
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
            die("Order creation failed: " . $e->getMessage());
        }
    }
    // --- UPDATES ---
    // --- UPDATE QUANTITY (from products page quick add) ---
    elseif ($_POST['action'] === 'update_qty') {
        $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;
        
        // Get current quantity
        $current_qty = isset($_SESSION['cart'][$p_id]) ? $_SESSION['cart'][$p_id] : 0;
        $new_qty = $current_qty + $change;
        
        // Ensure quantity is within bounds
        if ($new_qty > 0) {
            $_SESSION['cart'][$p_id] = $new_qty;
        } else {
            unset($_SESSION['cart'][$p_id]);
        }
        
        // SYNC TO DB IMMEDIATELY
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        syncCartToDb($user_id, $_SESSION['cart'], session_id());

        $totals = calculateCartTotals($_SESSION['cart'], $products);
        echo json_encode(['success' => true, 'summary' => [
            'count' => $totals['item_count'],
            'subtotal' => $totals['subtotal'],
            'total' => $totals['total']
        ]]);
        exit;
    }
    // --- UPDATE QUANTITY ---
    elseif ($_POST['action'] === 'update') {
        $qty = (int)$_POST['quantity'];
        if ($qty > 0) {
            $_SESSION['cart'][$p_id] = $qty;
        } else {
            unset($_SESSION['cart'][$p_id]);
        }
        
        // SYNC TO DB IMMEDIATELY
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        syncCartToDb($user_id, $_SESSION['cart'], session_id());

        $totals = calculateCartTotals($_SESSION['cart'], $products);
        echo json_encode(['success' => true, 'totals' => $totals]);
        exit;
    }

    // --- REMOVE ITEM ---
    elseif ($_POST['action'] === 'remove') {
        unset($_SESSION['cart'][$p_id]);
        
        // SYNC TO DB IMMEDIATELY
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        syncCartToDb($user_id, $_SESSION['cart'], session_id());

        $totals = calculateCartTotals($_SESSION['cart'], $products);
        echo json_encode(['success' => true, 'totals' => $totals]);
        exit;
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
    
    // --- SYNC TO DB AFTER ANY UPDATE ---
    $current_session_id = session_id(); // Get PHP Session ID
    if (isset($_SESSION['user'])) {
        syncCartToDb($_SESSION['user']['id'], $_SESSION['cart'], $current_session_id);
    } else {
        // Guest Sync
        if (!empty($_SESSION['cart'])) {
             syncCartToDb(null, $_SESSION['cart'], $current_session_id);
        }
    }

    // AJAX Response
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        $totals = calculateCartTotals($_SESSION['cart'], $products);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'summary' => [
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
        ]]);
        exit;
    }
    header("Location: cart.php");
    exit;
}
include '../includes/header.php';
?>
<?php include '../templates/cart.php'; ?>
<?php include '../includes/footer.php'; ?>