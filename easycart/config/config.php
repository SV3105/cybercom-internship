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
?>
