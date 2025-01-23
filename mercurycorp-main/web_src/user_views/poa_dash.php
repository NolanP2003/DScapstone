
<?php
//include('../includes/auth.php');

//if (!isResident()) {
    //header("Location: /views/login.php");
   // exit;
//}

//based on different permissions given to poa. where the resident would like to be buried as well as 
// directives and any emergency information
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/styles.css">
    <title>POA Dashboard</title>
</head>
<body>
    <h1>POA Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['user']; ?>!</p>

    <h2>Recent Information</h2>
    <ul>
        <li>John Doe - Ambulance transfer a at 2100 on 10/9/24</li>
        <li>John Doe - Resident Refused PM medication</li>
    </ul>

    <a href="/public/index.php">Logout</a>
</body>
</html>
<?PHP
    include "../footer.php";
?>
