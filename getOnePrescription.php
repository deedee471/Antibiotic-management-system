<?php

session_start();
require_once '../mainProject/DbOperations.php';

if (isset($_SESSION['userId'])) {

    if (isset($_GET['PrescriptionId'])) {
        $PrescriptionId = $_GET['PrescriptionId'];

        $db = new DbOperation();

        if ($db) {
            $Prescription = $db->getPrescription($PrescriptionId);
            if ($Prescription) {
                header('Content-Type: application/json');
                echo json_encode($Prescription);
                exit();
            } else {
                $response = ['error' => true, 'message' => 'Failed to retrieve Prescription data'];
            }

        } else {
            $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
        }

    } else {
        $response = ['error' => true, 'message' => 'Prescription ID is required'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
