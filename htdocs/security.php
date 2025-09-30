<?php
session_start();
include 'config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch security user from database
    $query = "SELECT * FROM users WHERE username='$username' AND role='security'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if ($password == $user['password']) {  // Use password_verify() if passwords are hashed
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'security';
            header("Location: security-dashboard.php");  // Redirect to security dashboard
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Login</title>
</head>
<body>
    <h2>Security Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
