<?php
include 'config.php';

$date = date("Y-m-d");
$filename = "reports/visitor_log_$date.csv";
$file = fopen($filename, "w");

$headers = ["Date", "Name", "Phone", "Persons", "Purpose", "Entry Time", "Exit Time"];
fputcsv($file, $headers);

$query = "SELECT name, phone, persons_count, purpose, entry_time, exit_time FROM visitors WHERE DATE(entry_time) = CURDATE()";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($file, $row);
}

fclose($file);

// Email the report
include 'email.php';
send_email_to_admin($filename);

echo "Report generated and emailed!";
?>
