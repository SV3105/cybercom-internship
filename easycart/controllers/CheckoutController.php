<?php
// controllers/CheckoutController.php
// Handles checkout process and order placement

class CheckoutController {
    private $cartModel;
    private $productModel;
    private $orderModel;
    
    public function __construct() {
        global $pdo;
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Product.php';
        require_once __DIR__ . '/../models/Order.php';
        
        $this->cartModel = new Cart($pdo);
        $this->productModel = new Product($pdo);
        $this->orderModel = new Order($pdo);
    }
    
    /**
     * Display checkout page
     */
    public function index() {
        // Auth Check
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }
        
        // Empty Cart Check
        if (empty($_SESSION['cart'])) {
            header("Location: cart");
            exit;
        }
        
        $user = $_SESSION['user'];
        $user_id = $user['id'];
        
        // Calculate Totals
        $products = $this->productModel->getAllProducts();
        $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;
        $promo_code = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
        
        $cart_totals = $this->cartModel->calculateTotals($_SESSION['cart'], $products, $shipping_method, $promo_code);
        
        // Determine selected shipping method (could be default calculated one if session matches)
        // Ensure session has valid method
        if ($cart_totals['selected_method'] !== $shipping_method) {
            $_SESSION['shipping_method'] = $cart_totals['selected_method'];
        }
        
        // Prepare view variables
        $cart_items = $_SESSION['cart'];
        $subtotal = $cart_totals['subtotal'];
        $shipping_cost = $cart_totals['shipping_cost'];
        $tax = $cart_totals['tax'];
        $total = $cart_totals['total'];
        $smart_discount = $cart_totals['smart_discount'];
        $promo_discount = $cart_totals['promo_discount'] ?? 0;
        $applied_promo_code = $cart_totals['promo_code'] ?? '';
        
        // Pre-fill user data
        $prefill = [
            'firstname' => explode(' ', $user['name'])[0] ?? '',
            'lastname' => explode(' ', $user['name'], 2)[1] ?? '',
            'email' => $user['email'] ?? '',
            'phone' => $user['phone'] ?? '',
            'city' => $user['location'] ?? ''
        ];
        
        $title = "Checkout - EasyCart";
        $page = "checkout";
        $extra_css = "checkout.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/checkout/checkout.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Handle Order Placement (AJAX)
     */
    public function placeOrder() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }
        
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Please login to checkout.']);
            exit;
        }
        
        if (empty($_SESSION['cart'])) {
             echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
             exit;
        }
        
        $user_id = $_SESSION['user']['id'];
        
        // 1. Gather Data
        $address = [
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'street' => $_POST['street'] ?? '',
            'city' => $_POST['city'] ?? '',
            'postcode' => $_POST['postcode'] ?? ''
        ];
        
        $payment = [
            'method' => $_POST['payment_method'] ?? 'cod',
            'info' => $_POST['payment_info'] ?? null
        ];

        // 1.5 Server-side Payment Validation
        if ($payment['method'] === 'card') {
            $info = json_decode($payment['info'], true);
            if (!$info || !isset($info['expiry']) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $info['expiry'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid expiry date format (MM/YY).']);
                exit;
            }
            
            list($month, $year) = explode('/', $info['expiry']);
            $currentYear = (int)date('y');
            $currentMonth = (int)date('m');
            
            if ((int)$year < $currentYear || ((int)$year === $currentYear && (int)$month < $currentMonth)) {
                echo json_encode(['success' => false, 'message' => 'The payment card has expired.']);
                exit;
            }
        }
        
        // 2. Recalculate Totals (Secure source of truth)
        $products = $this->productModel->getAllProducts();
        $cart = $_SESSION['cart'] ?? [];
        
        // Stock Pre-Check
        foreach ($cart as $pId => $qty) {
            foreach ($products as $p) {
                if ($p['id'] == $pId) {
                    if ($p['stock_qty'] < $qty) {
                        echo json_encode(['success' => false, 'message' => "Sorry, " . $p['title'] . " only has " . $p['stock_qty'] . " units in stock."]);
                        exit;
                    }
                    break;
                }
            }
        }

        $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;
        $promo_code = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
        
        // Note: In a real app we might validate stock here too
        
        $totals = $this->cartModel->calculateTotals($_SESSION['cart'], $products, $shipping_method, $promo_code);
        
        // 3. Create Order
        $result = $this->orderModel->createOrder($user_id, $_SESSION['cart'], $totals, $address, $payment);
        
        if ($result['success']) {
            // Clear Cart
            $_SESSION['cart'] = [];
            unset($_SESSION['shipping_method']);
            unset($_SESSION['applied_promo']);
            
            // Deactivate DB Cart (Optional - done in Cart.php sync usually but good to be explicit or let generic sync handle empty)
            // Ideally we'd mark the specific cart ID as converted to order, but our simple sync handles "active" carts.
            // Let's just sync empty cart to DB to "clear" it effectively or deactivate it if we had that method
             $current_session_id = session_id();
             $this->cartModel->syncCartToDb($user_id, [], $current_session_id);
            
            echo json_encode(['success' => true, 'redirect' => 'order-details?id=' . $result['order_id']]); // Redirect to specific order details
        } else {
            echo json_encode(['success' => false, 'message' => 'Order placement failed: ' . $result['message']]);
        }
        exit;
    }
}
?>
