<?php
session_start(); // Start session at the beginning

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "victosah";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['send'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $otp = $_POST['otp'];
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Store email in session for verification page
    $_SESSION['email'] = $email;
    
    // Hash the password for security before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Modified query to use prepared statement for security
    $sql = "INSERT INTO users (email, password, otp, status, otp_send_time, ip) VALUES (?, ?, ?, 'pending', NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $hashed_password, $otp, $ip_address);
    
    if ($stmt->execute()) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'samuelojeyinka@gmail.com';
            $mail->Password   = 'teir bvqp ijrx rijl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->Timeout    = 60;
            $mail->SMTPKeepAlive = true;
            
            // SSL options to bypass verification issues
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom('samuelojeyinka@gmail.com', 'Victosah');
            $mail->addAddress($email);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = isset($_POST['subject']) ? $_POST['subject'] : 'Your OTP Verification Code';
            $mail->Body    = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                <h2 style='color: #1A237E; text-align: center;'>Victosah Solution</h2>
                <p style='font-size: 16px; line-height: 1.5;'>Hello,</p>
                <p style='font-size: 16px; line-height: 1.5;'>Thank you for signing up with Victosah Solution. To verify your account, please use the following OTP code:</p>
                <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                    {$otp}
                </div>
                <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this code, please ignore this email.</p>
                <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Victosah Team</p>
            </div>
            ";
            $mail->AltBody = "Your OTP Verification code is: {$otp}";

            $mail->send();
            echo "
            <script>
            alert('Verification code has been sent to your email.');
            document.location.href='verify.php';
            </script>
            ";
        }
        catch(Exception $e) {
            echo "
            <script>
            alert('Error sending email: {$mail->ErrorInfo}');
            document.location.href='index.php';
            </script>
            ";
        }
    } else{
        echo "
        <script>
        alert('Error inserting data: {$conn->error}');
        document.location.href='index.php';
        </script>
        ";
    }
}
?>