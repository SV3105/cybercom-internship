<?php
$title = "Login / Sign Up - EasyCart";
$base_path = "../";
$page = "auth";
$extra_css = "auth.css";
include '../includes/header.php';
// include '../data/usersdata.php'; // No longer needed for frontend
// $users_json = json_encode([]); // No longer needed
?>


<?php include '../templates/auth.php'; ?>

<?php include '../includes/footer.php'; ?>
