<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function send_email_to_faculty($faculty_id, $visitor_name, $visitor_phone) {
    global $conn;
    $query = "SELECT email FROM faculty WHERE id='$faculty_id'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $faculty = $result->fetch_assoc();
        $faculty_email = $faculty['email'];

        $mail = new PHPMailer(true);
        try {
            $mail->setFrom('admin@kprcas.com', 'Visitor Management System');
            $mail->addAddress($faculty_email);
            $mail->Subject = "Visitor Entry Notification";
            $mail->Body = "Hello, A visitor ($visitor_name, $visitor_phone) has arrived for a meeting.";
            $mail->send();
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
        }
    }
}
?>
