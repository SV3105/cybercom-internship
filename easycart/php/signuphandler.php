<?php
// php/signuphandler.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    // Capture new fields
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            exit;
        }

        // Hash Password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password, phone, location) VALUES (?, ?, ?, ?, ?)");
        if ($stmtInsert->execute([$name, $email, $hashedPassword, $phone, $location])) {
            
            // Allow immediate login (optional)
            // Or just return success
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed.']);
        }

    } catch (PDOException $e) {
        error_log("Signup Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'System error occurred.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
