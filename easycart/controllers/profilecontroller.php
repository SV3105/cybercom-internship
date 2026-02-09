<?php
// controllers/ProfileController.php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/order.php';
require_once __DIR__ . '/../models/product.php';

class ProfileController {
    private $pdo;
    private $userModel;
    private $orderModel;
    private $productModel;
    private $wishlistModel;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->orderModel = new Order($pdo);
        $this->productModel = new Product($pdo);
        // Load Wishlist Model
        require_once __DIR__ . '/../models/wishlist.php';
        $this->wishlistModel = new Wishlist($pdo);
    }

    // ... (index method) ...
    public function index() {
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            session_destroy();
            header("Location: auth");
            exit;
        }

        $stats = $this->orderModel->getUserStats($userId);
        $total_orders = $stats['count'];
        $total_spent = $stats['total'];

        $allOrders = $this->orderModel->getOrdersByUser($userId);
        $recent_orders = array_slice($allOrders, 0, 5);

        $chart_data = $this->orderModel->getSpendingChartData($userId);

        // Fetch Wishlist Count via Model
        $wishlist_count = $this->wishlistModel->getCount($userId);

        $title = "My Profile - EasyCart";
        $page = "profile";
        $extra_css = "profile.css";
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profile/profile.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // ... (update method remains same) ...
    public function update() {
        // ... (Keep existing update logic) ...
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // Handle Profile Info Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'update_profile') {
                $name = $_POST['name'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $location = $_POST['location'] ?? '';

                if ($this->userModel->updateProfile($userId, $name, $phone, $location)) {
                    $_SESSION['user']['name'] = $name;
                    $_SESSION['user']['phone'] = $phone;
                    $_SESSION['user']['location'] = $location;
                    header("Location: profile?success=1");
                } else {
                    header("Location: profile?error=update_failed");
                }
                exit;
            }

            // Handle Password Change
            if ($_POST['action'] === 'change_password') {
                $current = $_POST['current_password'] ?? '';
                $new = $_POST['new_password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                $user = $this->userModel->getUserById($userId);

                if (!password_verify($current, $user['password'])) {
                    header("Location: profile?error=password_incorrect");
                    exit;
                }
                if ($new !== $confirm) {
                    header("Location: profile?error=password_mismatch");
                    exit;
                }
                if (strlen($new) < 6) {
                    header("Location: profile?error=password_short");
                    exit;
                }
                if ($this->userModel->changePassword($userId, $new)) {
                    header("Location: profile?success=password_changed");
                } else {
                    header("Location: profile?error=password_update_failed");
                }
                exit;
            }
        }
        header("Location: profile");
        exit;
    }

    public function wishlist() {
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        
        // Sync Logic
        if ($user_id) {
            $db_wishlist = $this->wishlistModel->getWishlistIds($user_id);
            $_SESSION['wishlist'] = $db_wishlist; 
        } else {
            if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
        }

        // Fetch Product Details
        $wishlist_items = [];
        $wishlist_ids = $_SESSION['wishlist'] ?? [];
        
        if (!empty($wishlist_ids)) {
             $allProducts = $this->productModel->getAllProducts();
             foreach ($allProducts as $p) {
                 if (in_array($p['id'], $wishlist_ids)) {
                     $wishlist_items[] = $p;
                 }
             }
        }

        $title = "My Wishlist - EasyCart";
        $page = "wishlist";
        $extra_css = "products.css";
        $extra_css_2 = "wishlist.css";
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profile/wishlist.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function wishlistAction() {
        header('Content-Type: application/json');
        
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            
            if ($p_id > 0) {
                $action = $_POST['action'];

                // 1. DB Handling
                if ($user_id) {
                    if ($action === 'add') {
                        $this->wishlistModel->add($user_id, $p_id);
                    } elseif ($action === 'remove') {
                        $this->wishlistModel->remove($user_id, $p_id);
                    }
                }

                // 2. Session Handling
                if ($action === 'add') {
                    if (!in_array($p_id, $_SESSION['wishlist'])) {
                        $_SESSION['wishlist'][] = $p_id;
                    }
                } elseif ($action === 'remove') {
                    if (($key = array_search($p_id, $_SESSION['wishlist'])) !== false) {
                        unset($_SESSION['wishlist'][$key]);
                        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
                    }
                }
            }
            
            $count = count($_SESSION['wishlist']);
            if ($user_id) {
                $count = $this->wishlistModel->getCount($user_id);
            }
            
            echo json_encode(['success' => true, 'count' => $count]);
            exit;
        }
        
        echo json_encode(['success' => false]);
        exit;
    }
}
?>
