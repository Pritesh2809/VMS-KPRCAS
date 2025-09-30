<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from database
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Compare plain text password
        if ($password == $user['password']) {  
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: security-dashboard.php");
            }
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
    <title>Visitor Management System - Login</title>
  

    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Background Image with Dark Overlay */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: url('https://kprcas.ac.in/file/wp-content/uploads/2024/12/kprcas-2048x912.jpg') 
                        no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Dark Overlay to Reduce Brightness */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Darker overlay */
            z-index: 1;
        }

        /* Glassmorphic Login Box */
        .login-container {
            position: relative;
            z-index: 2;
            width: 360px;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(15px);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Neon Title */
        .login-container h2 {
            font-size: 26px;
            color: #ffffff;
            text-shadow: 0 0 5px #4CAF50, 0 0 10px #4CAF50;
            margin-bottom: 20px;
        }

        /* Input Fields */
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: 0.3s ease-in-out;
        }

        /* Input Focus Effect */
        .login-container input:focus {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid #4CAF50;
            box-shadow: 0 0 10px #4CAF50;
        }

        /* Animated Button */
        .login-container button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #4CAF50, #2E8B57);
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.4s ease-in-out;
            box-shadow: 0 0 5px #4CAF50, 0 0 10px #4CAF50;
        }

        /* Button Hover Effect */
        .login-container button:hover {
            background: linear-gradient(45deg, #2E8B57, #4CAF50);
            transform: scale(1.05);
            box-shadow: 0 0 15px #4CAF50, 0 0 30px #4CAF50;
        }

        /* Error Message */
        .error-message {
            color: #ff4d4d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .login-container {
                width: 90%;
                padding: 25px;
            }

            .login-container h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔒 Secure Login</h2>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
