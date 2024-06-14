<?php

//begin a session
session_start();
$sid=session_id();
//path to access the functions in DbConnect
require_once '../mainProject/DbOperations.php';

if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['allergy']) && isset($_POST['age']) && isset($_POST['patGender'])
&& isset($_POST['kinFirstName']) && isset($_POST['kinLastName']) && isset($_POST['kinEmail']) && isset($_POST['kinPhone']) && isset($_POST['relationship'])){


    function validate($data){
        $data= trim($data);
        $data= stripslashes($data);
        $data= htmlspecialchars($data);
        return $data;
    }
}

$firstName = htmlspecialchars($_POST['firstName']); 
$lastName = htmlspecialchars($_POST['lastName']); 
$age = htmlspecialchars($_POST['age']);
$phoneNumber = htmlspecialchars($_POST['phone']);
$email = htmlspecialchars($_POST['email']);
$patGender = htmlspecialchars($_POST['patGender']);
$allergy = htmlspecialchars($_POST['allergy']);

$kinfirstName = htmlspecialchars($_POST['kinFirstName']); 
$kinlastName = htmlspecialchars($_POST['kinLastName']); 
$kinphoneNumber = htmlspecialchars($_POST['kinPhone']);
$kinemail = htmlspecialchars($_POST['kinEmail']);
$relationship = htmlspecialchars($_POST['relationship']);
$userId= $_SESSION['userId'];

if(empty($firstName) or empty($lastName) or empty($age) or empty($email) or empty($patGender) or empty($phoneNumber)
or empty($kinfirstName) or empty($kinlastName) or empty($kinphoneNumber) or empty($kinemail) or empty($relationship)
){
    header ("Location: ../mainProject/Med-Dashboard/addpatientform.php?error= Please Fill In all the required Fields");
    exit();
}else{
    
    $db= new DbOperation();

    if($db->isPatientExist($email)){
        header ("Location: ../mainProject/Med-Dashboard/addpatientform.php?error= Patient with this Email Already Exists");
        exit();
    }else{
        if($db->createPatient($firstName,$lastName,$age,$phoneNumber,$email,$patGender,$allergy,$kinfirstName,$kinlastName,$relationship,$kinphoneNumber,$kinemail, $userId)){;
            header ("Location: ../mainProject/Med-Dashboard/addpatientform.php?success= Patient Created Successfully");
            header("Location: ../mainProject/Med-Dashboard/Patients.php");
            exit();
        }else{
            header ("Location: ../mainProject/Med-Dashboard/addpatientform.php?error= There was an issue creating the patient, Please try again");
            exit();
        }
    }
}
?>