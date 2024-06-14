<?php

session_start();
require_once '../mainProject/DbOperations.php';

if (isset($_SESSION['userId'])) {

    if (isset($_GET['doctorId'])) {
        $doctorId = $_GET['doctorId'];

        $db = new DbOperation();

        if ($db) {
            $doctor = $db->getDoctor($doctorId);
            if ($doctor) {
                header('Content-Type: application/json');
                echo json_encode($doctor);
                exit();
            } else {
                $response = ['error' => true, 'message' => 'Failed to retrieve doctor data'];
            }

        } else {
            $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
        }

    } else {
        $response = ['error' => true, 'message' => 'doctor ID is required'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
