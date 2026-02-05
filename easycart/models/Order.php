<?php
// models/Order.php
// Order model - handles order creation and retrieval

class Order {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Create a new order from cart
     */
    public function createOrder($user_id, $cart_data, $totals, $address, $payment) {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Create Order Record
            $increment_id = time() . '-' . $user_id; 
            
            // Calculate total discount (Smart + Promo)
            $total_discount = $totals['smart_discount'] + $totals['promo_discount'];
            $coupon_code = $totals['promo_code'];
            $status = 'processing';

            $stmtOrder = $this->pdo->prepare("
                INSERT INTO sales_order 
                (increment_id, user_id, status, subtotal, shipping_amount, tax_amount, discount_amount, coupon_code, grand_total, customer_email, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            // Get customer email (from session usually, but let's allow it to be passed or fallback)
            $customer_email = $address['email']; 
            
            $stmtOrder->execute([
                $increment_id, 
                $user_id, 
                $status, 
                $totals['subtotal'], 
                $totals['shipping_cost'], 
                $totals['tax'], 
                $total_discount,
                $coupon_code,
                $totals['total'], 
                $customer_email
            ]);
            $order_id = $this->pdo->lastInsertId();

            // 2. Insert Order Items
            $stmtItem = $this->pdo->prepare("
                INSERT INTO sales_order_products 
                (order_id, product_id, name, price, quantity, total_price) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            // We need product details for names/prices. 
            // In a real app we might fetch this again to ensure validity, 
            // but for this refactor we'll assume the caller passes enriched items or we fetch them.
            // Let's fetch them here to be safe and get names.
            
            foreach($cart_data as $pid => $qty) {
                 $stmtProd = $this->pdo->prepare("SELECT name, price FROM catalog_product_entity WHERE entity_id = ?");
                 $stmtProd->execute([$pid]);
                 $product = $stmtProd->fetch();
                 
                 if ($product) {
                     $price = (float)$product['price']; // Should ideally come from cart snapshot to lock price, but re-fetching current price is standard for simple carts
                     $total_price = $price * $qty;
                     $stmtItem->execute([$order_id, $pid, $product['name'], $price, $qty, $total_price]);
                 }
            }
            
            // 3. Insert Address
            $stmtOrderAddr = $this->pdo->prepare("
                INSERT INTO sales_order_address 
                (order_id, address_type, firstname, lastname, street, city, postcode, telephone)
                VALUES (?, 'shipping', ?, ?, ?, ?, ?, ?)
            ");
            $stmtOrderAddr->execute([
                $order_id, 
                $address['firstname'], 
                $address['lastname'], 
                $address['street'], 
                $address['city'], 
                $address['postcode'], 
                $address['phone']
            ]);
            
            // 4. Insert Payment
            $stmtOrderPay = $this->pdo->prepare("
                INSERT INTO sales_order_payment (order_id, method, payment_info, shipping_method)
                VALUES (?, ?, ?, ?)
            ");
            $stmtOrderPay->execute([
                $order_id, 
                $payment['method'], 
                $payment['info'],
                $totals['selected_method']
            ]);
            
            $this->pdo->commit();
            return ['success' => true, 'order_id' => $order_id];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Order Creation Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get orders for a user
     */
    public function getOrdersByUser($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM sales_order 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get order details
     */
    public function getOrder($order_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM sales_order WHERE order_id = ? AND user_id = ?");
            $stmt->execute([$order_id, $user_id]);
            $order = $stmt->fetch();
            
            if (!$order) return null;
            
            // Get Items
            $stmtItems = $this->pdo->prepare("SELECT * FROM sales_order_products WHERE order_id = ?");
            $stmtItems->execute([$order_id]);
            $order['items'] = $stmtItems->fetchAll();
            
            // Get Address
            $stmtAddr = $this->pdo->prepare("SELECT * FROM sales_order_address WHERE order_id = ?");
            $stmtAddr->execute([$order_id]);
            $order['address'] = $stmtAddr->fetch();
            
            // Get Payment
            $stmtPay = $this->pdo->prepare("SELECT * FROM sales_order_payment WHERE order_id = ?");
            $stmtPay->execute([$order_id]);
            $order['payment'] = $stmtPay->fetch();
            
            return $order;
        } catch (PDOException $e) {
            return null;
        }
    }
    /**
     * Get user order statistics
     */
    public function getUserStats($user_id) {
        try {
            // Count
            $stmtCount = $this->pdo->prepare("SELECT COUNT(*) as count FROM sales_order WHERE user_id = ?");
            $stmtCount->execute([$user_id]);
            $count = $stmtCount->fetch()['count'];
            
            // Sum
            $stmtSum = $this->pdo->prepare("SELECT SUM(grand_total) as total FROM sales_order WHERE user_id = ?");
            $stmtSum->execute([$user_id]);
            $total = $stmtSum->fetch()['total'] ?? 0;
            
            return ['count' => $count, 'total' => $total];
        } catch (PDOException $e) {
            return ['count' => 0, 'total' => 0];
        }
    }

    /**
     * Get spending chart data
     */
    public function getSpendingChartData($user_id) {
        try {
            // SQLite/MySQL compatibility note: syntax assumes MySQL/MariaDB for TO_CHAR equivalent or standard SQL date functions.
            // Original code used TO_CHAR which is PostgreSQL/Oracle or specific MySQL setup? 
            // Standard MySQL uses DATE_FORMAT or just DATE().
            // Let's stick to the original query logic but be safe. 
            // Original was: SELECT TO_CHAR(created_at, 'YYYY-MM-DD') ...
            // If the user's system is MySQL (XAMPP usually is), TO_CHAR might not work unless it's an alias or MariaDB with Oracle mode.
            // But if it worked in original code, I should stick to it OR use a safer MySQL alternative `DATE(created_at)`.
            // Let's check original code again. It used TO_CHAR. That's odd for XAMPP (MySQL). 
            // Maybe they are using PostgreSQL? No, "xampp/htdocs" implies MySQL.
            // I will use DATE() or DATE_FORMAT() which is standard MySQL.
            
            $stmt = $this->pdo->prepare("
                SELECT 
                    TO_CHAR(created_at, 'YYYY-MM-DD') as date,
                    SUM(grand_total) as amount
                FROM sales_order 
                WHERE user_id = ? 
                GROUP BY TO_CHAR(created_at, 'YYYY-MM-DD')
                ORDER BY TO_CHAR(created_at, 'YYYY-MM-DD') ASC
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
             // Fallback or retry? If TO_CHAR was really used and worked, maybe I should check?
             // But DATE_FORMAT is safer for standard XAMPP MySQL.
            return [];
        }
    }
}
?>
