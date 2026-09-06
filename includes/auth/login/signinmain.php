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
    <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Welcome Back!</h3>
    
    <!-- Show error message if exists -->
    <?php if(!empty($error)): ?>
    <section id="dangeralert" class="flex flex-col items-center w-full bg-[#FDECEC] shadow-lg mt-2 py-3 px-4 rounded relative overflow-hidden">
        <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
        <div class="flex items-center gap-2 mr-auto">
            <img src="<?php echo DOMAIN; ?>/assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[24px] cursor-pointer" />
            <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                Login Error
            </p>
        </div>
        <p class="text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[3rem] pr-3">
            <?php echo $error; ?>
        </p>
    </section>
    <?php endif; ?>

    <form method="POST" class="flex flex-col gap-4 pt-4">
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
                required
                placeholder="Enter your email address"
                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
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
            id="signinpassword"
            placeholder="Enter your password"
            required
            class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
        <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="showPasswordIcon" class="w-[24px] cursor-pointer" />
        <img src="<?php echo DOMAIN; ?>/assets/global/eye.svg" id="hidePasswordIcon" class="w-[24px] cursor-pointer hidden" />
    </div>
</div>

 <a href="<?php echo DOMAIN; ?>/includes/auth/login/signin.php" class="text-[14px] font-['Open Sans'] text-[#C2185B] font-regular cursor-pointer">Forgot Password</a>

        <button type="submit" name="login" class="w-full py-[8px] px-3 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Sign In</button>
    </form>

    <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
        Or
    </p>

    <a href="<?= $url ?>" class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
        <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[20px]" />
        <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#262626] py-3">
            Sign In  with Google
        </p>
    </a>
</div>

<!-- Simple JavaScript for password toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const showPasswordIcon = document.getElementById('showPasswordIcon');
    const hidePasswordIcon = document.getElementById('hidePasswordIcon');
    const signinpassword = document.getElementById('signinpassword');
    
    if(showPasswordIcon && hidePasswordIcon && password) {
        // Show password (eye icon clicked)
        showPasswordIcon.addEventListener('click', function() {
            // Debug
            console.log('Show password clicked');
            
            // Important: This actually changes the password field to show text
            signinpassword.type = 'text';
            
            // Toggle icon visibility
            showPasswordIcon.classList.add('hidden');
            hidePasswordIcon.classList.remove('hidden');
        });
        
        // Hide password (eye-slash icon clicked)
        hidePasswordIcon.addEventListener('click', function() {
            // Debug
            console.log('Hide password clicked');
            
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