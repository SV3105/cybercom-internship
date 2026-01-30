<?php 
session_start();
echo "Welcome ". $_SESSION['username'];

?>

<form method="post">
    <button name="logout">Logout</button>
</form>

<?php 
if(isset($_POST['logout'])){
    session_destroy();
    echo "Logged out succesfully";
}
?>