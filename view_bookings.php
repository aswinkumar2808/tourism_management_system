<?php
// Include the database connection
include('db.php');

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Use a prepared statement to fetch user's bookings
$sql = "SELECT b.booking_id, p.name AS package_name, b.travel_date, b.number_of_people, b.total_price 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.package_id 
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user_id as an integer
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings</title>
</head>
<body>
    <h2>Your Bookings</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Package Name</th>
                <th>Travel Date</th>
                <th>Number of People</th>
                <th>Total Price</th>
            </tr>

            <?php while ($booking = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['travel_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['number_of_people']); ?></td>
                    <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                </tr>
            <?php endwhile; ?>

        </table>
    <?php else: ?>
        <p>You have no bookings yet.</p>
    <?php endif; ?>
</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
