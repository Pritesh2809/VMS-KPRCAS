<?php
session_start();
include 'config.php';
require('fpdf/fpdf.php'); // Ensure the correct path to FPDF

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access denied!");
}

// Get filters from POST request
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';
$purpose = isset($_POST['purpose']) ? $_POST['purpose'] : '';

// Construct the query with filters
$query = "SELECT name, phone, persons_count, entry_time, exit_time, purpose, faculty_name FROM visitors WHERE 1=1";

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND DATE(entry_time) BETWEEN '$from_date' AND '$to_date'";
}

if (!empty($search_query)) {
    $query .= " AND (name LIKE '%$search_query%' OR phone LIKE '%$search_query%')";
}

if (!empty($purpose)) {
    $query .= " AND purpose = '$purpose'";
}

$query .= " ORDER BY entry_time DESC";

$result = $conn->query($query);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Visitor Report', 1, 1, 'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, 'Visitor Name', 1);
$pdf->Cell(30, 10, 'Phone', 1);
$pdf->Cell(20, 10, 'Persons', 1);
$pdf->Cell(40, 10, 'Entry Time', 1);
$pdf->Cell(40, 10, 'Exit Time', 1);
$pdf->Cell(30, 10, 'Purpose', 1);
$pdf->Ln();

// Fetch and display records
$pdf->SetFont('Arial', '', 10);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['name'], 1);
    $pdf->Cell(30, 10, $row['phone'], 1);
    $pdf->Cell(20, 10, $row['persons_count'], 1);
    $pdf->Cell(40, 10, $row['entry_time'], 1);
    $pdf->Cell(40, 10, $row['exit_time'] ? $row['exit_time'] : 'N/A', 1);
    $pdf->Cell(30, 10, $row['purpose'], 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'Visitor_Report.pdf'); // Forces download
?>
