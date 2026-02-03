<?php
// php/invoice.php
require_once '../includes/db.php';

$order_id = isset($_GET['id']) ? $_GET['id'] : null;
$order = null;

if ($order_id) {
    try {
        // 1. Fetch Order Details (Join with User for legacy, but we should use order address from sales_order_address if available)
        // For now, assuming basic user linkage or direct stored fields
        $stmt = $pdo->prepare("SELECT * FROM sales_order WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $orderData = $stmt->fetch();

        if ($orderData) {
             // 2. Fetch Order Items
            $stmtItems = $pdo->prepare("SELECT * FROM sales_order_products WHERE order_id = ?");
            $stmtItems->execute([$order_id]);
            $items = $stmtItems->fetchAll();

             // 3. Prepare Data Object for Template
            $order = [
                'id' => $orderData['increment_id'] ?? $orderData['order_id'],
                'date' => date("F j, Y, g:i a", strtotime($orderData['created_at'])),
                'items' => [],
                // Add billing info if available in sales_order or sales_order_address
                'billing_name' => $orderData['customer_name'] ?? 'Walk-in Customer', // Should come from order record
                'billing_email' => $orderData['customer_email'] ?? '',
                'total' => $orderData['grand_total'],
                'subtotal' => $orderData['subtotal'],
                'tax' => $orderData['tax_amount']
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
