<?php
session_start();
require_once "../config/config.php";

// Check if admin is verified for password reset
if(!isset($_SESSION['admin_reset_email']) || !isset($_SESSION['admin_reset_id']) || !isset($_SESSION['admin_reset_verified'])) {
    header("Location: ./forgotten-password.php");
    exit();
}

// Initialize variables
$error_message = "";
$password_error = "";

// Process password reset
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if(empty($password) || empty($confirm_password)) {
        $error_message = "Please enter and confirm your new password.";
    } elseif($password !== $confirm_password) {
        $password_error = "Passwords don't match";
    } elseif(strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif(!preg_match('/[A-Z]/', $password)) {
        $error_message = "Password must contain at least one uppercase letter.";
    } elseif(!preg_match('/[a-z]/', $password)) {
        $error_message = "Password must contain at least one lowercase letter.";
    } elseif(!preg_match('/[0-9]/', $password)) {
        $error_message = "Password must contain at least one number.";
    } elseif(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $error_message = "Password must contain at least one special character.";
    } else {
      

        require_once "../config/servername.php";
        
        $conn = db();
        
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password in database
        $sql = "UPDATE administrators SET password_hash = ?, otp_code = NULL, otp_expires = NULL WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $_SESSION['admin_reset_id']);
        
        if($stmt->execute()) {
            // Clear reset session variables
            unset($_SESSION['admin_reset_email']);
            unset($_SESSION['admin_reset_id']);
            unset($_SESSION['admin_reset_verified']);
            
            // Redirect to success page
            header("Location: ./change-password-success.php");
            exit();
        } else {
            $error_message = "Error updating password. Please try again.";
        }
        
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Change Password</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">

        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between">
            <a href="./forgotten-password-verify.php" class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[20px]" alt="Back"></i>
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

        <p class="text-[#C2185B] font-['Open Sans'] text-[18px] text-[22px] font-medium text-left pl-[2.5%] pt-5">
            Reset Password
        </p>
        <p class="text-left text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2 pl-[2.5%]">
            Enter your new password
        </p>

        <form method="POST" action="" class="w-full mt-[1rem] flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label
                    for="password"
                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626] pl-[2.5%]">
                    Password
                </label>

                <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3 mx-[2.5%]">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="togglePassword" class="w-[24px] cursor-pointer" />
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label
                    for="confirm_password"
                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626] pl-[2.5%]">
                    Confirm Password
                </label>

                <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3 mx-[2.5%]">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                        class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="toggleConfirm" class="w-[24px] cursor-pointer" />
                </div>

                <?php if (!empty($password_error)): ?>
                <p class="text-[#D93939] font-['Open Sans'] text-[15px] text-[16px] font-regular text-left pl-[2.5%]">
                    <?php echo $password_error; ?>
                </p>
                <?php endif; ?>
            </div>

            <button
                type="submit"
                class="text-center mx-auto w-[95%] text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#C2185B] text-white rounded-[8px] mt-10 cursor-pointer">
                Reset Password
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                if (password.type === 'password') {
                    password.type = 'text';
                    this.src = '<?php echo DOMAIN; ?>/assets/global/eye.svg';
                } else {
                    password.type = 'password';
                    this.src = '<?php echo DOMAIN; ?>/assets/global/eye-slash.svg';
                }
            });
        }
        
        // Confirm password toggle
        const toggleConfirm = document.getElementById('toggleConfirm');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (toggleConfirm && confirmPassword) {
            toggleConfirm.addEventListener('click', function() {
                if (confirmPassword.type === 'password') {
                    confirmPassword.type = 'text';
                    this.src = '<?php echo DOMAIN; ?>/assets/global/eye.svg';
                } else {
                    confirmPassword.type = 'password';
                    this.src = '<?php echo DOMAIN; ?>/assets/global/eye-slash.svg';
                }
            });
        }
    });
    </script>
</body>
</html>