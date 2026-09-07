<?php
session_start();
require_once "../config/config.php";

// If admin is already logged in, redirect to dashboard
if(isset($_SESSION['admin_id'])) {
    header("Location: " . DOMAIN . "/admin/dashboard/overview.php");
    exit();
}

// Initialize error message
$error_message = "";

// Process login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Debug info - uncomment for troubleshooting
    // echo "Attempting to log in with email: $email and password: $password<br>";
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password";
    } else {
        require_once "../config/servername.php";
        
        $conn = db();
        
        // Check if admin exists - NOTE: Updated table name from 'admin' to 'administrators'
        $sql = "SELECT * FROM administrators WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Debug info - uncomment for troubleshooting
            // echo "Found admin account. Stored hash: " . $admin['password_hash'] . "<br>";
            
            // Check status first - NOTE: Updated 'status' to 'account_status'
            if ($admin['account_status'] !== 'active') {
                $error_message = "This account is inactive. Please contact the administrator.";
            } else {
                // Verify password - NOTE: Updated 'password' to 'password_hash'
                // Debug info - uncomment for troubleshooting
                // echo "Verifying password...<br>";
                // echo "Result of verification: " . (password_verify($password, $admin['password_hash']) ? "TRUE" : "FALSE") . "<br>";
                
if (password_verify($password, $admin['password_hash'])) {
                    // Store admin details in session and log in
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_fullname'] = $admin['full_name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    
                    // Update last login time
                    $conn->query("UPDATE administrators SET last_login = NOW() WHERE admin_id = " . (int)$admin['admin_id']);
                    
                    header("Location: " . DOMAIN . "/admin/dashboard/overview.php");
                    exit();
                } else {
                    $error_message = "Invalid email or password";
                }
            }
        } else {
            $error_message = "Invalid email or password";
        }
        
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Sign In</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <?php if (isset($_SESSION['login_message'])): ?>
    <div class="login-message" id="loginMessage"><?php echo $_SESSION['login_message']; unset($_SESSION['login_message']); ?></div>
    <?php endif; ?>
    
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">

        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between">
            <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">WELCOME BACK</h3>

            <div class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
            </div>
        </div>
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-['Open Sans'] font-medium pt-5">ADMIN PANEL</h3>

        <?php if (!empty($error_message)): ?>
        <section id="dangeralert" class="flex flex-col items-center w-full bg-[#FDECEC] shadow-lg mt-2 py-3 px-4 rounded relative overflow-hidden">
            <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <div class="flex items-center gap-2 mr-auto">
                <img src="<?php echo DOMAIN; ?>/assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[24px] cursor-pointer" />
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                    Authentication Error
                </p>
            </div>
            <p class="text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[3rem] pr-3 ">
                <?php echo $error_message; ?>
            </p>
        </section>
        <?php endif; ?>

        <form method="POST" action="" class="flex flex-col gap-4 pt-4">
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
                    placeholder="Enter your email address"
                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"
                    required />
            </div>

            <div class="flex flex-col gap-1">
                <label
                    for="password"
                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                    Password
                </label>

<div class="flex items-center border-[1px] border-[#E1E1E1] rounded-[8px] pr-3 focus-within:border-[#C2185B] focus-within:ring-2 focus-within:ring-[#C2185B]/10">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] pl-2 pr-1 text-[14px] md:text-[16px]"
                        required />
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="toggleEye" class="w-[22px] md:w-[24px] shrink-0 cursor-pointer" data-target="password" />
                </div>
            </div>

            <a href="./forgotten-password.php" class='text-[14px] font-["Open Sans"] text-[#777777] font-regular cursor-pointer'>Forgot Password?</a>

            <input type="hidden" name="signin" value="1">
            <button type="submit" class="w-full py-[8px] px-3 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Sign In</button>
        </form>


        <a href="./set-up.php" class='text-[14px] font-["Open Sans"] text-blue-700 font-regular cursor-pointer mt-5'>Finish Account Setup</a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        const passwordInput = document.getElementById('password');
        const toggleEye = document.getElementById('toggleEye');
        
        if (toggleEye) {
            toggleEye.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleEye.src = '<?php echo DOMAIN; ?>/assets/global/eye.svg';
                } else {
                    passwordInput.type = 'password';
                    toggleEye.src = '<?php echo DOMAIN; ?>/assets/global/eye-slash.svg';
                }
            });
        }
        
        // Close danger alert
        const closeAlert = document.getElementById('closedangeralert');
        const dangerAlert = document.getElementById('dangeralert');
        
        if (closeAlert && dangerAlert) {
            closeAlert.addEventListener('click', function() {
                dangerAlert.style.display = 'none';
            });
        }
        
        // Auto-hide login message after 5 seconds
        const loginMessage = document.getElementById('loginMessage');
        if (loginMessage) {
            setTimeout(function() {
                loginMessage.style.opacity = '0';
                loginMessage.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    loginMessage.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });
    </script>
</body>
</html>