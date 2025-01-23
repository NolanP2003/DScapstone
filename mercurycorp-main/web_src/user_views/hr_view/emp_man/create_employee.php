<?php
include('../../../../data_src/includes/db_config.php');  // Include the database connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $database);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $mobile_no = $_POST['mobile_no'];
    $dob = $_POST['dob'];
    $job_title = $_POST['job_title'];
    $salary = $_POST['salary'];
    $email = $_POST['email'];
    $department_id = $_POST['department_id'];
    $address = $_POST['address'];
    $hire_date = $_POST['hire_date'];

    
    $query = "INSERT INTO employees (first_name, last_name, mobile_no, dob, job_title, salary, email, department_id, address, hire_date)
              VALUES ('$first_name', '$last_name', '$mobile_no', '$dob', '$job_title', '$salary', '$email', '$department_id', '$address', '$hire_date')";
    
    
    if ($conn->query($query) === TRUE) {
        echo '<div style="background-color: green; color: white; padding: 10px; border-radius: 5px;">Employee added successfully!</div>';
    } else {
        echo '<div style="background-color: red; color: white; padding: 10px; border-radius: 5px;">Employee not added. Error: ' . $conn->error . '</div>';
    }


    $conn->close();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Employee</title>
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="employee_man.php">Back</a></li>
                </ul>
            </div>

            
        </div>
    </nav>
    <br>
    <div id="intro" class="section-text d-flex justify-content-center align-items-center" style="height: auto;">
    <div class="white-box text-center" style="background-color: white; padding: 40px; width: 600px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
        <h2 class="text-center mb-4">Add a New Employee</h2>
        <form action="" method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label"><strong>First Name</strong></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label"><strong>Last Name</strong></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="mobile_no" class="form-label"><strong>Mobile Number</strong></label>
                <input type="text" class="form-control" id="mobile_no" name="mobile_no" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dob" class="form-label"><strong>Date of Birth</strong></label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label"><strong>Hire Date</strong></label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="job_title" class="form-label"><strong>Job Title</strong></label>
                <input type="text" class="form-control" id="job_title" name="job_title" required>
            </div>

            <div class="mb-3">
                <label for="salary" class="form-label"><strong>Salary</strong></label>
                <input type="number" class="form-control" id="salary" name="salary" required style="appearance: none; -moz-appearance: none;">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><strong>Email</strong></label>
                <input type="text" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label"><strong>Department</strong></label>
                <select class="form-control" id="department_id" name="department_id" required>
                    <option value="" disabled selected>Select a department</option>
                    <option value="1">Nursing</option>
                    <option value="2">Accounting</option>
                    <option value="3">HR</option>
                    <option value="4">Records</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label"><strong>Address</strong></label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Add New Employee</button>
        </form>

        <br>

    </div>
</div>
<br>
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