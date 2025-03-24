<?php
require_once "../../../config/config.php";
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: " . DOMAIN . "/user/orders.php");
    exit();
}

$servername = "localhost";
$dbname = 'victosah';
$username = 'root';
$password = '';

$conn = new mysqli($servername, $username, $password, $dbname);

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
                    $error = "Please verify your account first. <a href='" . DOMAIN . "/includes/auth/verify.php' class='text-[#1A237E]'>Verify now</a>";
                }
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH - Sign In</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../style.css" />
    <link rel="stylesheet" href="../../../styles/faq.css" />
    <link rel="stylesheet" href="../../../styles/modal.css">
    <link rel="stylesheet" href="../../../styles/tabs.css">
    <link rel="stylesheet" href="../../../styles/inputs.css">
</head>
<body>
<?php
    include(__DIR__ . '/../../header.php');
    include(__DIR__ . '/../../options.php');
?>

<div class="w-full bg-[#FEFEFE]">
    <div class="md:w-[50%] mx-auto p-4 bg-white border border-[1px] border-[#EFEFEF] my-5 rounded-md">
        <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Welcome Back!</h3>
        
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
        
        <form action="" method="POST" class="flex flex-col gap-4 pt-4">
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
                    require
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
                        id="mspassword"
                        placeholder="Enter your password"
                        required
                        class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" id="togglePassword" class="w-[24px] cursor-pointer" />
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4" />
                    <label for="remember" class="font-['Open Sans'] text-[14px] text-[#2C2C2C]">Remember me</label>
                </div>
                <a href="<?php echo DOMAIN; ?>/includes/auth/password-reset/mail.php" class="font-['Open Sans'] text-[14px] text-[#1A237E]">Forgot password?</a>
            </div>
            
            <button type="submit" name="login" value="1" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">
                Sign In
            </button>
        </form>
        
        <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
            Or
        </p>
        
        <div class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
            <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[20px]" />
            <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#262626] py-3">
                Sign in with Google
            </p>
        </div>
        
        <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#7A7A7A] mt-4">
            Don't have an account? <a href="../create-account/sign-up.php" class="text-[#1A237E]">Create an account</a>
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