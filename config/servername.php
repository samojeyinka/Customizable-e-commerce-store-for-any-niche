<?php
// Database configuration
$servername = "localhost";
$dbname = "victosah";
$username = "root";
$dbpassword = "";

// Create connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $dbpassword);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // You can customize error handling as needed
    die("Connection failed: " . $e->getMessage());
}

// Alternative connection using mysqli if you prefer:
/*
$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
*/
?>