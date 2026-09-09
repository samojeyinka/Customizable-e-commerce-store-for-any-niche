<?php
require_once "../../../config/config.php";

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: " . DOMAIN . "/user/orders.php");
    exit();
}

// Use the project's existing db() helper from config.php
$conn = db();

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error variable
$error = '';

// Check if there's a login error stored in session first
if(isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    // Clear the error after displaying it
    unset($_SESSION['login_error']);
}

// Process login form submission
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate required fields
    if(empty($email) || empty($password)) {
        $error = "All fields are required";
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
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_status'] = $user['status'];
                    
                    // Store last login time
                    $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    
                    // Redirect to dashboard
                    header("Location: " . DOMAIN . "/user/orders.php");
                    exit();
                } else {
                    // Store email in session for verification page
                    $_SESSION['email'] = $email;
                    $error = "Please verify your account first. <a href='" . DOMAIN . "/includes/auth/verify.php' class='text-[" . store_color('color_primary') . "]'>Verify now</a>";
                }
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}

require_once "../google.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY - Sign In</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../../../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<?php
    include(__DIR__ . '/../../header.php');
    include(__DIR__ . '/../../options.php');
   ?>

<div class="w-full bg-[<?php echo store_color('color_bg'); ?>]">
    <div class="w-[92%] max-w-[480px] mx-auto py-10 md:py-16">
        <div class="bg-white rounded-[20px] border border-[#262626]/[0.07] shadow-[0_30px_70px_-45px_rgba(0,0,0,0.3)] p-6 md:p-10">
            <div class="text-center">
                <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[30px] md:text-[34px] leading-[1.15] font-['Cormorant_Garamond'] font-medium">Welcome Back!</h3>
            </div>

            <?php if(!empty($error)): ?>
            <section id="dangeralert" class="flex flex-col items-start w-full bg-[#FDECEC] border-l-[3px] border-[#EE3F3F] rounded-[10px] py-3 px-4 mt-5 relative overflow-hidden">
                <div class="flex items-center gap-2 w-full">
                    <img src="<?php echo DOMAIN; ?>/assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[18px] cursor-pointer" />
                    <p class="text-[15px] text-[#2C2C2C] font-['Open_Sans'] font-semibold">
                        Login Error
                    </p>
                </div>
                <p class="text-[13px] text-start w-full font-['Open_Sans'] text-[#7F7F7F] mt-1.5 pl-7 pr-2">
                    <?php echo $error; ?>
                </p>
            </section>
            <?php endif; ?>

            <form action="" method="POST" class="flex flex-col gap-4 md:gap-5 pt-6">
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/80">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Enter your email address"
                        require
                        class="w-full font-['Open_Sans'] bg-white outline-none border border-[#262626]/10 text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 px-4 text-[14px] md:text-[15px] rounded-[10px] focus:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/80">Password</label>

                    <div class="flex items-center gap-1 bg-white border border-[#262626]/10 rounded-[10px] pl-4 pr-2 focus-within:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">
                        <input
                            type="password"
                            name="password"
                            id="mspassword"
                            placeholder="Enter your password"
                            required
                            class="w-full font-['Open_Sans'] bg-transparent outline-none text-[#2C2C2C] placeholder:text-[#B8BBD7] py-3 text-[14px] md:text-[15px]" />
                        <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="togglePassword" class="w-[18px] cursor-pointer opacity-50 hover:opacity-100 transition-opacity" alt="Show password" />
                    </div>
                </div>

                <div class="flex justify-between items-center gap-3 pt-1">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-[<?php echo store_color('color_primary'); ?>]" />
                        <label for="remember" class="font-['Open_Sans'] text-[13px] text-[#2C2C2C]">Remember me</label>
                    </div>
                    <a href="<?php echo DOMAIN; ?>/includes/auth/password-reset/mail.php" class="font-['Montserrat'] text-[11px] tracking-[0.06em] uppercase font-semibold text-[<?php echo store_color('color_primary'); ?>] hover:underline">Forgot password?</a>
                </div>

                <button type="submit" name="login" value="1" class="w-full py-3.5 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] md:text-[13px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] hover:shadow-[0_14px_30px_-12px_rgba(0,0,0,0.3)] transition-all duration-300">
                    Sign In
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
                    Sign in with Google
                </span>
            </a>

            <p class="text-center font-['Open_Sans'] text-[14px] text-[#7A7A7A] mt-6">
                Don't have an account? <a href="../create-account/sign-up.php" class="font-['Montserrat'] font-semibold text-[<?php echo store_color('color_primary'); ?>]">Create an account</a>
            </p>
        </div>
    </div>
</div>

<?php
    include(__DIR__ . '/../../footer.php');
?>

<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the close button and alert elements
    const closeAlert = document.getElementById('closedangeralert');
    const dangerAlert = document.getElementById('dangeralert');
    
    // Check if both elements exist before adding the event listener
    if (closeAlert && dangerAlert) {
        closeAlert.addEventListener('click', function() {
            dangerAlert.style.display = 'none';
        });
    }
    
    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    const mspassword = document.getElementById('mspassword');
    
    if (toggleBtn && mspassword) {
        toggleBtn.addEventListener('click', function() {
            // Toggle password visibility
            if (mspassword.type === 'password') {
                mspassword.type = 'text';
                // Change to eye-slash.svg
                this.src = '<?php echo DOMAIN; ?>/assets/global/eye.svg';
            } else {
                mspassword.type = 'password';
                // Change back to eye.svg
                this.src = '<?php echo DOMAIN; ?>/assets/global/eye-slash.svg';
            }
        });
    }
});
</script>

</body>
</html>