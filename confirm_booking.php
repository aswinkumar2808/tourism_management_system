<?php
// Include database connection
include('db.php');

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $package_id = $_POST['package_id'];
    $user_id = $_SESSION['user_id']; // Get user ID from the session
    $travel_date = $_POST['travel_date'];
    $number_of_people = (int)$_POST['number_of_people'];

    // Fetch the price of the selected package
    $sql = "SELECT price FROM packages WHERE package_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $stmt->bind_result($price_per_person);
    $stmt->fetch();
    $stmt->close();

    // Calculate the total price
    $total_price = $price_per_person * $number_of_people;

    // Insert booking into the bookings table with status set to 'Pending'
    $sql = "INSERT INTO bookings (user_id, package_id, travel_date, number_of_people, total_price, status, booking_date) 
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisid", $user_id, $package_id, $travel_date, $number_of_people, $total_price);

    if ($stmt->execute()) {
        // Booking successful: Redirect to the View Bookings page with success message
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


