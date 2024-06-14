<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../mainProject/Med-Dashboard/editPrescription.php?PrescriptionId=$PrescriptionId&error=User is not logged in");
    exit();
}

// Check if the Prescription ID is received from the URL parameter
if (!isset($_GET['PrescriptionId'])) {
    header("Location: ../mainProject/Med-Dashboard/editPrescription.php?PrescriptionId=$PrescriptionId&error=Prescription ID not provided");
    exit();
}

$administeredBy = $_SESSION['userId'];
$PrescriptionId = $_GET['PrescriptionId'];

// Check if the required parameters are received from the form data
if (!isset($_POST['patient_id']) || !isset($_POST['description']) || !isset($_POST['dosage']) || !isset($_POST['antibiotic_id']) ||
    !isset($_POST['frequency']) || !isset($_POST['start_date']) || !isset($_POST['end_date'])) {
    header("Location: ../mainProject/Med-Dashboard/editPrescription.php?PrescriptionId=$PrescriptionId&error=Missing Parameters");
    exit();
}

// Get the other received parameters from the form data
$patientId = $_POST['patient_id'];
$description = $_POST['description'];
$dosage = $_POST['dosage'];
$antibioticId = $_POST['antibiotic_id'];
$frequency = $_POST['frequency'];
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];

// Instantiate DbOperations class
$db = new DbOperation();

// Update the Prescription record
$result = $db->updatePrescription($PrescriptionId, $patientId, $administeredBy, $description, $dosage, $antibioticId, $frequency, $startDate, $endDate);

// Prepare and return response
if ($result) {
    header("Location: ../mainProject/Med-Dashboard/editPrescription.php?PrescriptionId=$PrescriptionId&success=Prescription Updated Successfully");
    header("Location: ../mainProject/Med-Dashboard/Prescription.php");
    exit();
} else {
    header("Location: ../mainProject/Med-Dashboard/editPrescription.php?PrescriptionId=$PrescriptionId&error=There was a problem updating Prescription details");
    exit();
}
?>
