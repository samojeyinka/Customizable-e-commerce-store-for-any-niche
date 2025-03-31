<?php
session_start(); // Start session at the beginning

// PHPMailer namespace declarations
require '../includes/auth/create-account/phpmailer/src/Exception.php';
require '../includes/auth/create-account/phpmailer/src/PHPMailer.php';
require '../includes/auth/create-account/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Process the form only if the send button is clicked
if (isset($_POST['send'])) {
    // Get form data and sanitize inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars($_POST['name']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $message = htmlspecialchars($_POST['message']);
    $subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : 'Message from VICTOSAH';
    
    // Record IP address for logging
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Validate input (basic validation)
    if (empty($email) || empty($name) || empty($message)) {
        $_SESSION['contact_status'] = 'error';
        $_SESSION['contact_message'] = 'Please fill all required fields.';
        header('Location: contact.php'); // Redirect back to the contact page
        exit();
    }
    
    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samuelojeyinka@gmail.com';
        $mail->Password   = 'teir bvqp ijrx rijl'; // Use App Password for Gmail
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port       = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL/TLS
    $mail->Port = 465;
        $mail->Timeout    = 60;
        $mail->SMTPKeepAlive = true;
        
        // SSL options to bypass verification issues
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Sender and recipient
        $mail->setFrom('samuelojeyinka@gmail.com', 'Victosah Contact Form');
        $mail->addAddress('samuelojeyinka@gmail.com', 'Victosah Admin'); // The recipient's email
        
        // Add reply-to so you can easily reply to the sender
        $mail->addReplyTo($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Create a nicely formatted HTML email body
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;'>
            <h2 style='color: #1A237E; text-align: center;'>New Contact Form Submission</h2>
            <p style='font-size: 16px; line-height: 1.5;'><strong>From:</strong> {$name}</p>
            <p style='font-size: 16px; line-height: 1.5;'><strong>Email:</strong> {$email}</p>
            <p style='font-size: 16px; line-height: 1.5;'><strong>Phone:</strong> {$phoneNumber}</p>
            <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p style='font-size: 16px; line-height: 1.5;'><strong>Message:</strong></p>
                <p style='font-size: 16px; line-height: 1.5;'>{$message}</p>
            </div>
            <p style='font-size: 14px; color: #666; margin-top: 20px;'>This message was sent from IP: {$ip_address} at " . date('Y-m-d H:i:s') . "</p>
        </div>
        ";
        
        // Plain text version for email clients that don't support HTML
        $mail->AltBody = "From: {$name}\nEmail: {$email}\nPhone: {$phoneNumber}\n\nMessage:\n{$message}\n\nSent from IP: {$ip_address}";

        // Send the email
        if ($mail->send()) {
            // Optional: Log the contact in a database
            // saveContactToDatabase($name, $email, $phoneNumber, $message, $ip_address);
            
            $_SESSION['contact_status'] = 'success';
            $_SESSION['contact_message'] = 'Your message has been sent successfully. We will contact you soon.';
        } else {
            $_SESSION['contact_status'] = 'error';
            $_SESSION['contact_message'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        $_SESSION['contact_status'] = 'error';
        $_SESSION['contact_message'] = 'An error occurred: ' . $e->getMessage();
    }
    
    // Redirect back to the contact page
    header('Location: contact-us.php');
    exit();
} else {
    // If someone tries to access this file directly without submitting the form
    header('Location: contact-us.php');
    exit();
}

/**
 * Save contact form submission to database (optional)
 * Uncomment and implement if you want to store submissions in a database
 */
/*
function saveContactToDatabase($name, $email, $phone, $message, $ip) {
    global $conn;
    
    $sql = "INSERT INTO contact_submissions (name, email, phone, message, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $phone, $message, $ip);
    $stmt->execute();
}
*/
?>