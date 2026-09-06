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
    // If the files are not found in the relative path, try looking for them in the admin directory
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

// If admin is already logged in, redirect to dashboard
if(isset($_SESSION['admin_id'])) {
    header("Location: ./dashboard.php");
    exit();
}

// Initialize variables
$error_message = "";
$success_message = "";

// Process forgot password form
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Validate input
    if(empty($email)) {
        $error_message = "Please enter your email address";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address";
    } else {
        // Database connection
        require_once "../config/servername.php";
        
        $conn = db();
        
        // Check if email exists in the administrators table
        $sql = "SELECT * FROM administrators WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Generate OTP
            $otp = sprintf("%04d", rand(1000, 9999));
            
            // Store OTP in database
            $update_sql = "UPDATE administrators SET otp_code = ?, otp_expires = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE admin_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $otp, $admin['admin_id']);
            
            if($update_stmt->execute()) {
                // Store email in session for verification
                $_SESSION['admin_reset_email'] = $email;
                $_SESSION['admin_reset_id'] = $admin['admin_id'];
                
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
                    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    // $mail->Port       = 587;
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
                    $mail->Subject = 'Password Reset Verification Code';
                    $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                        <h2 style='color: #C2185B; text-align: center;'>Glorefy</h2>
                        <p style='font-size: 16px; line-height: 1.5;'>Hello {$admin['full_name']},</p>
                        <p style='font-size: 16px; line-height: 1.5;'>You requested to reset your password. To verify your identity, please use the following OTP code:</p>
                        <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                            {$otp}
                        </div>
                        <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this, please ignore this email.</p>
                        <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Glorefy Team</p>
                    </div>
                    ";
                    $mail->AltBody = "Your password reset verification code is: {$otp}";
                    
                    $mail->send();
                    
                    // Redirect to verification page
                    header("Location: ./forgotten-password-verify.php");
                    exit();
                    
                } catch (Exception $e) {
                    $error_message = "Error sending verification email: " . $mail->ErrorInfo;
                }
            } else {
                $error_message = "Error processing your request. Please try again.";
            }
        } else {
            // Email not found - for security, don't reveal this
            $error_message = "Your email address is not recognized as an administrator.";
        }
        
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Forgot Password</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">

        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between">
            <a href="./index.php" class="flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-[20px]"></i>
                <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
            </a>
            <div class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo DOMAIN; ?>/assets/global/logo.png" alt="GLOREFY" class="w-[31.35px] md:w-[41.35px]" />
            </div>
        </div>
        
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-['Open Sans'] font-medium pt-5">ADMIN PANEL</h3>

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

        <p class="font-['Open Sans'] text-[19px] text-[24px] font-medium text-left mt-5 text-[#C2185B]">
            Forgot Password
        </p>
        <p class="font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#777777] mt-2">
            Enter your email address and we'll send you a code to reset your password
        </p>

        <form method="POST" action="" class="flex flex-col gap-4 pt-4">
            <div class="flex flex-col gap-1">
                <label
                    for="email"
                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Enter your email address"
                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"
                    required />
            </div>

            <button type="submit" class="w-full py-[8px] px-3 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center mt-4">Send Reset Code</button>
        </form>

        <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#7A7A7A] py-3">
            Remember your password? <a href="./index.php" class="text-[#C2185B] font-medium">Sign in</a>
        </p>
    </div>
</body>
</html>