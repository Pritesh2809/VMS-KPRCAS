<?php
session_start();
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : "";
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";
    $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : "Other";
    $faculty_name = NULL;

    // Get faculty name if selected
    if ($purpose === "Faculty Meeting" && isset($_POST['faculty_id']) && !empty($_POST['faculty_id'])) {
        $faculty_id = intval($_POST['faculty_id']);
        $faculty_stmt = $conn->prepare("SELECT name, email FROM faculty WHERE id = ?");
        $faculty_stmt->bind_param("i", $faculty_id);
        $faculty_stmt->execute();
        $faculty_result = $faculty_stmt->get_result();
        
        if ($faculty_row = $faculty_result->fetch_assoc()) {
            $faculty_name = $faculty_row['name'];
            $faculty_email = $faculty_row['email'];
        }
    }

    date_default_timezone_set('Asia/Kolkata');
    $entry_time = date("Y-m-d H:i:s");

    // Handle Image Upload
    $photoData = isset($_POST['photo_data']) && !empty($_POST['photo_data']) ? $_POST['photo_data'] : NULL;
    $imageBase64 = NULL;

    if (!empty($photoData)) {
        $imageParts = explode(";base64,", $photoData);
        if (isset($imageParts[1])) {
            $imageBase64 = base64_decode($imageParts[1]); // Decode Base64
        }
    }

    // Debugging to Check Image Data
    if (empty($name) || empty($phone) || empty($purpose)) {
        die("Error: Some required fields are empty.");
    }

    // Insert visitor data into database
    $stmt = $conn->prepare("INSERT INTO visitors (name, phone, purpose, faculty_name, photo, entry_time) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $phone, $purpose, $faculty_name, $null, $entry_time);
    
    if (!empty($imageBase64)) {
        $stmt->send_long_data(4, $imageBase64); // Store image as BLOB
    }

    if ($stmt->execute()) {
        // Send Email Notification for Faculty Meeting
        if ($purpose === "Faculty Meeting" && !empty($faculty_email)) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'kprcas.vms@gmail.com'; // Replace with your Gmail
                $mail->Password = 'fqvv nwiw gnkc ffdy'; // Replace with your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your-email@gmail.com', 'Visitor Management System');
                $mail->addAddress($faculty_email, $faculty_name);

                $mail->isHTML(true);
                $mail->Subject = 'Visitor Notification';
                $mail->Body = "<p>Dear $faculty_name,</p>
                               <p>A visitor has arrived to meet you.</p>
                               <p><strong>Name:</strong> $name</p>
                               <p><strong>Phone:</strong> $phone</p>
                               <p><strong>Purpose:</strong> Faculty Meeting</p>
                               <p>Thank you.</p>";

                $mail->send();
            } catch (Exception $e) {
                error_log("Mail Error: " . $mail->ErrorInfo);
            }
        }

        echo "<script>alert('Visitor Added Successfully!'); window.location.href='security-dashboard.php';</script>";
    } else {
        die("Database Error: " . $stmt->error);
    }
} else {
    die("Invalid request!");
}
?>
