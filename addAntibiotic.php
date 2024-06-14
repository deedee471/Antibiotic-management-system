<?php
// Begin a session
session_start();

// Include the DbOperations class file
require_once '../mainProject/DbOperations.php';

// Check if the required POST parameters are set
if (isset($_POST['antName']) && isset($_POST['antDescription']) && isset($_POST['antManufacturer']) && isset($_POST['antAgeRange']) && isset($_POST['antQuantity'])) {
    
    // Function to validate data
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Extract and validate POST data
    $antName = validate($_POST['antName']);
    $antDescription = validate($_POST['antDescription']);
    $antManufacturer = validate($_POST['antManufacturer']);
    $antAgeRange = validate($_POST['antAgeRange']);
    $antQuantity = validate($_POST['antQuantity']);
    $userId= $_SESSION['userId'];

    // Instantiate DbOperations class
    $db = new DbOperation();

    // Check if the required fields are empty
    if (empty($antName) || empty($antDescription) || empty($antManufacturer) || empty($antAgeRange) || empty($antQuantity)) {
        header("Location: ../mainProject/Med-Dashboard/addantibiotic.php?error=Please fill in all the required fields");
        exit();
    } else {
        // Call the function to add the antibiotic
        if ($db->createAntibiotic($antName, $antDescription, $antManufacturer, $antAgeRange, $antQuantity, $userId)) {
            header("Location: ../mainProject/Med-Dashboard/addantibiotic.php?success=Antibiotic added successfully");
            header("Location: ../mainProject/Med-Dashboard/antibiotic.php");
            exit();
        } else {
            header("Location: ../mainProject/Med-Dashboard/addantibiotic.php?error=There was an issue adding the antibiotic. Please try again");
            exit();
        }
    }
} else {
    // Redirect to the admin page with an error message if required parameters are missing
    header("Location: ../mainProject/Med-Dashboard/addantibiotic.php?error=Missing parameters");
    exit();
}
?>
