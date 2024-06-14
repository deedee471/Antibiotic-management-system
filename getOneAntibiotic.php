<?php

session_start();
require_once '../mainProject/DbOperations.php';

if (isset($_SESSION['userId'])) {

    if (isset($_GET['antibioticId'])) {
        $antibioticId = $_GET['antibioticId'];

        $db = new DbOperation();

        if ($db) {
            $antibiotic = $db->getAntibiotic($antibioticId);
            if ($antibiotic) {
                header('Content-Type: application/json');
                echo json_encode($antibiotic);
                exit();
            } else {
                $response = ['error' => true, 'message' => 'Failed to retrieve antibiotic data'];
            }

        } else {
            $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
        }

    } else {
        $response = ['error' => true, 'message' => 'antibiotic ID is required'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
