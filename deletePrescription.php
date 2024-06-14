<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    $response = ['error' => true, 'message' => 'User not logged in'];
    echo json_encode($response);
    exit();
}

// Check if the Prescription ID is provided
if (!isset($_GET['PrescriptionId'])) {
    $response = ['error' => true, 'message' => 'Prescription ID is required'];
    echo json_encode($response);
    exit();
}

// Extract the Prescription ID from the URL parameter
$PrescriptionId = $_GET['PrescriptionId'];
$userId= $_SESSION['userId'];

// Create a new DbOperations object
$db = new DbOperation();

// Check if the DbOperations object was created successfully
if (!$db) {
    $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    echo json_encode($response);
    exit();
}

// Attempt to delete the Prescription
$success = $db->deletePrescription($PrescriptionId, $userId);

// Check if the Prescription was deleted successfully
if ($success) {
    $response = ['error' => false, 'message' => 'Prescription deleted successfully'];
} else {
    $response = ['error' => true, 'message' => 'Failed to delete Prescription'];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
