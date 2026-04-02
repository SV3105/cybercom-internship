<?php
// controllers/VendorController.php

class VendorController {
    private $vendorModel;
    private $productModel;
    
    public function __construct($vendorModel, $productModel) {
        $this->vendorModel = $vendorModel;
        $this->productModel = $productModel;
        $this->checkVendor();
    }

    /**
     * Check if the logged-in user has vendor privileges
     */
    private function checkVendor() {
        if (!isset($_SESSION['vendor_user'])) {
            setFlash('error', 'Unauthorized access! Please login as a vendor.');
            header('Location: ' . BASE_URL . 'vendor/login');
            exit;
        }
    }
    
    /**
     * Display vendor dashboard
     */
    public function dashboard() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $stats = $this->vendorModel->getDashboardStats($vendorId);
        
        $title = "Vendor Dashboard - EasyCart";
        $page = "vendor_dashboard";
        $page_title = "Vendor Dashboard";
        $extra_css = "admin.css"; // Reuse admin styles for dashboard layout
        $base_path = '../';
        
        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/dashboard.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    /**
     * Display vendor's products
     */
    public function products() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $search = $_GET['search'] ?? '';
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;
        
        $title = "My Products - Vendor Dashboard";
        $page = "vendor_products";
        $page_title = "My Products";
        $extra_css = "admin.css"; 
        $base_path = '../';

        $products = $this->vendorModel->getProducts($vendorId, $search, $limit, $offset);
        $totalItems = $this->vendorModel->getTotalProductsCount($vendorId, $search);
        $totalPages = ceil($totalItems / $limit);
        
        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/products.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    /**
     * Display orders relevant to the vendor
     */
    public function orders() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;

        $title = "My Orders - Vendor Dashboard";
        $page = "vendor_orders";
        $page_title = "My Orders";
        $extra_css = "admin.css";
        $base_path = '../';

