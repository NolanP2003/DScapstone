<?php
//include('../includes/auth.php');

//if (!isResident()) {
    //header("Location: /views/login.php");
   // exit;
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/styles.css">
    <title>Resident Dashboard</title>
</head>
<body>
    <h1>Resident Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['user']; ?>!</p>

    <h2>Your Schedule</h2>
    <ul>
        <li>Breakfast - 8:00 AM</li>
        <li>Activity: Yoga - 10:00 AM</li>
    </ul>

    <a href="/public/index.php">Logout</a>
</body>
</html>
<?PHP
    include "../footer.php";
?>
