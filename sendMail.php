<?php
require_once '../mainProject/email-service.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $response = array();
        
        $db= new EmailUtilsOperation();
        try{
        if ($db->forgotPassword($email)) {
            header ("Location: ../mainProject/Med-Dashboard/emailPassword.php?success= A reset link has been sent to your email");
            exit();
        } else {
            header ("Location: ../mainProject/Med-Dashboard/emailPassword.php?error= User with this email not found");
            exit();
        }
    }catch(Exception $e){
        header ("Location: ../mainProject/Med-Dashboard/emailPassword.php?error= Error");
        exit();
    }

    }
    echo json_encode($response);
?>
