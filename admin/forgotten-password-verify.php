<?php
session_start();
require_once "../config/config.php";

// Include PHPMailer
$phpmailer_path = '../includes/auth/create-account/phpmailer/src/';
if (file_exists($phpmailer_path . 'Exception.php')) {
    require $phpmailer_path . 'Exception.php';
    require $phpmailer_path . 'PHPMailer.php';
    require $phpmailer_path . 'SMTP.php';
} else {
    // Try alternate path
    $phpmailer_path = 'phpmailer/src/';
    if (file_exists($phpmailer_path . 'Exception.php')) {
        require $phpmailer_path . 'Exception.php';
        require $phpmailer_path . 'PHPMailer.php';
        require $phpmailer_path . 'SMTP.php';
    } else {
        die("PHPMailer files not found. Please check the path.");
    }
}

// Import namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the session variables are set
if(!isset($_SESSION['admin_reset_email']) || !isset($_SESSION['admin_reset_id'])) {
    header("Location: ./forgotten-password.php");
    exit();
}

$email = $_SESSION['admin_reset_email'];
$admin_id = $_SESSION['admin_reset_id'];

// Initialize variables
$error_message = "";
$success_message = "";

// Process OTP verification
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    // Combine OTP digits
    $otp = $_POST['digit1'] . $_POST['digit2'] . $_POST['digit3'] . $_POST['digit4'];
    
    // Validate input
    if(strlen($otp) !== 4 || !is_numeric($otp)) {
        $error_message = "Please enter a valid 4-digit code";
    } else {
        // Database connection
        require_once "../config/servername.php";
        
        $conn = db();
        
        // Check if OTP matches and is not expired
        $sql = "SELECT * FROM administrators WHERE admin_id = ? AND otp_code = ? AND otp_expires > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $admin_id, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            // OTP is valid, redirect to password reset page
            $_SESSION['admin_reset_verified'] = true;
            header("Location: ./change-password.php");
            exit();
        } else {
            // Check if OTP is expired
            $check_sql = "SELECT * FROM administrators WHERE admin_id = ? AND otp_code = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("is", $admin_id, $otp);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if($check_result->num_rows > 0) {
                $error_message = "OTP has expired. Please request a new one.";
            } else {
                $error_message = "Invalid OTP. Please try again.";
            }
        }
        
        
    }
}

// Process resend request
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    // Database connection
    require_once "../config/servername.php";
    
    $conn = db();
    
    // Generate new OTP
    $otp = sprintf("%04d", rand(1000, 9999));
    
    // Update OTP in database
    $update_sql = "UPDATE administrators SET otp_code = ?, otp_expires = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE admin_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $otp, $admin_id);
    
    if($update_stmt->execute()) {
        // Get admin information
        $admin_sql = "SELECT * FROM administrators WHERE admin_id = ?";
        $admin_stmt = $conn->prepare($admin_sql);
        $admin_stmt->bind_param("i", $admin_id);
        $admin_stmt->execute();
        $admin_result = $admin_stmt->get_result();
        $admin = $admin_result->fetch_assoc();
        
        // Send OTP via email
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'samuelojeyinka@gmail.com'; // Update with your email
            $mail->Password   = 'teir bvqp ijrx rijl'; // Update with your app password
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
            $mail->setFrom('samuelojeyinka@gmail.com', 'Victosah Admin');
            $mail->addAddress($admin['email'], $admin['full_name']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code (Resent)';
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                <h2 style='color: #C2185B; text-align: center;'>Glorefy</h2>
                <p style='font-size: 16px; line-height: 1.5;'>Hello {$admin['full_name']},</p>
                <p style='font-size: 16px; line-height: 1.5;'>You requested a new verification code. To verify your identity, please use the following OTP code:</p>
                <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                    {$otp}
                </div>
                <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this, please ignore this email.</p>
                <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Glorefy Team</p>
            </div>
            ";
            $mail->AltBody = "Your password reset verification code is: {$otp}";
            
            $mail->send();
            
            $success_message = "A new verification code has been sent to your email.";
        } catch (Exception $e) {
            $error_message = "Error sending verification email: " . $mail->ErrorInfo;
        }
    } else {
        $error_message = "Error generating new OTP. Please try again.";
    }
    
    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Verify</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">

        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between">
            <a href="./forgotten-password.php" class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[20px]" alt="Back"></i>
                <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
            </a>
            <div class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
            </div>
        </div>

        <p class="font-['Open Sans'] text-[18px] text-[22px] font-medium text-center">
            ADMIN PANEL
        </p>

        <?php if (!empty($error_message)): ?>
        <div class="bg-[#FDECEC] shadow-lg mt-4 py-3 px-4 rounded relative">
            <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">
                Error
            </p>
            <p class="text-[13px] md:text-[14px] text-[#7F7F7F] mt-1">
                <?php echo $error_message; ?>
            </p>
        </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
        <div class="bg-[#E0F8E9] shadow-lg mt-4 py-3 px-4 rounded relative">
            <div class="h-[100%] w-[5px] bg-[#28C76F] absolute left-0 top-0"></div>
            <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">
                Success
            </p>
            <p class="text-[13px] md:text-[14px] text-[#7F7F7F] mt-1">
                <?php echo $success_message; ?>
            </p>
        </div>
        <?php endif; ?>

        <p class="pl-[2.5%] font-['Open Sans'] text-[18px] text-[22px] font-medium text-left pt-5 text-[#C2185B]">
            Verification
        </p>
        <p class="pl-[2.5%] font-['Open Sans'] text-[16px] text-[20px] font-medium text-left">
            Let us verify it's you
        </p>
        <p class="pl-[2.5%] mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Enter the 4 digit code sent to <?php echo htmlspecialchars($email); ?> to reset your password
        </p>

        <form method="POST" action="" class="w-full mt-[1rem] flex items-center flex-col">
            <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                Enter Code
            </p>

            <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
                <input
                    type="text"
                    name="digit1"
                    inputMode="numeric"
                    maxlength="1"
                    class="otp-input w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                    required
                    autofocus
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
            <button type="submit" class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#C2185B] text-white rounded-[8px] mt-10 cursor-pointer">
                Verify
            </button>
        </form>

        <form method="POST" action="" id="resendForm">
            <input type="hidden" name="resend" value="1">
        </form>

        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left pl-[2.5%]">
            <span id="countdown-text">Resend code in <span class="text-[#C2185B]" id="countdown">60</span>sec</span>
            <a href="#" id="resendLink" class="text-[#C2185B] hidden" onclick="document.getElementById('resendForm').submit(); return false;">Resend code</a>
        </p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // OTP input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        
        otpInputs.forEach((input, index) => {
            // Only allow numbers
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Auto move to next input
                if (this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            
            // Handle backspace to go to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });
        
        // Countdown timer for resend
        let countdownTime = 60;
        const countdownElement = document.getElementById('countdown');
        const countdownTextElement = document.getElementById('countdown-text');
        const resendLink = document.getElementById('resendLink');
        
        // Initialize countdown
        countdownElement.textContent = countdownTime;
        
        // Make sure initial state is correct
        countdownTextElement.style.display = 'inline';
        resendLink.style.display = 'none';
        
        function updateCountdown() {
            if (countdownTime <= 0) {
                clearInterval(countdownInterval);
                countdownTextElement.style.display = 'none';
                resendLink.style.display = 'inline';
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