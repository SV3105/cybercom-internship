<?php
// controllers/OrderController.php
// Handles order history and details

class OrderController {
    private $orderModel;
    
    public function __construct() {
        global $pdo;
        require_once __DIR__ . '/../models/Order.php';
        $this->orderModel = new Order($pdo);
    }
    
    /**
     * List user orders
     */
    public function list() {
        // Auth Check
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }
        
        $user_id = $_SESSION['user']['id'];
        
        // Fetch Orders
        $rawOrders = $this->orderModel->getOrdersByUser($user_id);
        
        $orders = [];
        foreach ($rawOrders as $row) {
            $orderId = $row['order_id'];
            
            // For list view, we just need basic info. 
            // The model's getOrder() is too heavy as it fetches everything.
            // Let's rely on the raw fetch or add a getOrderSummary() to model if needed.
            // But usually listing iterates.
            // We need items for the preview in the list though.
            
            // Re-using logic from original PHP to keep it simple but cleaner
            global $pdo;
            $stmtItems = $pdo->prepare("SELECT * FROM sales_order_products WHERE order_id = ?");
            $stmtItems->execute([$orderId]);
            $items = $stmtItems->fetchAll();
    
            $formattedItems = [];
            foreach ($items as $item) {
                $formattedItems[] = [
                    'title' => $item['name'],
                    'qty'   => $item['quantity'],
                    'price' => number_format($item['price'])
                ];
            }
            
            // Get Shipping Method
            $stmtShipping = $pdo->prepare("SELECT shipping_method FROM sales_order_payment WHERE order_id = ?");
            $stmtShipping->execute([$orderId]);
            $shippingData = $stmtShipping->fetch();
            
            $shippingLabels = [
                'standard' => 'Standard Shipping',
                'express' => 'Express Delivery',
                'white_glove' => 'White Glove Service',
                'freight' => 'Freight Shipping'
            ];
            $shippingMethod = $shippingData['shipping_method'] ?? 'standard';
            $shippingType = $shippingLabels[$shippingMethod] ?? 'Standard Shipping';
    
            $orders[] = [
                'id'          => $row['increment_id'] ?? $row['order_id'],
                'db_id'       => $row['order_id'],
                'date'        => date("F j, Y", strtotime($row['created_at'])),
                'status'      => ucfirst($row['status']),
                'status_code' => strtolower($row['status']),
                'shipping_type' => $shippingType,
                'items'       => $formattedItems,
                'total'       => number_format($row['grand_total'], 2)
            ];
        }

        $title = "My Orders - EasyCart";
        $page = "orders";
        $extra_css = "profile.css"; // Reuse profile styles often used for orders
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/orders/list.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * View order details
     */
    public function details() {
        // Auth Check
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }
        
