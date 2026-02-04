<?php
// php/invoice.php
require_once '../includes/db.php';

$order_id = isset($_GET['id']) ? $_GET['id'] : null;
$order = null;

if ($order_id) {
    try {
        // 1. Fetch Order Details (Join with Users to get name)
        $stmt = $pdo->prepare("
            SELECT o.*, u.name as customer_name 
            FROM sales_order o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $orderData = $stmt->fetch();

        if ($orderData) {
             // 2. Fetch Order Items
            $stmtItems = $pdo->prepare("SELECT * FROM sales_order_products WHERE order_id = ?");
            $stmtItems->execute([$order_id]);
            $items = $stmtItems->fetchAll();

             // 3. Fetch Address
             $stmtAddr = $pdo->prepare("SELECT * FROM sales_order_address WHERE order_id = ? AND address_type = 'shipping'");
             $stmtAddr->execute([$order_id]);
             $address = $stmtAddr->fetch();

             // 4. Fetch Payment
             $stmtPay = $pdo->prepare("SELECT * FROM sales_order_payment WHERE order_id = ?");
             $stmtPay->execute([$order_id]);
             $payment = $stmtPay->fetch();
             
             // Payment Method Label Map
             $methodLabels = [
                 'cash' => 'Cash on Delivery',
                 'card' => 'Credit / Debit Card',
                 'upi' => 'UPI',
                 'netbanking' => 'Net Banking'
             ];
             $methodLabel = isset($payment['method']) ? ($methodLabels[$payment['method']] ?? ucfirst($payment['method'])) : 'Unknown';

             // 5. Prepare Data Object for Template
            $order = [
                'id' => $orderData['increment_id'] ?? $orderData['order_id'],
                'date' => date("F j, Y, g:i a", strtotime($orderData['created_at'])),
                'items' => [],
                
                // Dynamic Customer Info
                'billing_name' => ($address['firstname'] ?? '') . ' ' . ($address['lastname'] ?? ''),
                'billing_email' => $orderData['customer_email'],
                'billing_address' => ($address['street'] ?? '') . ', ' . ($address['city'] ?? '') . ', ' . ($address['postcode'] ?? ''),
                'billing_phone' => $address['telephone'] ?? '',
                
                // Dynamic Payment Info
                'payment_method' => $methodLabel,
                'payment_status' => ucfirst($orderData['status']) == 'Delivered' ? 'Paid' : (ucfirst($orderData['status']) == 'Processing' ? 'Paid' : 'Pending'), // Simple logic
                
                'total' => $orderData['grand_total'],
                'subtotal' => $orderData['subtotal'],
                'tax' => $orderData['tax_amount'],
                'shipping' => $orderData['shipping_amount'],
                'discount' => $orderData['discount_amount'] ?? 0,
                'coupon' => $orderData['coupon_code'] ?? ''
            ];

            foreach ($items as $item) {
                $order['items'][] = [
                    'title' => $item['name'],
                    'qty' => $item['quantity'],
                    'price' => number_format($item['price']) // Keeping format string for template compatibility
                ];
            }
        }

    } catch (PDOException $e) {
        die("Error fetching invoice: " . $e->getMessage());
    }
}

if (!$order) {
    die("Order not found.");
}
?>

<?php include '../templates/invoice.php'; ?>
