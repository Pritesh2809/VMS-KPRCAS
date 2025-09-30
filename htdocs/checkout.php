<?php
include 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['visitor_id'])) {
    $visitor_id = $_POST['visitor_id'];

    // Get current time in IST
    date_default_timezone_set('Asia/Kolkata');
    $exit_time = date("Y-m-d H:i:s");

    // Update exit time in the database
    $stmt = $conn->prepare("UPDATE visitors SET exit_time = ? WHERE id = ?");
    $stmt->bind_param("si", $exit_time, $visitor_id);

    if ($stmt->execute()) {
        echo "<script>alert('Visitor Checked Out Successfully!'); window.location.href='security-dashboard.php';</script>";
    } else {
        die("Error Checking Out Visitor: " . $stmt->error);
    }
} else {
    die("Invalid Request!");
}
?>
