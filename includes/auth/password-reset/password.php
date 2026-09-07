<?php
require_once "../../../config/config.php";

// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if reset email is stored in session, if not - redirect to mail page
// Use the user-specific session variable
if(!isset($_SESSION['user_reset_email'])) {
    header("Location: " . DOMAIN . "/includes/auth/password-reset/mail.php");
    exit();
}

// Initialize message variables
$error_message = "";
$success_message = "";

// Process the form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password
    if($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif(strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif(preg_match_all('/[A-Z]/', $password) < 2) {
        $error_message = "Password must contain at least 2 uppercase letters.";
    } elseif(preg_match_all('/[a-z]/', $password) < 2) {
        $error_message = "Password must contain at least 2 lowercase letters.";
    } elseif(!preg_match('/[0-9]/', $password)) {
        $error_message = "Password must contain at least 1 number.";
    } elseif(!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $error_message = "Password must contain at least 1 special character.";
    } else {
        // Store password in session to be used after OTP verification
        // Use a user-specific session variable to avoid conflicts
        $_SESSION['user_new_password'] = $password;
        
        // Redirect to verification page
        header("Location: " . DOMAIN . "/includes/auth/password-reset/verify.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY - NEW PASSWORD</title>
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
        <a href="<?php echo DOMAIN; ?>/includes/auth/password-reset/mail.php">
            <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />
        </a>

        <p class="font-['Open Sans'] text-[19px] text-[24px] font-medium text-center">
            Reset Password
        </p>
        <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Enter your new password
        </p>

        <?php if(!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="w-full mt-[1rem] flex flex-col gap-4" id="passwordResetForm">
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
            required
            class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
        <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="toggle-password" class="w-[24px] cursor-pointer" />
    </div>
</div>

<div class="flex flex-col gap-1">
    <label
        for="confirm_password"
        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
        Confirm Password
    </label>

    <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
        <input
            type="password"
            name="confirm_password"
            id="confirm_password"
            required
            placeholder="Confirm your password"
            class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
        <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="toggle-confirm" class="w-[24px] cursor-pointer" />
    </div>
</div>

            <p id="passwordError" class='text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular' style="display: none;">Password must have at least 6 characters, 2 uppercase letters, 2 lowercase letters, 1 number, and 1 special character.</p>

            <input type="hidden" name="update" value="1">

            <button
                type="submit"
                class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[8px] mt-10 cursor-pointer">
                Continue
            </button>
        </form>
    </div>
</div>

<?php
    include(__DIR__ . '/../../footer.php');
?>

<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const togglePassword = document.getElementById('toggle-password');
    const password = document.getElementById('password');
    
    if(togglePassword && password) {
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
    const toggleConfirm = document.getElementById('toggle-confirm');
    const confirmPassword = document.getElementById('confirm_password');
    
    if(toggleConfirm && confirmPassword) {
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
    
    // Password validation
    const passwordError = document.getElementById('passwordError');
    
    function validatePassword(password) {
        // At least 6 characters
        if (password.length < 6) return false;
        
        // At least 2 uppercase letters
        if ((password.match(/[A-Z]/g) || []).length < 2) return false;
        
        // At least 2 lowercase letters
        if ((password.match(/[a-z]/g) || []).length < 2) return false;
        
        // At least 1 number
        if (!/\d/.test(password)) return false;
        
        // At least 1 special character
        if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) return false;
        
        return true;
    }
    
    function updatePasswordError(isValid) {
        if(passwordError) {
            passwordError.style.display = 'block';
            
            if (!isValid) {
                passwordError.className = 'text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular';
                passwordError.textContent = 'Password must have at least 6 characters, 2 uppercase letters, 2 lowercase letters, 1 number, and 1 special character.';
            } else {
                passwordError.className = 'text-[14px] font-["Open Sans"] text-[#22C55E] font-regular';
                passwordError.textContent = 'Password meets all requirements!';
            }
        }
    }
    
    // Password validation on input
    if(password) {
        password.addEventListener('input', function() {
            const isValid = validatePassword(this.value);
            updatePasswordError(isValid);
            
            // Check if passwords match
            if(confirmPassword && confirmPassword.value) {
                if(this.value !== confirmPassword.value) {
                    if(passwordError) {
                        passwordError.className = 'text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular';
                        passwordError.textContent = 'Passwords do not match.';
                        passwordError.style.display = 'block';
                    }
                }
            }
        });
    }
    
    // Confirm password validation
    if(confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if(password && password.value) {
                if(this.value !== password.value) {
                    if(passwordError) {
                        passwordError.className = 'text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular';
                        passwordError.textContent = 'Passwords do not match.';
                        passwordError.style.display = 'block';
                    }
                } else {
                    const isValid = validatePassword(password.value);
                    updatePasswordError(isValid);
                }
            }
        });
    }
});
</script>
</body>
</html>