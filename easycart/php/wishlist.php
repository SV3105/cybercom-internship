<?php
// php/wishlist.php
session_start();
require_once '../includes/db.php';
require_once '../data/productsdata.php'; // Needed for product details display

// --- WISHLIST LOGIC ---

// 1. Determine User
$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

// Initialize session wishlist if needed (fallback for guest)
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// 2. Handle AJAX Actions (Add/Remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($p_id > 0) {
        $action = $_POST['action'];

        // SCENARIO A: Logged In -> Use DB
        if ($user_id) {
            try {
                if ($action === 'add') {
                    // UPSERT logic: Insert if not exists
                    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
                    $stmt->execute([$user_id, $p_id]);
                } elseif ($action === 'remove') {
                    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                    $stmt->execute([$user_id, $p_id]);
                }
            } catch (PDOException $e) {
                // Log error
            }
        } 
        
        // SCENARIO B: Guest -> Use Session (Mirrored even if logged in for immediate UI responsiveness if desirable, but let's keep separate)
        // Actually, let's keep SESSION in sync for simple checking in views
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

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        
        // Return accurate count
        $count = count($_SESSION['wishlist']);
        if ($user_id) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $count = $stmt->fetchColumn();
        }
        
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
    }
    
    header("Location: wishlist.php");
    exit;
}

// 3. Page Load: Sync DB to Session if logged in (so templates see it)
if ($user_id) {
    try {
        $stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $db_wishlist = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $_SESSION['wishlist'] = $db_wishlist; // Override session with valid DB data
    } catch (PDOException $e) {
        // Fallback
    }
}

$title = "My Wishlist - EasyCart";
$base_path = "../";
$page = "wishlist";
$extra_css = "wishlist.css";

include '../includes/header.php';
?>

<?php include '../templates/wishlist.php'; ?>

<?php include '../includes/footer.php'; ?>
