<?php
include('../../../../data_src/includes/db_config.php');  
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

$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'];
$averageSalary = $conn->query("SELECT AVG(salary) AS average FROM employees")->fetch_assoc()['average'];
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
  <link href="../../../style.css" rel="stylesheet">
  <!-- Google Font API-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
  <!-- JavaScript Source-->
  <!-- <script src="main.js"></script> -->
    <title>Employee Management</title>
    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../../../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
      </header>  
      <nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
    
            
            <div class="collapse navbar-collapse" id="navbarNav">
            <h3>Employee Management</h3>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../hr_dash.php">HR Dash</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../logout.php">Logout</a></li>
                </ul>
            </div>

            
        </div>
    </nav>
    <br>

<!-- Search / Filter form -->
<!-- <form method="GET" action="">
    <label for="role">Role:</label>
    <select name="role" id="role">
        <option value="">All</option>
        <option value="nurse">Nurse</option>
        <option value="physician">Physician</option> -->
        <!-- Add other roles  -->
    <!-- </select>
    <button type="submit">Filter</button>
</form> <br><br> -->

<div class="d-flex justify-content-between" style="margin-bottom: 20px;">
    <!-- Search Form -->
    <form method="GET" action="" class="search-form" style="max-width: 400px;">
    <input type="text" name="search" placeholder="Search by name" class="form-control" style="display: inline-block; width: 70%; margin-right: 10px;">
    <button type="submit" style="border-radius: 12px; display: inline-block;">Search</button>
</form>

    <!-- Add New Employee Button -->
    <form action="create_employee.php" method="get">
        <button type="submit" style="border-radius: 12px;" >Add New Employee</button>
    </form>
</div>

<?php
// Handle Search
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT * FROM employees WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%'";
    $search_result = $conn->query($query);

    if ($search_result && $search_result->num_rows > 0) {
        echo "<h3>Search Results:</h3>";
        echo "<ul>";
        while ($row = $search_result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No employees found.</p>";
    }
}
 ?>
<!-- Statistics Section -->
<div class="statistics">
    <h4>Overview</h4>
    <div class="stat-item">Total Employees: <?php echo $totalEmployees; ?></div>
    <div class="stat-item">Average Salary: <?php echo number_format($averageSalary, 2); ?> USD</div>
</div>

<br><br>
<!-- Employee Table -->
<table class="table table-striped table-bordered mt-4" >
    <thead>
        <tr>
            <th>Name</th>
            <th>Job Title</th>
            <th>Department</th>
            <th>Salary</th>
            <th>Email</th>
            <th>Date of Birth</th>
            <th>Age</th>
            <th>Hire Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $employee): 
            $dob = new DateTime($employee['dob']);
            $today = new DateTime();
            $age = $employee['dob'] ? $today->diff($dob)->y : "N/A";
        ?> 
            <tr>
                <td><?php echo htmlspecialchars($employee['first_name']) . " " . htmlspecialchars($employee['last_name']); ?></td>
                <td><?php echo htmlspecialchars($employee['job_title']); ?></td>
                <td><?php echo htmlspecialchars($employee['dept_name']); ?></td>
                <td>$<?php echo number_format($employee['salary'], 2); ?></td>
                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                <td><?php echo htmlspecialchars($employee['dob']); ?></td>
                <td><?php echo htmlspecialchars($age); ?></td>
                <td><?php echo htmlspecialchars($employee['hire_date']); ?></td>
                <td><a href="#" class="btn btn-warning">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table><br><br>

<footer>
  <p> 2024 Mercury Corp. All rights reserved.</p>
  <p>Follow us on social media!</p>
    <a href="https://github.com/Laneyeh">
  <img class="socialMediaIcon" src="../../../photos/facebook.png" alt="Facebook">
</a>
<a href="https://github.com/torrescschool">
  <img class="socialMediaIcon" src="../../../photos/instagram.png" alt="Instagram">
</a>
<a href="https://github.com/Mildred1999">
  <img class="socialMediaIcon" src="../../../photos/twitter.png" alt="Twitter">
</a>
</footer>
</body>
</html>



