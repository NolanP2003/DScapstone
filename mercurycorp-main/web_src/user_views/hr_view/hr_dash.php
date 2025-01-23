<?php
session_start();
include('../../../data_src/includes/db_config.php');
// Establish a database connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// // Check if the user is logged in by verifying if 'username' is set in the session
// if (!isset($_SESSION['username'])) {
//     // If the username is not set, redirect to the login page
//     header("Location: ../../../data_src/api/user/login.php");
//     exit;
// }

// Start the session to get the logged-in user's username

$username = $_SESSION['username'];
$firstName = '';
$totalEmployees = 0;
$totalDepartments = 0;

// Step 1: Fetch the logged-in user's first name
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

// Step 2: Fetch the total number of employees
$sqlEmployees = "SELECT COUNT(*) as total FROM employees";
$resultEmployees = $conn->query($sqlEmployees);

if ($resultEmployees && $resultEmployees->num_rows > 0) {
    $rowEmployees = $resultEmployees->fetch_assoc();
    $totalEmployees = $rowEmployees['total'];
}

// Step 3: Fetch the total number of departments
$sqlDepartments = "SELECT COUNT(*) as total FROM departments";
$resultDepartments = $conn->query($sqlDepartments);

if ($resultDepartments && $resultDepartments->num_rows > 0) {
    $rowDepartments = $resultDepartments->fetch_assoc();
    $totalDepartments = $rowDepartments['total'];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <!-- CSS Source-->
  <link href="../../style.css" rel="stylesheet">
  <!-- Google Font API-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
  <!-- JavaScript Source-->
  <!-- <script src="main.js"></script> -->
  <title>HR Dashboard</title>
  <!--Font Awesome-->
  <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
      </header>  
<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
            <!-- Collapsible button on the left
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- Navbar content collapses into a dropdown menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="emp_man/employee_man.php">Employee Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="department_man.php">Department Management</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance & Leave</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="payroll.php">Payroll</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../employee_dash.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    <li class="nav-item"><a href="../../index.php" class="nav-link">Home</a></li>
                </ul>
    </nav>
    
<!-- Main Content -->
    <br><br>
    <h4 >Welcome,<?php echo htmlspecialchars($firstName); ?></h4>
    <p>Here's an overview of your HR management tools.</p>

<div class="row text-center my-4">
    <div class="col-md-3">
        <div class="card" style="background-color: #5a7f85; color: #fdf8ef;">
            <div class="card-body">
                <h5>Total Employees</h5>
                <h3><?php echo htmlspecialchars($totalEmployees); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background-color: rgba(98, 75, 182, 0.667); color: #fdf8ef;" >
            <div class="card-body">
                <h5>Departments</h5>
                <h3><?php echo htmlspecialchars($totalDepartments); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background-color: #5a7f85; color: #fdf8ef;">
            <div class="card-body">
                <h5>Open Positions</h5>
                <h3>5</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background-color: rgba(98, 75, 182, 0.667); color: #fdf8ef;">
            <div class="card-body">
                <h5>Pending Leave Requests</h5>
                <h3>12</h3>
            </div>
        </div>
    </div>
</div>

<br>
<div class="card my-4">
    <div class="card-header bg-secondary text-white">Recent Activities</div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">John Doe requested leave on 11-05-2024</li>
        <li class="list-group-item">New employee added: Jane Smith</li>
        <li class="list-group-item">Payroll processed for October 2024</li>
    </ul>
</div>

</body>

<footer>
  <p> 2024 Mercury Corp. All rights reserved.</p>
  <p>Follow us on social media!</p>
    <a href="https://github.com/Laneyeh">
  <img class="socialMediaIcon" src="../../photos/facebook.png" alt="Facebook">
</a>
<a href="https://github.com/torrescschool">
  <img class="socialMediaIcon" src="../../photos/instagram.png" alt="Instagram">
</a>
<a href="https://github.com/Mildred1999">
  <img class="socialMediaIcon" src="../../photos/twitter.png" alt="Twitter">
</a>
</footer>
</html>