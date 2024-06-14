<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../mainProject/Med-Dashboard/editpatient.php?patientId=$patientId&error=User is not logged in");
    exit();
}

// Check if the patient ID is received from the URL parameter
if (!isset($_GET['patientId'])) {
    header("Location: ../mainProject/Med-Dashboard/editpatient.php?patientId=$patientId&error=Patient ID not provided");
    exit();
}

// Get the patient ID from the URL parameter
$patientId = $_GET['patientId'];
$userId= $_SESSION['userId'];

// Check if the required parameters are received from the form data
if (!isset($_POST['firstName']) || !isset($_POST['lastName']) || !isset($_POST['age']) || !isset($_POST['phone']) || !isset($_POST['email']) || !isset($_POST['patGender']) ||
    !isset($_POST['allergy']) || !isset($_POST['kinFirstName']) || !isset($_POST['kinLastName']) || !isset($_POST['relationship']) || !isset($_POST['kinPhone']) || !isset($_POST['kinEmail'])) {
        header("Location: ../mainProject/Med-Dashboard/editpatient.php?patientId=$patientId?error=Missing Parameters");
        exit();
}

// Get the other received parameters from the form data
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$age = $_POST['age'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$gender = $_POST['patGender'];
$allergy = $_POST['allergy'];
$kinFirstName = $_POST['kinFirstName'];
$kinLastName = $_POST['kinLastName'];
$kinRelationship = $_POST['relationship'];
$kinPhone = $_POST['kinPhone'];
$kinEmail = $_POST['kinEmail'];

// Instantiate DbOperations class
$db = new DbOperation();

// Update the patient record
$result = $db->updatePatient($patientId, $firstName, $lastName, $age, $phone, $email, $gender, $allergy, $kinFirstName, $kinLastName, $kinRelationship, $kinPhone, $kinEmail, $userId);

// Prepare and return response
if ($result) {
    header("Location: ../mainProject/Med-Dashboard/editpatient.php?patientId=$patientId&success= Patient Updated Successfully");
    header("Location: ../mainProject/Med-Dashboard/Patients.php");
    exit();
} else {
    header("Location: ../mainProject/Med-Dashboard/editpatient.php?patientId=$patientId&error=There was a problem updating patient details");
    exit();
}

?>
