<?php
require_once __DIR__ . '/../../../config/config.php';

$conn = db();

// Login logic
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $has_error = false;
    $error_message = "";
    
    // Validate required fields
    if(empty($email) || empty($password)) {
        $has_error = true;
        $error_message = "All fields are required";
    } else {
        // Check if user exists and is verified
        $sql = "SELECT id, email, password, status FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if(password_verify($password, $user['password'])) {
                // Check if account is verified
                if($user['status'] == 'active') {
                    // Set session variables
                    if (!isset($_SESSION)) {
                        session_start();
                    }
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_status'] = $user['status'];
                    
                    // Store last login time
                    $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    
                    // Redirect to dashboard using JavaScript
                    echo "<script>window.location.href = '" . DOMAIN . "/user/orders.php';</script>";
                    exit();
                } else {
                    $has_error = true;
                    $error_message = "Please verify your account first";
                }
            } else {
                $has_error = true;
                $error_message = "Invalid email or password";
            }
        } else {
            $has_error = true;
            $error_message = "Invalid email or password";
        }
    }
    
    // If there's an error, redirect to signin page with error message
    if($has_error) {
        // Store error in session
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['login_error'] = $error_message;
        
        // Redirect to signin page
        echo "<script>window.location.href = '" . DOMAIN . "/includes/auth/login/signin.php';</script>";
        exit();
    }
}

// Include Google configuration

?>

<!-- Your form HTML -->
<div id="SignIn" class="tabcontent">
    <div class="text-center mb-7">
        <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[28px] md:text-[32px] leading-[1.15] font-['Cormorant_Garamond'] font-medium">Welcome Back!</h3>
    </div>
    
    <!-- Show error message if exists -->
    <?php if(!empty($error)): ?>
    <section id="dangeralert" class="flex flex-col items-start w-full bg-[#FDECEC] border-l-[3px] border-[#EE3F3F] rounded-[10px] py-3 px-4 mb-4 relative overflow-hidden">
        <div class="flex items-center gap-2 w-full">
            <img src="<?php echo DOMAIN; ?>/assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[18px] cursor-pointer" />
            <p class="text-[15px] text-[#2C2C2C] font-['Open_Sans'] font-semibold">Login Error</p>
        </div>
        <p class="text-[13px] text-start w-full font-['Open_Sans'] text-[#7F7F7F] mt-1.5 pl-8 pr-2">
            <?php echo $error; ?>
        </p>
    </section>
    <?php endif; ?>

    <form method="POST" class="flex flex-col gap-4 md:gap-5">
        <div class="flex flex-col gap-1.5">
            <label for="email" class="text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/80">Email</label>
            <input
                type="email"
                name="email"
                id="email"
                required
                placeholder="Enter your email address"
                class="w-full font-['Open_Sans'] bg-white outline-none border border-[#262626]/10 text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 px-4 text-[14px] md:text-[15px] rounded-[10px] focus:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200" />
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password" class="text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/80">Password</label>

            <div class="flex items-center gap-1 bg-white border border-[#262626]/10 rounded-[10px] pl-4 pr-2 focus-within:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">
                <input
                    type="password"
                    name="password"
                    id="signinpassword"
                    placeholder="Enter your password"
                    required
                    class="w-full font-['Open_Sans'] bg-transparent outline-none text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 text-[14px] md:text-[15px]" />
                <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="showPasswordIcon" class="w-[18px] cursor-pointer opacity-50 hover:opacity-100 transition-opacity" alt="Show password" />
                <img src="<?php echo DOMAIN; ?>/assets/global/eye.svg" id="hidePasswordIcon" class="w-[18px] cursor-pointer opacity-50 hover:opacity-100 transition-opacity hidden" alt="Hide password" />
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <a href="<?php echo DOMAIN; ?>/includes/auth/login/signin.php" class="text-[12px] tracking-[0.08em] uppercase font-['Montserrat'] font-medium text-[<?php echo store_color('color_primary'); ?>] hover:underline cursor-pointer">Forgot Password?</a>
        </div>

        <button type="submit" name="login" class="w-full py-3.5 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] md:text-[13px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] hover:shadow-[0_14px_30px_-12px_rgba(0,0,0,0.3)] transition-all duration-300">Sign In</button>
    </form>

    <div class="flex items-center gap-4 my-5 md:my-6">
        <span class="h-px flex-1 bg-[#262626]/10"></span>
        <span class="text-[10px] tracking-[0.24em] uppercase font-['Montserrat'] font-semibold text-[#9A9A9A]">Or</span>
        <span class="h-px flex-1 bg-[#262626]/10"></span>
    </div>

    <a href="<?= $url ?>" class="group w-full flex items-center justify-center gap-3 border border-[#262626]/10 bg-white rounded-full px-6 py-3 cursor-pointer hover:border-[#262626]/25 transition-all duration-200">
        <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[18px]" alt="Google" />
        <span class="text-[13px] font-['Montserrat'] font-medium text-[#262626]">Sign In with Google</span>
    </a>
</div>

<!-- Simple JavaScript for password toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const showPasswordIcon = document.getElementById('showPasswordIcon');
    const hidePasswordIcon = document.getElementById('hidePasswordIcon');
    const signinpassword = document.getElementById('signinpassword');
    
    if(showPasswordIcon && hidePasswordIcon && signinpassword) {
        // Show password (eye icon clicked)
        showPasswordIcon.addEventListener('click', function() {
            // Important: This actually changes the password field to show text
            signinpassword.type = 'text';
            
            // Toggle icon visibility
            showPasswordIcon.classList.add('hidden');
            hidePasswordIcon.classList.remove('hidden');
        });
        
        // Hide password (eye-slash icon clicked)
        hidePasswordIcon.addEventListener('click', function() {
            // Important: This actually changes the password field back to hide text
            signinpassword.type = 'password';
            
            // Toggle icon visibility
            hidePasswordIcon.classList.add('hidden');
            showPasswordIcon.classList.remove('hidden');
        });
    }
    
    // Close danger alert if present
    const closeDangerAlert = document.getElementById('closedangeralert');
    const dangerAlert = document.getElementById('dangeralert');
    
    if(closeDangerAlert && dangerAlert) {
        closeDangerAlert.addEventListener('click', function() {
            dangerAlert.style.display = 'none';
        });
    }
});
</script>