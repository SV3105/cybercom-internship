<?php
// controllers/AuthController.php
// Authentication controller

class AuthController {
    private $userModel;
    
    public function __construct() {
        global $pdo;
        require_once __DIR__ . '/../models/User.php';
        //__DIR__ : This is a special PHP "magic constant". It returns the exact folder path of the file currently running.
        $this->userModel = new User($pdo);
    }
    
    /**
     * Show login/signup page
     */
    public function showLogin() {
        // If already logged in, redirect to profile
        if (isset($_SESSION['user'])) {
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
        
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            exit;
        }
        
        $user = $this->userModel->login($email, $password);
        
        if ($user) {
            // Login Success
            session_regenerate_id(true); // new session ID
             $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'location' => $user['location']
            ];
            
            // Sync valid cart from session to DB if needed
            // This logic was in cart.php, we might need a way to trigger it or just rely on next page load
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
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
             require_once __DIR__ . '/../models/Cart.php';
             $cartModel = new Cart($pdo);
             $cartModel->deactivateUserCart($_SESSION['user']['id']);
        }

        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        // Start a fresh session with a new ID
        session_start();
        session_regenerate_id(true); // Critical: Force new ID
        
        header("Location: is_home"); // Will rely on .htaccess/routing to handle empty path or redirect to home
        header("Location: ./");
        exit;
    }
}
?>
