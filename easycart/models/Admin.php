<?php
// models/Admin.php

class Admin {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPDO() {
        return $this->pdo;
    }

    /**
     * Authenticate an admin user
     */
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    /**
     * Check if a user ID has admin privileges
     */
    public function isAdmin($adminId) {
        $stmt = $this->pdo->prepare("SELECT id FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        return (bool)$stmt->fetchColumn();
    }

    public function getDashboardStats() {
        $stats = [];
        try {
            // Total products
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM catalog_product_entity");
            $stats['total_products'] = $stmt->fetchColumn();
            
            // Low stock products (less than 10)
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM catalog_product_entity WHERE stock_qty < 10");
            $stats['low_stock'] = $stmt->fetchColumn();
            
            // Total orders
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM sales_order");
            $stats['total_orders'] = $stmt->fetchColumn();
            
            // Pending orders
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM sales_order WHERE status = 'pending'");
            $stats['pending_orders'] = $stmt->fetchColumn();
            
            // Total revenue
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(grand_total), 0) FROM sales_order WHERE status != 'cancelled'");
            $stats['total_revenue'] = $stmt->fetchColumn();
            
            // Total users
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
            $stats['total_users'] = $stmt->fetchColumn();
            
            // Recent orders
            $stmt = $this->pdo->query("
                SELECT o.order_id, o.increment_id, o.grand_total, o.status, o.created_at, u.name as customer_name
                FROM sales_order o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT 10
            ");
            $stats['recent_orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top selling products
            $stmt = $this->pdo->query("
                SELECT p.name, p.image, SUM(op.quantity) as total_sold, SUM(op.total_price) as revenue
                FROM sales_order_products op
                JOIN sales_order o ON op.order_id = o.order_id
                JOIN catalog_product_entity p ON op.product_id = p.entity_id
                WHERE o.status != 'cancelled'
                GROUP BY p.entity_id, p.name, p.image
                ORDER BY total_sold DESC
                LIMIT 10
            ");
            $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['top_products'] = [];
            if (!empty($candidates)) {
                $maxSold = $candidates[0]['total_sold'];
                foreach ($candidates as $product) {
                    if ($product['total_sold'] == $maxSold) {
                        $stats['top_products'][] = $product;
                    } else {
                        break;
                    }
                }
            }
            
            // Low stock products list
            $stmt = $this->pdo->query("
                SELECT entity_id, name, sku, stock_qty, price
                FROM catalog_product_entity
                WHERE stock_qty < 10
                ORDER BY stock_qty ASC
                LIMIT 10
            ");
            $stats['low_stock_products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Category-wise Stock Data (for Chart)
            $stmt = $this->pdo->query("
                SELECT c.name as category, SUM(p.stock_qty) as total_stock
                FROM catalog_product_entity p
                LEFT JOIN catalog_category_entity c ON p.category_id = c.entity_id
                GROUP BY c.name
                ORDER BY total_stock DESC
            ");
            $stats['category_stock'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Dashboard stats error: " . $e->getMessage());
        }
        return $stats;
    }

    public function getProducts($search = '', $limit = 10, $offset = 0) {
        $sql = "
            SELECT p.*, c.name as category_name
            FROM catalog_product_entity p
            LEFT JOIN catalog_category_entity c ON p.category_id = c.entity_id
        ";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE p.name ILIKE ? OR p.sku ILIKE ? ";
            $params = ["%$search%", "%$search%"];
        }
        
        $sql .= " ORDER BY p.entity_id ASC ";
        $sql .= " LIMIT ? OFFSET ? ";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalProductsCount($search = '') {
        $sql = "SELECT COUNT(*) FROM catalog_product_entity p";
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE p.name ILIKE ? OR p.sku ILIKE ? ";
            $params = ["%$search%", "%$search%"];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getOrders($limit = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email,
                   (SELECT COALESCE(SUM(quantity), 0) FROM sales_order_products WHERE order_id = o.order_id) as total_item_count
            FROM sales_order o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOrdersCount() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM sales_order")->fetchColumn();
    }

    public function deleteProduct($id) {
        $stmt = $this->pdo->prepare("DELETE FROM catalog_product_entity WHERE entity_id = ?");
        return $stmt->execute([$id]);
    }

    public function getProductById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM catalog_product_entity WHERE entity_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        return $this->pdo->query("SELECT * FROM catalog_category_entity")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrands() {
        return $this->pdo->query("SELECT * FROM catalog_brand_entity")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveProduct($id, $data) {
        if ($id) {
            $stmt = $this->pdo->prepare("
                UPDATE catalog_product_entity 
                SET sku=?, name=?, price=?, stock_qty=?, category_id=?, brand_id=?, description=?, image=?, is_featured=?, updated_at=NOW()
                WHERE entity_id=?
            ");
            return $stmt->execute([
                $data['sku'], $data['name'], $data['price'], $data['stock_qty'], 
                $data['category_id'], $data['brand_id'], $data['description'], $data['image'], $data['is_featured'], $id
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO catalog_product_entity 
                (sku, name, price, stock_qty, category_id, brand_id, description, image, is_featured, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            return $stmt->execute([
                $data['sku'], $data['name'], $data['price'], $data['stock_qty'], 
                $data['category_id'], $data['brand_id'], $data['description'], $data['image'], $data['is_featured']
            ]);
        }
    }

    public function getOrderDetails($id) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email 
            FROM sales_order o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.order_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($id) {
        $stmt = $this->pdo->prepare("
            SELECT i.*, p.sku, p.image 
            FROM sales_order_products i 
            LEFT JOIN catalog_product_entity p ON i.product_id = p.entity_id
            WHERE i.order_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderAddress($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sales_order_address WHERE order_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderPayment($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sales_order_payment WHERE order_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE sales_order SET status = ? WHERE order_id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function saveOrderNotes($id, $notes) {
        $stmt = $this->pdo->prepare("UPDATE sales_order SET admin_notes = ? WHERE order_id = ?");
        return $stmt->execute([$notes, $id]);
    }

    /**
     * Create a new administrator account
     */
    public function createAdmin($name, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $hashedPassword]);
        } catch (PDOException $e) {
            error_log("Admin creation error: " . $e->getMessage());
            return false;
        }
    }
}
?>
