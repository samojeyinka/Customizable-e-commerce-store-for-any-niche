<?php
session_start(); // Start session at the beginning

// PHPMailer namespace declarations
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//db connection
// require_once "../../../config/servername.php";

$servername = "localhost";
$dbname = "victosah";
$username = "root";
$dbpassword = "";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['send'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Always generate a fresh OTP for better security
    $otp = rand(1000, 9999);
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // IMPORTANT: First check if the email already exists
    $check_sql = "SELECT * FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email already exists - get the existing user data
        $user = $result->fetch_assoc();
        
        // Check if the account is already verified
        if ($user['status'] == 'active') {
            // User is already registered and verified
            echo "
            <script>
            alert('This email is already registered. Please sign in instead.');
            document.location.href='../login/signin.php';
            </script>
            ";
            exit();
        } else {
            // User exists but hasn't verified yet - use fresh OTP and update
            // No need to use $_POST['otp'] here - we've generated a new one
            
            // Update the existing record with new OTP and timestamp
            $update_sql = "UPDATE users SET otp = ?, otp_send_time = NOW() WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $otp, $email);
            
            if ($update_stmt->execute()) {
                // Store email in session for verification page
                $_SESSION['email'] = $email;
                
                // Continue with sending email with new OTP
                $mail = new PHPMailer(true);
                
                try {
                    // Server settings
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'samuelojeyinka@gmail.com';
                    $mail->Password   = 'teir bvqp ijrx rijl';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL/TLS
                    $mail->Port = 465;
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
                        <p style='font-size: 16px; line-height: 1.5;'>You have requested to create an account with Victosah Solution. To verify your account, please use the following OTP code:</p>
                        <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                            $otp
                        </div>
                        <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this code, please ignore this email.</p>
                        <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Victosah Team</p>
                    </div>
                    ";
                    $mail->AltBody = "Your OTP Verification code is: $otp";

                    $mail->send();
                    echo "
                    <script>
                    alert('A new verification code has been sent to your email.');
                    document.location.href='verify.php';
                    </script>
                    ";
                } catch(Exception $e) {
                    echo "
                    <script>
                    alert('Error sending email: {$mail->ErrorInfo}');
                    document.location.href='index.php';
                    </script>
                    ";
                }
            } else {
                echo "
                <script>
                alert('Error updating data: {$conn->error}');
                document.location.href='index.php';
                </script>
                ";
            }
            
            exit(); // Exit after handling existing email
        }
    }
    
    // If email doesn't exist, proceed with new registration
    
    // Store email in session for verification page
    $_SESSION['email'] = $email;
    
    // Hash the password for security before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Modified query to use prepared statement for security
    $sql = "INSERT INTO users (email, password, otp, status, otp_send_time, ip) VALUES (?, ?, ?, 'pending', NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $hashed_password, $otp, $ip_address);
    
    if ($stmt->execute()) {
        // Get the newly inserted ID
        $new_user_id = $conn->insert_id;
        
        // You can store it in session if needed
        $_SESSION['temp_user_id'] = $new_user_id;
        
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'samuelojeyinka@gmail.com';
            $mail->Password   = 'teir bvqp ijrx rijl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL/TLS
            $mail->Port = 465;
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
                    $otp
                </div>
                <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this code, please ignore this email.</p>
                <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Victosah Team</p>
            </div>
            ";
            $mail->AltBody = "Your OTP Verification code is: $otp";

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