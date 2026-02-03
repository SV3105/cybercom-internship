<?php
// php/profile.php
session_start();
require_once '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// 1. Fetch User Data from DB (Always fresh)
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if(!$user) {
        // User not found in DB? Logout.
        session_destroy();
        header("Location: auth.php");
        exit;
    }
    }
 catch (PDOException $e) {
    die("Error fetching profile: " . $e->getMessage());
}

// 2. Fetch Recent Orders (Limit 5)
$recent_orders = [];
try {
    $stmtOrders = $pdo->prepare("SELECT * FROM sales_order WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmtOrders->execute([$user_id]);
    $recent_orders = $stmtOrders->fetchAll();
} catch (PDOException $e) {
    // Fail silently for orders, just empty list
    error_log("Profile Orders Error: " . $e->getMessage());
}

// 2. Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $newName = $_POST['name'] ?? $user['name'];
    $newPhone = $_POST['phone'] ?? $user['phone'];
    $newLocation = $_POST['location'] ?? $user['location'];

    try {
        $updateStmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, location = ? WHERE id = ?");
        $updateStmt->execute([$newName, $newPhone, $newLocation, $user_id]);
        
        // Refresh session data
        $_SESSION['user']['name'] = $newName;
        $_SESSION['user']['phone'] = $newPhone;
        $_SESSION['user']['location'] = $newLocation;
        
        // Reload fresh data for display
        $user['name'] = $newName;
        $user['phone'] = $newPhone;
        $user['location'] = $newLocation;

        header("Location: profile.php?success=1");
        exit;

    } catch (PDOException $e) {
        // Handle error (e.g., set an error message variable to display)
        $error = "Update failed: " . $e->getMessage();
    }
}

$title = "My Profile - EasyCart";
$base_path = "../";
$page = "profile";
$extra_css = "profile.css";

include '../includes/header.php';
?>

<?php include '../templates/profile.php'; ?>

<?php include '../includes/footer.php'; ?>
