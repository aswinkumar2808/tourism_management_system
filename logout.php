<?php
// Start session
session_start();

// Destroy session to log out
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
