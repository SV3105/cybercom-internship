<?php
// php/orders.php
session_start();
require_once '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$title = "My Orders - EasyCart";
$base_path = "../";
$page = "orders";
$extra_css = "profile.css"; 

// --- FETCH ORDERS FROM DB ---
$orders = [];
try {
    // 1. Get Main Order Info
    $stmt = $pdo->prepare("SELECT * FROM sales_order WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $rawOrders = $stmt->fetchAll();

    foreach ($rawOrders as $row) {
        $orderId = $row['order_id'];
        
        // 2. Get Items for this order
        $stmtItems = $pdo->prepare("SELECT * FROM sales_order_products WHERE order_id = ?");
        $stmtItems->execute([$orderId]);
        $items = $stmtItems->fetchAll();

        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'title' => $item['name'],
                'qty'   => $item['quantity'],
                'price' => number_format($item['price']) // Format for display
            ];
        }

        $orders[] = [
            'id'          => $row['increment_id'] ?? $row['order_id'], // Use Increment ID if available, else PK
            'db_id'       => $row['order_id'], // Real PK for links (invoice etc)
            'date'        => date("F j, Y", strtotime($row['created_at'])),
            'status'      => ucfirst($row['status']),
            'status_code' => strtolower($row['status']),
            'items'       => $formattedItems,
            'total'       => number_format($row['grand_total'])
        ];
    }

} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
}

include '../includes/header.php';

// Include the Orders Template
include '../templates/orders.php';

include '../includes/footer.php';
?>
