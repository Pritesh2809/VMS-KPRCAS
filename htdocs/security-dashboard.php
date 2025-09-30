<?php
session_start();
include 'config.php';

// Check if security is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'security') {
    header("Location: index.php");
    exit();
}

// Fetch faculty list (sorted by department)
$faculty_list = $conn->query("SELECT id, name, department FROM faculty ORDER BY department ASC, name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
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

        .logout-btn {
            background: linear-gradient(45deg, #ff4d4d, #cc0000);
            color: white;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            display: block;
            text-decoration: none;
            transition: background 0.3s ease-in-out, transform 0.2s ease;
        }

        .logout-btn:hover {
            background: linear-gradient(45deg, #cc0000, #990000);
            transform: scale(1.05);
        }

        .main-content { 
            flex: 1; 
            padding: 20px; 
            background: rgba(255, 255, 255, 0.2); 
            backdrop-filter: blur(10px); 
            border-radius: 10px; 
            margin-left: 270px; 
        }

        h2 { font-size: 24px; margin-bottom: 20px; }

        .visitor-form { background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 8px; margin-top: 20px; }
        .visitor-form input, .visitor-form select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        .visitor-form button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; }

        .photo-section { text-align: center; margin-top: 20px; }
        #camera { width: 100%; max-width: 400px; border-radius: 10px; }
        #capturedImage { display: none; width: 100%; max-width: 400px; border-radius: 10px; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <h2>Security Panel</h2>
            <ul>
                <li><a href="security-dashboard.php">Dashboard</a></li>
                <li><a href="active-visitors.php">Active Visitors</a></li>
            </ul>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h2>Security Dashboard</h2>

        <div class="visitor-form">
            <h3>Log Visitor Entry</h3>
            <form action="capture.php" method="POST">
                <input type="text" name="name" placeholder="Visitor Name" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <select name="purpose" id="purposeSelect" required onchange="toggleFacultyDropdown()">
                    <option value="">Select Purpose</option>
                    <option value="Admission">Admission</option>
                    <option value="Faculty Meeting">Faculty Meeting</option>
                    <option value="Event Entry">Event Entry</option>
                    <option value="Other">Other</option>
                </select>

                <select name="faculty_id" id="facultyDropdown" style="display: none;">
                    <option value="">Select Faculty</option>
                    <?php while ($faculty = $faculty_list->fetch_assoc()) { ?>
                        <option value="<?php echo $faculty['id']; ?>">
                            <?php echo $faculty['name'] . " (" . $faculty['department'] . ")"; ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="number" name="persons_count" placeholder="Number of Persons" min="1" value="1" required>

                <div class="photo-section">
                    <video id="camera" autoplay></video>
                    <canvas id="photoCanvas" style="display: none;"></canvas>
                    <img id="capturedImage" style="display: none;">
                    <input type="hidden" name="photo_data" id="photoData">
                </div>

                <button type="button" onclick="capturePhoto()">Capture Photo</button>
                <button type="submit">Submit Entry</button>
            </form>
        </div>
    </div>

<script>
    function toggleFacultyDropdown() {
        let purposeSelect = document.getElementById("purposeSelect");
        let facultyDropdown = document.getElementById("facultyDropdown");
        facultyDropdown.style.display = (purposeSelect.value === "Faculty Meeting") ? "block" : "none";
    }

    let video = document.getElementById("camera");
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { video.srcObject = stream; })
        .catch(err => { alert("Camera error: " + err.message); });

    function capturePhoto() {
        let canvas = document.getElementById("photoCanvas");
        let context = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        let imageData = canvas.toDataURL("image/png");
        document.getElementById("photoData").value = imageData;

        let capturedImage = document.getElementById("capturedImage");
        capturedImage.src = imageData;
        capturedImage.style.display = "block";
        video.style.display = "none";
    }
</script>

</body>
</html>
