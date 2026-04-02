<?php
// models/Vendor.php

class Vendor {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPDO() {
        return $this->pdo;
    }

    /**
     * Authenticate a vendor
     */
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM vendors WHERE email = ?");
        $stmt->execute([$email]);
        $vendor = $stmt->fetch();

        if ($vendor && password_verify($password, $vendor['password'])) {
            return $vendor;
        }
        return false;
    }

    /**
     * Register a new vendor
     */
    public function register($name, $email, $password, $store_name) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM vendors WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered.'];
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO vendors (name, email, password, store_name) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword, $store_name])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Registration failed.'];
            }
        } catch (PDOException $e) {
            error_log("Vendor registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'System error occurred.'];
        }
    }

    /**
     * Get basic stats for the vendor dashboard
     */
    public function getDashboardStats($vendorId) {
        $stats = [
            'total_products' => 0,
            'total_orders' => 0,
            'total_revenue' => 0,
            'pending_orders' => 0,
            'recent_orders' => []
        ];
        
        try {
            // Total products of vendor
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM catalog_product_entity WHERE vendor_id = ?");
            $stmt->execute([$vendorId]);
            $stats['total_products'] = (int)$stmt->fetchColumn();

            // Distinct orders containing vendor's products
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT op.order_id) 
                FROM sales_order_products op
                JOIN catalog_product_entity p ON op.product_id = p.entity_id
                WHERE p.vendor_id = ?
            ");
            $stmt->execute([$vendorId]);
            $stats['total_orders'] = (int)$stmt->fetchColumn();

            // Pending orders for vendor (distinct orders)
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT o.order_id)
                FROM sales_order o
                JOIN sales_order_products op ON o.order_id = op.order_id
                JOIN catalog_product_entity p ON op.product_id = p.entity_id
                WHERE p.vendor_id = ? AND o.status = 'pending'
            ");
            $stmt->execute([$vendorId]);
            $stats['pending_orders'] = (int)$stmt->fetchColumn();

            // Total revenue from vendor's products
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(op.total_price), 0)
                FROM sales_order_products op
                JOIN catalog_product_entity p ON op.product_id = p.entity_id
                JOIN sales_order o ON op.order_id = o.order_id
                WHERE p.vendor_id = ? AND o.status != 'cancelled'
            ");
            $stmt->execute([$vendorId]);
            $stats['total_revenue'] = (float)$stmt->fetchColumn();

            // Recent orders (last 5)
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT o.order_id, o.increment_id, o.status, o.created_at, u.name as customer_name
                FROM sales_order o
                JOIN users u ON o.user_id = u.id
                JOIN sales_order_products op ON o.order_id = op.order_id
                JOIN catalog_product_entity p ON op.product_id = p.entity_id
                WHERE p.vendor_id = ?
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$vendorId]);
            $stats['recent_orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Vendor dashboard stats error: " . $e->getMessage());
        }
        return $stats;
    }

    /**
     * Get vendor's products
     */
    public function getProducts($vendorId, $search = '', $limit = 10, $offset = 0) {
        $sql = "
            SELECT p.*, c.name as category_name
            FROM catalog_product_entity p
            LEFT JOIN catalog_category_entity c ON p.category_id = c.entity_id
            WHERE p.vendor_id = ?
        ";
        
        $params = [$vendorId];
        if (!empty($search)) {
            $sql .= " AND (p.name ILIKE ? OR p.sku ILIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY p.entity_id DESC ";
        $sql .= " LIMIT ? OFFSET ? ";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalProductsCount($vendorId, $search = '') {
        $sql = "SELECT COUNT(*) FROM catalog_product_entity p WHERE p.vendor_id = ?";
        $params = [$vendorId];
        if (!empty($search)) {
            $sql .= " AND (p.name ILIKE ? OR p.sku ILIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Delete vendor product
     */
    public function deleteProduct($vendorId, $productId) {
        $stmt = $this->pdo->prepare("DELETE FROM catalog_product_entity WHERE entity_id = ? AND vendor_id = ?");
        return $stmt->execute([$productId, $vendorId]);
    }

    /**
     * Get product by ID (ensuring it belongs to vendor)
     */
    public function getProductById($vendorId, $productId) {
        $stmt = $this->pdo->prepare("SELECT * FROM catalog_product_entity WHERE entity_id = ? AND vendor_id = ?");
        $stmt->execute([$productId, $vendorId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getCategories() {
        return $this->pdo->query("SELECT * FROM catalog_category_entity")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrands() {
        return $this->pdo->query("SELECT * FROM catalog_brand_entity")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save product for vendor
     */
    public function saveProduct($vendorId, $id, $data) {
        if ($id) {
            // Verify ownership first
            $check = $this->getProductById($vendorId, $id);
            if (!$check) return false;

            $stmt = $this->pdo->prepare("
                UPDATE catalog_product_entity 
                SET sku=?, name=?, price=?, stock_qty=?, category_id=?, brand_id=?, description=?, image=?, is_featured=?, updated_at=NOW()
                WHERE entity_id=? AND vendor_id=?
            ");
            return $stmt->execute([
                $data['sku'], $data['name'], $data['price'], $data['stock_qty'], 
                $data['category_id'], $data['brand_id'], $data['description'], $data['image'], $data['is_featured'], $id, $vendorId
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO catalog_product_entity 
                (vendor_id, sku, name, price, stock_qty, category_id, brand_id, description, image, is_featured, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            return $stmt->execute([
                $vendorId, $data['sku'], $data['name'], $data['price'], $data['stock_qty'], 
                $data['category_id'], $data['brand_id'], $data['description'], $data['image'], $data['is_featured']
            ]);
        }
    }

    /**
     * Get orders containing vendor's products
     */
    public function getOrders($vendorId, $limit = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT o.*, u.name as customer_name, u.email as customer_email,
                   (SELECT COALESCE(SUM(op_inner.quantity), 0) 
                    FROM sales_order_products op_inner 
                    JOIN catalog_product_entity p_inner ON op_inner.product_id = p_inner.entity_id
                    WHERE op_inner.order_id = o.order_id AND p_inner.vendor_id = ?) as total_item_count
            FROM sales_order o 
            LEFT JOIN users u ON o.user_id = u.id 
            JOIN sales_order_products op ON o.order_id = op.order_id
            JOIN catalog_product_entity p ON op.product_id = p.entity_id
            WHERE p.vendor_id = ?
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$vendorId, $vendorId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOrdersCount($vendorId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT o.order_id)
            FROM sales_order o
            JOIN sales_order_products op ON o.order_id = op.order_id
            JOIN catalog_product_entity p ON op.product_id = p.entity_id
            WHERE p.vendor_id = ?
        ");
        $stmt->execute([$vendorId]);
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Get order items (only vendor's products for a specific order)
     */
    public function getOrderItems($vendorId, $orderId) {
        $stmt = $this->pdo->prepare("
            SELECT op.*, p.sku, p.image 
            FROM sales_order_products op
            JOIN catalog_product_entity p ON op.product_id = p.entity_id
            WHERE op.order_id = ? AND p.vendor_id = ?
        ");
        $stmt->execute([$orderId, $vendorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Coupons Logic
    public function getCoupons($vendorId) {
        $stmt = $this->pdo->prepare("SELECT * FROM vendor_coupons WHERE vendor_id = ? ORDER BY created_at DESC");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCouponById($vendorId, $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM vendor_coupons WHERE id = ? AND vendor_id = ?");
        $stmt->execute([$id, $vendorId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteCoupon($vendorId, $id) {
        $stmt = $this->pdo->prepare("DELETE FROM vendor_coupons WHERE id = ? AND vendor_id = ?");
        return $stmt->execute([$id, $vendorId]);
    }

    public function saveCoupon($vendorId, $id, $data) {
        if ($id) {
            $check = $this->getCouponById($vendorId, $id);
            if (!$check) return false;

            $stmt = $this->pdo->prepare("
                UPDATE vendor_coupons 
                SET code=?, discount_type=?, discount_value=?, min_order_amount=?, valid_until=?, is_active=?
                WHERE id=? AND vendor_id=?
            ");
            return $stmt->execute([
                $data['code'], $data['discount_type'], $data['discount_value'], $data['min_order_amount'], 
                $data['valid_until'] ?: null, $data['is_active'], $id, $vendorId
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO vendor_coupons 
                (vendor_id, code, discount_type, discount_value, min_order_amount, valid_until, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $vendorId, $data['code'], $data['discount_type'], $data['discount_value'], 
                $data['min_order_amount'], $data['valid_until'] ?: null, $data['is_active']
            ]);
        }
    }
}
?>
