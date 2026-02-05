<?php
// controllers/ProfileController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

class ProfileController {
    private $pdo;
    private $userModel;
    private $orderModel;
    private $productModel; // For wishlist items

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->orderModel = new Order($pdo);
        $this->productModel = new Product($pdo);
    }

    /**
     * Show Profile Dashboard
     */
    public function index() {
        // Auth Check
        if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // 1. Fetch Fresh User Data
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            // User deleted or invalid? Logout.
            session_destroy();
            header("Location: auth");
            exit;
        }

        // 2. Fetch Stats
        $stats = $this->orderModel->getUserStats($userId);
        $total_orders = $stats['count'];
        $total_spent = $stats['total'];

        // 3. Fetch Recent Orders (Limit 5)
        // We reuse getOrdersByUser but might want to limit it here.
        // Current getOrdersByUser fetches ALL. For performance, ideally we'd have a limit param.
        // For now, fetch all and slice array in PHP (not ideal for huge datasets but fine for this scale)
        // Or adding a limit method to OrderModel would be better.
        // Let's just slice for now as per refactoring "copy logic" rule unless we improve it.
        // Actually the original code did explicit query LIMIT 5.
        // Our getOrdersByUser fetches all. Let's slice it.
        $allOrders = $this->orderModel->getOrdersByUser($userId);
        $recent_orders = array_slice($allOrders, 0, 5);

        // 4. Fetch Chart Data
        $chart_data = $this->orderModel->getSpendingChartData($userId);

        // View Variables
        // Passed to view via extract or directly uses $user, $recent_orders etc.
        // We'll rely on included view using local variables.
        
        $title = "My Profile - EasyCart";
        $page = "profile";
        $extra_css = "profile.css";
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profile/profile.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Update Profile Actions
     */
    public function update() {
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
                    // Update Session
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
        
        // If not POST or invalid action
        header("Location: profile");
        exit;
    }

    /**
     * Wishlist View
     */
    public function wishlist() {
        // Auth check or guest allowed? 
        // Original code allowed guest session wishlist.
        
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        
        // Sync Logic (from original)
        if ($user_id) {
            try {
                $stmt = $this->pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $db_wishlist = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $_SESSION['wishlist'] = $db_wishlist; 
            } catch (Exception $e) { /* Ignore */ }
        } else {
            if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
        }

        // Fetch Product Details for items in wishlist
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
        $extra_css = "products.css"; // Reuse product card styles
        $extra_css_2 = "wishlist.css";
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profile/wishlist.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Wishlist Actions (Add/Remove)
     */
    public function wishlistAction() {
        // AJAX mostly
        header('Content-Type: application/json');
        
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            
            if ($p_id > 0) {
                $action = $_POST['action'];

                // 1. DB Handling if logged in
                if ($user_id) {
                    try {
                        if ($action === 'add') {
                            // Postgres syntax for ignore duplicates
                            // Assumes unique constraint on (user_id, product_id)
                            $stmt = $this->pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
                            $stmt->execute([$user_id, $p_id]);
                        } elseif ($action === 'remove') {
                            $stmt = $this->pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                            $stmt->execute([$user_id, $p_id]);
                        }
                    } catch (Exception $e) {}
                }

                // 2. Session Handling (Always sync session for UI)
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
            
            // Return count
            $count = count($_SESSION['wishlist']);
            // If logged in, maybe fetch real count from DB?
            // "Return accurate count... if user_id... select count..."
            if ($user_id) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $count = $stmt->fetchColumn();
            }
            
            echo json_encode(['success' => true, 'count' => $count]);
            exit;
        }
        
        echo json_encode(['success' => false]);
        exit;
    }
}
?>