        $orders = $this->vendorModel->getOrders($vendorId, $limit, $offset);
        $totalItems = $this->vendorModel->getTotalOrdersCount($vendorId);
        $totalPages = ceil($totalItems / $limit);

        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/orders.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    /**
     * Delete a vendor product
     */
    public function deleteProduct() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            if ($this->vendorModel->deleteProduct($vendorId, $id)) {
                setFlash('success', 'Product deleted successfully!');
            } else {
                setFlash('error', 'Failed to delete product.');
            }
        }
        
        header('Location: ' . BASE_URL . 'vendor/products');
        exit;
    }

    /**
     * Edit Product Form
     */
    public function editProduct() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $id = $_GET['id'] ?? null;
        $product = null;

        if ($id) {
            $product = $this->vendorModel->getProductById($vendorId, $id);
            if (!$product) {
                setFlash('error', 'Product not found or unauthorized.');
                header('Location: ' . BASE_URL . 'vendor/products');
                exit;
            }
        }

        // Use the vendorModel to get categories and brands
        $categories = $this->vendorModel->getCategories();
        $brands = $this->vendorModel->getBrands();

        $title = $product ? "Edit Product" : "Add Product";
        $page = "vendor_products";
        $extra_css = "admin.css";
        $base_path = '../';

        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/productform.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    /**
     * Save Product
     */
    public function saveProduct() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $id = $_POST['entity_id'] ?? null;
        $data = [
            'sku' => $_POST['sku'] ?? '',
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'stock_qty' => $_POST['stock_qty'] ?? 0,
            'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
            'brand_id' => !empty($_POST['brand_id']) ? $_POST['brand_id'] : null,
            'description' => $_POST['description'] ?? '',
            'is_featured' => isset($_POST['is_featured']) ? 't' : 'f',
            'image' => $_POST['current_image'] ?? null
        ];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/images/';
            $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newFileName = 'vend_prod_' . time() . '_' . rand(1000, 9999) . '.' . $fileExt;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFileName)) {
                $data['image'] = $newFileName;
            }
        }

        if ($this->vendorModel->saveProduct($vendorId, $id, $data)) {
            setFlash('success', $id ? 'Product updated successfully!' : 'Product created successfully!');
        } else {
            setFlash('error', 'Error saving product.');
        }

        header('Location: ' . BASE_URL . 'vendor/products');
        exit;
    }

    /**
     * View Order Details
     */
    public function orderDetails() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: ' . BASE_URL . 'vendor/orders');
            exit;
        }

        // We need to fetch order details, then restrict items to only those belonging to vendor.
        // For simplicity, we can just use the Admin model's getOrderDetails and filter items.
        // Wait, Vendor model already has getOrderItems
        $items = $this->vendorModel->getOrderItems($vendorId, $id);
        
        if (empty($items)) {
            setFlash('error', 'Order not found or no items belong to you.');
            header('Location: ' . BASE_URL . 'vendor/orders');
            exit;
        }

        // Instead of writing a new method in vendor model for order details (unless needed),
        // we can fetch order basic info via raw PDO or an existing method.
        // I will add a simple query here for order basics.
        $stmt = $this->vendorModel->getPDO()->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email,
            a.firstname, a.lastname, a.street, a.city, a.postcode, a.telephone
            FROM sales_order o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN sales_order_address a ON o.order_id = a.order_id
            WHERE o.order_id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $title = "Order Details #" . ($order['increment_id'] ?: $order['order_id']);
        $page = "vendor_orders";
        $extra_css = "admin.css"; 
        $base_path = '../';

        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/orderdetails.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    // --- Coupons ---
    
    public function coupons() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $coupons = $this->vendorModel->getCoupons($vendorId);
        
        $title = "Coupons - Vendor Hub";
        $page = "vendor_coupons";
        $page_title = "Coupons";
        $extra_css = "admin.css";
        $base_path = '../';
        
        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/coupons.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    public function couponForm() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $id = $_GET['id'] ?? null;
        $coupon = null;

        if ($id) {
            $coupon = $this->vendorModel->getCouponById($vendorId, $id);
            if (!$coupon) {
                setFlash('error', 'Coupon not found.');
                header('Location: ' . BASE_URL . 'vendor/coupons');
                exit;
            }
        }

        $title = $coupon ? "Edit Coupon" : "Add Coupon";
        $page = "vendor_coupons";
        $page_title = $title;
        $extra_css = "admin.css";
        $base_path = '../';

        require_once __DIR__ . '/../views/layouts/vendor/header.php';
        require_once __DIR__ . '/../views/vendor/couponform.php';
        require_once __DIR__ . '/../views/layouts/vendor/footer.php';
    }

    public function saveCoupon() {
        $vendorId = $_SESSION['vendor_user']['id'];
        $id = $_POST['id'] ?? null;
        
        $data = [
            'code' => $_POST['code'],
            'discount_type' => $_POST['discount_type'],
            'discount_value' => $_POST['discount_value'],
            'min_order_amount' => $_POST['min_order_amount'] ?? 0,
            'valid_until' => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
            'is_active' => isset($_POST['is_active']) ? 't' : 'f'
        ];

        if ($this->vendorModel->saveCoupon($vendorId, $id, $data)) {
            setFlash('success', 'Coupon saved successfully.');
        } else {
            setFlash('error', 'Failed to save coupon. The code may already exist.');
        }

        header('Location: ' . BASE_URL . 'vendor/coupons');
        exit;
    }

    public function deleteCoupon() {
        $vendorId = $_SESSION['vendor_user']['id'];
        if (isset($_GET['id'])) {
            if ($this->vendorModel->deleteCoupon($vendorId, $_GET['id'])) {
                setFlash('success', 'Coupon deleted successfully.');
            } else {
                setFlash('error', 'Failed to delete coupon.');
            }
        }
        header('Location: ' . BASE_URL . 'vendor/coupons');
        exit;
    }
}
?>
