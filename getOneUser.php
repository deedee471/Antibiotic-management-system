<?php

session_start();
require_once '../mainProject/DbOperations.php';

if (isset($_SESSION['userId'])) {

    if (isset($_GET['userId'])) {
        $userId = $_GET['userId'];

        $db = new DbOperation();

        if ($db) {
            $user = $db->getUser($userId);
            if ($user) {
                header('Content-Type: application/json');
                echo json_encode($user);
                exit();
            } else {
                $response = ['error' => true, 'message' => 'Failed to retrieve user data'];
            }

        } else {
            $response = ['error' => true, 'message' => 'Failed to create DbOperation object'];
        }

    } else {
        $response = ['error' => true, 'message' => 'user ID is required'];
    }
} else {
    $response = ['error' => true, 'message' => 'User not logged in'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
