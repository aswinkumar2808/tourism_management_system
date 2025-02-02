<?php
// Start session
session_start();

// Include database connection
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if package ID is passed in the URL
if (isset($_GET['package_id'])) {
    $package_id = $_GET['package_id'];

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM packages WHERE package_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id); // Bind package_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch package details if available
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

<!-- Link to the new CSS file -->
<link rel="stylesheet" href="book_package.css">

<!-- Booking Form -->
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
            <input type="number" name="number_of_people" required>
        </div>

        <div class="price-info">
        <p>Total Price: ₹<?php echo number_format($package['price'], 2); ?> x <span id="people_count">1</span> people = ₹<span id="total_price"><?php echo number_format($package['price'], 2); ?></span></p>
        </div>

        <button type="submit" class="btn">Confirm Booking</button>
    </form>
</div>

<script>
    // Update total price based on number of people
    document.querySelector('input[name="number_of_people"]').addEventListener('input', function() {
        var numPeople = this.value;
        var pricePerPerson = <?php echo $package['price']; ?>;
        var totalPrice = numPeople * pricePerPerson;
        
        document.getElementById('people_count').textContent = numPeople;
        document.getElementById('total_price').textContent = totalPrice;
    });
</script>
