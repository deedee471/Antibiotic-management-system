<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    $response = ['error' => true, 'message' => 'User not logged in'];
    echo json_encode($response);
    exit();
}

// Check if the patient ID is provided
if (!isset($_GET['patientId'])) {
    $response = ['error' => true, 'message' => 'Patient ID is required'];
    echo json_encode($response);
    exit();
}

// Extract the patient ID from the URL parameter
$patientId = $_GET['patientId'];
$userId= $_SESSION['userId'];

// Create a new DbOperations object
$db = new DbOperation();

// Check if the DbOperations object was created successfully
if (!$db) {
    $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    echo json_encode($response);
    exit();
}

// Attempt to delete the patient
$success = $db->deletePatient($patientId, $userId);

// Check if the patient was deleted successfully
if ($success) {
    $response = ['error' => false, 'message' => 'Patient deleted successfully'];
} else {
    $response = ['error' => true, 'message' => 'Failed to delete patient'];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
