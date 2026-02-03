<?php 
session_start();
$title = "EasyCart India - The Big Sale is Live!";
$base_path = "./";
$page = "home";

include './data/productsdata.php';


include './includes/header.php'; 
?>


<?php include './templates/home.php'; ?>


<?php include './includes/footer.php'; ?>
