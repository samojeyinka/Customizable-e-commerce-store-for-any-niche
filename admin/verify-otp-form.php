<?php
session_start();

// Redirect if no email is stored in session (meaning they didn't request an OTP)
if (!isset($_SESSION['setup_email'])) {
    $_SESSION['setup_error'] = "Please request an OTP first.";
    header("Location: ./set-up.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH ADMIN | Verify OTP</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
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
        
        .container {
            background-color: white;
            border-radius: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }
        
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 1px solid #E1E1E1;
            border-radius: 8px;
            margin: 0 6px;
            background-color: transparent;
            outline: none;
        }
        
        .otp-input:focus {
            border-color: #1A237E;
            box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.2);
        }
        
        @media (max-width: 480px) {
            .otp-input {
                width: 40px;
                height: 40px;
                font-size: 20px;
                margin: 0 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[#262626] text-[20px] md:text-[24px] font-medium">Verify OTP</h3>
            <div class="flex items-center gap-2">
                <img src="./assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
            </div>
        </div>
        
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-medium pt-2 pb-4">ADMIN PANEL</h3>
        
        <div class="text-center mb-6">
            <p class="text-[#1A237E] font-medium text-[18px]">Verification Required</p>
            <p class="text-[#777777] mt-2">
                Enter the 6-digit code sent to: <span class="font-semibold text-[#333333]"><?php echo htmlspecialchars($_SESSION['setup_email']); ?></span>
            </p>
        </div>
        
        <?php if (isset($_SESSION['setup_message'])): ?>
            <div class="bg-[#E0F8E9] shadow-lg mb-4 py-3 px-4 rounded relative">
                <div class="h-full w-[5px] bg-[#28C76F] absolute left-0 top-0"></div>
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Success</p>
                <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $_SESSION['setup_message']; unset($_SESSION['setup_message']); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['setup_error'])): ?>
            <div class="bg-[#FDECEC] shadow-lg mb-4 py-3 px-4 rounded relative">
                <div class="h-full w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Error</p>
                <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $_SESSION['setup_error']; unset($_SESSION['setup_error']); ?></p>
            </div>
        <?php endif; ?>
        
        <form action="./verify-otp.php" method="POST" class="mt-6">
            <div class="flex justify-center mb-6">
                <input type="password" name="digit1" class="otp-input" maxlength="1" inputmode="numeric" required autofocus>
                <input type="password" name="digit2" class="otp-input" maxlength="1" inputmode="numeric" required>
                <input type="password" name="digit3" class="otp-input" maxlength="1" inputmode="numeric" required>
                <input type="password" name="digit4" class="otp-input" maxlength="1" inputmode="numeric" required>
                <input type="password" name="digit5" class="otp-input" maxlength="1" inputmode="numeric" required>
                <input type="password" name="digit6" class="otp-input" maxlength="1" inputmode="numeric" required>
            </div>
            
            <input type="hidden" name="verify_otp" value="1">
            <button type="submit" class="w-full py-[12px] px-3 bg-[#1A237E] text-white text-[16px] font-medium cursor-pointer rounded-[8px] hover:bg-[#0e1442] transition-colors">
                Verify Code
            </button>
        </form>
        
        <div class="text-center text-[#777777] mt-6 text-[14px]" id="countdown">
            OTP expires in: <span class="text-[#1A237E] font-medium" id="timer">15:00</span>
        </div>
        
        <div class="text-center mt-6">
            <a href="./request-password-setup.php?resend=true" class="text-[#1A237E] font-medium text-[14px] hidden" id="resend-link">
                Resend verification code
            </a>
            <p class="text-[#777777] text-[14px]" id="resend-timer">
                Resend code in <span class="text-[#1A237E] font-medium">03:00</span>
            </p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // OTP input handling
            const inputs = document.querySelectorAll('.otp-input');
            
            // Auto-focus next input and only allow numbers
            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    // Allow only numbers
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Move to next input after entering a digit
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });
                
                // Handle backspace to go to previous input
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
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
                    }
                });
            });
            
            // OTP expiry timer (15 minutes)
            const timerElement = document.getElementById('timer');
            const countdownElement = document.getElementById('countdown');
            let timeLeft = 15 * 60; // 15 minutes in seconds
            
            const expiryInterval = setInterval(function() {
                timeLeft--;
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(expiryInterval);
                    countdownElement.innerHTML = "OTP has <span class='text-[#EE3F3F] font-medium'>expired</span>. Please request a new one.";
                    document.getElementById('resend-link').classList.remove('hidden');
                    document.getElementById('resend-timer').classList.add('hidden');
                }
            }, 1000);
            
            // Resend countdown timer (3 minutes)
            const resendLink = document.getElementById('resend-link');
            const resendTimer = document.getElementById('resend-timer');
            let resendTimeLeft = 3 * 60; // 3 minutes
            
            const resendInterval = setInterval(function() {
                resendTimeLeft--;
                
                const minutes = Math.floor(resendTimeLeft / 60);
                const seconds = resendTimeLeft % 60;
                
                const resendTimeDisplay = document.querySelector('#resend-timer span');
                resendTimeDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (resendTimeLeft <= 0) {
                    clearInterval(resendInterval);
                    resendLink.classList.remove('hidden');
                    resendTimer.classList.add('hidden');
                }
            }, 1000);
        });


        
    </script>
</body>
</html>