<?php
// Include database configuration
include('../../../data_src/includes/db_config.php');  
$conn = new mysqli($host, $dbUsername, $dbPassword, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch departments and their employee counts
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $searchQuery = " WHERE d.dept_name LIKE '%$search%'";
}

$sql = "SELECT d.dept_id, d.dept_name, COUNT(e.emp_id) AS employee_count
        FROM departments d
        LEFT JOIN employees e ON d.dept_id = e.department_id
        $searchQuery
        GROUP BY d.dept_id, d.dept_name";
$result = $conn->query($sql);

// Check if results exist
$departments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// get the list of employees for each department
$employees = [];
$departmentName = "";
if (isset($_GET['dept_id']) && !empty($_GET['dept_id'])) {
    $dept_id = $conn->real_escape_string($_GET['dept_id']);
    $sqlEmployees = "SELECT e.first_name, e.last_name, e.job_title, d.dept_name 
                     FROM employees e
                     JOIN departments d ON e.department_id = d.dept_id
                     WHERE d.dept_id = $dept_id";
    $resultEmployees = $conn->query($sqlEmployees);

    if ($resultEmployees && $resultEmployees->num_rows > 0) {
        while ($row = $resultEmployees->fetch_assoc()) {
            $employees[] = $row;
            // Get the department name from the first row (same for all employees)
            if (empty($departmentName)) {
                $departmentName = $row['dept_name'];
            }
        }
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title>Department Management</title>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury Corp</h1>
        </div>
      </header>  
      <nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
    
            
            <div class="collapse navbar-collapse" id="navbarNav">
            <h4>Department Management</h4>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="hr_dash.php">HR Dash</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>

            
        </div>
    </nav>
<br><br>
<!-- Search Form -->
<form method="GET" action="">
    <input type="text" name="search" placeholder="Search Departments" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <button type="submit">Search</button>
</form>

<br>

<!-- Departments Table -->
<div style="display: flex; justify-content: center;">
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; text-align: center; width: 80%;" >
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000;">Department ID</th>
                <th style="border: 1px solid #000;">Department Name</th>
                <th style="border: 1px solid #000;">Number of Employees</th>
                <th style="border: 1px solid #000;">Budget</th>
                <th style="border: 1px solid #000;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($departments)): ?>
                <?php foreach ($departments as $department): ?>
                    <tr>
                        <td style="border: 1px solid #000;"><?php echo htmlspecialchars($department['dept_id']); ?></td>
                        <td style="border: 1px solid #000;"><?php echo htmlspecialchars($department['dept_name']); ?></td>
                        <td style="border: 1px solid #000;"><?php echo htmlspecialchars($department['employee_count']); ?></td>
                        <td style="border: 1px solid #000;"><?php echo "$" . number_format(rand(50000, 200000), 2); ?></td>
                        <td style="border: 1px solid #000;"> 
                            <form method="GET" action="">
                            <input type="hidden" name="dept_id" value="<?php echo $department['dept_id']; ?>">
                            <button  type="submit">View Employees</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="border: 1px solid #000;">No departments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<br><br>
<!-- employees  table -->
<?php if (!empty($employees)): ?>
    <h3 style="text-align: center;">Employees in <?php echo htmlspecialchars($departmentName); ?> Department</h3>
    <div style="display: flex; justify-content: center;">
        <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; text-align: center; width: 80%;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #000;">First Name</th>
                    <th style="border: 1px solid #000;">Last Name</th>
                    <th style="border: 1px solid #000;">Job Title</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td style="border: 1px solid #000;"><?php echo htmlspecialchars($employee['first_name']); ?></td>
                        <td style="border: 1px solid #000;"><?php echo htmlspecialchars($employee['last_name']); ?></td>
                        <td style="border: 1px solid #000;"><?php echo htmlspecialchars($employee['job_title']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif (isset($_GET['dept_id'])): ?>
    <p style="text-align: center;">No employees found in this department.</p>
<?php endif; ?>

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
