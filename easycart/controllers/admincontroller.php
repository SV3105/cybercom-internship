<?php
// controllers/AdminController.php
// Admin controller for product management

class AdminController {
    private $productModel;
    private $adminModel;
    
    public function __construct($productModel, $adminModel) {
        $this->productModel = $productModel;
        $this->adminModel = $adminModel;
        $this->checkAdmin();
    }

    /**
     * Check if the logged-in user has admin privileges
     */
    private function checkAdmin() {
        // Exclude login routes from security check
        $currentRoute = trim(str_replace(BASE_URL, '', $_SERVER['REQUEST_URI']), '/');
        if (strpos($currentRoute, 'admin/login') === 0 || strpos($currentRoute, 'admin/login-process') === 0) {
            return;
        }

        if (!isset($_SESSION['admin_user'])) {
            setFlash('error', 'Unauthorized access! Please login as administrator.');
            header('Location: ' . BASE_URL . 'auth');
            exit;
        }
    }
    
    /**
     * Display admin dashboard
     */
    public function dashboard() {
        // Get statistics using model
        $stats = $this->adminModel->getDashboardStats();
        
        $title = "Admin Dashboard - EasyCart";
        $page = "admin";
        $page_title = "Dashboard Overview";
        $extra_css = "admin.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/dashboard.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Display all products
     */
    public function products() {
        $search = $_GET['search'] ?? '';
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;
        
        $title = "Products - EasyCart Admin";
        $page = "products"; // Active sidebar link
        $page_title = "Product Management";
        $extra_css = "admin.css"; 
        $base_path = '';

        // Fetch products using model with pagination
        $products = $this->adminModel->getProducts($search, $limit, $offset);
        $totalItems = $this->adminModel->getTotalProductsCount($search);
        $totalPages = ceil($totalItems / $limit);
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/products.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Display all orders
     */
    public function orders() {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;

        $title = "Orders - EasyCart Admin";
        $page = "orders"; // Active sidebar link
        $page_title = "Order Management";
        $extra_css = "admin.css";
        $base_path = '';

        // Fetch all orders using model with pagination
        $orders = $this->adminModel->getOrders($limit, $offset);
        $totalItems = $this->adminModel->getTotalOrdersCount();
        $totalPages = ceil($totalItems / $limit);

        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/orders.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }
    
    
    /**
     * Display export page
     */
    public function exportPage() {
        $title = "Export Products - EasyCart";
        $page = "export";
        $page_title = "Export Products";
        $extra_css = "admin.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/export.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }
    
    /**
     * Export products to CSV and download
     */
    public function exportProducts() {
        // Check for filters
        $filter = $_GET['filter'] ?? null;
        $templateOnly = isset($_GET['template']) && $_GET['template'] === 'true';
        
        if ($templateOnly) {
            // Export empty template
            $this->exportTemplate();
            return;
        }
        
        $result = $this->productModel->exportToCSV($filter);
        
        if (!$result['success']) {
            die("Export failed: " . $result['error']);
        }
        
        $products = $result['data'];
        
        // Set headers for CSV download
        $filterSuffix = $filter ? "_{$filter}" : '';
        $filename = 'products_export' . $filterSuffix . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Create file pointer connected to output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Output column headers
        if (count($products) > 0) {
            fputcsv($output, array_keys($products[0]));
        }
        
        // Output data rows
        foreach ($products as $product) {
            fputcsv($output, $product);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export CSV template (headers only)
     */
    private function exportTemplate() {
        $filename = 'products_template_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Template headers
        $headers = ['sku', 'name', 'price', 'old_price', 'category', 'brand', 'description', 'image', 'is_featured', 'rating', 'review_count', 'stock_qty'];
        fputcsv($output, $headers);
        
        // Add sample row
        $sample = [
            'SAMPLE001',
            'Sample Product Name',
            '999.00',
            '1299.00',
            'electronics',
            'samsung',
            'This is a sample product description',
            'product.jpg',
            'false',
            '4.5',
            '120',
            '50'
        ];
        fputcsv($output, $sample);
        
        fclose($output);
        exit;
    }
    
    /**
     * Display import page
     */
    public function importPage() {
        $title = "Import Products - EasyCart";
        $page = "import";
        $page_title = "Import Products";
        $extra_css = "admin.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/import.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }
    
    /**
     * Handle CSV import
     */
    public function handleImport() {
        $response = [
            'success' => false,
            'message' => '',
            'results' => null
        ];
        
        // Check if file was uploaded
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $response['message'] = 'Please select a CSV file to upload';
            echo json_encode($response);
            exit;
        }
        
        $file = $_FILES['csv_file'];
        
        // Validate file type
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($fileExt !== 'csv') {
            $response['message'] = 'Only CSV files are allowed';
            echo json_encode($response);
            exit;
        }
        
        // Parse CSV file
        $csvData = [];
        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
            // Get headers
            $headers = fgetcsv($handle);
            
            // Sanitize headers (remove BOM, trim, lowercase)
            if (!empty($headers) && isset($headers[0])) {
                // Remove BOM from first element if present
                $bom = pack('H*','EFBBBF');
                $headers[0] = preg_replace("/^$bom/", '', $headers[0]);
            }
            
            // Normalize all headers
            $headers = array_map(function($h) {
                return strtolower(trim($h));
            }, $headers);
            
            // Read data rows
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $csvData[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        
        if (empty($csvData)) {
            $response['message'] = 'CSV file is empty or invalid';
            echo json_encode($response);
            exit;
        }
        
        // Import products
        $results = $this->productModel->importFromCSV($csvData);

        // Set flash message for session (since handleImport is AJAX, this will show on next page load if we redirect, 
        // but here it returns JSON. We should notify the user to show it manually or use JSON response.)
        // However, the import page might refresh or redirect.
        
        // Generate Failed CSV if needed
        if (!empty($results['failed_rows'])) {
             $failedDir = __DIR__ . '/../public/csv/';
             if (!is_dir($failedDir)) mkdir($failedDir, 0777, true);
             
             $failedFile = 'failed_import_' . time() . '.csv';
             $failedPath = $failedDir . $failedFile;
             
             $fp = fopen($failedPath, 'w');
             
             // Get headers from first failed row
             $failedHeaders = array_keys($results['failed_rows'][0]);
             // Ensure 'import_error_reason' is last or prominent
             
             fputcsv($fp, $failedHeaders);
             foreach ($results['failed_rows'] as $row) {
                 fputcsv($fp, $row);
             }
             fclose($fp);
             

             
             // Update URL to use controller
             $results['failed_csv_url'] = 'admin/download-failed?file=' . $failedFile;
        }
        
        $response['success'] = true;
        $response['results'] = [
            'new_products' => $results['success'],
            'updated_products' => $results['updated'],
            'failed_rows' => $results['failed'],
            'failed_file' => $failedFile ?? null
        ];
        $response['message'] = sprintf(
            'Import completed: %d new, %d updated, %d failed',
            $results['success'],
            $results['updated'],
            $results['failed']
        );
        
        echo json_encode($response);
        exit;
    }

    /**
     * Download failed import CSV
     */
    public function downloadFailed() {
        if (!isset($_GET['file'])) {
            die('No file specified');
        }
        
        $file = basename($_GET['file']);
        $filepath = __DIR__ . '/../public/csv/' . $file;
        
        if (!file_exists($filepath)) {
            die('File not found');
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream'); // Force download better than text/csv sometimes
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        // Clean buffer to prevent corruption
        if (ob_get_level()) ob_end_clean();
        flush();
        
        readfile($filepath);
        exit;
    }

    /**
     * Delete product
     */
    public function deleteProduct() {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            if ($this->adminModel->deleteProduct($id)) {
                setFlash('success', 'Product deleted successfully!');
            } else {
                setFlash('error', 'Failed to delete product.');
            }
        }
        
        header('Location: ' . BASE_URL . 'admin/products');
        exit;
    }

    /**
     * Edit Product Form
     */
    public function editProduct() {
        $id = $_GET['id'] ?? null;
        $product = null;

        if ($id) {
            $product = $this->adminModel->getProductById($id);
        }

        // Fetch Categories & Brands for dropdowns
        $categories = $this->adminModel->getCategories();
        $brands = $this->adminModel->getBrands();

        $title = $product ? "Edit Product" : "Add Product";
        $page = "products";
        $extra_css = "admin.css";
        $base_path = '';

        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/productform.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Save Product (Create/Update)
     */
    public function saveProduct() {
        // Basic validation/sanitization
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

        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/images/';
            $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newFileName = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $fileExt;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFileName)) {
                $data['image'] = $newFileName;
            }
        }

        if ($this->adminModel->saveProduct($id, $data)) {
            setFlash('success', $id ? 'Product updated successfully!' : 'Product created successfully!');
        } else {
            setFlash('error', 'Error saving product.');
        }

        header('Location: ' . BASE_URL . 'admin/products');
        exit;
    }

    public function orderDetails() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: ' . BASE_URL . 'admin/orders');
            exit;
        }

        // Fetch Order Info
        $order = $this->adminModel->getOrderDetails($id);

        if (!$order) {
            header('Location: ' . BASE_URL . 'admin/orders');
            exit;
        }

        // Fetch Items with Product Info (Image/SKU)
        $items = $this->adminModel->getOrderItems($id);

        // Fetch Address
        $address = $this->adminModel->getOrderAddress($id);

        // Fetch Payment
        $payment = $this->adminModel->getOrderPayment($id);

        $title = "Order Details #" . ($order['increment_id'] ?: $order['order_id']);
        $page = "orders";
        $extra_css = "admin.css"; 
        $base_path = '';

        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/orderdetails.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    public function updateOrderStatus() {
        $id = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($id && $status) {
            if ($this->adminModel->updateOrderStatus($id, $status)) {
                setFlash('success', 'Order status updated to ' . ucfirst($status));
            } else {
                setFlash('error', 'Error updating status.');
            }
        }

        header('Location: ' . BASE_URL . 'admin/orderview?id=' . $id);
        exit;
    }

    /**
     * Save order notes
     */
    /**
     * Save order notes
     */
    public function saveOrderNotes() {
        $id = $_POST['order_id'] ?? null;
        $notes = $_POST['admin_notes'] ?? '';

        if ($id) {
            if ($this->adminModel->saveOrderNotes($id, $notes)) {
                setFlash('success', 'Order notes saved successfully!');
            } else {
                setFlash('error', 'Error saving notes.');
            }
        }

        header('Location: ' . BASE_URL . 'admin/orderview?id=' . $id);
        exit;
    }

    /**
     * Display admin profile/settings
     */
    public function profile() {
        // If logged in, get admin data from session
        $user = $_SESSION['admin_user'] ?? null;
        
        $title = "Admin Profile - EasyCart";
        $page = "profile"; 
        $page_title = "Admin Profile";
        $extra_css = "admin.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/profile.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Show form to create new admin
     */
    public function showCreateAdmin() {
        $title = "Create New Administrator";
        $page = "create_admin";
        $page_title = "Admin Management";
        $extra_css = "admin.css";
        $base_path = '';

        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/createadmin.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Handle creation of new admin
     */
    public function handleCreateAdmin() {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

        if (empty($name) || empty($email) || empty($password)) {
            setFlash('error', 'All fields are required.');
            header('Location: ' . BASE_URL . 'admin/createadmin');
            exit;
        }

        if ($password !== $confirm_password) {
            setFlash('error', 'Passwords do not match.');
            header('Location: ' . BASE_URL . 'admin/createadmin');
            exit;
        }

        if ($this->adminModel->createAdmin($name, $email, $password)) {
            setFlash('success', 'New administrator account created successfully!');
            header('Location: ' . BASE_URL . 'admin/dashboard');
        } else {
            setFlash('error', 'Failed to create admin account. Email might already exist.');
            header('Location: ' . BASE_URL . 'admin/createadmin');
        }
        exit;
    }
}
?>
