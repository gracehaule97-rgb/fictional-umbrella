<?php
session_start();
include 'config.php';

// Clear remember token from database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE users SET remember_token=NULL WHERE id='$user_id'");
}

// Clear session
session_destroy();

// Clear remember me cookie
setcookie('remember_token', '', time() - 3600, "/");

header("Location: login.php");
exit();
?>