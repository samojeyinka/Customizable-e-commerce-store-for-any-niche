<?php
session_start();
require_once "../config/config.php";

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize message variables
$error_message = "";
$success_message = "";

// Check if the required session variables are set
if (!isset($_SESSION['temp_admin_id']) || !isset($_SESSION['admin_email'])) {
    // Redirect to login page if verification is accessed directly
    header("Location: ./index.php");
    exit();
}

// If admin_fullname is not set, we can try to use a default or set it to an empty string
if (!isset($_SESSION['admin_fullname'])) {
    $_SESSION['admin_fullname'] = "Admin User"; // Default value
}

// Debug function for logging
function debug_log($message) {
    error_log("[ADMIN OTP DEBUG] " . $message);
}

// Include PHPMailer setup
$phpmailer_path = '../includes/auth/create-account/phpmailer/src/';
if (file_exists($phpmailer_path . 'Exception.php')) {
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';
} else {
    $phpmailer_path = 'phpmailer/src/';
    if (file_exists($phpmailer_path . 'Exception.php')) {
        require_once $phpmailer_path . 'Exception.php';
        require_once $phpmailer_path . 'PHPMailer.php';
        require_once $phpmailer_path . 'SMTP.php';
    } else {
        // If we can't find PHPMailer, log it but continue
        debug_log("PHPMailer files not found in either location");
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Process OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    // Get the OTP and ensure it's a string
    $otp = trim($_POST['otp']);
    $admin_id = $_SESSION['temp_admin_id'];
    
    // Print out OTP for debugging
    echo "<div style='background: #f8f9fa; padding: 5px; margin-bottom: 10px; border: 1px solid #ddd;'>
          <strong>Debug:</strong> Verifying OTP: '<span style='color:red'>$otp</span>' for admin ID: $admin_id
          </div>";
    
    error_log("Verifying OTP: '$otp' for admin ID: $admin_id");
    
    try {
        // Connect to database
        require_once "../config/servername.php";
        
        $conn = db();
        
        // Debug query to see what's in the database
        $debug_sql = "SELECT admin_id, otp_code, otp_expires FROM administrators WHERE admin_id = ?";
        $debug_stmt = $conn->prepare($debug_sql);
        $debug_stmt->bind_param("i", $admin_id);
        $debug_stmt->execute();
        $debug_result = $debug_stmt->get_result();
        
        if ($debug_result->num_rows > 0) {
            $debug_row = $debug_result->fetch_assoc();
            echo "<div style='background: #f8f9fa; padding: 5px; margin-bottom: 10px; border: 1px solid #ddd;'>
                  <strong>Debug DB Values:</strong> admin_id: " . $debug_row['admin_id'] . 
                  ", DB otp_code: '<span style='color:red'>" . $debug_row['otp_code'] . "</span>', 
                  Expires: " . $debug_row['otp_expires'] . "
                  </div>";
            
            error_log("DB Values - admin_id: " . $debug_row['admin_id'] . 
                      ", DB otp_code: " . $debug_row['otp_code'] . 
                      ", Expires: " . $debug_row['otp_expires']);
        }
        
        // Check if OTP matches
        $sql = "SELECT * FROM administrators WHERE admin_id = ? AND otp_code = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Important: Make sure both values are passed as strings for consistent comparison
        $stmt->bind_param("is", $admin_id, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<div style='background: #f8f9fa; padding: 5px; margin-bottom: 10px; border: 1px solid #ddd;'>
              <strong>Debug:</strong> OTP check result rows: " . $result->num_rows . "
              </div>";
        
        error_log("OTP check result rows: " . $result->num_rows);
        
        if ($result->num_rows > 0) {
            // OTP verification successful
            $row = $result->fetch_assoc();
            
            // Update admin status
            $update_sql = "UPDATE administrators SET otp_code = NULL, otp_expires = NULL, account_status = 'active', last_login = NOW() WHERE admin_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $admin_id);
            
            if ($update_stmt->execute()) {
                // Set admin session
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['admin_role'] = $row['role'];
                
                // Clear temporary session variables
                unset($_SESSION['temp_admin_id']);
                
                // Redirect to dashboard
                header("Location: ./dashboard/overview.php");
                exit();
            } else {
                $error_message = "Failed to update admin status. Please try again.";
            }
        } else {
            $error_message = "Invalid OTP. Please try again.";
        }
        
        
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
        error_log("Exception: " . $e->getMessage());
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    debug_log("Resend OTP process started");
    
    try {
        // Connect to database
        require_once "../config/servername.php";
        
        $conn = db();
        
        // Generate new OTP - 4 digits
        $otp = sprintf("%04d", rand(1000, 9999));
        $admin_id = $_SESSION['temp_admin_id'];
        
        debug_log("Generated new OTP: $otp for admin ID: $admin_id");
        
        // Update OTP in database
        $update_sql = "UPDATE administrators SET otp_code = ?, otp_expires = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE admin_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $update_stmt->bind_param("si", $otp, $admin_id);
        
        if ($update_stmt->execute()) {
            debug_log("Database updated successfully");
            
            // Make sure session email is set
            if (!isset($_SESSION['admin_email']) || empty($_SESSION['admin_email'])) {
                debug_log("Admin email not in session, fetching from database");
                
                // Fetch email from database if not in session
                $get_email_sql = "SELECT email, full_name FROM administrators WHERE admin_id = ?";
                $email_stmt = $conn->prepare($get_email_sql);
                
                if (!$email_stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $email_stmt->bind_param("i", $admin_id);
                $email_stmt->execute();
                $email_result = $email_stmt->get_result();
                
                if ($email_result->num_rows > 0) {
                    $admin_data = $email_result->fetch_assoc();
                    $_SESSION['admin_email'] = $admin_data['email'];
                    $_SESSION['admin_fullname'] = $admin_data['full_name'];
                    debug_log("Retrieved email: " . $_SESSION['admin_email']);
                } else {
                    throw new Exception("Admin record not found");
                }
            }
            
            // Send email with new OTP
            try {
                debug_log("Creating PHPMailer instance");
                $mail = new PHPMailer(true);
                
                // Server settings
                $mail->SMTPDebug = 0;  // Set to 2 for verbose debug output
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'samuelojeyinka@gmail.com'; // Update with your email
                $mail->Password   = 'teir bvqp ijrx rijl'; // Update with your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL/TLS
                $mail->Port       = 465;
                $mail->Timeout    = 60;
                $mail->SMTPKeepAlive = true;
                
                debug_log("Setting SMTP options");
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                
                // Recipients
                debug_log("Setting email recipient: " . $_SESSION['admin_email']);
                $mail->setFrom('samuelojeyinka@gmail.com', 'Victosah Admin');
                $mail->addAddress($_SESSION['admin_email'], $_SESSION['admin_fullname']);
                
                // Content
                debug_log("Setting email content");
                $mail->isHTML(true);
                $mail->Subject = 'Admin Login Verification Code';
                
                // Use direct variable in string with concatenation
                $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
                    <h2 style='color: #C2185B; text-align: center;'>Glorefy</h2>
                    <p style='font-size: 16px; line-height: 1.5;'>Hello " . $_SESSION['admin_fullname'] . ",</p>
                    <p style='font-size: 16px; line-height: 1.5;'>You requested a new OTP code. To verify your identity, please use the following code:</p>
                    <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                        " . $otp . "
                    </div>
                    <p style='font-size: 16px; line-height: 1.5;'>This code is valid for 10 minutes. If you did not request this, please contact the system administrator immediately.</p>
                    <p style='font-size: 16px; line-height: 1.5;'>Best regards,<br>Glorefy Team</p>
                </div>
                ";
                $mail->AltBody = "Your admin login verification code is: " . $otp;
                
                debug_log("Attempting to send email");
                if($mail->send()) {
                    debug_log("Email sent successfully");
                    $success_message = "New OTP has been sent to your email.";
                } else {
                    debug_log("Email send failed with no exception");
                    $error_message = "Failed to send verification email. Please try again.";
                }
            } catch (Exception $e) {
                debug_log("Email exception: " . $e->getMessage());
                $error_message = "Error sending verification email: " . $e->getMessage();
            }
        } else {
            throw new Exception("Error updating database: " . $conn->error);
        }
        
        
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
        debug_log("Exception in resend process: " . $e->getMessage());
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Verify Login</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div class="w-[90%] lg:w-[450px] h-[fit-content] mx-auto bg-white rounded-[24px] p-6 shadow-lg">
        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between mb-4">
            <h3 class="text-[#262626] text-[20px] md:text-[24px] font-medium">Verify Login</h3>
            <div class="flex items-center gap-2">
                <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
            </div>
        </div>
        
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-medium pt-2 pb-4">ADMIN PANEL</h3>
        
        <div class="text-center mb-6">
            <p class="text-[#C2185B] font-medium text-[18px]">Login Verification</p>
            <p class="text-[#777777] mt-2">
                Enter the 4-digit code sent to: <span class="font-semibold text-[#333333]"><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
            </p>
        </div>

        <?php if (!empty($error_message)): ?>
        <div class="bg-[#FDECEC] shadow-lg mb-4 py-3 px-4 rounded relative" id="errorAlert">
            <div class="h-full w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <div class="flex items-center justify-between">
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Error</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer text-[#777]" viewBox="0 0 20 20" fill="currentColor" onclick="document.getElementById('errorAlert').style.display='none'">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $error_message; ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
        <div class="bg-[#E0F8E9] shadow-lg mb-4 py-3 px-4 rounded relative" id="successAlert">
            <div class="h-full w-[5px] bg-[#28C76F] absolute left-0 top-0"></div>
            <div class="flex items-center justify-between">
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Success</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer text-[#777]" viewBox="0 0 20 20" fill="currentColor" onclick="document.getElementById('successAlert').style.display='none'">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" action="" id="verifyForm" class="mt-4">
            <input type="hidden" name="verify_otp" value="1">
            
            <div class="otp-input-group">
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric" id="otp1" autofocus>
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric" id="otp2">
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric" id="otp3">
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric" id="otp4">
                <input type="hidden" name="otp" id="otpFull">
            </div>
            
            <div class="timer" id="otpTimer">
                OTP expires in: <span id="timer" class="timer-highlight">10:00</span>
            </div>

            <button type="submit" class="w-full py-[12px] px-3 bg-[#C2185B] text-white text-[16px] font-medium cursor-pointer rounded-[8px] transition-colors hover:bg-[#0e1442]">
                Verify & Continue
            </button>
        </form>
        
        <div class="text-center mt-6">
            <a href="#" id="resendLink" class="text-[#C2185B] font-medium text-[14px] hidden">
                Resend verification code
            </a>
            <p class="text-[#777777] text-[14px]" id="resendTimer">
                Resend code in <span class="text-[#C2185B] font-medium" id="resendCounter">60</span> seconds
            </p>
        </div>
        
        <form method="POST" action="" id="resendForm" class="hidden">
            <input type="hidden" name="resend_otp" value="1">
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // OTP input handling
        const inputs = document.querySelectorAll('.otp-input');
        const otpFull = document.getElementById('otpFull');
        const form = document.getElementById('verifyForm');
        
        // Function to update the hidden input with complete OTP
        function updateOtpValue() {
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });
            otpFull.value = otp;
            console.log("Current OTP value:", otpFull.value);
        }
        
        // Auto-focus next input and only allow numbers
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Allow only numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Move to next input after entering a digit
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                updateOtpValue();
                
                // Removed auto-submit functionality
            });
            
            // Handle backspace to go to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (this.value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
                    } else {
                        this.value = '';
                        updateOtpValue();
                    }
                    e.preventDefault();
                }
            });
            
            // Handle paste event
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').trim();
                if (/^\d+$/.test(pasteData)) { // Check if paste data contains only digits
                    // Fill inputs with pasted digits
                    for (let i = 0; i < Math.min(pasteData.length, inputs.length); i++) {
                        inputs[i].value = pasteData[i];
                    }
                    // Focus on appropriate input after paste
                    if (pasteData.length >= inputs.length) {
                        inputs[inputs.length - 1].focus();
                    } else {
                        inputs[pasteData.length].focus();
                    }
                    updateOtpValue();
                    
                    // Removed auto-submit functionality
                }
            });
        });
        
        // Ensure OTP is correctly set before form submission
        form.addEventListener('submit', function(e) {
            updateOtpValue();
            
            // Final validation - make sure we have a 4-digit OTP
            if (otpFull.value.length !== 4 || !/^\d{4}$/.test(otpFull.value)) {
                e.preventDefault();
                alert('Please enter a valid 4-digit OTP');
                return false;
            }
            
            console.log("Submitting OTP:", otpFull.value);
        });
        
        // OTP expiry timer (10 minutes)
        let timeLeft = 10 * 60; // 10 minutes in seconds
        const timerElement = document.getElementById('timer');
        const timerInterval = setInterval(function() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = "Expired";
                timerElement.style.color = "#EE3F3F";
                document.getElementById('resendLink').classList.remove('hidden');
                document.getElementById('resendTimer').classList.add('hidden');
            } else {
                timeLeft--;
            }
        }, 1000);
        
        // Resend OTP timer and link handling
        let resendCounter = 60;
        const resendCounterElement = document.getElementById('resendCounter');
        const resendTimerElement = document.getElementById('resendTimer');
        const resendLinkElement = document.getElementById('resendLink');
        const resendForm = document.getElementById('resendForm');
        
        const resendInterval = setInterval(function() {
            resendCounterElement.textContent = resendCounter;
            
            if (resendCounter <= 0) {
                clearInterval(resendInterval);
                resendTimerElement.classList.add('hidden');
                resendLinkElement.classList.remove('hidden');
            } else {
                resendCounter--;
            }
        }, 1000);
        
        // Handle resend link click
        resendLinkElement.addEventListener('click', function(e) {
            e.preventDefault();
            resendLinkElement.classList.add('hidden');
            resendTimerElement.classList.remove('hidden');
            resendCounter = 60;
            resendCounterElement.textContent = resendCounter;
            
            // Start the counter again
            const newResendInterval = setInterval(function() {
                resendCounterElement.textContent = resendCounter;
                
                if (resendCounter <= 0) {
                    clearInterval(newResendInterval);
                    resendTimerElement.classList.add('hidden');
                    resendLinkElement.classList.remove('hidden');
                } else {
                    resendCounter--;
                }
            }, 1000);
            
            // Submit the form
            resendForm.submit();
        });
        
        // Close alerts
        const closeButtons = document.querySelectorAll('[id$="Alert"] svg');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.closest('[id$="Alert"]').style.display = 'none';
            });
        });
    });
    </script>
</body>
</html>