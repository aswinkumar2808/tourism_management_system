<?php
include('db.php');

$errors = [
    'full_name' => '',
    'email' => '',
    'username' => '',
    'password' => '',
    'confirm_password' => '',
    'general' => ''
];

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match!";
    }

    if (!isValidEmail($email)) {
        $errors['email'] = "Invalid email format!";
    }

    if (!isValidUsername($username)) {
        $errors['username'] = "Username must be 3-20 alphanumeric characters only!";
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors['email'] = "Email already in use!";
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors['username'] = "Username already in use!";
    }

    if (empty(array_filter($errors))) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $username, $hashed_password);

        if ($stmt->execute()) {
            header("Location: success.php");
            exit();
        } else {
            $errors['general'] = "Database error: " . $stmt->error;
        }

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

<?php if ($errors['general']): ?>
    <div class="message-container">
        <div class="error"><?php echo $errors['general']; ?></div>
    </div>
<?php endif; ?>

<div class="container">
    <h2>Sign Up</h2>
    <form method="POST" action="signup.php">
        
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" placeholder="Full Name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
            <?php if ($errors['full_name']): ?>
                <div class="error"><?php echo $errors['full_name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            <?php if ($errors['email']): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            <?php if ($errors['username']): ?>
                <div class="error"><?php echo $errors['username']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Password" required>
            <?php if ($errors['password']): ?>
                <div class="error"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>

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
