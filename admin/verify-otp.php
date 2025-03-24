<?php
session_start();
require_once '../config/connect.php';

// Redirect if no email is stored in session
if (!isset($_SESSION['setup_email'])) {
    $_SESSION['setup_error'] = "Please request an OTP first.";
    header("Location: ./set-up.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect individual digits and combine them
    $digit1 = isset($_POST['digit1']) ? trim($_POST['digit1']) : '';
    $digit2 = isset($_POST['digit2']) ? trim($_POST['digit2']) : '';
    $digit3 = isset($_POST['digit3']) ? trim($_POST['digit3']) : '';
    $digit4 = isset($_POST['digit4']) ? trim($_POST['digit4']) : '';
    $digit5 = isset($_POST['digit5']) ? trim($_POST['digit5']) : '';
    $digit6 = isset($_POST['digit6']) ? trim($_POST['digit6']) : '';
    
    // Combine digits into a single OTP
    $otp = $digit1 . $digit2 . $digit3 . $digit4 . $digit5 . $digit6;
    
    // For debugging
    // error_log("Submitted OTP: " . $otp);
    
    $email = $_SESSION['setup_email'];
    
    // Validate OTP format
    if (empty($otp) || strlen($otp) != 6 || !ctype_digit($otp)) {
        $_SESSION['setup_error'] = "Please enter a valid 6-digit OTP code.";
        header("Location: ./verify-otp-form.php");
        exit;
    }
    
    // Check if OTP exists and is valid
    $check_query = "SELECT admin_id, full_name, otp_code, otp_expires FROM administrators WHERE email = ?";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) === 0) {
        $_SESSION['setup_error'] = "Invalid request. Please start over.";
        header("Location: ./set-up.php");
        exit;
    }
    
    $admin = mysqli_fetch_assoc($result);
    
    // For debugging - compare stored OTP with submitted OTP
    // error_log("Stored OTP: " . $admin['otp_code']);
    
    // Check if OTP matches
    if ($admin['otp_code'] !== $otp) {
        $_SESSION['setup_error'] = "Invalid OTP code. Please try again.";
        header("Location: ./verify-otp-form.php");
        exit;
    }
    
    // Check if OTP has expired
    $now = new DateTime();
    $expires = new DateTime($admin['otp_expires']);
    
    if ($now > $expires) {
        $_SESSION['setup_error'] = "OTP has expired. Please request a new one.";
        header("Location: ./set-up.php");
        exit;
    }
    
    // OTP is valid, store admin ID in session and redirect to password setup
    $_SESSION['admin_setup_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['full_name'];
    
    // Create a session token for added security
    $_SESSION['setup_token'] = bin2hex(random_bytes(32));
    
    // Redirect to the password setup page
    header("Location: ./set-password.php");
    exit;
}

// If not a POST request, redirect to the form
header("Location: ./verify-otp-form.php");
exit;
?>