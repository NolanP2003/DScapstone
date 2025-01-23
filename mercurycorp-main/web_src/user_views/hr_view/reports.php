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
// Fetch total employees
$sqlTotalEmployees = "SELECT COUNT(*) as total_employees FROM employees";
$totalEmployees = $conn->query($sqlTotalEmployees)->fetch_assoc()['total_employees'];

// Fetch average salary by department
$sqlAvgSalaryDept = "SELECT d.dept_name, AVG(e.salary) as avg_salary 
                     FROM employees e 
                     JOIN departments d ON e.department_id = d.dept_id 
                     GROUP BY d.dept_name";
$avgSalaryDept = $conn->query($sqlAvgSalaryDept);

// Fetch total payroll data
$sqlPayroll = "SELECT SUM(salary) as total_salary FROM employees";
$totalPayroll = $conn->query($sqlPayroll)->fetch_assoc()['total_salary'];
$monthlyPayroll = $totalPayroll / 12;

// Calculate taxes and deductions
$totalTaxes = $totalPayroll * 0.2; // Assuming 20% tax rate
$totalDeductions = $totalPayroll * 0.1; // Assuming 10% deductions

// Fetch salary distribution
$sqlSalaryDist = "SELECT MIN(salary) as min_salary, MAX(salary) as max_salary FROM employees";
$salaryDist = $conn->query($sqlSalaryDist)->fetch_assoc();

// Fetch average tenure
$sqlAvgTenure = "SELECT AVG(DATEDIFF(CURDATE(), hire_date) / 365) as avg_tenure FROM employees";
$avgTenure = round($conn->query($sqlAvgTenure)->fetch_assoc()['avg_tenure'], 2);
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
    <title>Reports</title>
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
            <h3>Reports</h3>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="hr_dash.php">HR Dash</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>

            
        </div>
    </nav>
    <br>
    
    <div class="container mt-5">
    
    <!-- Employee Reports Section -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h3 class="text-primary">Employee Reports</h3>
            <p>Total Employees: <strong><?php echo $totalEmployees; ?></strong></p>
            <p>Average Salary by Department:</p>
            <ul>
            <?php while ($row = $avgSalaryDept->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($row['dept_name']); ?>: $<?php echo number_format($row['avg_salary'], 2); ?></li>
            <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- Payroll Reports Section -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h3 class="text-success">Payroll Reports</h3>
            <p>Monthly Total Payroll: <strong>$<?php echo number_format($monthlyPayroll, 2); ?></strong></p>
            <p>Yearly Total Payroll: <strong>$<?php echo number_format($totalPayroll, 2); ?></strong></p>
            <p>Taxes and Deductions Summary:</p>
            <ul>
                <li>Total Taxes: $<?php echo number_format($totalTaxes, 2); ?></li>
                <li>Total Deductions: $<?php echo number_format($totalDeductions, 2); ?></li>
            </ul>
        </div>
    </div>

    <!-- Department Performance Section -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h3 class="text-warning">Department Performance</h3>
            <p>Salary Distribution:</p>
            <ul>
                <li>Lowest Salary: $<?php echo number_format($salaryDist['min_salary'], 2); ?></li>
                <li>Highest Salary: $<?php echo number_format($salaryDist['max_salary'], 2); ?></li>
            </ul>
            <p>Employee Tenure:</p>
            <ul>
                <li>Average Tenure: <?php echo $avgTenure; ?> years</li>
            </ul>
        </div>
    </div>

    <!-- Export Options -->
    <!-- <div class="card bg-light mb-4">
        <div class="card-body text-center">
            <h3 class="text-info">Export Options</h3>
            <button class="btn btn-primary">Export as PDF</button>
            <button class="btn btn-secondary">Export as CSV</button>
        </div>
    </div> -->
</div>

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
