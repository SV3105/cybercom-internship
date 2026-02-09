<?php
// controllers/CartController.php
// Cart controller for viewing and updating cart

class CartController {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        global $pdo;
        require_once __DIR__ . '/../models/cart.php';
        require_once __DIR__ . '/../models/product.php';
        $this->cartModel = new Cart($pdo);
        $this->productModel = new Product($pdo); // For product data access
    }
    
    /**
     * Display cart page
     */
    public function index() {
        // Handle guest vs logged-in
        // Note: The requirements said "Restrict cart PAGE access to logged-in users only"
        // But also "allow AJAX requests for guest users". 
        // We'll follow the original logic.
        $isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (!isset($_SESSION['user']) && !$isAjaxRequest) {
            header("Location: auth");
            exit;
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Sync Logic on Load (if logged in)
        if (isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            $db_cart = $this->cartModel->loadCartFromDb($user_id);
            
            $merged = $db_cart;
            foreach ($_SESSION['cart'] as $pid => $qty) {
                if (isset($merged[$pid])) {
                     $merged[$pid] = $qty; // Session wins (most recent local change)
                } else {
                     $merged[$pid] = $qty;
                }
            }
            if (empty($_SESSION['cart']) && !empty($db_cart)) {
                $_SESSION['cart'] = $db_cart;
            }
        }
        
        // Calculate totals for view
        $products = $this->productModel->getAllProducts();
        $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;
        $promo_code = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
        
        $cart_totals = $this->cartModel->calculateTotals($_SESSION['cart'], $products, $shipping_method, $promo_code);
        
        // Store updated method back to session if changed by logic
        if ($cart_totals['selected_method'] !== $shipping_method) {
            $_SESSION['shipping_method'] = $cart_totals['selected_method'];
        }

        // View Variables
        $title = "Shopping Cart - EasyCart";
        $page = "cart";
        $extra_css = "cart.css";
        $base_path = '';
        
        // If POST request (AJAX updates redirected here commonly in old code), handle it
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $this->handlePostAction();
            return;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cart/cart.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Handle POST actions (AJAX updates)
     */
    private function handlePostAction() {
        $action = $_POST['action'];
        $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        // Initialize session cart if needed
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        $response = ['success' => false];
        
        switch ($action) {
            case 'update_qty': // Add/Subtract relatively
                $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;
                $current_qty = isset($_SESSION['cart'][$p_id]) ? $_SESSION['cart'][$p_id] : 0;
                $new_qty = $current_qty + $change;
                
                if ($new_qty > 0) {
                    $_SESSION['cart'][$p_id] = $new_qty;
                } else {
                    unset($_SESSION['cart'][$p_id]);
                }
                $response = $this->finalizeUpdate();
                break;
                
            case 'update': // Set absolute quantity
                $qty = (int)$_POST['quantity'];
                if ($qty > 0) {
                    $_SESSION['cart'][$p_id] = $qty;
                } else {
                    unset($_SESSION['cart'][$p_id]);
                }
                $response = $this->finalizeUpdate(true);
                break;
                
            case 'remove':
                unset($_SESSION['cart'][$p_id]);
                $response = $this->finalizeUpdate(true);
                break;
                
            case 'set_shipping':
                if (isset($_POST['method'])) {
                    $_SESSION['shipping_method'] = $_POST['method'];
                }
                // Recalculate only
                $response = $this->finalizeUpdate(true);
                break;
                
            case 'clear':
                $_SESSION['cart'] = [];
                unset($_SESSION['shipping_method']);
                unset($_SESSION['applied_promo']);
                $response = $this->finalizeUpdate(true);
                break;
                
            case 'apply_promo':
                if (isset($_POST['code'])) {
                    $code = trim($_POST['code']);
                    $allowed_codes = ['SAVE5', 'SAVE10', 'SAVE15', 'SAVE20'];
                    if (in_array($code, $allowed_codes)) {
                        $_SESSION['applied_promo'] = $code;
                    } else {
                         $_SESSION['applied_promo'] = null;
                    }
                }
                $response = $this->finalizeUpdate(true);
                break;
        }
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            // Fallback redirect
            header("Location: cart");
            exit;
        }
    }
    
    /**
     * Common logic to sync DB, calculate totals, and prepare response
     */
    private function finalizeUpdate($full_totals = false) {
        // Sync to DB
        $current_session_id = session_id();
        $user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
        
        // Sync if logged in or if guest has items
        $promo_code_to_sync = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
        $shipping_method_to_sync = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;
        
        if ($user_id || !empty($_SESSION['cart'])) {
            $this->cartModel->syncCartToDb($user_id, $_SESSION['cart'], $current_session_id, $promo_code_to_sync, $shipping_method_to_sync);
        }
        
        // Calculate Totals
        $products = $this->productModel->getAllProducts();
        $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;
        $promo_code = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
        
        $totals = $this->cartModel->calculateTotals($_SESSION['cart'], $products, $shipping_method, $promo_code);
        
        // Update session shipping method if changed by logic
         if ($totals['selected_method'] !== $shipping_method) {
            $_SESSION['shipping_method'] = $totals['selected_method'];
        }
        
        $summary = [
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
        ];
        
        $response = ['success' => true];
        
        if ($full_totals) {
            $response['totals'] = $totals; // Old generic key often used
            $response['summary'] = $summary; // Unified summary
        } else {
            $response['summary'] = [
                'count' => $totals['item_count'],
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total']
            ];
        }
        
        return $response;
    }
}
?>
