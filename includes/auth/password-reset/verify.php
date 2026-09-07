<?php
// Include PHPMailer at the top of the file
require '../create-account/phpmailer/src/Exception.php';
require '../create-account/phpmailer/src/PHPMailer.php';
require '../create-account/phpmailer/src/SMTP.php';

// PHPMailer namespace declarations must be at the top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../../../config/config.php";

// Start session
session_start();

// Check if reset email and new password are stored in session, if not - redirect
// Use user-specific session variables
if(!isset($_SESSION['user_reset_email']) || !isset($_SESSION['user_new_password'])) {
    header("Location: " . DOMAIN . "/includes/auth/password-reset/mail.php");
    exit();
}

$email = $_SESSION['user_reset_email'];


//db connection
require_once "../../../config/servername.php";

$conn = db();

// Initialize message variables
$error_message = "";
$success_message = "";

// Process OTP verification
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    // Combine the 4 OTP digits
    $digit1 = $_POST['digit1'];
    $digit2 = $_POST['digit2'];
    $digit3 = $_POST['digit3'];
    $digit4 = $_POST['digit4'];
    
    $entered_otp = $digit1 . $digit2 . $digit3 . $digit4;
    
    // Check if OTP matches and is not expired (10 minutes validity)
    $sql = "SELECT * FROM users WHERE email = ? AND otp = ? AND otp_send_time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $entered_otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        // Get the user data
        $user = $result->fetch_assoc();
        
        // Hash the new password
        $hashed_password = password_hash($_SESSION['user_new_password'], PASSWORD_DEFAULT);
        
        // Update the password in the database
        $update_sql = "UPDATE users SET password = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $email);
        
        if($update_stmt->execute()) {
            // IMPORTANT CHANGE: Set the original session variable for success page
            $_SESSION['reset_email'] = $email;
            
            // Clear the user-specific session variables
            unset($_SESSION['user_new_password']);
            
            // Don't unset user_reset_email yet as success page might need it
            
            // Redirect to success page
            header("Location: " . DOMAIN . "/includes/auth/password-reset/success.php");
            exit();
        } else {
            $error_message = "Error updating password. Please try again.";
        }
    } else {
        // Check if OTP is expired
        $check_expired = "SELECT * FROM users WHERE email = ? AND otp = ?";
        $expired_stmt = $conn->prepare($check_expired);
        $expired_stmt->bind_param("ss", $email, $entered_otp);
        $expired_stmt->execute();
        $expired_result = $expired_stmt->get_result();
        
        if($expired_result->num_rows > 0) {
            $error_message = "OTP has expired. Please request a new one.";
        } else {
            $error_message = "Invalid OTP. Please try again.";
        }
    }
}

// Process resend OTP code
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    // Generate new OTP
    $new_otp = rand(1000, 9999);
    
    // Update OTP in database
    $update_sql = "UPDATE users SET otp = ?, otp_send_time = NOW() WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $new_otp, $email);
    
    if($update_stmt->execute()) {
        // Send email with PHPMailer
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
            $mail->Subject = 'Your New OTP for Password Reset';
            $mail->Body    = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                <h2 style='color: #C2185B; text-align: center;'>Glorefy</h2>
                <p style='font-size: 16px; line-height: 1.5;'>Hello,</p>
                <p style='font-size: 16px; line-height: 1.5;'>You requested a new verification code for your password reset. Please use the following OTP code:</p>
                <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                    {$new_otp}
                </div>
                <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this code, please ignore this email.</p>
                <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Glorefy Team</p>
                </div>
            ";
            $mail->AltBody = "Your new OTP for password reset is: {$new_otp}";
            
            $mail->send();
            
            $success_message = "New verification code has been sent to your email.";
            
        } catch(Exception $e) {
            $error_message = "Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        $error_message = "Error updating OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY - VERIFY RESET</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../../../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    
<?php
    include(__DIR__ . '/../../header.php');
    include(__DIR__ . '/../../options.php');
?>

<div class="w-full">
    <div class="w-[95%] md:w-[50%] mx-auto p-4 bg-white border border-[1px] border-[#EFEFEF] my-5 rounded-md relative">
        <a href="<?php echo DOMAIN; ?>/includes/auth/password-reset/password.php">
            <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />
        </a>

        <p class="font-['Open Sans'] text-[19px] text-[24px] font-medium text-center">
            Let us verify it's you
        </p>
        
        <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Enter the 4 digit code sent to <?php echo htmlspecialchars($email); ?> to reset your password
        </p>
        
        <?php if(!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if(!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>

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
                class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[8px] mt-10 cursor-pointer">
                Verify & Reset Password
            </button>
        </form>
        
        <form method="POST" action="" id="resendForm">
            <input type="hidden" name="resend" value="1">
        </form>
        
        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-center">
            <span id="countdown-text">Resend code in <span class="text-[<?php echo store_color('color_primary'); ?>]" id="countdown">60</span>sec</span>
            <a href="#" id="resendLink" class="text-[<?php echo store_color('color_primary'); ?>] hidden" onclick="document.getElementById('resendForm').submit(); return false;">Resend code</a>
        </p>
    </div>
</div>

<?php
    include(__DIR__ . '/../../footer.php');
?>

<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

<script>
// OTP input handling - auto-focus next input
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Countdown timer for resend
    let countdownTime = 60;
    const countdownElement = document.getElementById('countdown');
    const countdownTextElement = document.getElementById('countdown-text');
    const resendLink = document.getElementById('resendLink');
    
    // Initialize countdown display
    countdownElement.textContent = countdownTime;
    
    // Make sure the initial state is correct
    countdownTextElement.classList.remove('hidden');
    resendLink.classList.add('hidden');
    
    function updateCountdown() {
        if (countdownTime <= 0) {
            clearInterval(countdownInterval);
            countdownTextElement.classList.add('hidden');
            resendLink.classList.remove('hidden');
        } else {
            countdownTime--;
            countdownElement.textContent = countdownTime;
        }
    }
    
    const countdownInterval = setInterval(updateCountdown, 1000);
});
</script>
</body>
</html>