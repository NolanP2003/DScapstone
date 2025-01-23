<?php
include('../../../data_src/includes/db_config.php');  
$conn = new mysqli($host, $dbUsername, $dbPassword, $database);


// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all employees (nurses and other roles)
$sql = "SELECT e.emp_id, e.first_name, e.last_name, e.job_title, e.department_id, e.email, e.salary, d.dept_name, e.dob, e.hire_date
        FROM employees e JOIN departments d ON e.department_id = d.dept_id";
$result = $conn->query($sql);

// Check if there are employees
$employees = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
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
    <title>Payroll</title>
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
      <nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
    
            
            <div class="collapse navbar-collapse" id="navbarNav">
            <h3>Payroll</h3>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="hr_dash.php">HR Dash</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>

            
        </div>
    </nav>
    <br>
    
<!-- Employee Table -->
<table class="table table-striped table-bordered mt-4">
    <thead>
        <tr>
            <th>Name</th>
            <th>Job Title</th>
            <th>Department</th>
            <th>Salary</th>
            <th>Tax</th>
            <th>Deductions</th>
            <th>Net Pay</th>
            <th>Pay Period</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $employee): 
            $salary = $employee['salary'];
            // Generate random values
            $tax = $salary * 0.2; // Tax up to 30% of salary
            $deductions = $salary * 0.05; // Deductions up to 10% of salary
            $netPay = $salary - ($tax + $deductions); // Ensure net pay is valid
        ?> 
            <tr>
                <td><?php echo htmlspecialchars($employee['first_name']) . " " . htmlspecialchars($employee['last_name']); ?></td>
                <td><?php echo htmlspecialchars($employee['job_title']); ?></td>
                <td><?php echo htmlspecialchars($employee['dept_name']); ?></td>
                <td>$<?php echo number_format($salary, 2); ?></td>
                <td>$<?php echo number_format($tax, 2); ?></td>
                <td>$<?php echo number_format($deductions, 2); ?></td>
                <td>$<?php echo number_format($netPay, 2); ?></td>
                <td>11/01/2024 - 11/30/2024</td>
                <td><a href="#" class="btn btn-warning">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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
</body>
</html>

