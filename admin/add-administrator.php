// add-administrator.php
<?php
session_start();
require_once 'db_connection.php';

// Check if logged in and has appropriate permissions
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: /admin/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $full_name = trim($_POST['full_name']);
    $phone_number = trim($_POST['phone_number']);
    $role = trim($_POST['role']);
    
    // Validate data
    if (empty($email) || empty($full_name) || empty($role)) {
        $_SESSION['admin_error'] = "All required fields must be filled.";
        header("Location: /admin/add-administrator.php");
        exit;
    }
    
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT admin_id FROM administrators WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        $_SESSION['admin_error'] = "Email address already in use.";
        header("Location: /admin/add-administrator.php");
        exit;
    }
    
    // Handle profile photo upload if present
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profile_photos/';
        $file_extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['admin_error'] = "Only JPG, JPEG and PNG files are allowed.";
            header("Location: /admin/add-administrator.php");
            exit;
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
            $profile_photo = $filename;
        }
    }
    
    // Insert new administrator
    $insertStmt = $pdo->prepare("INSERT INTO administrators (
        email, full_name, phone_number, role, profile_photo, created_by, account_status
    ) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    
    $result = $insertStmt->execute([
        $email, 
        $full_name, 
        $phone_number, 
        $role, 
        $profile_photo,
        $_SESSION['admin_id'] // Current admin is the creator
    ]);
    
    if ($result) {
        $_SESSION['admin_message'] = "Administrator added successfully. They will receive an email to set up their password.";
        
        // Generate OTP for the new admin
        $admin_id = $pdo->lastInsertId();
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $updateStmt = $pdo->prepare("UPDATE administrators SET otp_code = ?, otp_expires = ? WHERE admin_id = ?");
        $updateStmt->execute([$otp, $expires, $admin_id]);
        
        // Send email with OTP
        $subject = "Your Administrator Account Setup";
        $message = "Hello {$full_name},\n\n";
        $message .= "An administrator account has been created for you on our system.\n\n";
        $message .= "Your OTP code to set up your password is: {$otp}\n\n";
        $message .= "Please visit " . SITE_URL . "/admin/request-password-setup.php to complete your account setup.\n\n";
        $message .= "This code will expire in 24 hours.";
        
        mail($email, $subject, $message);
        
        header("Location: /admin/manage-administrators.php");
        exit;
    } else {
        $_SESSION['admin_error'] = "Error adding administrator.";
        header("Location: /admin/add-administrator.php");
        exit;
    }
}

// Display the form
require_once 'add-administrator-form.php';
?>