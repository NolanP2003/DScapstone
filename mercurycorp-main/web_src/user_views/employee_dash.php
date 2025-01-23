<?php
// Include database configuration
include('../../data_src/includes/db_config.php');  
session_start(); 

$conn = new mysqli($host, $dbUsername, $dbPassword, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
// if (!isset($_SESSION['emp_id'])) {
//     header('Location: login.php'); // Redirect to login if not logged in
//     exit;
// }

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
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <!-- CSS Source-->
  <link href="../style.css" rel="stylesheet">
  <!-- Google Font API-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
  <!-- JavaScript Source-->
  <!-- <script src="main.js"></script> -->
    <title>Employee Dashboard</title>
    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
    <title>Employee Dashboard</title>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
      </header>  
      <nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
    
            
            <div class="collapse navbar-collapse" id="navbarNav">
            <h3>Employee Dashboard</h3>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>

            <!-- Home button on the far right -->

            
        </div>
    </nav>
    <br>
    <!-- Employee Dashboard -->
    <h4>Welcome to your dashboard, <strong><?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?></strong>!</h4>
    <p>Your role: <strong><?php echo htmlspecialchars($employee['job_title']); ?></strong></p><br><br>


 <!-- Employee Information Card -->
 <section style="display: flex; justify-content: center; margin-bottom: 20px;">
    <div class="card" style="width: 60%; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
      <h5 class="card-title" style="text-align: center;"><strong>Your Information</strong></h5>
      <ul style="list-style: none; padding-left: 0;">
        <li><strong>Full Name:</strong> <?php echo htmlspecialchars($employee['first_name'] . " " . $employee['last_name']); ?></li>
        <li><strong>Job Title:</strong> <?php echo htmlspecialchars($employee['job_title']); ?></li>
        <li><strong>Department:</strong> <?php echo htmlspecialchars($employee['dept_name']); ?></li>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?></li>
        <li><strong>Phone:</strong> <?php echo htmlspecialchars($employee['mobile_no']); ?></li>
        <li><strong>Salary:</strong> <?php echo htmlspecialchars("$" . number_format($employee['salary'], 2)); ?></li>
        <li><strong>Hire Date:</strong> <?php echo htmlspecialchars($employee['hire_date']); ?></li>
      </ul>
    </div>
  </section>


    <section style="text-align: center; margin-top: 20px;">
        <h3>Announcements</h3>
        <p><strong>Upcoming Holiday:</strong> The office will be closed on December 25th for Christmas.</p>
        <p><strong>Employee of the month: </strong>Congratulations to <strong>Daniel Doe</strong> for outstanding performance and dedication!</p>
    </section>

    <footer>
  <p> 2024 Mercury Corp. All rights reserved.</p>
  <p>Follow us on social media!</p>
    <a href="https://github.com/Laneyeh">
  <img class="socialMediaIcon" src="../photos/facebook.png" alt="Facebook">
</a>
<a href="https://github.com/torrescschool">
  <img class="socialMediaIcon" src="../photos/instagram.png" alt="Instagram">
</a>
<a href="https://github.com/Mildred1999">
  <img class="socialMediaIcon" src="../photos/twitter.png" alt="Twitter">
</a>
</footer>
</body>
</html>
