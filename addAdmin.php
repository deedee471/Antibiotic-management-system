<?php

//begin a session
session_start();
$sid=session_id();
//path to access the functions in DbConnect
require_once '../mainProject/DbOperations.php';

if (isset($_POST['adminName']) && isset($_POST['adminEmail']) && isset($_POST['adminPhone']) && isset($_POST['adminPassword']) && isset($_POST['adminJobId']) && isset($_POST['adminGender'])){

    function validate($data){
        $data= trim($data);
        $data= stripslashes($data);
        $data= htmlspecialchars($data);
        return $data;
    }
}

$name = htmlspecialchars($_POST['adminName']);  
$phoneNumber = htmlspecialchars($_POST['adminPhone']);
$email = htmlspecialchars($_POST['adminEmail']);
$gender = htmlspecialchars($_POST['adminGender']);
$password = htmlspecialchars($_POST['adminPassword']); 
$jobId = htmlspecialchars($_POST['adminJobId']);

if(empty($name) or empty($email) or empty($password) or empty($jobId) or empty($gender) or empty($password)){
    header ("Location: ../mainProject/Med-Dashboard/addAdmin.php?error= Please Fill In all the required Fields");
    exit();
}else{
    
    $db= new DbOperation();

    if($db->isUserExist($email)){
        header ("Location: ../mainProject/Med-Dashboard/addAdmin.php?error= This Admin Already Exists");
        exit();
    }else if($db->jobIdMatch($jobId)){
        header ("Location: ../mainProject/Med-Dashboard/addAdmin.php?error= Admin with This Job Id Already Exists");
        exit();
    }else{
        $userId= $_SESSION['userId'];
        if($db->createadmin($name, $phoneNumber, $email, $gender, $password, $jobId, $userId)){;
            header ("Location: ../mainProject/Med-Dashboard/addAdmin.php?success= Admin Created Successfully");
            header("Location: ../mainProject/Med-Dashboard/Admins.php");
            exit();
        }else{
            header ("Location: ../mainProject/Med-Dashboard/addAdmin.php?error= There was an issue creating the admin, Please try again");
            exit();
        }
    }
}
?>