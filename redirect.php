<?php
require_once "./config/config.php";
require_once "./config/servername.php";
require_once "./vendor/autoload.php";

session_start();


$client = new Google\Client;
$client->setClientId("763211292189-o7vk7n690hbj637d4rb63hguebfd96pd.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri(DOMAIN . "/redirect.php");



if (!isset($_GET["code"])) {

    header("Location: " . DOMAIN . "/index.php");
    exit("Login failed");
}

try {
 
    $token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
    

    if (isset($token['error'])) {
        error_log("Google OAuth Token Error: " . $token['error']);
        header("Location: " . DOMAIN . "/auth/login/signin.php?error=token_" . urlencode($token['error']));
        exit();
    }
    
    $client->setAccessToken($token["access_token"]);
    

    $oauth = new Google\Service\Oauth2($client);
    $userinfo = $oauth->userinfo->get();
    
 
    $email = $userinfo->email;
    
    $con = db();
    
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    

    $checkUserSql = "SELECT * FROM users WHERE email = ?";
    $checkStmt = $con->prepare($checkUserSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();
        
  
        $updateLoginSql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $updateStmt = $con->prepare($updateLoginSql);
        $updateStmt->bind_param("i", $user['id']);
        $updateStmt->execute();
        
   
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_status'] = $user['status'];
        
    } else {

        $randomPassword = bin2hex(random_bytes(12));
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
        
     
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
 
        $insertSql = "INSERT INTO users (email, password, status, ip, otp_send_time, verified_at, last_login) 
                      VALUES (?, ?, 'active', ?, NOW(), NOW(), NOW())";
        $insertStmt = $con->prepare($insertSql);
        $insertStmt->bind_param("sss", $email, $hashedPassword, $ip_address);
        
        if ($insertStmt->execute()) {
           
            $userId = $con->insert_id;
            
     
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_status'] = 'active';
        } else {

            error_log("SQL Error: " . $insertStmt->error);
            header("Location: " . DOMAIN . "/index.php?error=registration");
            exit();
        }
    }
    

    
    

    header("Location: " . DOMAIN . "/user/profile.php");
    exit();
    
} catch (Exception $e) {

    error_log("Google OAuth Error: " . $e->getMessage());
    header("Location: " . DOMAIN . "/index.php?error=google_auth&message=" . urlencode($e->getMessage()));
    exit();
}
?>