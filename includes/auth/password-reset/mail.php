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

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: " . DOMAIN . "/user/orders.php");
    exit();
}

// Initialize message variables
$error_message = "";
$success_message = "";

// Process the form submission
if(isset($_POST['send'])) {
    $email = $_POST['email'];
    
// Database connection
//db connection
require_once "../../../config/servername.php";




    $conn = db();
    
    // Check if the email exists in the database
    $check_sql = "SELECT * FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if($result->num_rows == 0) {
        // Email doesn't exist
        $error_message = "This email is not registered. Please check your email or create a new account.";
    } else {
        // Email exists, generate OTP and store in the database
        $otp = rand(1000, 9999);
        
        // Update the user record with the new OTP
        $update_sql = "UPDATE users SET otp = ?, otp_send_time = NOW() WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $otp, $email);
        
        if($update_stmt->execute()) {
            // Store email in session for verification page
            // Use a different session variable name to avoid conflicts with admin reset
            $_SESSION['user_reset_email'] = $email;
            
            // Send the OTP via email
            $mail = new PHPMailer(true);
            
            try {
                // Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'samuelojeyinka@gmail.com';
                $mail->Password   = 'teir bvqp ijrx rijl';
                // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                // $mail->Port       = 587;
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
                $mail->Subject = 'Password Reset Verification Code';
                $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                    <h2 style='color: #C2185B; text-align: center;'>Glorefy</h2>
                    <p style='font-size: 16px; line-height: 1.5;'>Hello,</p>
                    <p style='font-size: 16px; line-height: 1.5;'>You requested to reset your password. To verify your identity, please use the following OTP code:</p>
                    <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                        {$otp}
                    </div>
                    <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this code, please ignore this email.</p>
                    <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Glorefy Team</p>
                </div>
                ";
                $mail->AltBody = "Your Password Reset Verification code is: {$otp}";

                $mail->send();
                
                // Redirect to password page
                header("Location: " . DOMAIN . "/includes/auth/password-reset/password.php");
                exit();
                
            } catch(Exception $e) {
                $error_message = "Error sending email: " . $mail->ErrorInfo;
            }
        } else {
            $error_message = "Error updating database. Please try again.";
        }
    }
    
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY - Password Reset</title>
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
    <a href="<?php echo DOMAIN; ?>/includes/auth/login/signin.php">
            <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />
        </a>
        <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Reset Your Password</h3>
        
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

        <form action="<?php echo DOMAIN; ?>/includes/auth/password-reset/mail.php" method="POST" class="flex flex-col gap-4 pt-4">
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
                    placeholder="Enter your registered email address"
                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"
                    required
                />
            </div>
            
            <input type="hidden" name="send" value="1">
            
            <button type="submit" class="w-full py-[8px] px-3 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Send Reset Code</button>
        </form>

        <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#7A7A7A] py-3">
            Remember your password? <a href="<?php echo DOMAIN; ?>/includes/auth/login/signin.php" class="text-[#C2185B] font-medium">Sign in</a>
        </p>
    </div>
</div>

<?php
    include(__DIR__ . '/../../footer.php');
?>

<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

</body>
</html>