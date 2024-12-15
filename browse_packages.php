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

// Initialize variables
$search = isset($_GET['search']) ? $_GET['search'] : "";
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : PHP_INT_MAX;

// Query to fetch packages based on search and price filters
$sql = "SELECT * FROM packages WHERE 
        (name LIKE '%$search%' OR description LIKE '%$search%') 
        AND price BETWEEN $min_price AND $max_price";

$result = $conn->query($sql); // Execute the query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Packages</title>
</head>
<body>

<h2>Browse Tour Packages</h2>

<!-- Search Form -->
<form method="GET" action="browse_packages.php">
    <input type="text" name="search" placeholder="Search packages..." value="<?php echo htmlspecialchars($search); ?>">
    <label>Min Price:</label>
    <input type="number" name="min_price" placeholder="Min Price" value="<?php echo $min_price; ?>">
    <label>Max Price:</label>
    <input type="number" name="max_price" placeholder="Max Price" value="<?php echo $max_price; ?>">
    <button type="submit">Search</button>
</form>

<!-- Display Packages -->
<?php if ($result->num_rows > 0): ?>
    <ul>
        <?php while ($package = $result->fetch_assoc()): ?>
            <li>
                <h3><?php echo $package['name']; ?></h3>
                <p><?php echo $package['description']; ?></p>
                <p>Price: ?<?php echo $package['price']; ?></p>
                <a href="book_package.php?package_id=<?php echo $package['package_id']; ?>">Book Now</a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No packages found.</p>
<?php endif; ?>

</body>
</html>
