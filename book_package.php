<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['package_id'])) {
    $package_id = $_GET['package_id'];

    $sql = "SELECT * FROM packages WHERE package_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        echo "Package not found!";
        exit();
    }
} else {
    echo "No package selected!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Package</title>
    <link rel="stylesheet" href="book_package.css">
</head>
<body>

<div class="booking-container">
    <h2>Booking: <?php echo htmlspecialchars($package['name']); ?></h2>
    <form method="POST" action="confirm_booking.php">
        <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

        <div class="form-group">
            <label for="travel_date">Travel Date:</label>
            <input type="date" name="travel_date" required>
        </div>

        <div class="form-group">
            <label for="number_of_people">Number of People:</label>
            <input type="number" name="number_of_people" required min="1">
        </div>

        <div class="price-info">
            <p>Total Price: ₹<?php echo number_format($package['price'], 2); ?> x <span id="people_count">1</span> people = ₹<span id="total_price"><?php echo number_format($package['price'], 2); ?></span></p>
        </div>

        <button type="submit" class="btn">Confirm Booking</button>
    </form>
</div>

<script>
    document.querySelector('input[name="number_of_people"]').addEventListener('input', function() {
        var numPeople = this.value;
        var pricePerPerson = <?php echo $package['price']; ?>;
        var totalPrice = numPeople * pricePerPerson;
        
        document.getElementById('people_count').textContent = numPeople;
        document.getElementById('total_price').textContent = totalPrice;
    });
</script>

</body>
</html>
