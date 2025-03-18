<?php
// Turn on error reporting to see all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "victosah";

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// First check if we're getting a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email from form submission
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    
    // Log the received email for debugging
    $response['debug_email'] = $email;
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format";
    } else {
        try {
            // Create database connection
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if subscriptions table exists
            $stmt = $conn->query("SHOW TABLES LIKE 'subscriptions'");
            if ($stmt->rowCount() == 0) {
                // Create subscriptions table if it doesn't exist
                $conn->exec("CREATE TABLE IF NOT EXISTS subscriptions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('active', 'unsubscribed') DEFAULT 'active'
                )");
                $response['debug_table_created'] = true;
            }
            
            // Prepare SQL statement to insert email
            $stmt = $conn->prepare("INSERT INTO subscriptions (email) VALUES (:email)");
            
            // Bind parameters
            $stmt->bindParam(':email', $email);
            
            // Execute the statement
            $stmt->execute();
            
            // Check if a new record was inserted
            $response['success'] = true;
            $response['message'] = "Thank you for subscribing!";
            
        } catch(PDOException $e) {
            // Check if the error is a duplicate entry error
            if ($e->getCode() == 23000) {
                $response['success'] = true;
                $response['message'] = "You are already subscribed.";
            } else {
                $response['message'] = "Database error: " . $e->getMessage();
                $response['error_code'] = $e->getCode();
            }
        }
        
        // Close connection
        $conn = null;
    }
} else {
    $response['message'] = "Invalid request method. Expected POST, got " . $_SERVER["REQUEST_METHOD"];
}

// Make sure nothing else is output before the JSON
// Clear any previous output
if (ob_get_length()) ob_clean();

// Send headers - IMPORTANT to prevent HTML output
header('Content-Type: application/json');
// Add cross-origin headers to be safe
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Output JSON only, nothing else
echo json_encode($response);
exit;
?>