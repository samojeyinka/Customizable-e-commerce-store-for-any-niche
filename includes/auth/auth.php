<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication to access a page
 * Redirects to login page if not authenticated
 * 
 * @param string $redirect_url URL to redirect to if not authenticated
 * @return void
 */
function requireAuth($redirect_url = '../includes/auth/login/signin.php') {
    if (!isAuthenticated()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Require guest (not authenticated) to access a page
 * Redirects to dashboard if already authenticated
 * 
 * @param string $redirect_url URL to redirect to if authenticated
 * @return void
 */
function requireGuest($redirect_url = 'dashboard.php') {
    if (isAuthenticated()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Get current user data from database
 * 
 * @return array|false User data as associative array, or false if not found
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return false;
    }
    
 

    //db connection
    $servername = "localhost";
    $dbname = "victosah";
    $username = "root";
    $dbpassword = "";




    $conn = new mysqli($servername, $username, $dbpassword, $dbname);

    if($conn->connect_error){
        return false;
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Logout current user
 * 
 * @param string $redirect_url URL to redirect to after logout
 * @return void
 */
function logout($redirect_url = DOMAIN . '/includes/auth/login/signin.php') {
    // Clear all session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: $redirect_url");
    exit();
}