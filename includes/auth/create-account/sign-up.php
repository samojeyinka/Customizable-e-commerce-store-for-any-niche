<?php
require_once "../../../config/config.php";
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: " . DOMAIN . "/user/orders.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account - Victosah Solution</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../style.css" />
    <link rel="stylesheet" href="../../../styles/faq.css" />
    <link rel="stylesheet" href="../../../styles/modal.css">
    <link rel="stylesheet" href="../../../styles/tabs.css">
    <link rel="stylesheet" href="../../../styles/inputs.css">
</head>
<body class="">
<?php
    include(__DIR__ . '/../../header.php');
    include(__DIR__ . '/../../options.php');
?>
     <div class="md:w-[50%] mx-auto p-4 bg-white border border-[1px] border-[#EFEFEF] my-5 rounded-md">
        <div class="text-center mb-6">
            <h2 class="text-[#262626] text-[24px] font-['Open Sans'] font-medium">Welcome to Victosah Solution</h2>
            <p class="text-[#7A7A7A] text-[16px] mt-2">Create your account to get started</p>
        </div>

        <form action="./send.php" method="POST" class="flex flex-col gap-4" id="userCreationForm">
            <div class="flex flex-col gap-1">
                <label for="email" class="font-['Open Sans'] text-[16px] font-medium text-[#262626]">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Enter your email address"
                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[16px] rounded-[8px]"
                    required
                />
            </div>

            <div class="flex flex-col gap-1">
                <label for="password" class="font-['Open Sans'] text-[16px] font-medium text-[#262626]">
                    Password
                </label>

                <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
    <input
        type="password"
        name="password"
        id="passwordcpp"
        placeholder="Enter your password"
        class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[16px]"
        required
    />
    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" class="w-[24px] cursor-pointer toggle-password" id="password-toggle" />
</div>

            </div>

            <input type="hidden" name="otp" id="otp" />
            <input type="hidden" name="send" value="1">
            <input type="hidden" name="subject" value="Receive OTP">

            <p id="passwordError" class='text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular underline cursor-pointer' style="display: none;"></p>
            
            <button type="submit" class="w-full py-[10px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center hover:bg-[#1A237E]/90 transition-colors duration-300">
                Create an account
            </button>
        </form>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#E1E1E1]"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-4 text-[#7A7A7A] text-[16px]">Or</span>
            </div>
        </div>

        <div class="cursor-pointer flex items-center justify-center gap-3 border-[1px] border-[#E1E1E1] rounded-[8px] py-3 hover:bg-gray-50 transition-colors duration-300">
            <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[20px]" />
            <p class="font-['Open Sans'] text-[16px] font-regular text-[#262626]">
                Create an account with Google
            </p>
        </div>

        <div class="text-center mt-6">
            <p class="text-[#7A7A7A] text-[14px]">
                Already have an account? 
                <a href="../login/signin.php" class="text-[#1A237E] font-medium hover:underline">Log in</a>
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

  
 // Password validation
 const passwordError = document.getElementById('passwordError');
    const form = document.getElementById('userCreationForm');
    
    function validatePassword(password) {
    // Check minimum length of 6 characters
    if (password.length < 6) {
        return {
            isValid: false,
            message: 'Password must be at least 6 characters long.'
        };
    }
    
    // Check for at least 2 uppercase letters
    const uppercaseMatches = password.match(/[A-Z]/g) || [];
    if (uppercaseMatches.length < 2) {
        return {
            isValid: false,
            message: 'Password must contain at least 2 uppercase letters.'
        };
    }
    
    // Check for at least 2 lowercase letters
    const lowercaseMatches = password.match(/[a-z]/g) || [];
    if (lowercaseMatches.length < 2) {
        return {
            isValid: false,
            message: 'Password must contain at least 2 lowercase letters.'
        };
    }
    
    // Check for at least 1 number
    const numberMatches = password.match(/\d/g) || [];
    if (numberMatches.length < 1) {
        return {
            isValid: false,
            message: 'Password must contain at least 1 number.'
        };
    }
    
    // Check for at least 1 special character
    const specialCharMatches = password.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g) || [];
    if (specialCharMatches.length < 1) {
        return {
            isValid: false,
            message: 'Password must contain at least 1 special character.'
        };
    }
    
    // If all checks pass
    return {
        isValid: true,
        message: 'Password meets all requirements!'
    };
}

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
    
    function updatePasswordError(validationResult) {
        passwordError.style.display = 'block';
        
        if (!validationResult.isValid) {
            passwordError.className = 'text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular';
            passwordError.textContent = validationResult.message;
        } else {
            passwordError.className = 'text-[14px] font-["Open Sans"] text-[#22C55E] font-regular';
            passwordError.textContent = validationResult.message;
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
    
    // Generate OTP (kept from original script)
    function generateRandomNumber() {
        let min = 1000;
        let max = 9999;
        
        let randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
        
        let lastGeneratedNumber = localStorage.getItem('lastGeneratedNumber');
        while(randomNumber === parseInt(lastGeneratedNumber)) {
            randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
        }
        
        localStorage.setItem('lastGeneratedNumber', randomNumber);
        return randomNumber;
    }
    
    document.getElementById('otp').value = generateRandomNumber();
});
        
    
</script>
</body>
</html>