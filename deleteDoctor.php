<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    $response = ['error' => true, 'message' => 'User not logged in'];
    echo json_encode($response);
    exit();
}

// Check if the doctor ID is provided
if (!isset($_GET['doctorId'])) {
    $response = ['error' => true, 'message' => 'doctor ID is required'];
    echo json_encode($response);
    exit();
}

// Extract the doctor ID from the URL parameter
$doctorId = $_GET['doctorId'];
$userId= $_SESSION['userId'];

// Create a new DbOperations object
$db = new DbOperation();

// Check if the DbOperations object was created successfully
if (!$db) {
    $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    echo json_encode($response);
    exit();
}

// Attempt to delete the doctor
$success = $db->deletedoctor($doctorId, $userId);

// Check if the doctor was deleted successfully
if ($success) {
    $response = ['error' => false, 'message' => 'doctor deleted successfully'];
} else {
    $response = ['error' => true, 'message' => 'Failed to delete doctor'];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
