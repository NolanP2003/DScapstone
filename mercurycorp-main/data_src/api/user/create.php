<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../includes/db_config.php');  // Include the database connection
// include('../includes/auth.php');    // Ensure the user is logged in
// Create database connection 
$mysqli = new mysqli($host, $dbUsername, $dbPassword, $database);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id = $_POST['id'];
    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Initialize the role variable
    $role = null;

    // Step 1: Check if the username already exists in the users table
    $checkUserSql = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($checkUserSql);
    $stmt->bind_param("s", $username); // Bind the username parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the username already exists, show an error
        $error = "An account already exists for this username.";
        echo $error . "<br>";
    } else {
        // Step 2: Determine the role based on the ID
        
            // Check if the id exists in the physicians table
            $sqlPhysician = "SELECT * FROM physician WHERE physician_id = ? AND email = ?";
            $stmtPhysician = $mysqli->prepare($sqlPhysician);

            // echo "ID: $id, Username: $username<br>";
            $stmtPhysician->bind_param("is", $id, $username);
            $stmtPhysician->execute();
            // Debugging: Check if execution was successful
            // if ($stmtPhysician->error) {
            //     echo "Error executing query: " . $stmtPhysician->error . "<br>";
            // } else {
            //     echo "Query executed successfully.<br>";
            // }
            $resultPhysician = $stmtPhysician->get_result();
            // var_dump($resultPhysician); // Check if the result object contains any rows

            if ($resultPhysician->num_rows > 0) {
                $role = 'physician';
                // echo "Role set to: $role<br>";
            } else {
                // echo "No match found in the physician table for ID: $id and email: $username.<br>";//debug
                // Check if the id exists in the employees table and get department name
                $sqlEmployeeDept = "SELECT e.emp_id, e.email, e.department_id, d.dept_name
                                    FROM employees e
                                    JOIN departments d ON e.department_id = d.dept_id
                                    WHERE e.emp_id = ? AND e.email = ?";
                $stmtDept = $mysqli->prepare($sqlEmployeeDept);
                $stmtDept->bind_param("is", $id, $username);
                // echo "ID: $id, Username: $username<br>"; // debug
                $stmtDept->execute();
                $resultDept = $stmtDept->get_result();

                if ($resultDept->num_rows > 0) {
                    // Fetch the department name
                    $row = $resultDept->fetch_assoc();
                    // echo "Row Data: ";
                    // print_r($row);
                    // echo "<br>";
                    $role = $row['dept_name']; // Assign department name as role
                    // echo "Assigned Role: $role<br>"; // Debugging statement
                 } else {
                //     echo "No department found matching ID: $id and email: $username.<br>"; //debugging statement
                } 
              
            }
        }
        // Debug statement to confirm role before insert
        // if ($role) {
        //     echo "Role ready for insert: $role<br>";
        // } else {
        //     echo "Role is not set before insert.<br>";
        // }
        // Step 3: If a valid role is determined, insert the new user into the users table
        if ($role) {
            // echo "Role ready for insert in if statement: $role<br>";
            $insertUserSql = "INSERT INTO users (username, password, role, id) VALUES (?, ?, ?, ?)";
            $stmtInsert = $mysqli->prepare($insertUserSql);
            $stmtInsert->bind_param("ssss", $username, $hashedPassword, $role, $id); // Use "i" for id if it's integer

            if ($stmtInsert->execute()) {
              $message = "User successfully created.";
            } 
            else {
              echo "Error during insert: " . $stmtInsert->error  . "<br>";
            }
            // $stmtDept->close();
            $stmtInsert->close();
        } else {
            $message = "Please enter a valid username and ID.";
            
        }
    }


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
  <link href="../../../web_src/style.css" rel="stylesheet">
  <!-- Google Font API-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
  <!-- JavaScript Source-->
  <!-- <script src="main.js"></script> -->
  <title>Mercury Corp - Software Engineering Final Project.</title>
  <!--Font Awesome-->
  <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../../../web_src/photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
      </header>      
      <nav class="navbar" style="background-color: rgb(133, 161,170); height: 70px">
        <!-- Navbar content -->
        <a id = "nav-bar options" href = "../../../web_src/index.php" class = "arima-subtitle">Home</a>
        <!-- <a id = "nav-bar options" href = "Residents.html" class = "arima-subtitle">Residents</a>
        <a id = "nav-bar options" href = "login.html" class = "arima-subtitle">Login</a> -->
      </nav><br>
      <h2 class = "arima-subtitle" font-stle:"italic"> Create your user account</h2>
      <div id="intro" class="section-text col d-flex justify-content-center align-items-center" style="height: 900px;">
    <div class="white-box text-center" style="background-color: white; padding: 70px; width: 500px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
        <div class="container mt-5">
            <form method="POST" action="">
                <div class="form-group mb-3">
                    <label for="username" class="form-label"><strong>Username:</strong></label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label"><strong>Password:</strong></label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group mb-4">
                    <label for="id" class="form-label"><strong>Enter Identifying Number:</strong></label>
                    <input type="text" id="id" name="id" class="form-control" required>
                </div>
        
                <button type="submit" class="btn btn-primary w-100" >Create User</button>
                <br><br><br>
                <a href="../../../web_src/login.php">Back to Log In</a>

                <!-- Display message inside the div -->
                <?php if (!empty($message)) : ?>
                  <div class="message">
                  <?php echo $message; ?>
                </div>
    <?php endif; ?>
                
            </form>
        </div>
    </div>
</div>
        

</body>

<footer>
  <p> 2024 Mercury Corp. All rights reserved.</p>
  <p>Follow us on social media!</p>
    <a href="https://github.com/Laneyeh">
  <img class="socialMediaIcon" src="../../../web_src/photos/facebook.png" alt="Facebook">
</a>
<a href="https://github.com/torrescschool">
  <img class="socialMediaIcon" src="../../../web_src/photos/instagram.png" alt="Instagram">
</a>
<a href="https://github.com/Mildred1999">
  <img class="socialMediaIcon" src="../../../web_src/photos/twitter.png" alt="Twitter">
</a>
</footer>
</html>
