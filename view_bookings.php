<?php
include('db.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.booking_id, p.name AS package_name, b.travel_date, b.number_of_people, b.total_price 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.package_id 
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$result = $stmt->get_result();
?>


<link rel="stylesheet" href="view_bookings.css">

<div class="bookings-container">
    <h2>Your Bookings</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Travel Date</th>
                    <th>Number of People</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['travel_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['number_of_people']); ?></td>
                        <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-bookings">You have no bookings yet.</p>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
?>

