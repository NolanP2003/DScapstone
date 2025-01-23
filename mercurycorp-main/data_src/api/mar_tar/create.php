<!-- Physician Orders: Capture a new physician order, including medication, dosage, frequency, 
start and end dates, and any specific instructions.
Medications/Treatments: Add new medications or treatments to a patient's profile, potentially based on physician orders. 
MAKE SURE IT INSERTS BY RESIDENT ID THEN INTO MEDICAL RECORDS-->

<?php

include('../../includes/db_config.php');  // Include the database connection
// include('../../user_views/auth.php');    // Ensure the user is logged in

// Create database connection 
$mysqli = new mysqli($host, $dbUsername, $dbPassword, $database);

try {
    // Establishing PDO connectin for security
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Capture POST data
    $medication = $_POST['medication'];
    $dosage = $_POST['dosage'];
    $frequency = $_POST['frequency'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $instructions = $_POST['instructions'];
    $physician_id = $_POST['physician_id'];
    $record_id = $_POST['record_id'];
    $employee_id = $_POST['employee_id']; // ID of the person administering treatment

    // Prepare the SQL statement to insert a new order into the `physician_orders` table
    $stmt = $pdo->prepare("INSERT INTO physician_orders (record_id, order_date, order_text, physician_id)
                           VALUES (:record_id, CURDATE(), :order_text, :physician_id)");
    
    // Generate an order text summarizing the order details                
    $order_text = "Medication: $medication, Dosage: $dosage, Frequency: $frequency, Start Date: $start_date, End Date: $end_date, Instructions: $instructions";
    
    // Bind param to SQL statement
    $stmt->bindParam(':record_id', $record_id);
    $stmt->bindParam(':order_text', $order_text);
    $stmt->bindParam(':physician_id', $physician_id);

    // Execute the statement to insert the order
    $stmt->execute();
    
    // Get the ID of the last inserted order to link to medications/treatments
    $order_id = $pdo->lastInsertId();

    // Insert the corresponding medication or treatment in the `meds_treats` table
    $stmt = $pdo->prepare("INSERT INTO meds_treats (type_name, emp_id, datetime_given, notes, medication_refused, order_id)
                           VALUES (:type_name, :emp_id, NOW(), :notes, :medication_refused, :order_id)");
    
    // Bind medication details and references to the physician order
    $stmt->bindParam(':type_name', $medication);
    $stmt->bindParam(':emp_id', $employee_id);
    $notes = "Dosage: $dosage, Frequency: $frequency, Instructions: $instructions";
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':medication_refused', $medication_refused = false, PDO::PARAM_BOOL);
    $stmt->bindParam(':order_id', $order_id);

    // Execute the statement to add the medication/treatment record
    $stmt->execute();

    // Confirm completion of order
    echo "Physician order and medication/treatment record successfully created.";
    
} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
}

// Close connection
$pdo = null;

?>