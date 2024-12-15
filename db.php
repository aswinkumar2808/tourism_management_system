<?php
// Database configuration
$host = 'localhost';      // Server host (localhost)
$username = 'root';       // MySQL username (default: 'root' for XAMPP)
$password = '';           // MySQL password (default: empty for XAMPP)
$dbname = 'tourism_system'; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Connection successful
?>
