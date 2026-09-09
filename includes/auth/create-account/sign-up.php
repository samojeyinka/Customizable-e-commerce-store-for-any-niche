<?php
require_once "../../../config/config.php";

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: " . DOMAIN . "/user/orders.php");
    exit();
}


require_once "../google.php"

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>Create an Account - Glorefy</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../../../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="">
<?php
    include(__DIR__ . '/../../header.php');
    include(__DIR__ . '/../../options.php');
?>
    <div class="w-[92%] max-w-[480px] mx-auto py-10 md:py-16">
        <div class="bg-white rounded-[20px] border border-[#262626]/[0.07] shadow-[0_30px_70px_-45px_rgba(0,0,0,0.3)] p-6 md:p-10">
            <div class="text-center mb-7">
                <h2 class="text-[<?php echo store_color('color_heading'); ?>] text-[30px] md:text-[34px] leading-[1.15] font-['Cormorant_Garamond'] font-medium">Welcome to Glorefy</h2>
                <p class="text-[#7A7A7A] text-[14px] md:text-[15px] font-['Open_Sans'] mt-2">Create your account to get started</p>
            </div>

            <form action="./send.php" method="POST" class="flex flex-col gap-4 md:gap-5" id="userCreationForm">
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/80">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Enter your email address"
                        class="w-full font-['Open_Sans'] bg-white outline-none border border-[#262626]/10 text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 px-4 text-[14px] md:text-[15px] rounded-[10px] focus:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200"
                        required
                    />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/80">Password</label>

                    <div class="flex items-center gap-1 bg-white border border-[#262626]/10 rounded-[10px] pl-4 pr-2 focus-within:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">
                        <input
                            type="password"
                            name="password"
                            id="passwordcpp"
                            placeholder="Enter your password"
                            class="w-full font-['Open_Sans'] bg-transparent outline-none text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 text-[14px] md:text-[15px]"
                            required
                        />
                        <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" class="w-[18px] cursor-pointer toggle-password opacity-50 hover:opacity-100 transition-opacity" id="password-toggle" alt="Show password" />
                    </div>
                </div>

                <input type="hidden" name="otp" id="otp" />
                <input type="hidden" name="send" value="1">
                <input type="hidden" name="subject" value="Receive OTP">

                <p id="passwordError" class="hidden text-[13px] font-['Open_Sans'] text-[#EE3F3F] leading-snug">Password must have at least 6 characters, 2 uppercase letters, 2 lowercase letters, 1 number, and 1 special character.</p>

                <button type="submit" class="w-full py-3.5 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] md:text-[13px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] hover:shadow-[0_14px_30px_-12px_rgba(0,0,0,0.3)] transition-all duration-300">
                    Create an account
                </button>
            </form>

            <div class="flex items-center gap-4 my-6">
                <span class="h-px flex-1 bg-[#262626]/10"></span>
                <span class="text-[10px] tracking-[0.24em] uppercase font-['Montserrat'] font-semibold text-[#9A9A9A]">Or</span>
                <span class="h-px flex-1 bg-[#262626]/10"></span>
            </div>

            <a href="<?= $url ?>" class="group w-full flex items-center justify-center gap-3 border border-[#262626]/10 bg-white rounded-full px-6 py-3 cursor-pointer hover:border-[#262626]/25 transition-all duration-200">
                <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[18px]" alt="Google" />
                <span class="text-[13px] font-['Montserrat'] font-medium text-[#262626]">
                    Create an account with Google
                </span>
            </a>

            <p class="text-center font-['Open_Sans'] text-[14px] text-[#7A7A7A] mt-6">
                Already have an account?
                <a href="../login/signin.php" class="font-['Montserrat'] font-semibold text-[<?php echo store_color('color_primary'); ?>] hover:underline">Log in</a>
            </p>
        </div>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
?>

<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('passwordcpp');
    const passwordToggle = document.getElementById('password-toggle');
    const passwordError = document.getElementById('passwordError');
    const form = document.getElementById('userCreationForm');
    
    // Toggle password visibility
    passwordToggle.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text'; // Change to text (show password)
            passwordToggle.src = "<?php echo DOMAIN; ?>/assets/global/eye.svg"; // Change to open eye
        } else {
            passwordInput.type = 'password'; // Change to password (hide password)
            passwordToggle.src = "<?php echo DOMAIN; ?>/assets/global/eye-slash.svg"; // Change to closed eye
        }
    });

    // Comprehensive password validation function
    function validatePassword(password) {
        const validationRules = [
            { 
                test: (pw) => pw.length >= 6, 
                message: 'Password must be at least 6 characters long'
            },
            { 
                test: (pw) => (pw.match(/[A-Z]/g) || []).length >= 2, 
                message: 'Password must contain at least 2 uppercase letters'
            },
            { 
                test: (pw) => (pw.match(/[a-z]/g) || []).length >= 2, 
                message: 'Password must contain at least 2 lowercase letters'
            },
            { 
                test: (pw) => /\d/.test(pw), 
                message: 'Password must contain at least 1 number'
            },
            { 
                test: (pw) => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pw), 
                message: 'Password must contain at least 1 special character'
            }
        ];

        // Check all validation rules
        const failedRules = validationRules.filter(rule => !rule.test(password));

        return {
            isValid: failedRules.length === 0,
            errors: failedRules.map(rule => rule.message)
        };
    }

    function updatePasswordError(validationResult) {
        passwordError.style.display = 'block';
        
        if (!validationResult.isValid) {
            // Show detailed error messages
            passwordError.className = 'text-[13px] font-["Open Sans"] text-[#EE3F3F] font-regular leading-snug';
            passwordError.textContent = validationResult.errors.join('. ');
        } else {
            passwordError.className = 'text-[13px] font-["Open Sans"] text-[#22C55E] font-regular leading-snug';
            passwordError.textContent = 'Password meets all requirements!';
        }
    }

    // Check password on input
    passwordInput.addEventListener('input', function() {
        const validationResult = validatePassword(this.value);
        updatePasswordError(validationResult);
    });

    // Form submission validation
    form.addEventListener('submit', function(event) {
        const validationResult = validatePassword(passwordInput.value);
        
        if (!validationResult.isValid) {
            event.preventDefault();
            updatePasswordError(validationResult);
            passwordInput.focus();
        }
    });
});
</script>
</body>
</html>