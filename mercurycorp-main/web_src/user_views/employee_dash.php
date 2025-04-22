<?php
// Include database configuration
include('../../data_src/includes/db_config.php');  
session_start(); 

$conn = new mysqli($host, $dbUsername, $dbPassword, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // Redirect to the login page
    header("Location: ../../web_src/login.php");
    exit;
}

// Fetch employee details
$username = $_SESSION['username'];

$sqlUser = "SELECT first_name FROM employees WHERE email = ?";
$stmtUser = $conn->prepare($sqlUser);

if ($stmtUser) {
    $stmtUser->bind_param("s", $username);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser && $resultUser->num_rows > 0) {
        $rowUser = $resultUser->fetch_assoc();
        $firstName = $rowUser['first_name'];
    }
    $stmtUser->close();
}

$sql = "SELECT e.emp_id, e.first_name, e.last_name, e.job_title, e.department_id, e.email, e.mobile_no, 
               d.dept_name, e.salary, e.hire_date
        FROM employees e 
        JOIN departments d ON e.department_id = d.dept_id 
        WHERE e.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$employee = $result->fetch_assoc();
if (!$employee) {
    echo "Employee not found.";
    exit;
}

// Handle announcement submission (if user has permission)
$announcementMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $employee['department_id'] == 1) {
    $announcement = $_POST['announcement'];
    $insertStmt = $conn->prepare("INSERT INTO announcements (content) VALUES (?)");
    $insertStmt->bind_param("s", $announcement);
    if ($insertStmt->execute()) {
        $announcementMessage = "Announcement updated successfully!";
    } else {
        $announcementMessage = "Failed to update announcement.";
    }
    $insertStmt->close();
}

// Fetch the latest announcement
$latestAnnouncement = "";
$announcementResult = $conn->query("SELECT content FROM announcements ORDER BY created_at DESC LIMIT 1");
if ($announcementResult && $announcementResult->num_rows > 0) {
    $rowAnnouncement = $announcementResult->fetch_assoc();
    $latestAnnouncement = $rowAnnouncement['content'];
}

$conn->close();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CSS Source-->
    <link href="../style.css" rel="stylesheet">
    <!-- Google Font API-->
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
    <title>Employee Dashboard</title>
</head>
<body>
<header class="row">
    <div class="col-1">
        <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
    </div>
    <div class="col">
        <h1 class="abril-fatface-regular">Mercury</h1>
    </div>
</header>  

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
            <!-- Navbar content collapses into a dropdown menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <h3>Nurse Dashboard</h3>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="nurse_dash.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="patient_analysis.php">Patient Analysis</a></li>
                    <li class="nav-item"><a class="nav-link" href="medical_records_dash.php">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_resident.php">Add Resident</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
    </nav><br>

<!-- Welcome Message -->
<div class="container mt-4">
    <h4>Welcome to your dashboard, <strong><?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?></strong>!</h4>
    <p>Your role: <strong><?php echo htmlspecialchars($employee['job_title']); ?></strong></p>
</div>

<!-- Employee Information -->
<section class="container mt-4">
    <div class="card shadow-sm">
        <!-- <div class="card-header" style="background-color: rgb(133, 161, 170); color: black; font-weight: bold">Your Information</div> -->
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>Full Name:</strong> <?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?></li>
                <li class="list-group-item"><strong>Job Title:</strong> <?php echo htmlspecialchars($employee['job_title']); ?></li>
                <li class="list-group-item"><strong>Department:</strong> <?php echo htmlspecialchars($employee['dept_name']); ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?></li>
                <li class="list-group-item"><strong>Phone:</strong> <?php echo htmlspecialchars($employee['mobile_no']); ?></li>
                <li class="list-group-item"><strong>Salary:</strong> <?php echo htmlspecialchars("$" . number_format($employee['salary'], 2)); ?></li>
                <li class="list-group-item"><strong>Hire Date:</strong> <?php echo htmlspecialchars($employee['hire_date']); ?></li>
            </ul>
        </div>
    </div>
</section>

<!-- Announcements -->
<section class="container mt-4">
    <h3 class="text-center mb-4" style="font-weight: bold;">Latest Announcement</h3>

    <div class="alert alert-info p-4 shadow-sm" style="border-radius: 8px; background-color: white; border-color: black; color: black;">
        <h5 class="alert-heading" style="font-weight: bold;">Announcement:</h5>
        <hr>
        <p><?php echo htmlspecialchars($latestAnnouncement); ?></p>
    </div>

    <?php if ($employee['department_id'] == 1): ?>
        <div class="card shadow-sm mt-4" style="border-radius: 8px;">
            <div class="card-header" style="background-color: rgb(133, 161, 170); color: white; font-weight: bold;">
                Create New Announcement
            </div>
            <div class="card-body">
                <form method="POST" class="mt-3">
                    <div class="form-group mb-3">
                        <label for="announcement" class="form-label" style="font-weight: bold;">New Announcement:</label>
                        <textarea name="announcement" class="form-control" rows="3" placeholder="Write your announcement here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #4b38a1;">Update Announcement</button>
                </form>
                <?php if (!empty($announcementMessage)): ?>
                    <div class="mt-2 alert alert-success text-center" style="margin-top: 10px;"><?php echo htmlspecialchars($announcementMessage); ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>


<footer class="text-center mt-4">
    <p>2024 Mercury Corp. All rights reserved.</p>
    <p>Follow us on social media!</p>
</footer>
</body>
</html>
