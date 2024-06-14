<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    $response = ['error' => true, 'message' => 'User not logged in'];
    echo json_encode($response);
    exit();
}

// Check if the antibiotic ID is provided
if (!isset($_GET['antibioticId'])) {
    $response = ['error' => true, 'message' => 'antibiotic ID is required'];
    echo json_encode($response);
    exit();
}

// Extract the antibiotic ID from the URL parameter
$antibioticId = $_GET['antibioticId'];
$userId= $_SESSION['userId'];

// Create a new DbOperations object
$db = new DbOperation();

// Check if the DbOperations object was created successfully
if (!$db) {
    $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    echo json_encode($response);
    exit();
}

// Attempt to delete the antibiotic
$success = $db->deleteAntibiotic($antibioticId, $userId);

// Check if the antibiotic was deleted successfully
if ($success) {
    $response = ['error' => false, 'message' => 'Antibiotic deleted successfully'];
} else {
    $response = ['error' => true, 'message' => 'Failed to delete antibiotic'];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
