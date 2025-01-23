<?php

header('Content-Type: application/json');
require_once 'config.php'; // Include your database configuration file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the input data
    $type_name = isset($_POST['type_name']) ? mysqli_real_escape_string($conn, $_POST['type_name']) : '';
    $emp_id = isset($_POST['emp_id']) ? mysqli_real_escape_string($conn, $_POST['emp_id']) : '';
    $datetime_given = isset($_POST['datetime_given']) ? mysqli_real_escape_string($conn, $_POST['datetime_given']) : '';
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    $medication_refused = isset($_POST['medication_refused']) ? (int)$_POST['medication_refused'] : 0;
    $order_id = isset($_POST['order_id']) ? mysqli_real_escape_string($conn, $_POST['order_id']) : '';

    // Validate the required fields
    if (empty($type_name) || empty($emp_id) || empty($datetime_given) || empty($order_id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        exit;
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO meds_treats (type_name, emp_id, datetime_given, notes, medication_refused, order_id) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "sissii", $type_name, $emp_id, $datetime_given, $notes, $medication_refused, $order_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            http_response_code(201);
            echo json_encode(['message' => 'Medication administration record created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create medication administration record']);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to prepare SQL statement']);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
