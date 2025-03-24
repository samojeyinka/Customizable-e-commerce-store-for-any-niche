<?php
session_start();
require_once '../config/connect.php';

// Security checks - prevent direct access and ensure proper flow
if (!isset($_SESSION['admin_setup_id']) || !isset($_SESSION['setup_token'])) {
    $_SESSION['setup_error'] = "Invalid request. Please complete the verification process first.";
    header("Location: ./set-up.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['admin_setup_id'];
    $submitted_token = $_POST['setup_token'] ?? '';
    $stored_token = $_SESSION['setup_token'];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Verify the token matches
    if ($submitted_token !== $stored_token) {
        $_SESSION['setup_error'] = "Invalid security token. Please request a new password setup.";
        
        // Clear invalid session data
        unset($_SESSION['admin_setup_id']);
        unset($_SESSION['setup_token']);
        unset($_SESSION['admin_name']);
        
        header("Location: ./set-up.php");
        exit;
    }
    
    // Validate input
    if (empty($password) || empty($confirm_password)) {
        $_SESSION['setup_error'] = "All fields are required.";
        header("Location: ./set-password.php");
        exit;
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['setup_error'] = "Passwords do not match.";
        header("Location: ./set-password.php");
        exit;
    }
    
    // Password strength validation
    $errors = [];
    
    // Check minimum length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    
    // Check for uppercase letters (at least 3)
    if (preg_match_all('/[A-Z]/', $password) < 3) {
        $errors[] = "Password must contain at least 3 uppercase letters.";
    }
    
    // Check for lowercase letters (at least 3)
    if (preg_match_all('/[a-z]/', $password) < 3) {
        $errors[] = "Password must contain at least 3 lowercase letters.";
    }
    
    // Check for numbers (at least 1)
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least 1 number.";
    }
    
    // Check for special characters (at least 1)
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $errors[] = "Password must contain at least 1 special character.";
    }
    
    // If there are any errors, redirect back with the error message
    if (!empty($errors)) {
        $_SESSION['setup_error'] = implode("<br>", $errors);
        header("Location: ./set-password.php");
        exit;
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Update the password and clear the OTP
    $update_query = "UPDATE administrators SET 
        password_hash = ?,
        otp_code = NULL,
        otp_expires = NULL,
        account_status = 'active'
        WHERE admin_id = ?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, "si", $password_hash, $admin_id);
    $result = mysqli_stmt_execute($update_stmt);
    
    if ($result) {
        // Clear all setup-related session variables
        unset($_SESSION['admin_setup_id']);
        unset($_SESSION['setup_token']);
        unset($_SESSION['setup_email']);
        unset($_SESSION['admin_name']);
        
        // Set success message and redirect to login
        $_SESSION['login_message'] = "Password set successfully. You can now login with your new password.";
        header("Location: ./index.php");
        exit;
    } else {
        // If there was an error updating the database
        $_SESSION['setup_error'] = "There was an error setting your password: " . mysqli_error($con);
        header("Location: ./set-password.php");
        exit;
    }
}

// If not a POST request, redirect back to the form
header("Location: ./set-password.php");
exit;
?>