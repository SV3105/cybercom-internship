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
    error_log("Profile Orders Error: " . $e->getMessage());
}

// 3. Fetch Dashboard Statistics
$total_orders = 0;
$total_spent = 0;
try {
    // Total orders count
    $stmtCount = $pdo->prepare("SELECT COUNT(*) as count FROM sales_order WHERE user_id = ?");
    $stmtCount->execute([$user_id]);
    $total_orders = $stmtCount->fetch()['count'];
    
    // Total amount spent
    $stmtSum = $pdo->prepare("SELECT SUM(grand_total) as total FROM sales_order WHERE user_id = ?");
    $stmtSum->execute([$user_id]);
    $total_spent = $stmtSum->fetch()['total'] ?? 0;
} catch (PDOException $e) {
    error_log("Stats Error: " . $e->getMessage());
}

// 4. Fetch Chart Data (Spending per day)
$chart_data = [];
try {
    // Grouping by date for the chart
    $stmtChart = $pdo->prepare("
        SELECT 
            TO_CHAR(created_at, 'YYYY-MM-DD') as date,
            SUM(grand_total) as amount
        FROM sales_order 
        WHERE user_id = ? 
        GROUP BY TO_CHAR(created_at, 'YYYY-MM-DD')
        ORDER BY TO_CHAR(created_at, 'YYYY-MM-DD') ASC
    ");
    $stmtChart->execute([$user_id]);
    $chart_data = $stmtChart->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Chart Data Error: " . $e->getMessage());
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

// 3. Handle Change Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: profile.php?error=password_empty");
        exit;
    }
    
    if ($newPassword !== $confirmPassword) {
        header("Location: profile.php?error=password_mismatch");
        exit;
    }
    
    if (strlen($newPassword) < 6) {
        header("Location: profile.php?error=password_short");
        exit;
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        header("Location: profile.php?error=password_incorrect");
        exit;
    }
    
    // Update password
    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmtPassword = $pdo->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmtPassword->execute([$hashedPassword, $user_id]);
        
        header("Location: profile.php?success=password_changed");
        exit;
    } catch (PDOException $e) {
        header("Location: profile.php?error=password_update_failed");
        exit;
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
