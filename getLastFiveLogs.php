<?php

session_start();
require_once '../mainProject/DbOperations.php';


if (isset($_SESSION['userId'])) {

    $db = new DbOperation();

    if ($db) {
        $role= $_SESSION['role'];
        if($role== 'DOCTOR'){
            $logs = $db->getLastFiveLogsExcludingUsers();   
        }else{
            $logs = $db->getLastFiveLogs();
        }

        if ($logs) {
            header('Content-Type: application/json');
            echo json_encode(['logs' => $logs]);
            exit();
        } else {
            $response = ['error' => true, 'message' => 'Failed to retrieve logs data'];
        }
    } else {
        $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
