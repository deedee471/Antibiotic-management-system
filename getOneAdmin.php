<?php

session_start();
require_once '../mainProject/DbOperations.php';

if (isset($_SESSION['userId'])) {

    if (isset($_GET['adminId'])) {
        $adminId = $_GET['adminId'];

        $db = new DbOperation();

        if ($db) {
            $admin = $db->getadmin($adminId);
            if ($admin) {
                header('Content-Type: application/json');
                echo json_encode($admin);
                exit();
            } else {
                $response = ['error' => true, 'message' => 'Failed to retrieve admin data'];
            }

        } else {
            $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
        }

    } else {
        $response = ['error' => true, 'message' => 'admin ID is required'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
