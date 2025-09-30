<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get filter values from POST request or previous submission
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';
$purpose = isset($_POST['purpose']) ? $_POST['purpose'] : '';

// Build SQL query with filters
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            display: flex;
            background: url('https://kprcas.ac.in/file/wp-content/uploads/2024/12/kprcas-2048x912.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px;
        }

        .sidebar h2 { text-align: center; font-size: 24px; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 15px; }
        .sidebar ul li a {
            font-size: 18px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover { background: #4CAF50; transform: translateX(5px); }

        .main-content {
            flex: 1;
            padding: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            margin-left: 270px;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            flex-wrap: wrap;
        }

        .filter-form label, .filter-form select, .filter-form input, .filter-form button {
            padding: 10px;
            border-radius: 5px;
        }

        .filter-form input {
            flex: 1;
            border: 1px solid #ccc;
        }

        .filter-form button {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            text-align: left;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        .download-btn {
            background: #ff4d4d;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        .download-btn:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="visitor-log.php"><i class="fas fa-users"></i> Visitor Logs</a></li>
            <li><a href="manage-faculty.php"><i class="fas fa-chalkboard-teacher"></i> Manage Faculty</a></li>
            <li><a href="manage-classes.php"><i class="fas fa-school"></i> Manage Classes</a></li>
            <li><a href="report.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        </ul>
    </div>

    <div class="main-content">
    <h1>Visitor Reports</h1>
        <form method="POST" class="filter-form">
            <label>From:</label>
            <input type="date" name="from_date" value="<?= $from_date ?>">
            <label>To:</label>
            <input type="date" name="to_date" value="<?= $to_date ?>">
            <label>Purpose:</label>
            <select name="purpose">
                <option value="">All</option>
                <option value="Faculty Meeting" <?= ($purpose == 'Faculty Meeting') ? 'selected' : '' ?>>Faculty Meeting</option>
                <option value="Event" <?= ($purpose == 'Event') ? 'selected' : '' ?>>Event</option>
            </select>
            <label>Search:</label>
            <input type="text" name="search_query" placeholder="Search by Name or Phone" value="<?= $search_query ?>">
            <button type="submit">Filter</button>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Visitor Name</th>
                        <th>Phone</th>
                        <th>Persons Count</th>
                        <th>Entry Time</th>
                        <th>Exit Time</th>
                        <th>Purpose</th>
                        <th>Faculty Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['persons_count'] ?></td>
                            <td><?= $row['entry_time'] ?></td>
                            <td><?= $row['exit_time'] ? $row['exit_time'] : 'N/A' ?></td>
                            <td><?= $row['purpose'] ?></td>
                            <td><?= $row['faculty_name'] ? $row['faculty_name'] : 'N/A' ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <form method="POST" action="generate_report.php">
            <input type="hidden" name="from_date" value="<?= $from_date ?>">
            <input type="hidden" name="to_date" value="<?= $to_date ?>">
            <input type="hidden" name="search_query" value="<?= $search_query ?>">
            <input type="hidden" name="purpose" value="<?= $purpose ?>">
            <br>
            <button type="submit" class="download-btn">Download Report (PDF)</button>
        </form>

    </div>

</body>
</html>
