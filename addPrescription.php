<?php
// Begin a session
session_start();

// Include the DbOperations class file
require_once '../mainProject/DbOperations.php';

// Check if the required POST parameters are set
if (isset($_POST['patient_id']) && isset($_POST['description']) && isset($_POST['dosage']) && isset($_POST['antibiotic_id']) && isset($_POST['frequency']) && isset($_POST['start_date']) && isset($_POST['end_date'])) {
    
    // Function to validate data
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Extract and validate POST data
    $patientId = validate($_POST['patient_id']);
    $description = validate($_POST['description']);
    $dosage = validate($_POST['dosage']);
    $antibioticId = validate($_POST['antibiotic_id']);
    $frequency = validate($_POST['frequency']);
    $startDate = validate($_POST['start_date']);
    $endDate = validate($_POST['end_date']);

    // Check if the user is logged in
    if (!isset($_SESSION['userId'])) {
        header("Location: ../mainProject/Med-Dashboard/addPrescription.php?error=User not logged in");
        exit();
    }
    
    // Get the currently logged-in user's ID
    $administeredBy = $_SESSION['userId'];

    // Instantiate DbOperations class
    $db = new DbOperation();

    // Check if the required fields are empty
    if (empty($patientId) || empty($description) || empty($dosage) || empty($antibioticId) || empty($frequency) || empty($startDate) || empty($endDate)) {
        header("Location: ../mainProject/Med-Dashboard/addPrescription.php?error=Please fill in all the required fields");
        exit();
    } else {
        // Call the function to add the prescription
        if ($db->createPrescription($patientId, $administeredBy, $description, $dosage, $antibioticId, $frequency, $startDate, $endDate)) {
            header("Location: ../mainProject/Med-Dashboard/addPrescription.php?success=Prescription added successfully");
            header("Location: ../mainProject/Med-Dashboard/Prescription.php");
            exit();
        } else {
            header("Location: ../mainProject/Med-Dashboard/addPrescription.php?error=There was an issue adding the prescription. Please try again");
            exit();
        }
    }
} else {
    // Redirect to the add prescription page with an error message if required parameters are missing
    header("Location: ../mainProject/Med-Dashboard/addPrescription.php?error=Missing parameters");
    exit();
}
?>
