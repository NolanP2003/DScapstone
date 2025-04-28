<?php
// Start the session if it's not already started (required for $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../navbarFunctions.php'); // Assumed to handle session logic or other setup
// include('../head.php') // This was commented out in your original, keeping it that way

// --- Mock Prediction Logic ---
$predicted_los = null; // Initialize prediction variable
$form_data = []; // To store submitted data for display or reuse

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['predict_los'])) {
    // 1. Retrieve and sanitize input data (basic example)
    $form_data['age'] = isset($_POST['patient_age']) ? filter_var($_POST['patient_age'], FILTER_VALIDATE_INT) : null;
    $form_data['admission_type'] = isset($_POST['admission_type']) ? htmlspecialchars($_POST['admission_type']) : null;
    $form_data['primary_diagnosis'] = isset($_POST['primary_diagnosis']) ? htmlspecialchars($_POST['primary_diagnosis']) : null;
    $form_data['comorbidities'] = isset($_POST['num_comorbidities']) ? filter_var($_POST['num_comorbidities'], FILTER_VALIDATE_INT, ["options" => ["min_range"=>0]]) : null;
    $form_data['mobility_status'] = isset($_POST['mobility_status']) ? htmlspecialchars($_POST['mobility_status']) : null;

    // 2. **Placeholder for Actual Model Call**
    // In a real application, you would pass $form_data (or relevant parts)
    // to your data science model API or function here.
    // For now, we'll simulate a result based on simple rules.

    // Basic Mock Calculation (Replace with real model output later)
    if ($form_data['age'] !== null && $form_data['comorbidities'] !== null) {
        $base_los = 3; // Base days
        $age_factor = ($form_data['age'] > 65) ? 2 : 0; // Add days if older
        $comorbidity_factor = $form_data['comorbidities'] * 1.5; // Add days per comorbidity
        $admission_factor = ($form_data['admission_type'] === 'Emergency') ? 2 : 0; // Add days for emergency
        $mobility_factor = ($form_data['mobility_status'] === 'Non-ambulatory') ? 3 : (($form_data['mobility_status'] === 'Assisted') ? 1 : 0); // Add days based on mobility

        $predicted_los = round($base_los + $age_factor + $comorbidity_factor + $admission_factor + $mobility_factor);

        // Ensure minimum LOS is reasonable
        if ($predicted_los < 1) {
            $predicted_los = 1;
        }
    } else {
        // Handle case where essential data is missing or invalid for prediction
        $error_message = "Invalid input provided. Please check age and number of comorbidities.";
        $predicted_los = null; // Ensure no prediction is shown if input is bad
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Analysis - Length of Stay Prediction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- CSS Source-->
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="chatbot/chatbot.css">
    <!-- Google Font API-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
    <style>
        /* Optional: Add specific styles for this page if needed */
        .prediction-card {
            border-left: 5px solid #0d6efd; /* Blue border */
            margin-top: 20px;
            background-color: #f8f9fa; /* Light background */
        }
         .prediction-result {
            font-size: 1.5rem;
            font-weight: bold;
            color: #198754; /* Green color for success */
         }
         .error-message {
            color: #dc3545; /* Red color for errors */
            font-weight: bold;
         }
    </style>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
        <div class="col-1">
            <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo" style="display: none;">
        </div>
</header>
<nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
            <!-- Navbar content collapses into a dropdown menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Updated Title for Context -->
                    <h3>Patient Analysis</h3>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="nurse_dash.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="medical_records_dash.php">Medical Records</a></li>
                    <!-- Added link to this page itself, marked as active -->
                    <li class="nav-item"><a class="nav-link" href="employee_dash.php">Profile</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="add_resident.php">Add Resident</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
    </nav>


    <!-- Welcome Message -->
    <div class="container mt-4">
        <h3 class="text-center text-muted">Use the form below to predict a patient's estimated length of stay based on key criteria.</h3>

        <!-- Patient Analysis Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="mb-0">Length of Stay Prediction Tool</h3>
            </div>
            <div class="card-body">
                <p class="card-text">Enter the patient's details to generate a prediction. This tool uses a predictive model (currently simulated) to estimate the duration of the patient's stay.</p>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="patient_age" class="form-label">Patient Age</label>
                            <input type="number" class="form-control" id="patient_age" name="patient_age" min="0" max="120" required
                                   value="<?php echo htmlspecialchars($form_data['age'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="admission_type" class="form-label">Admission Type</label>
                            <select class="form-select" id="admission_type" name="admission_type" required>
                                <option value="" disabled <?php echo !isset($form_data['admission_type']) ? 'selected' : ''; ?>>Select...</option>
                                <option value="Elective" <?php echo (isset($form_data['admission_type']) && $form_data['admission_type'] == 'Elective') ? 'selected' : ''; ?>>Elective</option>
                                <option value="Emergency" <?php echo (isset($form_data['admission_type']) && $form_data['admission_type'] == 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                                <option value="Transfer" <?php echo (isset($form_data['admission_type']) && $form_data['admission_type'] == 'Transfer') ? 'selected' : ''; ?>>Transfer</option>
                                <option value="Other" <?php echo (isset($form_data['admission_type']) && $form_data['admission_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                             <label for="primary_diagnosis" class="form-label">Primary Diagnosis / Reason for Admission</label>
                             <input type="text" class="form-control" id="primary_diagnosis" name="primary_diagnosis" placeholder="e.g., Pneumonia, Hip Replacement" required
                                    value="<?php echo htmlspecialchars($form_data['primary_diagnosis'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="num_comorbidities" class="form-label">Number of Major Conditions</label>
                            <input type="number" class="form-control" id="num_comorbidities" name="num_comorbidities" min="0" max="20" required
                                   value="<?php echo htmlspecialchars($form_data['comorbidities'] ?? '0'); ?>">
                             <small class="form-text text-muted">Count of significant pre-existing conditions (e.g., Diabetes, CHF, COPD).</small>
                        </div>
                         <div class="col-md-6">
                            <label for="mobility_status" class="form-label">Mobility Status on Admission</label>
                            <select class="form-select" id="mobility_status" name="mobility_status" required>
                                <option value="" disabled <?php echo !isset($form_data['mobility_status']) ? 'selected' : ''; ?>>Select...</option>
                                <option value="Fully Ambulatory" <?php echo (isset($form_data['mobility_status']) && $form_data['mobility_status'] == 'Fully Ambulatory') ? 'selected' : ''; ?>>Fully Ambulatory</option>
                                <option value="Assisted" <?php echo (isset($form_data['mobility_status']) && $form_data['mobility_status'] == 'Assisted') ? 'selected' : ''; ?>>Requires Assistance (e.g., walker, cane)</option>
                                <option value="Non-ambulatory" <?php echo (isset($form_data['mobility_status']) && $form_data['mobility_status'] == 'Non-ambulatory') ? 'selected' : ''; ?>>Non-ambulatory / Bedrest</option>
                            </select>
                        </div>

                    </div>

                    <button type="submit" name="predict_los" class="btn btn-primary mt-4">Predict Length of Stay</button>
                </form>
            </div>
        </div>

        <!-- Prediction Result Display Area -->
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['predict_los'])): ?>
            <div class="card mt-4 prediction-card">
                <div class="card-body">
                    <h4 class="card-title">Prediction Result</h4>
                    <?php if (isset($error_message)): ?>
                         <p class="error-message"><?php echo $error_message; ?></p>
                    <?php elseif ($predicted_los !== null): ?>
                        <p>Based on the provided information:</p>
                        <ul>
                            <li>Age: <?php echo htmlspecialchars($form_data['age']); ?></li>
                            <li>Admission: <?php echo htmlspecialchars($form_data['admission_type']); ?></li>
                            <li>Diagnosis: <?php echo htmlspecialchars($form_data['primary_diagnosis']); ?></li>
                            <li>Comorbidities: <?php echo htmlspecialchars($form_data['comorbidities']); ?></li>
                            <li>Mobility: <?php echo htmlspecialchars($form_data['mobility_status']); ?></li>
                        </ul>
                        <p class="mt-3">The estimated length of stay is:</p>
                        <p class="prediction-result"><?php echo $predicted_los; ?> days</p>
                        <small class="text-muted">Note: This is a simulated prediction for demonstration purposes.</small>
                    <?php else: ?>
                        <p>Could not generate a prediction. Please ensure all required fields are filled correctly.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div> <!-- /container -->

    <!-- Include Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

    <!-- beginning of chatbot stuff -->
    <div class="chatbot-content-area">
        <?php
        // Include the chatbot content from chatbot.php
        $chatbotPath = __DIR__ . '/chatbot/chatbot.php';
        if (file_exists($chatbotPath)) {
            include_once $chatbotPath;
        } else {
            echo '<div class="alert alert-danger m-3" role="alert">Error: Chatbot components could not be loaded. File not found: ' . htmlspecialchars($chatbotPath) . '</div>';
        }
        ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatbotToggle = document.getElementById("chatbot-toggle");
            const chatbotPopup = document.getElementById("chatbot-popup");
            const chatbotClose = document.getElementById("chatbot-close");

            // Ensure elements exist before adding listeners
            if (chatbotToggle && chatbotPopup && chatbotClose) {
                chatbotToggle.addEventListener("click", function() {
                    const isHidden = chatbotPopup.style.display === "none" || chatbotPopup.style.display === "";
                    chatbotPopup.style.display = isHidden ? "block" : "none";
                    chatbotToggle.setAttribute('aria-label', isHidden ? 'Close Chatbot' : 'Open Chatbot');
                });

                chatbotClose.addEventListener("click", function() {
                    chatbotPopup.style.display = "none";
                    chatbotToggle.setAttribute('aria-label', 'Open Chatbot');
                });
            } else {
                console.error("Chatbot toggle/popup/close elements not found.");
            }
        });
    </script>
    <!-- end of chatbot stuff -->

    <footer class="mt-5"> <!-- Added margin-top to footer -->
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