<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO faculty (name, department, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $department, $email, $phone);

    if ($stmt->execute()) {
        echo "<script>alert('Faculty Added Successfully!'); window.location.href='manage-faculty.php';</script>";
    } else {
        die("Error Adding Faculty: " . $stmt->error);
    }
}
?>
