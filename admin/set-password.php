<?php
session_start();

// Security check: Ensure user has gone through OTP verification
if (!isset($_SESSION['admin_setup_id']) || !isset($_SESSION['setup_token'])) {
    $_SESSION['setup_error'] = "Please complete the verification process first.";
    header("Location: ./set-up.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Set Password</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[#262626] text-[20px] md:text-[24px] font-medium">Set Password</h3>
            <div class="flex items-center gap-2">
                <img src="./assets/global/logo.png" alt="GLOREFY" class="w-[31.35px] md:w-[41.35px]" />
            </div>
        </div>
        
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-medium pt-2 pb-4">ADMIN PANEL</h3>
        
        <div class="text-center mb-6">
            <p class="text-[#C2185B] font-medium text-[18px]">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator'); ?>!</p>
            <p class="text-[#777777] mt-2">Create a strong password for your administrator account</p>
        </div>
        
        <?php if (isset($_SESSION['setup_error'])): ?>
            <div class="bg-[#FDECEC] shadow-lg mb-4 py-3 px-4 rounded relative">
                <div class="h-full w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Error</p>
                <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $_SESSION['setup_error']; unset($_SESSION['setup_error']); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['setup_success'])): ?>
            <div class="bg-[#E0F8E9] shadow-lg mb-4 py-3 px-4 rounded relative">
                <div class="h-full w-[5px] bg-[#28C76F] absolute left-0 top-0"></div>
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Success</p>
                <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $_SESSION['setup_success']; unset($_SESSION['setup_success']); ?></p>
            </div>
        <?php endif; ?>
        
        <form action="./process-password-setup.php" method="POST" class="mt-6">
            <!-- Include the security token as a hidden field -->
            <input type="hidden" name="setup_token" value="<?php echo htmlspecialchars($_SESSION['setup_token']); ?>">
            
            <div class="mb-5">
                <label for="password" class="block text-[#262626] text-[15px] font-medium mb-2">New Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" class="password-input" required>
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                
                <div class="mt-4 p-4 bg-[#F9F9F9] rounded-lg">
                    <p class="text-[#262626] text-[14px] font-medium mb-3">Password must meet all criteria:</p>
                    
                    <div class="requirement" id="length">
                        <span class="requirement-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="text-[14px]">Minimum 8 characters</span>
                    </div>
                    
                    <div class="requirement" id="uppercase">
                        <span class="requirement-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="text-[14px]">At least 3 uppercase letters</span>
                    </div>
                    
                    <div class="requirement" id="lowercase">
                        <span class="requirement-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="text-[14px]">At least 3 lowercase letters</span>
                    </div>
                    
                    <div class="requirement" id="number">
                        <span class="requirement-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="text-[14px]">At least 1 number</span>
                    </div>
                    
                    <div class="requirement" id="special">
                        <span class="requirement-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="text-[14px]">At least 1 special character</span>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-[#262626] text-[15px] font-medium mb-2">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" class="password-input" required>
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div id="match-status" class="mt-2 text-[14px] hidden">
                    Passwords do not match
                </div>
            </div>
            
            <button type="submit" id="submit-btn" class="w-full py-[12px] px-3 bg-[#C2185B] text-white text-[16px] font-medium cursor-pointer rounded-[8px] hover:bg-[#0e1442] transition-colors" disabled>
                Set Password & Continue
            </button>
        </form>
        
        <p class="text-center text-[#777777] mt-6 text-[14px]">
            Your password will be securely stored with industry-standard encryption
        </p>
    </div>
    
    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('.eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
        
        // Live password validation
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const submitButton = document.getElementById('submit-btn');
        const matchStatus = document.getElementById('match-status');
        
        // Requirements elements
        const lengthReq = document.getElementById('length');
        const uppercaseReq = document.getElementById('uppercase');
        const lowercaseReq = document.getElementById('lowercase');
        const numberReq = document.getElementById('number');
        const specialReq = document.getElementById('special');
        
        passwordInput.addEventListener('input', validatePassword);
        confirmInput.addEventListener('input', validatePasswordMatch);
        
        function validatePassword() {
            const password = passwordInput.value;
            let allValid = true;
            
            // Check length
            const isLengthValid = password.length >= 8;
            updateRequirement(lengthReq, isLengthValid);
            if (!isLengthValid) allValid = false;
            
            // Check uppercase letters
            const uppercaseCount = (password.match(/[A-Z]/g) || []).length;
            const isUppercaseValid = uppercaseCount >= 3;
            updateRequirement(uppercaseReq, isUppercaseValid);
            if (!isUppercaseValid) allValid = false;
            
            // Check lowercase letters
            const lowercaseCount = (password.match(/[a-z]/g) || []).length;
            const isLowercaseValid = lowercaseCount >= 3;
            updateRequirement(lowercaseReq, isLowercaseValid);
            if (!isLowercaseValid) allValid = false;
            
            // Check numbers
            const hasNumber = /[0-9]/.test(password);
            updateRequirement(numberReq, hasNumber);
            if (!hasNumber) allValid = false;
            
            // Check special characters
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            updateRequirement(specialReq, hasSpecial);
            if (!hasSpecial) allValid = false;
            
            validatePasswordMatch();
            checkAllRequirements();
        }
        
        function updateRequirement(element, isValid) {
            if (isValid) {
                element.classList.add('valid');
                element.classList.remove('invalid');
            } else {
                element.classList.add('invalid');
                element.classList.remove('valid');
            }
        }
        
        function validatePasswordMatch() {
            const doPasswordsMatch = passwordInput.value === confirmInput.value && confirmInput.value !== '';
            
            if (confirmInput.value !== '') {
                matchStatus.classList.remove('hidden');
                
                if (doPasswordsMatch) {
                    confirmInput.style.borderColor = '#28C76F';
                    confirmInput.style.boxShadow = '0 0 0 2px rgba(40, 199, 111, 0.2)';
                    matchStatus.textContent = 'Passwords match';
                    matchStatus.style.color = '#28C76F';
                } else {
                    confirmInput.style.borderColor = '#EE3F3F';
                    confirmInput.style.boxShadow = '0 0 0 2px rgba(238, 63, 63, 0.2)';
                    matchStatus.textContent = 'Passwords do not match';
                    matchStatus.style.color = '#EE3F3F';
                }
            } else {
                confirmInput.style.borderColor = '#E1E1E1';
                confirmInput.style.boxShadow = 'none';
                matchStatus.classList.add('hidden');
            }
            
            checkAllRequirements();
        }
        
        function checkAllRequirements() {
            const isPasswordValid = lengthReq.classList.contains('valid') && 
                                   uppercaseReq.classList.contains('valid') && 
                                   lowercaseReq.classList.contains('valid') && 
                                   numberReq.classList.contains('valid') && 
                                   specialReq.classList.contains('valid');
                                   
            const doPasswordsMatch = passwordInput.value === confirmInput.value && confirmInput.value !== '';
            
            submitButton.disabled = !(isPasswordValid && doPasswordsMatch);
            
            if (submitButton.disabled) {
                submitButton.classList.add('opacity-60');
                submitButton.classList.remove('hover:bg-[#0e1442]');
            } else {
                submitButton.classList.remove('opacity-60');
                submitButton.classList.add('hover:bg-[#0e1442]');
            }
        }
    </script>
</body>
</html>