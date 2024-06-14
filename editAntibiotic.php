<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../mainProject/Med-Dashboard/editantibiotic.php?antibioticId=$antibioticId&error=User is not logged in");
    exit();
}

// Check if the antibiotic ID is received from the URL parameter
if (!isset($_GET['antibioticId'])) {
    header("Location: ../mainProject/Med-Dashboard/editantibiotic.php?antibioticId=$antibioticId&error=Antibiotic ID not provided");
    exit();
}

// Get the antibiotic ID from the URL parameter
$antibioticId = $_GET['antibioticId'];
$userId= $_SESSION['userId'];

// Check if the required parameters are received from the form data
if (!isset($_POST['antDescription']) || !isset($_POST['antAgeRange'])) {
    header("Location: ../mainProject/Med-Dashboard/editantibiotic.php?antibioticId=$antibioticId&error=Missing Parameters");
    exit();
}

// Get the other received parameters from the form data
$antName = $_POST['antName'];
$antDescription = $_POST['antDescription'];
$antManufacturer = $_POST['antManufacturer'];
$antAgeRange = $_POST['antAgeRange'];
$antQuantity = $_POST['antQuantity'];

// Instantiate DbOperations class
$db = new DbOperation();

// Update the antibiotic record
$result = $db->updateAntibiotic($antibioticId, $antName, $antDescription, $antManufacturer, $antAgeRange, $antQuantity, $userId);

// Prepare and return response
if ($result) {
    header("Location: ../mainProject/Med-Dashboard/editantibiotic.php?antibioticId=$antibioticId&success=Antibiotic Updated Successfully");
    header("Location: ../mainProject/Med-Dashboard/antibiotic.php");
    exit();
} else {
    header("Location: ../mainProject/Med-Dashboard/editantibiotic.php?antibioticId=$antibioticId&error=There was a problem updating antibiotic details");
    exit();
}

?>
