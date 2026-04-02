<?php
// controllers/VendorAuthController.php

class VendorAuthController {
    private $vendorModel;

    public function __construct($vendorModel) {
        $this->vendorModel = $vendorModel;
        
        // Redirect to dashboard if already logged in
        if (isset($_SESSION['vendor_user'])) {
            $currentRoute = trim(str_replace(BASE_URL, '', $_SERVER['REQUEST_URI']), '/');
            if (strpos($currentRoute, 'vendor/login') === 0 || strpos($currentRoute, 'vendor/register') === 0) {
                header('Location: ' . BASE_URL . 'vendor/dashboard');
                exit;
            }
        }
    }

    public function showLogin() {
        $title = "Vendor Login - EasyCart";
        $extra_css = "auth.css";
        // To reuse the auth layout
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/vendor/login.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function showRegister() {
        $title = "Vendor Registration - EasyCart";
        $extra_css = "auth.css";
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/vendor/register.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('error', 'Please fill in all fields.');
            header('Location: ' . BASE_URL . 'vendor/login');
            exit;
        }

        $vendor = $this->vendorModel->login($email, $password);

        if ($vendor) {
            $_SESSION['vendor_user'] = [
                'id' => $vendor['id'],
                'name' => $vendor['name'],
                'email' => $vendor['email'],
                'store_name' => $vendor['store_name']
            ];
            setFlash('success', 'Welcome back to your Vendor Dashboard!');
            header('Location: ' . BASE_URL . 'vendor/dashboard');
        } else {
            setFlash('error', 'Invalid credentials.');
            header('Location: ' . BASE_URL . 'vendor/login');
        }
        exit;
    }

    public function register() {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $store_name = trim($_POST['store_name'] ?? '');

        if (empty($name) || empty($email) || empty($password) || empty($store_name)) {
            setFlash('error', 'Please fill in all fields.');
            header('Location: ' . BASE_URL . 'vendor/register');
            exit;
        }

        $result = $this->vendorModel->register($name, $email, $password, $store_name);

        if ($result['success']) {
            setFlash('success', 'Registration successful! You can now login.');
            header('Location: ' . BASE_URL . 'vendor/login');
        } else {
            setFlash('error', $result['message']);
            header('Location: ' . BASE_URL . 'vendor/register');
        }
        exit;
    }

    public function logout() {
        unset($_SESSION['vendor_user']);
        setFlash('success', 'You have been logged out.');
        header('Location: ' . BASE_URL . 'vendor/login');
        exit;
    }
}
?>
