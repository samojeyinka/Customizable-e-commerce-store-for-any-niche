<?php
session_start();
require_once "../config/config.php";

// Include PHPMailer at the top of the file - make sure the paths are correct
// Try the absolute path to PHPMailer files if relative path doesn't work
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

// Import namespaces at the top level
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If admin is already logged in, redirect to dashboard
if(isset($_SESSION['admin_id'])) {
    header("Location: " . DOMAIN . "./dashboard.php");
    exit();
}

// Initialize error message
$error_message = "";

// Process login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Debug info - uncomment for troubleshooting
    // echo "Attempting to log in with email: $email and password: $password<br>";
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password";
    } else {
        $servername = "localhost";
        $dbname = 'victosah';
        $username = 'root';
        $dbpassword = '';
        
        $conn = new mysqli($servername, $username, $dbpassword, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Check if admin exists - NOTE: Updated table name from 'admin' to 'administrators'
        $sql = "SELECT * FROM administrators WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Debug info - uncomment for troubleshooting
            // echo "Found admin account. Stored hash: " . $admin['password_hash'] . "<br>";
            
            // Check status first - NOTE: Updated 'status' to 'account_status'
            if ($admin['account_status'] !== 'active') {
                $error_message = "This account is inactive. Please contact the administrator.";
            } else {
                // Verify password - NOTE: Updated 'password' to 'password_hash'
                // Debug info - uncomment for troubleshooting
                // echo "Verifying password...<br>";
                // echo "Result of verification: " . (password_verify($password, $admin['password_hash']) ? "TRUE" : "FALSE") . "<br>";
                
                if (password_verify($password, $admin['password_hash'])) {
                    // Generate OTP
                    $otp = sprintf("%04d", rand(1000, 9999));
                    
                    // Update OTP in database - NOTE: Updated column names and table name
                    $update_sql = "UPDATE administrators SET otp_code = ?, otp_expires = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE admin_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("si", $otp, $admin['admin_id']);
                    
                    if ($update_stmt->execute()) {
                        // Store admin email and id in session for verification
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['temp_admin_id'] = $admin['admin_id']; // Updated 'id' to 'admin_id'
                        $_SESSION['admin_fullname'] = $admin['full_name'];
                        
                        $mail = new PHPMailer(true);
                        
                        try {
                            // Server settings
                            $mail->SMTPDebug = 0; // Set to 2 for debugging
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'samuelojeyinka@gmail.com'; // Update with your email
                            $mail->Password   = 'teir bvqp ijrx rijl'; // Update with your app password
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
                            $mail->setFrom('samuelojeyinka@gmail.com', 'Victosah Admin');
                            $mail->addAddress($admin['email'], $admin['full_name']);
                            
                            // Content
                            $mail->isHTML(true);
                            $mail->Subject = 'Admin Login Verification Code';
                            $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                                <h2 style='color: #1A237E; text-align: center;'>Victosah Solution</h2>
                                <p style='font-size: 16px; line-height: 1.5;'>Hello {$admin['full_name']},</p>
                                <p style='font-size: 16px; line-height: 1.5;'>You are attempting to log in to your admin account. To verify your identity, please use the following OTP code:</p>
                                <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                                    {$otp}
                                </div>
                                <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not attempt to log in, please contact the system administrator immediately.</p>
                                <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Victosah Team</p>
                            </div>
                            ";
                            $mail->AltBody = "Your admin login verification code is: {$otp}";
                            
                            $mail->send();
                            
                            // Redirect to verification page
                            header("Location: ./verify-signin.php");
                            exit();
                        } catch (Exception $e) {
                            $error_message = "Error sending verification email: " . $mail->ErrorInfo;
                        }
                    } else {
                        $error_message = "Error updating OTP: " . $conn->error;
                    }
                } else {
                    $error_message = "Invalid email or password";
                }
            }
        } else {
            $error_message = "Invalid email or password";
        }
        
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH ADMIN | Sign In</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">

    <style>
         body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url("./assets/global/bg.svg");
            background-position: center;
            background-size: cover;
        }
        
        
        .login-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #d4edda;
            color: #155724;
            padding: 10px 20px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            text-align: center;
        }
    </style>

</head>

<body>
    <?php if (isset($_SESSION['login_message'])): ?>
    <div class="login-message" id="loginMessage"><?php echo $_SESSION['login_message']; unset($_SESSION['login_message']); ?></div>
    <?php endif; ?>
    
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">

        <div class="w-[95%] mx-auto flex items-center justify-between">
            <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">WELCOME BACK</h3>

            <div class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo DOMAIN; ?>/assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
            </div>
        </div>
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-['Open Sans'] font-medium pt-5">ADMIN PANEL</h3>

        <?php if (!empty($error_message)): ?>
        <section id="dangeralert" class="flex flex-col items-center w-full bg-[#FDECEC] shadow-lg mt-2 py-3 px-4 rounded relative overflow-hidden">
            <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <div class="flex items-center gap-2 mr-auto">
                <img src="<?php echo DOMAIN; ?>/assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[24px] cursor-pointer" />
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                    Authentication Error
                </p>
            </div>
            <p class="text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[3rem] pr-3 ">
                <?php echo $error_message; ?>
            </p>
        </section>
        <?php endif; ?>

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

            <div class="flex flex-col gap-1">
                <label
                    for="password"
                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                    Password
                </label>

                <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]"
                        required />
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="toggleEye" class="w-[24px] cursor-pointer toggle-password" data-target="password" />
                </div>
            </div>

            <a href="./forgotten-password.php" class='text-[14px] font-["Open Sans"] text-[#777777] font-regular cursor-pointer'>Forgot Password?</a>

            <input type="hidden" name="signin" value="1">
            <button type="submit" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Sign In</button>
        </form>


        <a href="./set-up.php" class='text-[14px] font-["Open Sans"] text-blue-700 font-regular cursor-pointer mt-5'>Finish Account Setup</a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        const passwordInput = document.getElementById('password');
        const toggleEye = document.getElementById('toggleEye');
        
        if (toggleEye) {
            toggleEye.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleEye.src = '<?php echo DOMAIN; ?>/assets/global/eye.svg';
                } else {
                    passwordInput.type = 'password';
                    toggleEye.src = '<?php echo DOMAIN; ?>/assets/global/eye-slash.svg';
                }
            });
        }
        
        // Close danger alert
        const closeAlert = document.getElementById('closedangeralert');
        const dangerAlert = document.getElementById('dangeralert');
        
        if (closeAlert && dangerAlert) {
            closeAlert.addEventListener('click', function() {
                dangerAlert.style.display = 'none';
            });
        }
        
        // Auto-hide login message after 5 seconds
        const loginMessage = document.getElementById('loginMessage');
        if (loginMessage) {
            setTimeout(function() {
                loginMessage.style.opacity = '0';
                loginMessage.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    loginMessage.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });
    </script>
</body>
</html>