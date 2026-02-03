<?php
// php/loginhandler.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Verify Password
            // Note: In a real app we use password_verify(). 
            // Since migration might have brought in plain text passwords, we handle both (or assume plain for now based on previous code).
            // MIGRATION NOTE: Since usersdata.php had '123456', we assume plain text for legacy users.
            // New users from signup_handler should be hashed.
            // Let's implement a hybrid check:
            
            $passwordValid = false;
            
            // Check if hash matches (BCRYPT)
            if (password_verify($password, $user['password'])) {
                $passwordValid = true;
            } 
            // Fallback for legacy plain text passwords (remove this in production after migration!)
            elseif ($password === $user['password']) {
                $passwordValid = true;
            }

            if ($passwordValid) {
                // Login Success
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'location' => $user['location']
                ];
                
                // If there's a cart in session, associate it? (Optional enhancement)
                
                echo json_encode(['success' => true]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);

    } catch (PDOException $e) {
        error_log("Login Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'System error occurred.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
