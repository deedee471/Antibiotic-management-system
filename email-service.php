<?php

require_once "DbConnect.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

class EmailUtilsOperation
{
    private $con;

    // constructor which aids in db connection
    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    public function forgotPassword($email)
    {
        $stmt = $this->con->prepare("SELECT _id, name FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $stmt->bind_result($userId, $name);
            $stmt->fetch();
            $stmt->close();
        }

        if (!$userId) {
            throw new Exception("User with this email $email not found");
        }

        // generate a random token
        $token = bin2hex(random_bytes(16));

        date_default_timezone_set('Africa/Nairobi');
        // insert the token into the reset_password_tokens table
        $expiryDate = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $insertStmt = $this->con->prepare("INSERT INTO reset_password_tokens (user_id, token, expirationDate) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iss", $userId, $token, $expiryDate);
        if (!$insertStmt->execute()) {
            throw new Exception("Error executing the INSERT statement: " . $insertStmt->error);
        } else {
            $insertStmt->close();
        }


        // send the email
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug  = 0;
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth=true;
            $mail->Username = 'projectvictory01@gmail.com';
            $mail->Password = 'pbynpicdvdvpcqmg';
            $mail->SMTPSecure = 'ssl'; // Enable TLS encryption
            $mail->Port = 465; // TCP port to connect to

            $mail->setFrom('projectvictory01@gmail.com');
            $mail->addAddress($email);

            $mail->isHTML(true); // Set email format to HTML

            // Email subject and body content
            $mail->Subject = 'Password Reset Notification';
            $email_template= "
                <h2>Hello $name</h2>
                <h3>You are receiving this email because we received a password reset request for your account.</h3>
                <p>Please click the link below to reset your password:</p>
                <a href='http://localhost/PP/mainProject/Med-Dashboard/create_new_password.php?token=$token'>Click Me</a>
                <br><br>
                <p>Note that the link will expire in 15 minutes.</p>
            ";
            $mail->Body = $email_template;

            if (!$mail->send()) {
                return false;
            } else {
                return true;
            }

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function resetPassword() {
        $response = array();

            $new_password = mysqli_real_escape_string($this->con, $_POST['new-password']);
            $confirm_password = mysqli_real_escape_string($this->con, $_POST['confirm-new-password']);
            $token = mysqli_real_escape_string($this->con, $_POST['password_token']);
            $response = array();
            date_default_timezone_set('Africa/Nairobi');
            $time = date('Y-m-d H:i:s');
            if (!empty($token)) {
                if (!empty($new_password) && !empty($confirm_password)) {
                    $stmt = $this->con->prepare("SELECT expirationDate, user_id FROM reset_password_tokens WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    if (!$stmt->execute()) {
                        throw new Exception("Error executing the SELECT statement: " . $stmt->error);
                    } else {
                        $stmt->bind_result($expiry, $userID);
                        $stmt->fetch();
                        $stmt->close();
                    }
                    if ($time < $expiry) {
                        if ($new_password == $confirm_password) {
                            $password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $this->con->prepare("UPDATE user SET password=?, updated_at= NOW() WHERE _id = ?");
                            $stmt->bind_param("ss", $password, $userID);
                            $stmt->execute();
                            $response['error'] = false;
                            $response['message'] = 'New Password Updated Successfully. You can now try to Login';
                        } else {
                            $response['error'] = true;
                            $response['message'] = 'Password and Confirm Password do not match';
                        }
                    } else {
                        $response['error'] = true;
                        $response['message'] = 'Invalid Token. Please Send the Request Again';
                    }
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Please Fill in all required fields';
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'No Token Available';
            }
            echo json_encode($response);
    }

}

$emailUtils = new EmailUtilsOperation();
$emailUtils->resetPassword();

?>