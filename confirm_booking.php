<?php
include('db.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $package_id = $_POST['package_id'];
    $user_id = $_SESSION['user_id']; 
    $travel_date = $_POST['travel_date'];
    $number_of_people = (int)$_POST['number_of_people'];

    $sql = "SELECT price FROM packages WHERE package_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $stmt->bind_result($price_per_person);
    $stmt->fetch();
    $stmt->close();

    $total_price = $price_per_person * $number_of_people;

    $sql = "INSERT INTO bookings (user_id, package_id, travel_date, number_of_people, total_price, status, booking_date) 
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisid", $user_id, $package_id, $travel_date, $number_of_people, $total_price);

    if ($stmt->execute()) {
        header("Location: view_bookings.php?success=1");
        exit();
    } else {
        echo "Error: Could not complete the booking. Please try again.";
    }

    $stmt->close();
} else {
    echo "Invalid request!";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Packages</title>
    <link rel="stylesheet" href="confirm_booking.css"> 
</head>
<body>


</body>
</html>


