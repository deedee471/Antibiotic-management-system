<?php

session_start();
require_once '../mainProject/DbOperations.php';

// Check if the user is logged in
if (isset($_SESSION['userId'])) {
    $db = new DbOperation();

    if ($db) {

        $doctorsCount = $db->getTotalDoctorsCount();

        if ($doctorsCount !== false) {
            header('Content-Type: application/json');
            echo json_encode(['count' => $doctorsCount]);
            exit();
        } else {
            $response = ['error' => true, 'message' => 'Failed to retrieve doctors count'];
        }
    } else {
        $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
