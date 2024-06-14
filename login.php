<?php

//begin a session
session_start();
$sid=session_id();
//path to access the functions in DbConnect
require_once '../mainProject/DbOperations.php';

if (isset($_POST['email']) && isset($_POST['password'])){

    function validate($data){
        $data= trim($data);
        $data= stripslashes($data);
        $data= htmlspecialchars($data);
        return $data;
    }
}

$email = validate($_POST['email']);
$password = validate($_POST['password']);

if(empty($email) or empty($password)){
    header ("Location: ../mainProject/Med-Dashboard/signin.php?error= Please Fill In all the required Fields");
    exit();
}else{
    
    $db= new DbOperation();

    $db->createDefaultAdmin();
        
    if($db->userLogin($email,$password)==1){
        $user= $db->getUsersession($email);
        header ("Location: ../mainProject/Med-Dashboard/signin.php?success= Login Successful");
        echo "Login Successful";
        $_SESSION['email']= $email;
        $_SESSION['userId']=$user['_id'];
        $_SESSION['role']= $user['role'];
        $_SESSION['name']=$user['name'];
        header("Location: ../mainProject/Med-Dashboard/index.php");
        exit();
    }elseif($db->userLogin($email,$password)==2){
        $user= $db->getUsersession($email);
        header ("Location: ../mainProject/Med-Dashboard/signin.php?success= Login Successful, Welcome Admin");
        echo "Login Successful, Welcome Admin";
        $_SESSION['email']= $email;
        $_SESSION['userId']=$user['_id'];
        $_SESSION['role']= $user['role'];
        $_SESSION['name']=$user['name'];
        header("Location: ../mainProject/Med-Dashboard/index.php");
        exit();
    }else{
        header ("Location: ../mainProject/Med-Dashboard/signin.php?error= Incorrect Username Or Password");
        exit();
    }
}
?>