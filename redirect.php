<?php
require_once "./config/config.php";
require_once "./config/servername.php";
require_once "./vendor/autoload.php";

session_start();

// Initialize Google Client
$client = new Google\Client;
$client->setClientId("763211292189-o7vk7n690hbj637d4rb63hguebfd96pd.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/victosah/redirect.php");

// Check if authorization code is provided
if (!isset($_GET["code"])) {
    // Redirect to login page if no code
    header("Location: " . DOMAIN . "/auth/login/signin.php");
    exit("Login failed");
}

try {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
    
    // Check if we received an error in the token response
    if (isset($token['error'])) {
        error_log("Google OAuth Token Error: " . $token['error']);
        header("Location: " . DOMAIN . "/auth/login/signin.php?error=token_" . urlencode($token['error']));
        exit();
    }
    
    $client->setAccessToken($token["access_token"]);
    
    // Get user info from Google
    $oauth = new Google\Service\Oauth2($client);
    $userinfo = $oauth->userinfo->get();
    
    // Extract email (the only field we need based on your database structure)
    $email = $userinfo->email;
    
    // Connect to database
    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Check if the user already exists
    $checkUserSql = "SELECT * FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkUserSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // User exists, get their data
        $user = $result->fetch_assoc();
        
        // Update last login time
        $updateLoginSql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateLoginSql);
        $updateStmt->bind_param("i", $user['id']);
        $updateStmt->execute();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_status'] = $user['status'];
        
    } else {
        // User doesn't exist, create a new account
        // Generate a random password for the account
        $randomPassword = bin2hex(random_bytes(12));
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
        
        // Get IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        // Insert new user - only using fields that exist in your database
        $insertSql = "INSERT INTO users (email, password, status, ip, otp_send_time, verified_at, last_login) 
                      VALUES (?, ?, 'active', ?, NOW(), NOW(), NOW())";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sss", $email, $hashedPassword, $ip_address);
        
        if ($insertStmt->execute()) {
            // Get the newly created user ID
            $userId = $conn->insert_id;
            
            // Set session variables
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_status'] = 'active';
        } else {
            // If insert fails, log the error and redirect
            error_log("SQL Error: " . $insertStmt->error);
            header("Location: " . DOMAIN . "/auth/create-account/signup.php?error=registration");
            exit();
        }
    }
    
    // Close connection
    $conn->close();
    
    // Redirect to user dashboard
    header("Location: " . DOMAIN . "/user/orders.php");
    exit();
    
} catch (Exception $e) {
    // Handle any exceptions
    error_log("Google OAuth Error: " . $e->getMessage());
    header("Location: " . DOMAIN . "/auth/login/signin.php?error=google_auth&message=" . urlencode($e->getMessage()));
    exit();
}
?>