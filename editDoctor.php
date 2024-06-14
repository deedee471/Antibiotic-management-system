<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../mainProject/Med-Dashboard/editdoctor.php?doctorId=$doctorId&error=User is not logged in");
    exit();
}

// Check if the doctor ID is received from the URL parameter
if (!isset($_GET['doctorId'])) {
    header("Location: ../mainProject/Med-Dashboard/editdoctor.php?doctorId=$doctorId&error=Doctor ID not provided");
    exit();
}

// Get the doctor ID from the URL parameter
$doctorId = $_GET['doctorId'];
$userId= $_SESSION['userId'];

// Check if the required parameters are received from the form data
if (!isset($_POST['doctorName']) || !isset($_POST['doctorSpecialization']) || !isset($_POST['doctorGender']) || !isset($_POST['doctorEmail']) || !isset($_POST['doctorPhone'])) {
    header("Location: ../mainProject/Med-Dashboard/editdoctor.php?doctorId=$doctorId&error=Missing Parameters");
    exit();
}

// Get the other received parameters from the form data
$doctorName = $_POST['doctorName'];
$doctorSpecialization = $_POST['doctorSpecialization'];
$doctorGender = $_POST['doctorGender'];
$doctorEmail = $_POST['doctorEmail'];
$doctorPhone = $_POST['doctorPhone'];
$userId= $_SESSION['userId'];

// Instantiate DbOperations class
$db = new DbOperation();

// Update the doctor record
$result = $db->updateDoctor($doctorId, $doctorName, $doctorPhone, $doctorEmail, $doctorGender, $doctorSpecialization, $userId);

// Prepare and return response
if ($result) {
    header("Location: ../mainProject/Med-Dashboard/editdoctor.php?doctorId=$doctorId&success=Doctor Updated Successfully");
    header("Location: ../mainProject/Med-Dashboard/Doctors.php");
    exit();
} else {
    header("Location: ../mainProject/Med-Dashboard/editdoctor.php?doctorId=$doctorId&error=There was a problem updating doctor details");
    exit();
}

?>
