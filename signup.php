<?php
// Include database connection
include('db.php');

// Initialize message variables
$errors = [
    'full_name' => '',
    'email' => '',
    'username' => '',
    'password' => '',
    'confirm_password' => '',
    'general' => ''
];

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate username format (alphanumeric only, 3 to 20 characters)
// Function to validate username format (allowing underscores)
function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}


// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data with sanitization
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the passwords match
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match!";
    }

    // Validate email format
    if (!isValidEmail($email)) {
        $errors['email'] = "Invalid email format!";
    }

    // Validate username format
    if (!isValidUsername($username)) {
        $errors['username'] = "Username must be 3-20 alphanumeric characters only!";
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors['email'] = "Email already in use!";
    }

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors['username'] = "Username already in use!";
    }

    // If no errors, proceed
    if (empty(array_filter($errors))) {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $username, $hashed_password);

        if ($stmt->execute()) {
            header("Location: success.php");
            exit();
        } else {
            $errors['general'] = "Database error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .message-container { margin: 10px; text-align: center; }
        .error { color: red; font-size: 14px; }
        .container { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>

<!-- General error message -->
<?php if ($errors['general']): ?>
    <div class="message-container">
        <div class="error"><?php echo $errors['general']; ?></div>
    </div>
<?php endif; ?>

<!-- Signup Form -->
<div class="container">
    <h2>Sign Up</h2>
    <form method="POST" action="signup.php">
        
        <!-- Full Name -->
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" placeholder="Full Name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
            <?php if ($errors['full_name']): ?>
                <div class="error"><?php echo $errors['full_name']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            <?php if ($errors['email']): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Username -->
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            <?php if ($errors['username']): ?>
                <div class="error"><?php echo $errors['username']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Password" required>
            <?php if ($errors['password']): ?>
                <div class="error"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <?php if ($errors['confirm_password']): ?>
                <div class="error"><?php echo $errors['confirm_password']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Sign Up</button>
    </form>
    <a href="login.php">Already have an account? Log in here</a>
</div>

</body>
</html>
