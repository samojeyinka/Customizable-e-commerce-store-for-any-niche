<?php
session_start();
require_once '../config/connect.php';  // This gives you $con, not $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // First check if email exists at all
    $check_query = "SELECT admin_id, full_name, password_hash FROM administrators WHERE email = ?";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        // Email doesn't exist in the database
        $_SESSION['setup_error'] = "Email not found. Please contact an administrator.";
        header("Location: ./set-up.php");
        exit;
    }
    
    $admin = mysqli_fetch_assoc($check_result);
    
    // Check if password is already set
    if (!empty($admin['password_hash'])) {
        // Password already set, redirect to login
        $_SESSION['login_message'] = "Password already set. Please login.";
        header("Location: ./index.php");
        exit;
    }
    
    // If we reach here, email exists and password is not set
    // Generate a 6-digit OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));
    $expires = date('Y-m-d H:i:s', strtotime('+15 minutes')); // OTP expires in 15 minutes
    
    // Save OTP to database
    $update_query = "UPDATE administrators SET otp_code = ?, otp_expires = ? WHERE admin_id = ?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssi", $otp, $expires, $admin['admin_id']);
    mysqli_stmt_execute($update_stmt);
    
    // Send email with OTP
    $to = $email;
    $subject = "Your Password Setup OTP Code";
    $message = "Hello {$admin['full_name']},\n\n";
    $message .= "Your OTP code to set up your administrator password is: {$otp}\n\n";
    $message .= "This code will expire in 15 minutes.\n\n";
    $message .= "If you did not request this, please ignore this email.";
    
    mail($to, $subject, $message);
    
    // Store email in session for the next step
    $_SESSION['setup_email'] = $email;
    
    $_SESSION['setup_message'] = "OTP has been sent to your email. Please check and enter below.";
    header("Location: verify-otp-form.php");
    exit;
}
?>