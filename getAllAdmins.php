<?php

session_start();
require_once '../mainProject/DbOperations.php';


if (isset($_SESSION['userId'])) {

    $db = new DbOperation();

    if ($db) {
        $admins = $db->getAllAdmins();

        if ($admins) {
            header('Content-Type: application/json');
            echo json_encode(['admins' => $admins]);
            exit();
        } else {
            $response = ['error' => true, 'message' => 'Failed to retrieve admins data'];
        }
    } else {
        $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
