<?php


  class DbOperation{
   
    private $con;
    //constuctor which aids in db connection
    function __construct()
    {
        require_once  dirname(__FILE__).'/DbConnect.php';

        $db = new DbConnect();

        $this->con = $db->connect();
    }
    
    //function for creating a doc
    public function createDoctor($name,$phoneNumber,$email,$gender,$specialization,$pass,$jobId, $userId){

        $password=password_hash($pass, PASSWORD_DEFAULT);

        $stmt= $this->con->prepare("INSERT INTO `user` (`name`, `phone_number`, `email`, `gender`, `password`, `role`, `specialization`, `job_id` , `created_at`) VALUES( ?, ?, ?, ?, ?, 'DOCTOR' , ? , ? , NOW());");
        $stmt->bind_param("sssssss",$name,$phoneNumber,$email,$gender,$password, $specialization, $jobId);
    
         if($stmt->execute()){
            $uname = $this->getUserName($userId);
            $text = "$name has been added as a Doctor by $uname";
            $related= "User";
            $this->createLogs($userId, $text, $related);
            return true;
         }
         else{
            return false;
         }
    }

    public function createAdmin($name,$phoneNumber,$email,$gender,$pass, $jobId, $userId){

        $password=password_hash($pass, PASSWORD_DEFAULT);

        $stmt= $this->con->prepare("INSERT INTO `user` (`name`, `phone_number`, `email`, `gender`, `password`, `role`, `job_id` , `created_at`) VALUES( ?, ?, ?, ?, ? , 'ADMIN', ? , NOW());");
        $stmt->bind_param("ssssss",$name,$phoneNumber,$email,$gender,$password, $jobId);
    
         if($stmt->execute()){
            $uname = $this->getUserName($userId);
            $text = "$name has been added as an Admin by $uname";
            $related= "User";
            $this->createLogs($userId, $text, $related);
            return true;
         }
         else{
            return false;
         }
    }
    

    public function updateUser($userId, $name, $phoneNumber, $email, $gender, $specialization ) {

        $stmt = $this->con->prepare("UPDATE user SET name = ?, phone_number = ?, email = ?, gender = ?, specialization = ?, updated_at = NOW() WHERE _id = ?");
        $stmt->bind_param("ssssssi", $name, $phoneNumber, $email, $gender, $specialization, $userId);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateDoctor($doctorId, $name, $phoneNumber, $email, $gender, $specialization, $userId) {
        $stmt = $this->con->prepare("UPDATE user SET name = ?, phone_number = ?, email = ?, gender = ?, specialization = ?, updated_at = NOW() WHERE _id = ?");
        $stmt->bind_param("sssssi", $name, $phoneNumber, $email, $gender, $specialization, $doctorId);
        
        if ($stmt->execute()) {
            $uname = $this->getUserName($userId);
            $text = "Profile for $name has been updated by $uname";
            $related= "User";
            $this->createLogs($userId, $text, $related);
            return true;
        } else {
            return false;
        }
    }

    public function updateAdmin($doctorId, $name, $phoneNumber, $email, $gender, $userId) {
        $stmt = $this->con->prepare("UPDATE user SET name = ?, phone_number = ?, email = ?, gender = ?, updated_at = NOW() WHERE _id = ?");
        $stmt->bind_param("ssssi", $name, $phoneNumber, $email, $gender, $doctorId);
        
        if ($stmt->execute()) {
            $uname = $this->getUserName($userId);
            $text = "Profile for $name has been updated by $uname";
            $related= "User";
            $this->createLogs($userId, $text, $related);
            return true;
        } else {
            return false;
        }
    }
    
    

    //creating default admin
    public function createSuperAdmin(){
        $password=password_hash('super@123', PASSWORD_DEFAULT);
        $stmt= $this->con->prepare("INSERT INTO `user` (`name`, `phone_number`, `email`, `gender`, `job_id`, `password`, `role`, `created_at`) VALUES('Super Admin', '+254780222456', 'admin1@gmail.com', 'Male', 'SAD34512', '$password', 'SUPERADMIN', NOW());");
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    //checking if admin exists in db
    public function createDefaultAdmin(){
      $stmt =  $this->con->prepare("SELECT role from user WHERE role= ?");
      $role='SUPERADMIN';
      $stmt-> bind_param("s",$role);
      $stmt->execute();
      $stmt->store_result();
      if($stmt->num_rows == 0){
          $this->createSuperAdmin();
      }
  
    }
  
    //function for user login 
    public function userLogin($email, $pass){
        $stmt = $this->con->prepare("SELECT * from user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
            $cpass = $user['password'];
            $uId= $user['_id'];
    
            if(password_verify($pass, $cpass)){
                $crole = $user['role'];
    
                if($crole == 'ADMIN' || $crole == 'SUPERADMIN' ){
                    return 2;
                }else{
                    $uname = $this->getUserName($uId);
                    $text = "$uname has logged in to the system";
                    $related= "User";
                    $this->createLogs($uId, $text, $related);
                    return 1;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    

    //function to fetch user 
    public function getUser($userId){
        $stmt =  $this->con->prepare("SELECT * from user WHERE _id= ?");
        $stmt-> bind_param("s",$userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getUserName($userId) {
        $stmt = $this->con->prepare("SELECT name FROM user WHERE _id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();
        return $username;
    }

    public function getDoctor($doctorId){
        $stmt =  $this->con->prepare("SELECT * from user WHERE _id= ? AND role= 'DOCTOR'");
        $stmt-> bind_param("s",$doctorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function deleteDoctor($doctorId, $userId){
        $dname = $this->getUserName($doctorId);
        $stmt =  $this->con->prepare("DELETE from user WHERE _id= ?");
        $stmt->bind_param("i", $doctorId);
        
        if ($stmt->execute()) {
            $uname = $this->getUserName($userId);
            $text = " Doctor $dname has been deleted by $uname";
            $related= "User";
            $this->createLogs($userId, $text, $related);
            return true;
        } else {
            return false;
        }
    }

    public function getAllAdmins(){
        $stmt =  $this->con->prepare("SELECT * from user WHERE role= 'ADMIN'");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while($user = $result->fetch_assoc()){
            $users[] = $user;
        }
        return $users;
    }

    public function getAdmin($adminId){
        $stmt =  $this->con->prepare("SELECT * from user WHERE _id= ? AND role= 'ADMIN'");
        $stmt-> bind_param("s",$adminId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function deleteAdmin($adminId, $userId){
        $aname = $this->getUserName($adminId);
        $stmt =  $this->con->prepare("DELETE from user WHERE _id= ?");
        $stmt->bind_param("i", $adminId);
        
        if ($stmt->execute()) {
            $uname = $this->getUserName($userId);
            $text = "Admin $aname has been deleted by $uname";
            $related= "User";
            $this->createLogs($userId, $text, $related);
            return true;
        } else {
            return false;
        }
    }

    public function getAllDoctors(){
        $stmt =  $this->con->prepare("SELECT * from user WHERE role= 'DOCTOR'");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while($user = $result->fetch_assoc()){
            $users[] = $user;
        }
        return $users;
    }

    public function getUsersession($email){
        $stmt =  $this->con->prepare("SELECT * from user WHERE email= ?");
        $stmt-> bind_param("s",$email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    //function to get all users 
    public function getAllUsers(){
        $stmt = $this->con->prepare("SELECT * FROM user");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while($user = $result->fetch_assoc()){
            $users[] = $user;
        }
        return $users;
    }

    public function getTotalUserCount() {
        $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM user");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalCount = $row['total_count'];
        return $totalCount;
    } 

    public function getTotalDoctorsCount() {
        $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM user WHERE role= 'DOCTOR'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalCount = $row['total_count'];
        return $totalCount;
    } 

    public function getTotalAdminsCount() {
        $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM user WHERE role= 'ADMIN'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalCount = $row['total_count'];
        return $totalCount;
    } 

    //function to check if user exists in db
    public function isUserExist($email) {
        $stmt = $this->con->prepare("SELECT _id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function jobIdMatch($jobId) {
        $stmt = $this->con->prepare("SELECT _id FROM user WHERE job_id = ?");
        $stmt->bind_param("s", $jobId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    //function for logout
    public function logout(){
        session_start();
        unset($_SESSION['email']);
        session_destroy();
        return true;
    }

        //CRUD For Patients
        //function for creating a patient
        public function createPatient($firstName,$lastName,$age,$phoneNumber,$email,$gender,$allergy,$nokfirstName,$noklastName,$nokRelationship,$nokPhoneNumber,$nokEmail, $userId){
    
            $stmt = $this->con->prepare("INSERT INTO `patient` (`first_name`, `last_name`, `age`, `phone_number`, `email`, `gender`, `allergy`, `next_of_kin_first_name`, `next_of_kin_last_name`, `next_of_kin_relationship`, `next_of_kin_phone_number`, `next_of_kin_email`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssssssssss", $firstName, $lastName, $age, $phoneNumber, $email, $gender, $allergy, $nokfirstName, $noklastName, $nokRelationship, $nokPhoneNumber, $nokEmail);            
        
             if($stmt->execute()){
                $uname = $this->getUserName($userId);
                $text = "Patient $firstName $lastName has been created by $uname";
                $related= "Patient";
                $this->createLogs($userId, $text, $related);
                return true;
             }
             else{
                return false;
             }
        }

        public function isPatientExist($email) {
            $stmt = $this->con->prepare("SELECT _id FROM patient WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }
        
    
        public function updatePatient($patientId, $firstName, $lastName, $age, $phoneNumber, $email, $gender, $allergy, $nokfirstName, $noklastName, $nokRelationship, $nokPhoneNumber, $nokEmail, $userId) {
            $stmt = $this->con->prepare("UPDATE patient SET first_name = ?, last_name = ?, age = ?, phone_number = ?, email = ?, gender = ?, allergy = ?, next_of_kin_first_name = ?, next_of_kin_last_name = ?, next_of_kin_relationship = ?, next_of_kin_phone_number = ?, next_of_kin_email = ?, updated_at = NOW() WHERE _id = ?");
            $stmt->bind_param("ssssssssssssi", $firstName, $lastName, $age, $phoneNumber, $email, $gender, $allergy, $nokfirstName, $noklastName, $nokRelationship, $nokPhoneNumber, $nokEmail, $patientId);
            
            if ($stmt->execute()) {
                $uname = $this->getUserName($userId);
                $text = "Profile for Patient $firstName $lastName has been updated by $uname";
                $related= "Patient";
                $this->createLogs($userId, $text, $related);
                return true;
            } else {
                return false;
            }
        }
        
        

        public function getPatient($patientId){
            $stmt =  $this->con->prepare("SELECT * from patient WHERE _id= ?");
            $stmt-> bind_param("s",$patientId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    

        public function getAllPatients(){
            $stmt = $this->con->prepare("SELECT * FROM patient");
            $stmt->execute();
            $result = $stmt->get_result();
            $patients = array();
            while($patient = $result->fetch_assoc()){
                $patients[] = $patient;
            }
            return $patients;
        }
    
        public function getPatientsCount() {
            $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM patient");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $totalCount = $row['total_count'];
            return $totalCount;
        } 

        public function deletePatient($userId, $loggedIn) {
            $pname = $this->getPatientName($userId);
            $stmt = $this->con->prepare("DELETE FROM patient WHERE _id = ?");
            $stmt->bind_param("i", $userId);
            
            if ($stmt->execute()) {
                $uname = $this->getPatientName($loggedIn);
                $text = "Patient $pname has been deleted by $uname";
                $related= "Patient";
                $this->createLogs($userId, $text, $related);
                return true;
            } else {
                return false;
            }
        }

        public function getPatientName($patientId) {
            $stmt = $this->con->prepare("SELECT CONCAT(first_name, ' ', last_name) AS fullName FROM patient WHERE _id = ?");
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $stmt->bind_result($fullName);
            $stmt->fetch();
            $stmt->close();
            return $fullName;
        }



        //CRUD For Allergy

        public function createAllergy($name, $description, $severity) {
            $stmt = $this->con->prepare("INSERT INTO `allergy` (`name`, `description`, `severity`) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $description, $severity);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        // Function for updating an allergy
        public function updateAllergy($allergyId, $name, $description, $severity) {
            $stmt = $this->con->prepare("UPDATE allergy SET name = ?, description = ?, severity = ? WHERE _id = ?");
            $stmt->bind_param("sssi", $name, $description, $severity, $allergyId);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    
        // Function for retrieving an allergy
        public function getAllergy($allergyId) {
            $stmt =  $this->con->prepare("SELECT * FROM allergy WHERE _id = ?");
            $stmt->bind_param("i", $allergyId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    
        // Function for retrieving all allergies
        public function getAllAllergies() {
            $stmt = $this->con->prepare("SELECT * FROM allergy");
            $stmt->execute();
            $result = $stmt->get_result();
            $allergies = array();
            while ($allergy = $result->fetch_assoc()) {
                $allergies[] = $allergy;
            }
            return $allergies;
        }
    
        // Function for deleting an allergy
        public function deleteAllergy($allergyId) {
            $stmt = $this->con->prepare("DELETE FROM allergy WHERE _id = ?");
            $stmt->bind_param("i", $allergyId);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        
        
        
        
        // Function for creating an antibiotic
        public function createAntibiotic($name, $description, $manufacturer, $ageRange, $quantity, $userId) {
            $stmt = $this->con->prepare("INSERT INTO `antibiotics` (`name`, `description`, `manufacturer`, `age_range`, `quantity`) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $description, $manufacturer, $ageRange, $quantity);
            if ($stmt->execute()) {
                $uname = $this->getUserName($userId);
                $text = "Antibiotic $name has been added by $uname";
                $related= "Antibiotic";
                $this->createLogs($userId, $text, $related);
                return true;
            } else {
                return false;
            }
        }

        // Function for updating an antibiotic
        public function updateAntibiotic($antibioticId, $name, $description, $manufacturer, $ageRange, $quantity, $userId) {
            $stmt = $this->con->prepare("UPDATE antibiotics SET name = ?, description = ?, manufacturer = ?, age_range = ?, quantity = ? WHERE _id = ?");
            $stmt->bind_param("sssssi", $name, $description, $manufacturer, $ageRange, $quantity, $antibioticId);
            
            if ($stmt->execute()) {
                $uname = $this->getUserName($userId);
                $text = "Antibiotic $name has been updated by $uname";
                $related= "Antibiotic";
                $this->createLogs($userId, $text, $related);}
             else {
                return false;
            }}
            
        // Function for retrieving all antibiotics
        public function getAllAntibiotics() {
            $stmt = $this->con->prepare("SELECT * FROM antibiotics");
            $stmt->execute();
            $result = $stmt->get_result();
            $antibiotics = array();
            while ($antibiotic = $result->fetch_assoc()) {
                $antibiotics[] = $antibiotic;
            }
            return $antibiotics;
        }

        public function getAntibioticsCount() {
            $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM antibiotics");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $totalCount = $row['total_count'];
            return $totalCount;
        } 

        // Function for deleting an antibiotic
        public function deleteAntibiotic($antibioticId, $userId) {
            $aname = $this->getAntibioticName($antibioticId);
            $stmt = $this->con->prepare("DELETE FROM antibiotics WHERE _id = ?");
            $stmt->bind_param("i", $antibioticId);
            
            if ($stmt->execute()) {
                $uname = $this->getUserName($userId);
                $text = "Antibiotic $aname has deleted added by $uname";
                $related= "Antibiotic";
                $this->createLogs($userId, $text, $related);
                return true;
            } else {
                return false;
            }
        }

        public function getAntibioticName($aId) {
            $stmt = $this->con->prepare("SELECT name FROM antibiotic WHERE _id = ?");
            $stmt->bind_param("i", $aId);
            $stmt->execute();
            $stmt->bind_result($aname);
            $stmt->fetch();
            $stmt->close();
            return $aname;
        }



        // Function for creating an illness
        public function createIllness($name, $description) {
            $stmt = $this->con->prepare("INSERT INTO `illness` (`name`, `description`) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        // Function for updating an illness
        public function updateIllness($illnessId, $name, $description) {
            $stmt = $this->con->prepare("UPDATE illness SET name = ?, description = ? WHERE _id = ?");
            $stmt->bind_param("ssi", $name, $description, $illnessId);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        // Function for retrieving an illness
        public function getIllness($illnessId) {
            $stmt =  $this->con->prepare("SELECT * FROM illness WHERE _id = ?");
            $stmt->bind_param("i", $illnessId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

        // Function for retrieving all illnesses
        public function getAllIllnesses() {
            $stmt = $this->con->prepare("SELECT * FROM illness");
            $stmt->execute();
            $result = $stmt->get_result();
            $illnesses = array();
            while ($illness = $result->fetch_assoc()) {
                $illnesses[] = $illness;
            }
            return $illnesses;
        }

        // Function for deleting an illness
        public function deleteIllness($illnessId) {
            $stmt = $this->con->prepare("DELETE FROM illness WHERE _id = ?");
            $stmt->bind_param("i", $illnessId);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }




        // Function for creating a prescription
        public function createPrescription($patientId, $administeredBy, $description, $dosage, $antibioticId, $frequency, $startDate, $endDate) {
            $stmt = $this->con->prepare("INSERT INTO `prescription` (`patient_id`, `administered_by`, `description`, `dosage`, `antibiotic_id`, `frequency`, `start_date`, `end_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissssss", $patientId, $administeredBy, $description, $dosage, $antibioticId, $frequency, $startDate, $endDate);
            
            if ($stmt->execute()) {
                $pname = $this->getPatientName($patientId);
                $uname = $this->getUserName($administeredBy);
                $text = "A new prescription has been issued to $pname by $uname";
                $related= "Prescription";
                $this->createLogs($administeredBy, $text, $related);
                return true;
            } else {
                return false;
            }
        }

        public function getPrescriptionCount() {
            $stmt = $this->con->prepare("SELECT COUNT(*) AS total_count FROM prescription");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $totalCount = $row['total_count'];
            return $totalCount;
        } 

        // Function for updating a prescription
        public function updatePrescription($prescriptionId, $patientId, $administeredBy, $description, $dosage, $antibioticId, $frequency, $startDate, $endDate) {
            $stmt = $this->con->prepare("UPDATE prescription SET patient_id = ?, administered_by = ?, description = ?, dosage = ?, antibiotic_id = ?, frequency = ?, start_date = ?, end_date = ? WHERE _id = ?");
            $stmt->bind_param("iissssssi", $patientId, $administeredBy, $description, $dosage, $antibioticId, $frequency, $startDate, $endDate, $prescriptionId);
            
            if ($stmt->execute()) {
                $uname = $this->getUserName($administeredBy);
                $text = "A prescription has been updated by $uname";
                $related= "Prescription";
                $this->createLogs($administeredBy, $text, $related);
                return true;
            } else {
                return false;
            }
        }

        // Function for retrieving a prescription
        public function getPrescription($prescriptionId) {
            $stmt =  $this->con->prepare("SELECT * FROM prescription WHERE _id = ?");
            $stmt->bind_param("i", $prescriptionId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

        // Function for retrieving all prescriptions
        public function getAllPrescriptions() {
            $stmt = $this->con->prepare("SELECT * FROM prescription");
            $stmt->execute();
            $result = $stmt->get_result();
            $prescriptions = array();
            while ($prescription = $result->fetch_assoc()) {
                $prescriptions[] = $prescription;
            }
            return $prescriptions;
        }

        public function getPatientAntibiotics($patientId) {
            $stmt = $this->con->prepare("SELECT DISTINCT antibiotic_id FROM prescription WHERE patient_id = ?");
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $antibiotics = array();
            while ($row = $result->fetch_assoc()) {
                $antibiotics[] = $row['antibiotic_id'];
            }
    
            return $antibiotics;
        }

        public function getPatientPrescriptionHistory($patientId) {
            $stmt = $this->con->prepare("SELECT * FROM prescription WHERE patient_id = ?");
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $all = array();
            while ($one = $result->fetch_assoc()) {
                $all[] = $one;
            }
            return $all;
        }

        // Function for deleting a prescription
        public function deletePrescription($prescriptionId, $userId) {
            $stmt = $this->con->prepare("DELETE FROM prescription WHERE _id = ?");
            $stmt->bind_param("i", $prescriptionId);
            
            if ($stmt->execute()) {
                $uname = $this->getUserName($userId);
                $text = "A new prescription has been deleted by $uname";
                $related= "Prescription";
                $this->createLogs($userId, $text, $related);
                return true;
            } else {
                return false;
            }
        }

    public function createLogs($user, $action, $related) {
        $stmt = $this->con->prepare("INSERT INTO `logs` (`user_id`, `action`, `created_at`, `related_to`) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("iss", $user, $action, $related);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllLogs() {
        $stmt = $this->con->prepare("SELECT * FROM logs ORDER BY _id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = array();
        while ($log = $result->fetch_assoc()) {
            $logs[] = $log;
        }
        return $logs;
    }

    public function getLastFiveLogs() {
        $stmt = $this->con->prepare("SELECT * FROM logs ORDER BY _id DESC LIMIT 5");
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = array();
        while ($log = $result->fetch_assoc()) {
            $logs[] = $log;
        }
        return $logs;
    }

    public function getLastFiveLogsExcludingUsers() {
        $stmt = $this->con->prepare("SELECT * FROM logs WHERE related_to != 'User' ORDER BY _id DESC LIMIT 5");
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = array();
        while ($log = $result->fetch_assoc()) {
            $logs[] = $log;
        }
        $stmt->close();
        return $logs;
    }
    



}
?>