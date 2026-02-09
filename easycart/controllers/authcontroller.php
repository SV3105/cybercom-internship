<?php
// controllers/AuthController.php
// Authentication controller

class AuthController {
    private $userModel;
    private $adminModel;
    
    public function __construct() {
        global $pdo;
        require_once __DIR__ . '/../models/user.php';
        require_once __DIR__ . '/../models/admin.php';
        $this->userModel = new User($pdo);
        $this->adminModel = new Admin($pdo);
    }
    
    /**
     * Show login/signup page
     */
    public function showLogin() {
        // If already logged in, redirect to profile
        if (isset($_SESSION['user']) || isset($_SESSION['admin_user'])) {
            header("Location: profile");
            exit;
        }
        
        $title = "Login / Sign Up - EasyCart";
        $page = "auth";
        $extra_css = "auth.css";
        $base_path = '';
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/login.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Handle login request (AJAX)
     */
    public function login() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }
        
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            exit;
        }
        
        $user = $this->userModel->login($email, $password);
        
        if ($user) {
            // User Login Success
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'location' => $user['location']
            ];
            echo json_encode(['success' => true]);
            exit;
        } 
        
        // If regular user fails, check if admin
        $admin = $this->adminModel->login($email, $password);
        if ($admin) {
            // Admin Login Success
            session_regenerate_id(true);
            $_SESSION['admin_user'] = [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email']
            ];
            echo json_encode(['success' => true, 'is_admin' => true]);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    
    /**
     * Handle signup request (AJAX)
     */
    public function signup() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');
        
        // Basic validation
        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }
        
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'location' => $location
        ];
        
        $result = $this->userModel->register($data);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        // Deactivate Cart if user was logged in
        if (isset($_SESSION['user']['id'])) {
             global $pdo;
             require_once __DIR__ . '/../models/cart.php';
             $cartModel = new Cart($pdo);
             $cartModel->deactivateUserCart($_SESSION['user']['id']);
        }

        // Unset all session variables
        unset($_SESSION['user']);
        unset($_SESSION['admin_user']);
        
        // Destroy the session
        session_destroy();
        
        // Start a fresh session with a new ID
        session_start();
        session_regenerate_id(true); // Critical: Force new ID
        
        header("Location: auth");
        exit;
    }
}
?>
