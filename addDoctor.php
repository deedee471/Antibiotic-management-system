<?php

//begin a session
session_start();
$sid=session_id();
//path to access the functions in DbConnect
require_once '../mainProject/DbOperations.php';

if (isset($_POST['doctorName']) && isset($_POST['doctorEmail']) && isset($_POST['doctorPhone']) && isset($_POST['doctorSpecialization']) && isset($_POST['doctorPassword']) && isset($_POST['doctorJobId']) && isset($_POST['doctorGender'])){

    function validate($data){
        $data= trim($data);
        $data= stripslashes($data);
        $data= htmlspecialchars($data);
        return $data;
    }
}

$name = htmlspecialchars($_POST['doctorName']);  
$specialization = htmlspecialchars($_POST['doctorSpecialization']);
$phoneNumber = htmlspecialchars($_POST['doctorPhone']);
$email = htmlspecialchars($_POST['doctorEmail']);
$gender = htmlspecialchars($_POST['doctorGender']);
$password = htmlspecialchars($_POST['doctorPassword']); 
$jobId = htmlspecialchars($_POST['doctorJobId']);

if(empty($name) or empty($specialization) or empty($email) or empty($password) or empty($jobId) or empty($gender) or empty($password)){
    header ("Location: ../mainProject/Med-Dashboard/addDoctor.php?error= Please Fill In all the required Fields");
    exit();
}else{
    
    $db= new DbOperation();
    $userId= $_SESSION['userId'];

    if($db->isUserExist($email)){
        header ("Location: ../mainProject/Med-Dashboard/addDoctor.php?error= This Doctor Already Exists");
        exit();
    }else if($db->jobIdMatch($jobId)){
        header ("Location: ../mainProject/Med-Dashboard/addAdmin.php?error= Doctor with This Job Id Already Exists");
        exit();
    }else{
        if($db->createDoctor($name, $phoneNumber, $email, $gender, $specialization, $password, $jobId, $userId)){;
            header ("Location: ../mainProject/Med-Dashboard/addDoctor.php?success= Doctor Created Successfully");
            header("Location: ../mainProject/Med-Dashboard/Doctors.php");
            exit();
        }else{
            header ("Location: ../mainProject/Med-Dashboard/addDoctor.php?error= There was an issue creating the doctor, Please try again");
            exit();
        }
    }
}
?>