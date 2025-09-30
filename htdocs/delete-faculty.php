<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['faculty_id'])) {
    $faculty_id = $_POST['faculty_id'];

    $stmt = $conn->prepare("DELETE FROM faculty WHERE id = ?");
    $stmt->bind_param("i", $faculty_id);

    if ($stmt->execute()) {
        echo "<script>alert('Faculty Deleted Successfully!'); window.location.href='manage-faculty.php';</script>";
    } else {
        die("Error Deleting Faculty: " . $stmt->error);
    }
}
?>
