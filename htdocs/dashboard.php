<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch statistics
$today_visitors = $conn->query("SELECT COUNT(*) AS count FROM visitors WHERE DATE(entry_time) = CURDATE()")->fetch_assoc();
$total_visitors = $conn->query("SELECT COUNT(*) AS count FROM visitors")->fetch_assoc();
$active_visitors = $conn->query("SELECT COUNT(*) AS count FROM visitors WHERE exit_time IS NULL")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        /* Background */
        body { 
            display: flex; 
            height: 100vh; 
            background: url('https://kprcas.ac.in/file/wp-content/uploads/2024/12/kprcas-2048x912.jpg') no-repeat center center fixed; 
            background-size: cover; 
        }

        /* Sidebar */
        .sidebar { 
            position: fixed; 
            left: 0; 
            top: 0; 
            width: 250px; 
            height: 100vh; 
            background: rgba(0, 0, 0, 0.9); 
            color: white; 
            padding: 20px; 
            transition: width 0.3s ease-in-out;
            z-index: 3;
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

        .sidebar ul li a i { font-size: 22px; }

        .sidebar ul li a:hover { background: #4CAF50; transform: translateX(5px); }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 10px;
                text-align: center;
            }

            .sidebar h2 {
                display: none;
            }

            .sidebar ul li a {
                font-size: 0;
                justify-content: center;
            }

            .sidebar ul li a i {
                font-size: 22px;
            }

            .sidebar:hover {
                width: 200px;
                text-align: left;
            }

            .sidebar:hover ul li a {
                font-size: 16px;
                justify-content: left;
                padding-left: 20px;
            }
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            margin-left: 270px;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 80px;
            }
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            font-size: 24px;
            font-weight: bold;
        }

        .logout-btn {
            background: linear-gradient(45deg, #ff4d4d, #cc0000);
            padding: 12px 18px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s ease-in-out, transform 0.2s ease;
            box-shadow: 0px 4px 8px rgba(255, 0, 0, 0.3);
        }

        .logout-btn:hover {
            background: linear-gradient(45deg, #cc0000, #990000);
            transform: scale(1.05);
            box-shadow: 0px 6px 12px rgba(255, 0, 0, 0.5);
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
            flex: 1;
            min-width: 250px;
            color: white;
        }

        .card h3 { font-size: 20px; }
        .card p { font-size: 24px; font-weight: bold; color: #4CAF50; }

        /* Stack Cards for Mobile */
        @media (max-width: 768px) {
            .dashboard-cards { flex-direction: column; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
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

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="dashboard-cards">
            <div class="card"><h3>Visitors Today</h3><p><?php echo $today_visitors['count']; ?></p></div>
            <div class="card"><h3>Total Visitors</h3><p><?php echo $total_visitors['count']; ?></p></div>
            <div class="card"><h3>Active Visitors</h3><p><?php echo $active_visitors['count']; ?></p></div>
        </div>
    </div>

</body>
</html>
