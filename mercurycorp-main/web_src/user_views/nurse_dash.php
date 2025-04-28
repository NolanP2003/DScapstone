<?php
session_start(); // Start the session

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // Redirect to the login page
    header("Location: ../../web_src/login.php");
    exit;
}

// Show the loading animation only if just logged in
$showLoading = isset($_SESSION['just_logged_in']);
if ($showLoading) {
    unset($_SESSION['just_logged_in']); // Reset the flag after showing the animation
}

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
    //$username = $_SESSION['username'];
    $firstName = '';

    $sqlUser = "SELECT first_name FROM employees WHERE email = ?";
    $stmtUser = $pdo->prepare($sqlUser);

    if ($stmtUser) {
    // Bind the parameter and execute the statement
    //$stmtUser->execute([$username]);
    
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
    <title>Nurse Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="chatbot/chatbot.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
</head>

<body>

    <?php if ($showLoading): ?>
    <!-- Full-screen loading animation -->
    <div id="loading-screen">
        <div id="logo-container">
            <img src="../photos/mercuryCorpLogo.png" alt="Mercury Logo" id="loading-logo">
            <div class="circle large-circle"></div>
            <div class="circle large-circle"></div>
            <div class="circle large-circle"></div>
        </div>
    </div>

    <script>
        // Hide the loading screen after the page fully loads
        window.addEventListener("load", function() {
            const loadingScreen = document.getElementById("loading-screen");
            loadingScreen.style.animation = "zoomOut 1s ease forwards";
            setTimeout(() => {
                loadingScreen.style.display = "none";
            }, 1000);
        });
    </script>
    <?php endif; ?>

    <!-- Header -->
    <header class="row">
        <div class="col-1">
          <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
            <!-- make Mercury center and larger -->
          <h1 class="abril-fatface-regular">Mercury</h1>
        </div>
        <div class="col-1">
            <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo" style="display: none;">
        </div>
      </header>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
            <!-- Navbar content collapses into a dropdown menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <h3>Nurse Dashboard</h3>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="patient_analysis.php">Patient Analysis</a></li>
                    <li class="nav-item"><a class="nav-link" href="medical_records_dash.php">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="employee_dash.php">Profile</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="add_resident.php">Add Resident</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
    </nav><br>
    
<body>
    <!-- Welcome and Tasks Section -->
    <?php
    // Get the logged-in user's employee ID
    $employeeName = '';
    if (isset($_SESSION['username'])) {
        $sqlEmpName = "SELECT first_name FROM employees WHERE email = ?";
        $stmtEmpName = $pdo->prepare($sqlEmpName);
        $stmtEmpName->execute([$_SESSION['username']]);
        $rowEmpName = $stmtEmpName->fetch(PDO::FETCH_ASSOC);

        if ($rowEmpName) {
            $employeeName = $rowEmpName['first_name'];
        }
    }
    ?>

    <div class="welcome-container text-center my-4">
        <h3 class="welcome-message">Welcome, <?php echo htmlspecialchars($employeeName); ?>!</h3>

        <div class="tasks-container bg-light p-4 rounded shadow-sm">
            <h4 class="mb-3">Today's Tasks</h4>
            <?php if (!empty($todaysTasks)): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($todaysTasks as $task): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($task['task_description']); ?>
                            <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($task['task_time']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No tasks for today.</p>
            <?php endif; ?>
        </div>
    </div>


    <!-- Filter medication administration records by resident -->
    <div class="form-container">
        <h2 class="text-center mb-4">Medication Administration Records</h2>
        <form action="nurse_dash.php" method="GET">
            <div class="form-header-label">Resident</div>
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
    <?php
        // Get the selected resident's name
        $residentName = '';
        if (isset($_GET['res_id'])) {
            $selectedResidentId = $_GET['res_id'];
            $sqlResName = "SELECT first_name, last_name FROM residents WHERE res_id = ?";
            $stmtResName = $pdo->prepare($sqlResName);
            $stmtResName->execute([$selectedResidentId]);
            $rowResName = $stmtResName->fetch(PDO::FETCH_ASSOC);

            if ($rowResName) {
                $residentName = $rowResName['first_name'] . ' ' . $rowResName['last_name'];
            }
        }
    ?>

    <?php if (!empty($medsTreatsRecords)): ?>
        <div class="medication-report">
            <h2 class="text-center mb-4">Medication Report for <?php echo htmlspecialchars($residentName); ?></h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
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
                                <td>
                                    <?php echo $record['medication_refused'] ? 
                                        '<span class="badge bg-danger">Yes</span>' : 
                                        '<span class="badge bg-success">No</span>'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($record['emp_id']); ?></td>
                                <td>
                                    <?php if (empty($record['datetime_given'])): ?>
                                        <form action="nurse_dash.php" method="POST" class="d-inline-block">
                                            <input type="hidden" name="res_id" value="<?php echo htmlspecialchars($selectedResidentId); ?>">
                                            <input type="hidden" name="type_id" value="<?php echo htmlspecialchars($record['type_id']); ?>">
                                            <div class="mb-2">
                                                <input type="text" name="notes" class="form-control mb-1" placeholder="Add Notes" required>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="medication_refused" id="medRefused<?php echo $record['type_id']; ?>">
                                                    <label class="form-check-label" for="medRefused<?php echo $record['type_id']; ?>">Refused</label>
                                                </div>
                                                <label for="emp_id" class="mt-2">Employee ID:</label>
                                                <input type="number" name="emp_id" class="form-control mb-2" placeholder="Employee ID" required>
                                                <button type="submit" class="btn btn-primary w-100">Administer</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-success">Administered</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No medication records found for the selected resident.</div>
    <?php endif; ?>

<!-- Two forms side by side with better formatting -->
<div class="container my-5">
    <div class="row g-4">
        <!-- Create Medication Administration Record Form -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Create Medication Administration Record</h2>
                    <form action="nurse_dash.php" method="POST" class="form-layout">
                        
                        <div class="mb-3">
                            <label class="form-label">Medication/Treatment Name:</label>
                            <input type="text" name="new_type_name" class="form-control" placeholder="Enter medication or treatment name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes:</label>
                            <textarea name="new_notes" class="form-control" rows="3" placeholder="Enter any relevant notes"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Medication Refused:</label><br>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="new_medication_refused" id="new_medication_refused">
                                <label class="form-check-label" for="new_medication_refused">Yes</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Physician Order:</label>
                            <select name="new_order_id" class="form-select" required>
                                <option value="">Select an order</option>
                                <?php foreach ($recentOrders as $order): ?>
                                    <option value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                        <?php echo htmlspecialchars($order['order_text']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employee ID (Administered by):</label>
                            <input type="number" name="new_emp_id" class="form-control" placeholder="Enter employee ID" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Create Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Physician Order Form -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Create Physician Order</h2>
                    <form action="../../data_src/api/mar_tar/create_order.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Medication:</label>
                            <input type="text" name="medication" class="form-control" required placeholder="Enter medication name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dosage:</label>
                            <input type="text" name="dosage" class="form-control" required placeholder="Enter dosage">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Frequency:</label>
                            <input type="text" name="frequency" class="form-control" required placeholder="Enter frequency">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Date:</label>
                            <input type="date" name="start_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Date:</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instructions:</label>
                            <textarea name="instructions" class="form-control" rows="3" placeholder="Enter instructions"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Physician:</label>
                            <select name="physician_id" class="form-select" required>
                                <option value="">Select a physician</option>
                                <?php foreach ($physicians as $physician): ?>
                                    <option value="<?php echo htmlspecialchars($physician['physician_id']); ?>">
                                        <?php echo htmlspecialchars($physician['last_name'] . ', ' . $physician['first_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Resident:</label>
                            <select name="res_id" class="form-select" required>
                                <option value="">Select a resident</option>
                                <?php foreach ($residents as $resident): ?>
                                    <option value="<?php echo htmlspecialchars($resident['res_id']); ?>">
                                        <?php echo htmlspecialchars($resident['last_name'] . ', ' . $resident['first_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Employee ID (Administered by):</label>
                            <input type="number" name="emp_id" class="form-control" required placeholder="Enter employee ID">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Submit Order</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- beginning of chatbot stuff -->
    <div class="chatbot-content-area">
        <?php
        // Include the chatbot content from chatbot.php
        $chatbotPath = __DIR__ . '/chatbot/chatbot.php';
        if (file_exists($chatbotPath)) {
            include_once $chatbotPath;
        } else {
            echo '<div class="alert alert-danger m-3" role="alert">Error: Chatbot components could not be loaded. File not found.</div>';
        }
        ?>
    </div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatbotToggle = document.getElementById("chatbot-toggle");
        const chatbotPopup = document.getElementById("chatbot-popup");
        const chatbotClose = document.getElementById("chatbot-close");

        chatbotToggle.addEventListener("click", function() {
            const isHidden = chatbotPopup.style.display === "none";
            chatbotPopup.style.display = isHidden ? "block" : "none";
            chatbotToggle.setAttribute('aria-label', isHidden ? 'Close Chatbot' : 'Open Chatbot');
        });
        
        chatbotClose.addEventListener("click", function() {
            chatbotPopup.style.display = "none";
            chatbotToggle.setAttribute('aria-label', 'Open Chatbot');
        });
    });
</script>
<!-- end of chatbot stuff -->

    
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
