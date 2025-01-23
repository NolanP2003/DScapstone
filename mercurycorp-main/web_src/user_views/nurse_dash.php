<?php
session_start(); // Start the session

include('../../data_src/includes/db_config.php');

// Database credentials
//$host = "156.67.74.51";
//$dbUsername = "u413142534_mercurycorp";
//$dbPassword = "H3@lthM@tters!";
//$database = "u413142534_mercurycorp";

try {
    
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the logged-in user's first name
    $username = $_SESSION['username'];
    $firstName = '';

    $sqlUser = "SELECT first_name FROM employees WHERE email = ?";
    $stmtUser = $pdo->prepare($sqlUser);

    if ($stmtUser) {
    // Bind the parameter and execute the statement
    $stmtUser->execute([$username]);
    
    // Fetch the result
    $rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($rowUser) {
        $firstName = $rowUser['first_name'];
    }
}

    // Query to fetch recent physician orders
    $stmt = $pdo->prepare("SELECT order_id, rec_id, order_date, order_text, physician_id 
                           FROM physician_orders
                           ORDER BY order_id DESC 
                           LIMIT 10");
    $stmt->execute();
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to fetch physicians for the dropdown
    $physicianStmt = $pdo->prepare("SELECT physician_id, last_name, first_name FROM physician");
    $physicianStmt->execute();
    $physicians = $physicianStmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to fetch residents for the dropdown
    $residentStmt = $pdo->prepare("SELECT res_id, last_name, first_name FROM residents");
    $residentStmt->execute();
    $residents = $residentStmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to fetch medication administration records from meds_treats for a specific resident if filtered
    $medsTreatsRecords = [];
    if (isset($_GET['res_id']) && !empty($_GET['res_id'])) {
        $selectedResidentId = $_GET['res_id'];
        $medsStmt = $pdo->prepare("SELECT mt.type_id, mt.type_name, mt.datetime_given, mt.notes, mt.medication_refused, mt.emp_id, po.order_text
                                    FROM meds_treats mt
                                    JOIN physician_orders po ON mt.order_id = po.order_id
                                    WHERE po.rec_id = (SELECT rec_id FROM residents WHERE res_id = :res_id)");
        $medsStmt->bindValue(':res_id', $selectedResidentId, PDO::PARAM_STR);
        $medsStmt->execute();
        $medsTreatsRecords = $medsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle medication administration update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type_id'])) {
        $typeId = $_POST['type_id'];
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_SPECIAL_CHARS);
        $medicationRefused = isset($_POST['medication_refused']) ? 1 : 0;
        $datetimeGiven = date('Y-m-d H:i:s');
        $empId = $_POST['emp_id'];

        $updateStmt = $pdo->prepare("UPDATE meds_treats SET datetime_given = :datetime_given, notes = :notes, medication_refused = :medication_refused, emp_id = :emp_id WHERE type_id = :type_id");
        $updateStmt->bindValue(':datetime_given', $datetimeGiven, PDO::PARAM_STR);
        $updateStmt->bindValue(':notes', $notes, PDO::PARAM_STR);
        $updateStmt->bindValue(':medication_refused', $medicationRefused, PDO::PARAM_BOOL);
        $updateStmt->bindValue(':emp_id', $empId, PDO::PARAM_INT);
        $updateStmt->bindValue(':type_id', $typeId, PDO::PARAM_INT);
        $updateStmt->execute();
        header("Location: nurse_dash.php?res_id=" . $_POST['res_id']);
        exit;
    }

    // Handle adding a new medication administration record
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_type_name'])) {
        $newTypeName = filter_input(INPUT_POST, 'new_type_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $newEmpId = filter_input(INPUT_POST, 'new_emp_id', FILTER_SANITIZE_NUMBER_INT);
        $newDatetimeGiven = date('Y-m-d H:i:s');
        $newNotes = filter_input(INPUT_POST, 'new_notes', FILTER_SANITIZE_SPECIAL_CHARS);
        $newMedicationRefused = isset($_POST['new_medication_refused']) ? 1 : 0;
        $newOrderId = filter_input(INPUT_POST, 'new_order_id', FILTER_SANITIZE_NUMBER_INT);

        $insertStmt = $pdo->prepare("INSERT INTO meds_treats (type_name, emp_id, datetime_given, notes, medication_refused, order_id) VALUES (:type_name, :emp_id, :datetime_given, :notes, :medication_refused, :order_id)");
        $insertStmt->bindValue(':type_name', $newTypeName, PDO::PARAM_STR);
        $insertStmt->bindValue(':emp_id', $newEmpId, PDO::PARAM_INT);
        $insertStmt->bindValue(':datetime_given', $newDatetimeGiven, PDO::PARAM_STR);
        $insertStmt->bindValue(':notes', $newNotes, PDO::PARAM_STR);
        $insertStmt->bindValue(':medication_refused', $newMedicationRefused, PDO::PARAM_BOOL);
        $insertStmt->bindValue(':order_id', $newOrderId, PDO::PARAM_INT);
        $insertStmt->execute();
        header("Location: nurse_dash.php");
        exit;
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
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
  <!-- <link rel="stylesheet" href="/mercurycorp/mercurycorp/web_src/style.css"> -->
  <title>Nurse Dashboard</title>
  <!--Font Awesome-->
  <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
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
<!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
            <!-- Collapsible button on the left
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- Navbar content collapses into a dropdown menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <h3>Nurse Dashboard</h3>
                    
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="medical_records_dash.php">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="employee_dash.php">Profile</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="nurse_dash.php">Dashboard</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
    </nav><br>
<body>
    <!-- <h1>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Nurse';?>!</h1> -->
    <h3 >Welcome,<?php echo htmlspecialchars($firstName); ?></h3>
    
    <h2>Today's Tasks</h2>
    <ul>
        <?php if (!empty($todaysTasks)): ?>
            <?php foreach ($todaysTasks as $task): ?>
                <li><?php echo htmlspecialchars($task['task_description']) . " - " . htmlspecialchars($task['task_time']); ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No tasks for today.</li>
        <?php endif; ?>
    </ul>

    <!-- Filter medication administration records by resident -->
    <div class="form-container">
        <h2> Medication Administration Records</h2>
        <form action="nurse_dash.php" method="GET">
            <label for="res_id">Resident:</label>
            <select name="res_id" id="res_id" required>
                <option value="">Select a resident</option>
                <?php foreach ($residents as $resident): ?>
                    <option value="<?php echo htmlspecialchars($resident['res_id']); ?>" <?php echo (isset($selectedResidentId) && $selectedResidentId == $resident['res_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($resident['last_name'] . ', ' . $resident['first_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Display medication administration records -->
    <?php if (!empty($medsTreatsRecords)): ?>
        <h2>Medication Administration Records</h2>
        <form action="nurse_dash.php" method="POST">
            <input type="hidden" name="res_id" value="<?php echo htmlspecialchars($selectedResidentId); ?>">
            <table class="table table-bordered" style="margin: auto;">
                <thead>
                    <tr>
                        <th>Medication/Treatment</th>
                        <th>Date and Time Given</th>
                        <th>Notes</th>
                        <th>Medication Refused</th>
                        <th>Administered By (Employee ID)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medsTreatsRecords as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['type_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['datetime_given'] ?: 'Not administered yet'); ?></td>
                            <td><?php echo htmlspecialchars($record['notes'] ?: 'No notes'); ?></td>
                            <td><?php echo $record['medication_refused'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($record['emp_id']); ?></td>
                            <td>
                                <?php if (empty($record['datetime_given'])): ?>
                                    <input type="text" name="notes" placeholder="Notes">
                                    <label><input type="checkbox" name="medication_refused"> Medication Refused</label>
                                    <label for="emp_id">Employee ID:</label>
                                    <input type="number" name="emp_id" required>
                                    <button type="submit" name="type_id" value="<?php echo htmlspecialchars($record['type_id']); ?>">Mark as Administered</button>
                                <?php else: ?>
                                    Administered
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php elseif (isset($selectedResidentId)): ?>
        <p>No medication records found for the selected resident.</p>
    <?php endif; ?>

    <!-- Form to create a new medication administration record -->
    <div class="form-container">
        <h2>Create Medication Administration Record</h2>
        <form action="nurse_dash.php" method="POST">
            <label for="new_type_name">Medication/Treatment Name:</label>
            <input type="text" name="new_type_name" required>

            <label for="new_notes">Notes:</label>
            <input type="text" name="new_notes">

            <label for="new_medication_refused">Medication Refused:</label>
            <input type="checkbox" name="new_medication_refused">

            <!-- Dropdown for Physician Order ID -->
            <label for="new_order_id">Physician Order:</label>
            <select name="new_order_id" required>
                <option value="">Select an order</option>
                <?php foreach ($recentOrders as $order): ?>
                    <option value="<?php echo htmlspecialchars($order['order_id']); ?>">
                        <?php echo htmlspecialchars($order['order_text']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="new_emp_id">Employee ID (Administered by):</label>
            <input type="number" name="new_emp_id" required>

            <button type="submit">Create Record</button>
        </form>
    </div>

    <!-- Form to create a new physician order -->
    <div class = "form-container">
    <h2>Create Physician Order</h2>
    <form action="../../data_src/api/mar_tar/create_order.php" method="POST">

        <label for="medication">Medication:</label>
        <input type="text" name="medication" required>

        <label for="dosage">Dosage:</label>
        <input type="text" name="dosage" required>

        <label for="frequency">Frequency:</label>
        <input type="text" name="frequency" required>

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date">

        <label for="instructions">Instructions:</label>
        <textarea name="instructions"></textarea>

        <!-- Dropdown for Physician ID -->
        <label for="physician_id">Physician:</label>
        <select name="physician_id" required>
            <option value="">Select a physician</option>
            <?php foreach ($physicians as $physician): ?>
                <option value="<?php echo htmlspecialchars($physician['physician_id']); ?>">
                    <?php echo htmlspecialchars($physician['last_name'] . ', ' . $physician['first_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Dropdown for Resident ID -->
        <label for="res_id">Resident:</label>
        <select name="res_id" id="res_id" required>
            <option value="">Select a resident</option>
            <?php foreach ($residents as $resident): ?>
                <option value="<?php echo htmlspecialchars($resident['res_id']); ?>">
                    <?php echo htmlspecialchars($resident['last_name'] . ', ' . $resident['first_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="emp_id">Employee ID (Administered by):</label>
        <input type="number" name="emp_id" required>

        <button type="submit">Submit Order</button>
    </form>

    <!-- Display recent physician orders for testing -->
    <br></br>
    <h2>Recent Physician Orders</h2>
<table border="1" cellpadding="10" style="margin: auto;">
<table class="table table-bordered" style="margin: auto;">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Record ID</th>
            <th>Order Date</th>
            <th>Order Details</th>
            <th>Physician ID</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($recentOrders)): ?>
            <?php foreach ($recentOrders as $recentOrders): ?>
                <tr>
                    <td><?php echo htmlspecialchars($recentOrders['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($recentOrders['rec_id']); ?></td>
                    <td><?php echo htmlspecialchars($recentOrders['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($recentOrders['order_text']); ?></td>
                    <td><?php echo htmlspecialchars($recentOrders['physician_id']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No recent orders found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
    </div>
        <br></br>
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
