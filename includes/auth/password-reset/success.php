<?php
require_once "../../../config/config.php";

// Start session
session_start();

// IMPORTANT: We now accept either reset email variable
// This ensures compatibility with both new and old code
if(!isset($_SESSION['reset_email']) && !isset($_SESSION['user_reset_email'])) {
    header("Location: " . DOMAIN . "/includes/auth/password-reset/mail.php");
    exit();
}

// Clean up any remaining session variables for password reset
// This is a good place to clear all reset-related session data
if(isset($_SESSION['reset_email'])) {
    unset($_SESSION['reset_email']);
}
if(isset($_SESSION['user_reset_email'])) {
    unset($_SESSION['user_reset_email']);
}
if(isset($_SESSION['user_new_password'])) {
    unset($_SESSION['user_new_password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH - PASSWORD RESET SUCCESSFUL</title>
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

<div class="w-full">
    <form class="w-[95%] md:w-[50%] mx-auto p-4 bg-white border border-[1px] border-[#EFEFEF] my-5 rounded-md relative flex flex-col gap-2 items-center">
        <img src="<?php echo DOMAIN; ?>/assets/global/success.svg" alt="back" class="" />
      
        <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
            Password Reset Successful
        </p>
        <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            You have successfully reset your password
        </p>

        <a href="<?php echo DOMAIN; ?>/includes/auth/login/signin.php" class="text-center w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer"
            id="closeprsucces">
            Sign In
        </a>
    </form>
</div>

<?php
    include(__DIR__ . '/../../footer.php');
?>

<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

</body>
</html>