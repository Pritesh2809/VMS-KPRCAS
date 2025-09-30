<?php
session_start();
include 'config.php';

// Check if security is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'security') {
    header("Location: index.php");
    exit();
}

// Fetch active visitors (who haven't checked out)
$active_visitors = $conn->query("SELECT id, name, phone, purpose, faculty_name, entry_time, photo FROM visitors WHERE exit_time IS NULL ORDER BY entry_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Visitors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { 
            display: flex; 
            height: 100vh; 
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
            overflow-x: auto;
        }

        h2 { font-size: 24px; margin-bottom: 20px; color: white; }

        .visitor-list { margin-top: 20px; }
        .visitor-list table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #4CAF50; color: white; }
        .visitor-photo { width: 60px; height: 60px; border-radius: 50%; }
        .checkout-btn { background: red; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 5px; font-size: 14px; }

        @media (max-width: 768px) {
            .main-content { margin-left: 260px; padding: 10px; }
            th, td { font-size: 14px; padding: 8px; }
            .checkout-btn { font-size: 12px; padding: 6px 10px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Security Panel</h2>
        <ul>
            <li><a href="security-dashboard.php">Dashboard</a></li>
            <li><a href="active-visitors.php">Active Visitors</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Active Visitors</h2>
        
        <div class="visitor-list">
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Purpose</th>
                    <th>Faculty</th>
                    <th>Entry Time</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $active_visitors->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php if (!empty($row['photo'])) { ?>
                            <img src="data:image/png;base64,<?php echo base64_encode($row['photo']); ?>" class="visitor-photo">
                        <?php } else { ?>
                            No Photo
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                    <td><?php echo htmlspecialchars($row['faculty_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['entry_time']); ?></td>
                    <td>
                        <form action="checkout.php" method="POST">
                            <input type="hidden" name="visitor_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="checkout-btn">Checkout</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>
</html>
