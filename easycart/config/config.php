<?php
// config/config.php
// Database configuration and connection

$host = 'localhost';
$db   = 'easycart';
$user = 'postgres'; // Default PostgreSQL user, change if different
$pass = 'Sneha@3105'; // Change this to your PostgreSQL password
$port = "3000";

$dsn = "pgsql:host=$host;port=$port;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log this instead of showing it
    die("Database Connection Failed: " . $e->getMessage());
}

if (!function_exists('setFlash')) {
    function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

/**
 * Get and clear the flash message from the session
 */
if (!function_exists('getFlash')) {
    function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
?>
