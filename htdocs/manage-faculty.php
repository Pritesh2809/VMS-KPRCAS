<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch faculty data
$faculty_list = $conn->query("SELECT * FROM faculty ORDER BY department ASC, name ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty - Admin Dashboard</title>
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

        /* Main Content */
        .main-content { 
            flex: 1; 
            padding: 20px; 
            background: rgba(255, 255, 255, 0.2); 
            backdrop-filter: blur(10px); 
            border-radius: 10px; 
            margin-left: 270px; 
        }

        h2 { font-size: 24px; margin-bottom: 20px; }

        /* Add Faculty Form */
        .faculty-form { background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .faculty-form input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        .faculty-form button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; }

        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f2f2f2; }
        .delete-btn { background: red; color: white; padding: 5px; border: none; cursor: pointer; border-radius: 5px; }
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
        <h2>Manage Faculty</h2>

        <!-- Add Faculty Form -->
        <div class="faculty-form">
            <h3>Add Faculty Member</h3>
            <form action="add-faculty.php" method="POST">
                <input type="text" name="name" placeholder="Faculty Name" required>
                <input type="text" name="department" placeholder="Department" required>
                <input type="email" name="email" placeholder="Email ID" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <button type="submit">Add Faculty</button>
            </form>
        </div>

        <!-- Faculty List Table -->
        <table>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $faculty_list->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td>
                    <form action="delete-faculty.php" method="POST" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="faculty_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
