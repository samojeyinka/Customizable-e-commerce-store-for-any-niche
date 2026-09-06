<?php
require_once __DIR__ . '/../../../config/config.php';

$conn = db();

if (isset($_POST['send'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Always generate a fresh OTP for better security
    $otp = rand(1000, 9999);
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // IMPORTANT: First check if the email already exists
    $check_sql = "SELECT * FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email already exists - get the existing user data
        $user = $result->fetch_assoc();
        
        // Check if the account is already verified
        if ($user['status'] == 'active') {
            // User is already registered and verified
            echo "
            <script>
            alert('This email is already registered. Please sign in instead.');
            document.location.href='../login/signin.php';
            </script>
            ";
            exit();
        } else {
            // User exists but hasn't verified yet - use fresh OTP and update
            // No need to use $_POST['otp'] here - we've generated a new one
            
            // Update the existing record with new OTP and timestamp
            $update_sql = "UPDATE users SET otp = ?, otp_send_time = NOW() WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $otp, $email);
            
            if ($update_stmt->execute()) {
                // Store email in session for verification page
                $_SESSION['email'] = $email;
                
                echo "
                <script>
                alert('A new verification code has been saved to your account.');
                document.location.href='verify.php';
                </script>
                ";
            } else {
                echo "
                <script>
                alert('Error updating data: {$conn->error}');
                document.location.href='index.php';
                </script>
                ";
            }
            
            exit(); // Exit after handling existing email
        }
    }
    
    // If email doesn't exist, proceed with new registration
    
    // Store email in session for verification page
    $_SESSION['email'] = $email;
    
    // Hash the password for security before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Modified query to use prepared statement for security
    $sql = "INSERT INTO users (email, password, otp, status, otp_send_time) VALUES (?, ?, ?, 'pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $hashed_password, $otp);
    
    if ($stmt->execute()) {
        // Get the newly inserted ID
        $new_user_id = $conn->insert_id;
        
        // You can store it in session if needed
        $_SESSION['temp_user_id'] = $new_user_id;
        
        echo "
        <script>
        alert('A verification code has been saved to your account.');
        document.location.href='verify.php';
        </script>
        ";
    } else{
        echo "
        <script>
        alert('Error inserting data: {$conn->error}');
        document.location.href='index.php';
        </script>
        ";
    }
}
?>