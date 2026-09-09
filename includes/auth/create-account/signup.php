<div id="SignUp" class="tabcontent">
    <div class="text-center mb-7">
        <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[28px] md:text-[32px] leading-[1.15] font-['Cormorant_Garamond'] font-medium">Welcome to Glorefy</h3>
    </div>

    <form action="<?php echo DOMAIN; ?>/includes/auth/create-account/send.php" method="POST" class="flex flex-col gap-4 md:gap-5" id="userCreationForm">

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
                    id="password"
                    placeholder="Enter your password"
                    class="w-full font-['Open_Sans'] bg-transparent outline-none text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 text-[14px] md:text-[15px]"
                    required
                />
                <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" class="w-[18px] cursor-pointer toggle-password opacity-50 hover:opacity-100 transition-opacity" data-target="password" alt="Show password" />
                <img src="<?php echo DOMAIN; ?>/assets/global/eye.svg" class="w-[18px] cursor-pointer toggle-password opacity-50 hover:opacity-100 transition-opacity" data-target="password" alt="Hide password" />
            </div>
        </div>

        <input type="hidden" name="otp" id="otp" class="hidden w-full font-['Open_Sans'] bg-transparent outline-none text-[#2C2C2C] py-[10px] px-2 text-[14px] md:text-[16px]" />
        <input type="hidden" name="send" value="1">
        <input type="hidden" name="subject" value="Receive OTP">
        <p id="passwordError" class="hidden text-[13px] font-['Open_Sans'] text-[#EE3F3F] leading-snug">Password must have at least 6 characters, 2 uppercase letters, 2 lowercase letters, 1 number, and 1 special character.</p>

        <button type="submit" class="w-full py-3.5 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] md:text-[13px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] hover:shadow-[0_14px_30px_-12px_rgba(0,0,0,0.3)] transition-all duration-300">Create an account</button>

    </form>

    <div class="flex items-center gap-4 my-5 md:my-6">
        <span class="h-px flex-1 bg-[#262626]/10"></span>
        <span class="text-[10px] tracking-[0.24em] uppercase font-['Montserrat'] font-semibold text-[#9A9A9A]">Or</span>
        <span class="h-px flex-1 bg-[#262626]/10"></span>
    </div>

    <a href="<?= $url ?>" class="group w-full flex items-center justify-center gap-3 border border-[#262626]/10 bg-white rounded-full px-6 py-3 cursor-pointer hover:border-[#262626]/25 transition-all duration-200">
        <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[18px]" alt="Google" />
        <span class="text-[13px] font-['Montserrat'] font-medium text-[#262626]">Create an account with Google</span>
    </a>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // First, let's fix the HTML - we should hide one of the icons initially
    const eyeOpen = document.querySelector('img[src*="eye-slash.svg"]');
    const eyeClosed = document.querySelector('img[src*="eye.svg"]');
    
    // Initially hide the eye-slash icon
    if (eyeClosed) eyeClosed.style.display = 'none';
    
    // Password visibility toggle functionality
    const passwordInput = document.getElementById('password');
    if (!eyeOpen || !eyeClosed || !passwordInput) return;
    
    eyeOpen.addEventListener('click', function() {
        passwordInput.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'inline';
    });
    
    eyeClosed.addEventListener('click', function() {
        passwordInput.type = 'password';
        eyeClosed.style.display = 'none';
        eyeOpen.style.display = 'inline';
    });
    
    // Password validation
    const passwordError = document.getElementById('passwordError');
    const form = document.getElementById('userCreationForm');
    
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
        passwordError.style.display = 'block';
        
        if (!isValid) {
            passwordError.className = 'text-[13px] font-["Open Sans"] text-[#EE3F3F] font-regular';
            passwordError.textContent = 'Password must have at least 6 characters, 2 uppercase letters, 2 lowercase letters, 1 number, and 1 special character.';
        } else {
            passwordError.className = 'text-[13px] font-["Open Sans"] text-[#22C55E] font-regular';
            passwordError.textContent = 'Password meets all requirements!';
        }
    }
    
    // Check password on input
    passwordInput.addEventListener('input', function() {
        const isValid = validatePassword(this.value);
        updatePasswordError(isValid);
    });
    
    // Form submission validation
    form.addEventListener('submit', function(event) {
        const isValid = validatePassword(passwordInput.value);
        
        if (!isValid) {
            event.preventDefault();
            updatePasswordError(isValid);
            passwordInput.focus();
        }
    });
    
    // Generate OTP
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