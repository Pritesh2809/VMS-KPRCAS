<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Handle Add Department
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_department'])) {
    $department_name = trim($_POST['department_name']);
    if (!empty($department_name)) {
        $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (?)");
        $stmt->bind_param("s", $department_name);
        $stmt->execute();
    }
}

// Handle Delete Department
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage-classes.php");
    exit();
}

// Fetch Departments
$result = $conn->query("SELECT * FROM departments ORDER BY department_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; height: 100vh; background: url('https://kprcas.ac.in/file/wp-content/uploads/2024/12/kprcas-2048x912.jpg') no-repeat center center fixed; background-size: cover; }
        .sidebar { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: rgba(0, 0, 0, 0.9); color: white; padding: 20px; }
        .sidebar h2 { text-align: center; font-size: 24px; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li a { font-size: 18px; color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 5px; }
        .sidebar ul li a:hover { background: #4CAF50; }
        .main-content { flex: 1; padding: 20px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border-radius: 10px; margin-left: 270px; }
        h1 { color: white; }
        .form-container { margin-top: 20px; }
        input, button { padding: 10px; margin: 5px; border: none; border-radius: 5px; }
        button { background: #4CAF50; color: white; cursor: pointer; }
        button:hover { background: #388E3C; }

         /* Table Styles */
         table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f2f2f2; }
        .delete-btn { background: red; color: white; padding: 5px; border: none; cursor: pointer; border-radius: 5px; }
    

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
        <h1>Manage Classes</h1>
        <form method="POST" class="form-container">
            <input type="text" name="department_name" placeholder="Enter Department Name" required>
            <button type="submit" name="add_department">Add Department</button>
        </form>
        <table>
            <tr>
                <th>ID</th>
                <th>Department Name</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['department_name']) ?></td>
                <td><a href="?delete=<?= $row['id'] ?>" class="delete-btn">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