        $order_id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$order_id) {
            header("Location: orders");
            exit;
        }
        
        $user_id = $_SESSION['user']['id'];
        
        // Fetch specific order safely
        // Note: getOrder needs to ensure it belongs to user
        $orderData = $this->orderModel->getOrder($order_id, $user_id);
        
        if (!$orderData) {
            // Error or Not Found
            header("Location: orders");
            exit;
        }
        
        // Process Data for View
        // 1. Payment Method
        $paymentMethodLabels = [
            'cash' => 'Cash on Delivery',
            'card' => 'Credit / Debit Card',
        ];
        $method = $orderData['payment']['method'] ?? 'unknown';
        $paymentMethod = $paymentMethodLabels[$method] ?? ucfirst($method);
        
        // 2. Status
        $currentStatus = strtolower($orderData['status']);
        $statusSteps = [
            'pending' => 1,
            'processing' => 2,
            'transit' => 3,
            'delivered' => 4
        ];
        $currentStep = $statusSteps[$currentStatus] ?? 1;
        
        // 3. Format Items
        $formattedItems = [];
        foreach ($orderData['items'] as $item) {
            $imagePath = 'images/placeholder.jpg'; // Default relative to base
            // Fetch image from product if not in order_items (though usually order items should store snapshot)
            // Original code joined with catalog_product_entity. Model's getOrder didn't do that join join yet.
            // Let's quick fix: fetch image for each item.
            
            global $pdo; // Or use ProductModel
            $stmtImg = $pdo->prepare("SELECT image FROM catalog_product_entity WHERE entity_id = ?");
            $stmtImg->execute([$item['product_id']]);
            $img = $stmtImg->fetchColumn();
            
            if ($img) {
                $imagePath = 'images/' . $img;
            }
            
            $formattedItems[] = [
                'name' => $item['name'],
                'qty' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total_price'],
                'image' => $imagePath
            ];
        }
        
        // 4. Prepare View Order Object
        $order = [
            'id' => $orderData['increment_id'] ?? $orderData['order_id'],
            'db_id' => $orderData['order_id'],
            'date' => date("F j, Y, g:i a", strtotime($orderData['created_at'])),
            'status' => ucfirst($orderData['status']),
            'status_code' => $currentStatus,
            'current_step' => $currentStep,
            'items' => $formattedItems, // Replaces raw items
            
            'shipping_address' => [
                'name' => ($orderData['address']['firstname'] ?? '') . ' ' . ($orderData['address']['lastname'] ?? ''),
                'street' => $orderData['address']['street'] ?? '',
                'city' => $orderData['address']['city'] ?? '',
                'postcode' => $orderData['address']['postcode'] ?? '',
                'phone' => $orderData['address']['telephone'] ?? ''
            ],
            
            'payment_method' => $paymentMethod,
            'payment_status' => ($method == 'cash') 
                ? ($currentStatus == 'delivered' ? 'Paid' : 'Pending')
                : (($currentStatus == 'delivered' || $currentStatus == 'processing') ? 'Paid' : 'Pending'),
                
            'subtotal' => $orderData['subtotal'],
            'discount' => $orderData['discount_amount'] ?? 0,
            'coupon' => $orderData['coupon_code'] ?? '',
            'shipping' => $orderData['shipping_amount'],
            'tax' => $orderData['tax_amount'],
            'total' => $orderData['grand_total']
        ];
        
        $title = "Order #" . $order['id'] . " - EasyCart";
        $page = "orderdetails";
        $extra_css = "orderdetails.css";
        $base_path = '';

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/orders/details.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    /**
     * View order invoice
     */
    public function invoice() {
        // Auth Check
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }
        
        $order_id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$order_id) {
            die("Invalid Order ID.");
        }
        
        $user_id = $_SESSION['user']['id'];
        
        // Fetch Order specifically checking user ownership
        $orderData = $this->orderModel->getOrder($order_id, $user_id);
        
        if (!$orderData) {
            die("Order not found or access denied.");
        }
        
        // Helper for payment label
        $paymentMethodLabels = [
            'cash' => 'Cash on Delivery',
            'card' => 'Credit / Debit Card',
            'upi' => 'UPI',
            'netbanking' => 'Net Banking'
        ];
        $method = $orderData['payment']['method'] ?? 'unknown';
        $paymentMethod = $paymentMethodLabels[$method] ?? ucfirst($method);
        
        // Prepare View Data
        $order = [
            'id' => $orderData['increment_id'] ?? $orderData['order_id'],
            'date' => date("F j, Y, g:i a", strtotime($orderData['created_at'])),
            'items' => [],
            
            // Billing/Shipping Info (using shipping address as billing for now as per current schema)
            'billing_name' => ($orderData['address']['firstname'] ?? '') . ' ' . ($orderData['address']['lastname'] ?? ''),
            'billing_email' => $orderData['customer_email'],
            'billing_address' => ($orderData['address']['street'] ?? '') . ', ' . ($orderData['address']['city'] ?? '') . ', ' . ($orderData['address']['postcode'] ?? ''),
            'billing_phone' => $orderData['address']['telephone'] ?? '',
            
            // Payment Info
            'payment_method' => $paymentMethod,
            // Simple status logic for invoice display
            'payment_status' => (strtolower($orderData['status']) == 'delivered' || strtolower($orderData['status']) == 'processing') ? 'Paid' : 'Pending',
            
            'total' => $orderData['grand_total'],
            'subtotal' => $orderData['subtotal'],
            'tax' => $orderData['tax_amount'],
            'shipping' => $orderData['shipping_amount'],
            'discount' => $orderData['discount_amount'] ?? 0,
            'coupon' => $orderData['coupon_code'] ?? ''
        ];

        foreach ($orderData['items'] as $item) {
            $order['items'][] = [
                'title' => $item['name'],
                'qty' => $item['quantity'],
                'price' => number_format($item['price'], 2)
            ];
        }

        // Render Invoice View (No header/footer layouts usually for invoice print view)
        require_once __DIR__ . '/../views/orders/invoice.php';
    }
}
?>
