<?php
// controllers/AdminController.php
// Admin controller for platform management (CMS, Users, Vendors)

class AdminController {
    private $adminModel;
    
    public function __construct($adminModel) {
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
        $stats = $this->adminModel->getDashboardStats();
        
        $title = "Admin Dashboard - EasyCart";
        $page = "admin";
        $page_title = "Platform Overview";
        $extra_css = "admin.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/dashboard.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Display all users
     */
    public function users() {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;
        
        $title = "Platform Users - EasyCart Admin";
        $page = "users"; 
        $page_title = "User Management";
        $extra_css = "admin.css"; 
        $base_path = '';

        $users = $this->adminModel->getUsers($limit, $offset);
        $totalItems = $this->adminModel->getTotalUsersCount();
        $totalPages = ceil($totalItems / $limit);
        
        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/users.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }

    /**
     * Display all vendors
     */
    public function vendors() {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;

        $title = "Registered Vendors - Admin";
        $page = "vendors"; 
        $page_title = "Vendor Management";
        $extra_css = "admin.css";
        $base_path = '';

        $vendors = $this->adminModel->getVendors($limit, $offset);
        $totalItems = $this->adminModel->getTotalVendorsCount();
        $totalPages = ceil($totalItems / $limit);

        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/vendors.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }
    
    /**
     * Display Site Settings CMS
     */
    public function settings() {
        $title = "Site Settings - Admin";
        $page = "settings"; 
        $page_title = "Appearance & Settings";
        $extra_css = "admin.css";
        $base_path = '';

        $settings = $this->adminModel->getSiteSettings();

        // Handle Form Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateData = [
                'primary_color' => $_POST['primary_color'] ?? '#0ea5e9',
                'secondary_color' => $_POST['secondary_color'] ?? '#f1f5f9',
                'store_name' => $_POST['store_name'] ?? 'EasyCart Marketplace',
                'hero_banner_text' => $_POST['hero_banner_text'] ?? ''
            ];
            
            if ($this->adminModel->updateSiteSettings($updateData)) {
                setFlash('success', 'Settings updated successfully. Refreshed storefront!');
            } else {
                setFlash('error', 'Failed to update settings.');
            }
            header('Location: ' . BASE_URL . 'admin/settings');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/admin/header.php';
        require_once __DIR__ . '/../views/admin/settings.php';
        require_once __DIR__ . '/../views/layouts/admin/footer.php';
    }
}
?>
