<?php
include 'config.php';

$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$security_password = password_hash('security123', PASSWORD_DEFAULT);

$query = "INSERT INTO users (username, password, role) VALUES 
          ('admin', '$admin_password', 'admin'), 
          ('security', '$security_password', 'security')";

if ($conn->query($query) === TRUE) {
    echo "Admin & Security users created!";
} else {
    echo "Error: " . $conn->error;
}
?>
