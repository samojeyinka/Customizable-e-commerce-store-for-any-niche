<?php
session_start();

// Include PHPMailer at the top of the file
require './phpmailer/src/Exception.php';
require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$dbname = 'victosah';
$username = 'root';
$password = '';

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// Process OTP verification
if(isset($_POST['verify'])) {
    // Combine the 4 OTP digits
    $digit1 = $_POST['digit1'];
    $digit2 = $_POST['digit2'];
    $digit3 = $_POST['digit3'];
    $digit4 = $_POST['digit4'];
    
    $entered_otp = $digit1 . $digit2 . $digit3 . $digit4;
    
    // Get user email from session (assuming it was stored during registration)
    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        
        // Check if OTP matches and is not expired (10 minutes validity)
        $sql = "SELECT * FROM users WHERE email = ? AND otp = ? AND status = 'pending' AND otp_send_time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $entered_otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            // Get the user data
            $user = $result->fetch_assoc();
            
            // OTP is valid, update user status to active
            $update_sql = "UPDATE users SET status = 'active', verified_at = NOW() WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $email);
            
            if($update_stmt->execute()) {
                // Verification successful
                $_SESSION['user_verified'] = true;
                
                // Auto-login the user after verification
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_status'] = 'active';
                
                // Update last login time
                $login_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                $login_stmt = $conn->prepare($login_sql);
                $login_stmt->bind_param("i", $user['id']);
                $login_stmt->execute();
                
                echo "
                <script>
                alert('Account verification successful! You are now logged in.');
                document.location.href='../../../../dashboard.php';
                </script>
                ";
            } else {
                echo "
                <script>
                alert('Error updating account status');
                </script>
                ";
            }
        } else {
            // Check if OTP is expired
            $check_expired = "SELECT * FROM users WHERE email = ? AND otp = ? AND status = 'pending'";
            $expired_stmt = $conn->prepare($check_expired);
            $expired_stmt->bind_param("ss", $email, $entered_otp);
            $expired_stmt->execute();
            $expired_result = $expired_stmt->get_result();
            
            if($expired_result->num_rows > 0) {
                echo "
                <script>
                alert('OTP has expired. Please request a new one.');
                </script>
                ";
            } else {
                echo "
                <script>
                alert('Invalid OTP. Please try again.');
                </script>
                ";
            }
        }
    } else {
        echo "
        <script>
        alert('Session expired. Please register again.');
        document.location.href='index.php';
        </script>
        ";
    }
}

// Resend OTP code
if(isset($_POST['resend'])) {
    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        
        // Generate new OTP
        $new_otp = rand(1000, 9999);
        
        // Update OTP in database
        $update_sql = "UPDATE users SET otp = ?, otp_send_time = NOW() WHERE email = ? AND status = 'pending'";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_otp, $email);
        
        if($update_stmt->execute()) {
            // PHPMailer already included at the top of the file
            
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
                $mail->Subject = 'Your New OTP Verification Code';
                $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                    <h2 style='color: #1A237E; text-align: center;'>Victosah Solution</h2>
                    <p style='font-size: 16px; line-height: 1.5;'>Hello,</p>
                    <p style='font-size: 16px; line-height: 1.5;'>You requested a new verification code. Please use the following OTP code:</p>
                    <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                        {$new_otp}
                    </div>
                    <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this code, please ignore this email.</p>
                    <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Victosah Team</p>
                </div>
                ";
                $mail->AltBody = "Your new OTP Verification code is: {$new_otp}";
                
                $mail->send();
                
                echo "
                <script>
                alert('New verification code has been sent to your email.');
                </script>
                ";
            } catch(Exception $e) {
                echo "
                <script>
                alert('Error sending email: {$mail->ErrorInfo}');
                </script>
                ";
            }
        } else {
            echo "
            <script>
            alert('Error updating OTP');
            </script>
            ";
        }
    } else {
        echo "
        <script>
        alert('Session expired. Please register again.');
        document.location.href='index.php';
        </script>
        ";
    }
}

// Check if email is stored in session, if not - redirect to registration
if(!isset($_SESSION['email'])) {
    echo "
    <script>
    alert('Please complete registration first.');
    document.location.href='index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH - Verify Account</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../style.css" />
    <link rel="stylesheet" href="../../../styles/faq.css" />
    <link rel="stylesheet" href="../../../styles/modal.css">
    <link rel="stylesheet" href="../../../styles/tabs.css">
</head>
<body>
    <div>
        <div class="modal-content overflow-hidden p-4">
            <img src="../../../assets/global/back.svg" alt="back" id="backtoreg" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />
            
            <p class="font-['Open Sans'] text-[19px] text-[24px] font-medium text-center">
                Let us verify it's you
            </p>
            
            <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                Enter the 4 digit code sent to your email to create your account
            </p>
            
            <form method="POST" action="" class="w-full mt-[1rem] flex items-center flex-col">
                <div class="w-[fit-content] flex items-center gap-3 mx-auto">
                    <input
                        type="text"
                        name="digit1"
                        inputMode="numeric"
                        maxlength="1"
                        class="otp-input w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                        required
                    />
                    <input
                        type="text"
                        name="digit2"
                        inputMode="numeric"
                        maxlength="1"
                        class="otp-input w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                        required
                    />
                    <input
                        type="text"
                        name="digit3"
                        inputMode="numeric"
                        maxlength="1"
                        class="otp-input w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                        required
                    />
                    <input
                        type="text"
                        name="digit4"
                        inputMode="numeric"
                        maxlength="1"
                        class="otp-input w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                        required
                    />
                </div>
                
                <input type="hidden" name="verify" value="1">
                
                <button
                    type="submit"
                    class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                    Verify me
                </button>
            </form>
            
            <form method="POST" action="" id="resendForm">
                <input type="hidden" name="resend" value="1">
            </form>
            
            <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-center">
                <span id="countdown-text">Resend code in <span class="text-[#1A237E]" id="countdown">60</span>sec</span>
                <a href="#" id="resendLink" class="text-[#1A237E] hidden" onclick="document.getElementById('resendForm').submit(); return false;">Resend code</a>
            </p>
        </div>
    </div>

    <script>
        // OTP input handling - auto-focus next input
        const otpInputs = document.querySelectorAll('.otp-input');
        
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Allow only numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Move to next input after entering a digit
                if (this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            
            // Handle backspace - move to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && index > 0 && this.value.length === 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });
        
        // Back button functionality
        document.getElementById('backtoreg').addEventListener('click', function() {
            window.location.href = 'index.php';
        });
        
        // Countdown timer for resend
        let countdownTime = 60;
        const countdownElement = document.getElementById('countdown');
        const countdownTextElement = document.getElementById('countdown-text');
        const resendLink = document.getElementById('resendLink');
        
        function updateCountdown() {
            countdownElement.textContent = countdownTime;
            
            if (countdownTime <= 0) {
                clearInterval(countdownInterval);
                countdownTextElement.classList.add('hidden');
                resendLink.classList.remove('hidden');
            } else {
                countdownTime--;
            }
        }
        
        const countdownInterval = setInterval(updateCountdown, 1000);
    </script>
</body>
</html>