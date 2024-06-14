<?php
session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    $response = ['error' => true, 'message' => 'User not logged in'];
    echo json_encode($response);
    exit();
}

// Check if the admin ID is provided
if (!isset($_GET['adminId'])) {
    $response = ['error' => true, 'message' => 'admin ID is required'];
    echo json_encode($response);
    exit();
}

// Extract the admin ID from the URL parameter
$adminId = $_GET['adminId'];
$userId= $_SESSION['userId'];

// Create a new DbOperations object
$db = new DbOperation();

// Check if the DbOperations object was created successfully
if (!$db) {
    $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    echo json_encode($response);
    exit();
}

// Attempt to delete the admin
$success = $db->deleteAdmin($adminId, $userId);

// Check if the admin was deleted successfully
if ($success) {
    $response = ['error' => false, 'message' => 'Admin deleted successfully'];
} else {
    $response = ['error' => true, 'message' => 'Failed to delete admin'];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
