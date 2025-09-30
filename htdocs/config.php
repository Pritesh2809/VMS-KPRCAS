<?php
date_default_timezone_set('Asia/Kolkata'); // Set PHP timezone to IST

$servername = "mysql.selfmade.ninja";
$username = "VMSKPRCAS";
$password = "123456789";
$dbname = "VMSKPRCAS_DATA";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set MySQL session timezone to IST (UTC+5:30)
$conn->query("SET time_zone = '+05:30'");
?>
