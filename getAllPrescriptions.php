<?php

session_start();
require_once '../mainProject/DbOperations.php';


if (isset($_SESSION['userId'])) {

    $db = new DbOperation();

    if ($db) {
        $prescriptions = $db->getAllPrescriptions();

        if ($prescriptions) {
            header('Content-Type: application/json');
            echo json_encode(['prescriptions' => $prescriptions]);
            exit();
        } else {
            $response = ['error' => true, 'message' => 'Failed to retrieve patient data'];
        }
    } else {
        $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
