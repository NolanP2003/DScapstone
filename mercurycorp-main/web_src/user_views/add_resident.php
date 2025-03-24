<?php
session_start(); // Start the session

// Redirect to login if not logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: ../../web_src/login.php");
    exit;
}

require_once('../../data_src/includes/db_config.php'); // Ensure this file initializes $pdo

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create the PDO connection inside add_resident.php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_resident'])) {
    try {
        // Get form data and sanitize it
        $resId = filter_input(INPUT_POST, 'res_id', FILTER_SANITIZE_NUMBER_INT);
        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mailingAddress = filter_input(INPUT_POST, 'mailing_address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $physicianId = filter_input(INPUT_POST, 'physician_id', FILTER_SANITIZE_NUMBER_INT);
        $accountId = filter_input(INPUT_POST, 'account_id', FILTER_SANITIZE_NUMBER_INT);
        $ssn = filter_input(INPUT_POST, 'ssn', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $emergencyContact = filter_input(INPUT_POST, 'emergency_contact', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $careId = filter_input(INPUT_POST, 'care_id', FILTER_SANITIZE_NUMBER_INT);
        $recId = filter_input(INPUT_POST, 'rec_id', FILTER_SANITIZE_NUMBER_INT);
        $unitId = filter_input(INPUT_POST, 'unit_id', FILTER_SANITIZE_NUMBER_INT);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Check if required fields are empty
        if (empty($firstName) || empty($lastName) || empty($dob) || empty($ssn)) {
            $_SESSION['message'] = '<div class="alert alert-danger">Required fields are missing.</div>';
            header("Location: add_resident.php");
            exit;
        }

        // Prepare the SQL statement
        $insertStmt = $pdo->prepare("INSERT INTO residents 
            (res_id, first_name, last_name, dob, mailing_address, physician_id, account_id, ssn, emergency_contact, care_id, rec_id, unit_id, address) 
            VALUES (:res_id, :first_name, :last_name, :dob, :mailing_address, :physician_id, :account_id, :ssn, :emergency_contact, :care_id, :rec_id, :unit_id, :address)");

        // Bind values and execute
        $insertStmt->execute([
            ':res_id' => $resId,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':dob' => $dob,
            ':mailing_address' => $mailingAddress,
            ':physician_id' => $physicianId,
            ':account_id' => $accountId,
            ':ssn' => $ssn,
            ':emergency_contact' => $emergencyContact,
            ':care_id' => $careId,
            ':rec_id' => $recId,
            ':unit_id' => $unitId,
            ':address' => $address,
        ]);

        // Success message and redirect
        $_SESSION['message'] = '<div class="alert alert-success">Resident added successfully!</div>';
        header("Location: add_resident.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
        header("Location: add_resident.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resident</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
<header class="row">
    <div class="col-1">
        <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
    </div>
    <div class="col">
        <h1 class="abril-fatface-regular">Mercury</h1>
    </div>
</header>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
    <div class="container-fluid">
        <!-- Navbar content collapses into a dropdown menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <h3>Add Resident</h3>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="nurse_dash.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="patient_analysis.php">Patient Analysis</a></li>
                <li class="nav-item"><a class="nav-link" href="medical_records_dash.php">Medical Records</a></li>
                <li class="nav-item"><a class="nav-link" href="employee_dash.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav><br>

<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Resident</h2>

    <!-- Display session messages -->
    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']); // Clear message after displaying
    }
    ?>

<form method="POST" class="p-4 border rounded bg-light shadow">
        <div class="mb-3">
            <label for="first_name" class="form-label">Res ID:</label>
            <input type="text" name="res_id" class="form-control" placeholder="Enter resident ID" required>
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name:</label>
            <input type="text" name="first_name" class="form-control" placeholder="Enter first name" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name:</label>
            <input type="text" name="last_name" class="form-control" placeholder="Enter last name" required>
        </div>

        <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth:</label>
            <input type="date" name="dob" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="mailing_address" class="form-label">Mailing Address:</label>
            <input type="text" name="mailing_address" class="form-control" placeholder="Enter mailing address" required>
        </div>

        <div class="mb-3">
            <label for="physician_id" class="form-label">Physician ID:</label>
            <input type="number" name="physician_id" class="form-control" placeholder="Enter physician ID" required>
        </div>

        <div class="mb-3">
            <label for="account_id" class="form-label">Account ID:</label>
            <input type="number" name="account_id" class="form-control" placeholder="Enter account ID" required>
        </div>

        <div class="mb-3">
            <label for="ssn" class="form-label">SSN:</label>
            <input type="text" name="ssn" class="form-control" placeholder="Enter SSN" required>
        </div>

        <div class="mb-3">
            <label for="emergency_contact" class="form-label">Emergency Contact:</label>
            <input type="text" name="emergency_contact" class="form-control" placeholder="Enter emergency contact" required>
        </div>

        <div class="mb-3">
            <label for="care_id" class="form-label">Care ID:</label>
            <input type="number" name="care_id" class="form-control" placeholder="Enter care ID" required>
        </div>

        <div class="mb-3">
            <label for="rec_id" class="form-label">Record ID:</label>
            <input type="number" name="rec_id" class="form-control" placeholder="Enter record ID" required>
        </div>

        <div class="mb-3">
            <label for="unit_id" class="form-label">Unit ID:</label>
            <input type="number" name="unit_id" class="form-control" placeholder="Enter unit ID" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <input type="text" name="address" class="form-control" placeholder="Enter address" required>
        </div>

        <button type="submit" name="add_resident" class="btn btn-primary w-100">Add Resident</button>
    </form>
</div>

</body>
</html>
