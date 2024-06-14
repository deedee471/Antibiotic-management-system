<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../mainProject/Med-Dashboard/editadmin.php?adminId=$adminId&error=User is not logged in");
    exit();
}

// Check if the admin ID is received from the URL parameter
if (!isset($_GET['adminId'])) {
    header("Location: ../mainProject/Med-Dashboard/editadmin.php?adminId=$adminId&error=admin ID not provided");
    exit();
}

// Get the admin ID from the URL parameter
$adminId = $_GET['adminId'];

// Check if the required parameters are received from the form data
if (!isset($_POST['adminName']) || !isset($_POST['adminGender']) || !isset($_POST['adminEmail']) || !isset($_POST['adminPhone'])) {
    header("Location: ../mainProject/Med-Dashboard/editadmin.php?adminId=$adminId&error=Missing Parameters");
    exit();
}

// Get the other received parameters from the form data
$adminName = $_POST['adminName'];
$adminGender = $_POST['adminGender'];
$adminEmail = $_POST['adminEmail'];
$adminPhone = $_POST['adminPhone'];
$userId= $_SESSION['userId'];

// Instantiate DbOperations class
$db = new DbOperation();

// Update the admin record
$result = $db->updateadmin($adminId, $adminName, $adminPhone, $adminEmail, $adminGender, $userId);

// Prepare and return response
if ($result) {
    header("Location: ../mainProject/Med-Dashboard/editadmin.php?adminId=$adminId&success=Admin Updated Successfully");
    header("Location: ../mainProject/Med-Dashboard/Admins.php");
    exit();
} else {
    header("Location: ../mainProject/Med-Dashboard/editadmin.php?adminId=$adminId&error=There was a problem updating admin details");
    exit();
}

?>
