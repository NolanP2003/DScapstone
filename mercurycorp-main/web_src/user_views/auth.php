<?php
// checks if user is logged in and the correct role is assigned
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /views/login.php");
    exit;
}

function isNurse() {
    return $_SESSION['role'] === 'nurse';
}

function isResident() {
    return $_SESSION['role'] === 'resident';
}

function isHR(){
    return $_SESSION['role'] === 'hr';
}

function isEmployee(){
    return $_SESSION['role'] === 'employee';
}
?>
