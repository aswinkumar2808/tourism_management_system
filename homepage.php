<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); 
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="homepage.css">

</head>
<body>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>

    <ul>
        <li><a href="browse_packages.php">Browse Packages</a></li>
        <li><a href="view_bookings.php">My Bookings</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>

</body>
</html>
