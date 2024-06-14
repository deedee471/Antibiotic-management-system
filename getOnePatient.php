<?php

session_start();
require_once '../mainProject/DbOperations.php';

if (isset($_SESSION['userId'])) {

    if (isset($_GET['patientId'])) {
        $patientId = $_GET['patientId'];

        $db = new DbOperation();

        if ($db) {
            $patient = $db->getPatient($patientId);
            if ($patient) {
                header('Content-Type: application/json');
                echo json_encode($patient);
                exit();
            } else {
                $response = ['error' => true, 'message' => 'Failed to retrieve patient data'];
            }

        } else {
            $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
        }

    } else {
        $response = ['error' => true, 'message' => 'Patient ID is required'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
