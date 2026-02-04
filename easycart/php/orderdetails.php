<?php
// php/orderdetails.php
session_start();
require_once '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

$order_id = isset($_GET['id']) ? $_GET['id'] : null;
$user_id = $_SESSION['user']['id'];

if (!$order_id) {
    header("Location: orders.php");
    exit;
}

$order = null;

try {
    // 1. Fetch Order Details with User Info
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
        FROM sales_order o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $orderData = $stmt->fetch();

    if (!$orderData) {
        die("Order not found or you don't have permission to view it.");
    }

    // 2. Fetch Order Items with Product Details
    $stmtItems = $pdo->prepare("
        SELECT op.*, p.image 
        FROM sales_order_products op
        LEFT JOIN catalog_product_entity p ON op.product_id = p.entity_id
        WHERE op.order_id = ?
    ");
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll();

    // 3. Fetch Shipping Address
    $stmtAddr = $pdo->prepare("
        SELECT * FROM sales_order_address 
        WHERE order_id = ? AND address_type = 'shipping'
    ");
    $stmtAddr->execute([$order_id]);
    $address = $stmtAddr->fetch();

    // 4. Fetch Payment Info
    $stmtPay = $pdo->prepare("SELECT * FROM sales_order_payment WHERE order_id = ?");
    $stmtPay->execute([$order_id]);
    $payment = $stmtPay->fetch();

    // Payment Method Labels
    $paymentMethodLabels = [
        'cash' => 'Cash on Delivery',
        'card' => 'Credit / Debit Card',
        
    ];
    
    // Shipping Method Labels
    $shippingMethodLabels = [
        'standard' => 'Standard Shipping',
        'express' => 'Express Delivery',
        'white_glove' => 'White Glove Service',
        'freight' => 'Freight Shipping'
    ];
    
    // Determine payment and shipping methods
    $method = $payment['method'] ?? 'unknown';
    $storedShippingMethod = $payment['shipping_method'] ?? null;
    
    // Set payment method
    if (isset($paymentMethodLabels[$method])) {
        $paymentMethod = $paymentMethodLabels[$method];
    } else {
        $paymentMethod = ucfirst($method);
    }
    
    // Set shipping method from stored value or default
    if ($storedShippingMethod && isset($shippingMethodLabels[$storedShippingMethod])) {
        $shippingMethod = $shippingMethodLabels[$storedShippingMethod];
    } else {
        $shippingMethod = 'Standard Shipping';
    }

    // 5. Determine Order Status Timeline
    $statusSteps = [
        'pending' => ['Pending', 1],
        'processing' => ['Processing', 2],
        'transit' => ['In Transit', 3],
        'delivered' => ['Delivered', 4]
    ];
    
    $currentStatus = strtolower($orderData['status']);
    $currentStep = $statusSteps[$currentStatus][1] ?? 1;

    // 6. Format Items
    $formattedItems = [];
    foreach ($items as $item) {
      
        $imagePath = '../images/placeholder.jpg'; // Default
        
        if (!empty($item['image'])) {
            // Check if it's already a full path or just filename
            if (strpos($item['image'], '/') === false) {
                // Just filename like 'fan.png'
                $imagePath = '../images/' . $item['image'];
            } elseif (strpos($item['image'], 'images/') === 0) {
                // Relative path like 'images/fan.png'
                $imagePath = '../' . $item['image'];
            } else {
                // Already has path
                $imagePath = $item['image'];
            }
        }
        
        $formattedItems[] = [
            'name' => $item['name'],
            'qty' => $item['quantity'],
            'price' => $item['price'],
            'total' => $item['total_price'],
            'image' => $imagePath
        ];
    }

    // 7. Prepare Order Object
    $order = [
        'id' => $orderData['increment_id'] ?? $orderData['order_id'],
        'db_id' => $orderData['order_id'],
        'date' => date("F j, Y, g:i a", strtotime($orderData['created_at'])),
        'status' => ucfirst($orderData['status']),
        'status_code' => $currentStatus,
        'current_step' => $currentStep,
        'items' => $formattedItems,
        
        // Customer Info
        'customer_name' => $orderData['customer_name'],
        'customer_email' => $orderData['customer_email'],
        'customer_phone' => $orderData['customer_phone'],
        
        // Address
        'shipping_address' => [
            'name' => ($address['firstname'] ?? '') . ' ' . ($address['lastname'] ?? ''),
            'street' => $address['street'] ?? '',
            'city' => $address['city'] ?? '',
            'postcode' => $address['postcode'] ?? '',
            'phone' => $address['telephone'] ?? ''
        ],
        
        // Payment
        'payment_method' => $paymentMethod,
        'payment_status' => ($method == 'cash') 
            ? ($currentStatus == 'delivered' ? 'Paid' : 'Pending')  // COD: Paid only when delivered
            : (($currentStatus == 'delivered' || $currentStatus == 'processing') ? 'Paid' : 'Pending'), // Online: Paid when processing/delivered
        'shipping_method' => $shippingMethod,
        
        // Pricing
        'subtotal' => $orderData['subtotal'],
        'discount' => $orderData['discount_amount'] ?? 0,
        'coupon' => $orderData['coupon_code'] ?? '',
        'shipping' => $orderData['shipping_amount'],
        'tax' => $orderData['tax_amount'],
        'total' => $orderData['grand_total']
    ];

} catch (PDOException $e) {
    die("Error fetching order details: " . $e->getMessage());
}

$title = "Order #" . $order['id'] . " - EasyCart";
$base_path = "../";
$page = "orderdetails";
$extra_css = "orderdetails.css";

include '../includes/header.php';
?>

<?php include '../templates/orderdetails.php'; ?>

<?php include '../includes/footer.php'; ?>
