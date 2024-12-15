<?php
include('db.php'); // Include the database connection

// Check if the connection is successful
if ($conn) {
    echo "Connection successful!";
} else {
    echo "Connection failed.";
}
?>
