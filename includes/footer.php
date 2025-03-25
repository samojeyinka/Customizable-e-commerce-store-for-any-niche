<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "victosah";

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !empty($_POST['email'])) {
    // Get and sanitize email
    $email = trim(htmlspecialchars(stripslashes($_POST['email'])));
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = "Invalid email format";
    } else {
        try {
            // Create database connection
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
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
            }
            
            // Check if email already exists
            $checkStmt = $conn->prepare("SELECT id FROM subscriptions WHERE email = :email");
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                // Email already exists
                $response['success'] = true; // Still consider it a success
                $response['message'] = "You are already subscribed!";
            } else {
                // Insert email into database
                $stmt = $conn->prepare("INSERT INTO subscriptions (email) VALUES (:email)");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                
                $response['success'] = true;
                $response['message'] = "Thank you for subscribing!";
            }
            
        } catch(PDOException $e) {
            // Handle any database errors
            $response['success'] = false;
            $response['message'] = "Database error: " . $e->getMessage();
            error_log("Subscription error: " . $e->getMessage());
        }
        
        // Close connection
        $conn = null;
    }
    
    // Make sure there's no output before JSON
    if (ob_get_length()) ob_clean();
    
    // Return JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// Define DOMAIN constant if not already defined
if (!defined('DOMAIN')) {
    define('DOMAIN', '');  // Set to your domain or leave empty for relative paths
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscription</title>
    <style>
        /* Some basic styling to match your design */
        body {
            font-family: 'Open Sans', sans-serif;
        }
        .success-message {
            color: green;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
      
      <footer class="w-full bg-[#E8E9F2] py-7">
            <div class="w-[90%] flex gap-4 flex-col md:flex-row justify-between mx-auto">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-1">
                        <img src="<?php echo DOMAIN; ?>/assets/global/logo.svg" class="w-[50px] h-[48.15px]" />
                        <h1 class="text-[20px] text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            <img src="<?php echo DOMAIN; ?>/assets/global/location.svg" class="w-[24px] h-[24px]" />
                            <p class="text-[15px] text-[16px] font-['Open Sans'] font-regular">Tejuosho Main Complex Yaba.</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="<?php echo DOMAIN; ?>/assets/global/call.svg" class="w-[24px] h-[24px]" />
                            <p class="text-[15px] text-[16px] font-['Open Sans'] font-regular">09125559982</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="<?php echo DOMAIN; ?>/assets/global/mail.svg" class="w-[24px] h-[24px]" />
                            <p class="text-[15px] text-[16px] font-['Open Sans'] font-regular">support@victosah.com</p>
                        </div>

                    </div>

                </div>

                <div class="flex flex-col gap-2">
                    <h1 class="text-[#262626] text-[20px] md:text-[24px] font-['Montserrat'] font-medium">Quick Links</h1>
                    <ul class="flex flex-col gap-2">
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="<?php echo DOMAIN; ?>/details/about-us.php" class="text-[#777777]">About Us</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="<?php echo DOMAIN; ?>/user/orders.php" class="text-[#777777]">Track Your Order</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="<?php echo DOMAIN; ?>/details/refund-and-return-policy.php" class="text-[#777777]">Return Policy</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="<?php echo DOMAIN; ?>/details/contact-us.php" class="text-[#777777]">Contact Us</a></li>

                    </ul>
                </div>

                <div class="flex flex-col gap-2">
                    <h1 class="text-[#262626] text-[20px] md:text-[24px] font-['Montserrat'] font-medium">Get on the List</h1>
                    <p class="text-[#777777] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Sign up to know when we have new products</p>
                    <div id="response-message"></div>
                    <form id="subscription-form" class="flex items-center gap-2 mt-2">
                        <div class="flex items-center gap-2 border-[1px] border-[#B8BBD7] rounded-[4px] p-1">
                            <input type="email" placeholder="Enter your email address"  name="email" 
                                class="lg:w-[12rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                        </div>
                        <button type="submit" class="py-1 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]">
                            Subscribe
                        </button>
                    </form>
                 
                </div>

            </div>
            <div class="w-[90%] flex flex-col py-4 mx-auto">
                <div class="flex flex-col gap-2">
                    <h1 class="text-[#262626] text-[20px] md:text-[24px] font-['Montserrat'] font-medium">Connect with us on:</h1>
                    <div class="flex items-center gap-7">
                        <a href="https://www.facebook.com/victosahsols/" target="_blank"><img src="<?php echo DOMAIN; ?>/assets/global/e1.svg" alt="Search" class="w-[13.83px]" /></a>
                        <a href="https://www.instagram.com/victosahsols/" target="_blank"><img src="<?php echo DOMAIN; ?>/assets/global/e2.svg" alt="Search" class="w-[21.83px]" /></a>
                        <a href="https://wa.me/09125559982" target="_blank"><img src="<?php echo DOMAIN; ?>/assets/global/e3.svg" alt="Search" class="w-[21.83px]" /></a>
                        <a href="#"><img src="<?php echo DOMAIN; ?>/assets/global/e4.svg" alt="Search" class="w-[21.83px]" /></a>
                        <a href="#"><img src="<?php echo DOMAIN; ?>/assets/global/e5.svg" alt="Search" class="w-[17.83px]" /></a>
                        <a href="https://www.x.com/victosahsols/" target="_blank"><img src="<?php echo DOMAIN; ?>/assets/global/e6.svg" alt="Search" class="w-[30.22px]" /></a>
                    </div>
                </div>
                <div class="flex md:items-center flex-col gap-3 md:gap-0 md:flex-row justify-between mt-10">
                    <div class="flex items-center gap-[4rem]">
                        <p class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#777777]">Terms & Conditions</p>
                        <p class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#777777]">Privacy Policy</p>
                    </div>
                    <p class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#777777]">© 2025 Victosah Solutions | All Rights Reserved</p>

                </div>
            </div>

            <a href="https://wa.me/09125559982" target="_blank" class="fixed top-[55%] md:top-[70%] right-5 md:right-10">
            <img src="<?php echo DOMAIN; ?>/assets/global/whatsapp.svg" class="w-[50px] md:w-[60px] rounded-[50%] shadow-lg" />
        </a>
        </footer>
        <!-- ========================  The Footer section ends ======================== -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('subscription-form');
        const responseMessage = document.getElementById('response-message');
        
        if (!form || !responseMessage) {
            console.error('Could not find form or response message element');
            return;
        }
        
        // Add some styling to the response message
        responseMessage.style.padding = '8px';
        responseMessage.style.marginTop = '10px';
        responseMessage.style.borderRadius = '4px';
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            responseMessage.textContent = 'Processing...';
            responseMessage.style.color = '#666';
            responseMessage.style.backgroundColor = '#f8f8f8';
            
            const emailInput = form.querySelector('input[name="email"]');
            const email = emailInput ? emailInput.value : '';
            
            if (!email) {
                responseMessage.textContent = 'Please enter an email address';
                responseMessage.style.color = 'red';
                responseMessage.style.backgroundColor = '#ffeeee';
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('email', email);
            
            // Send AJAX request to the same page
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                
                // Find the JSON part of the response if there's extra content
                let jsonText = text;
                const jsonStart = text.indexOf('{');
                const jsonEnd = text.lastIndexOf('}');
                
                if (jsonStart >= 0 && jsonEnd >= 0 && jsonEnd > jsonStart) {
                    jsonText = text.substring(jsonStart, jsonEnd + 1);
                    console.log('Extracted JSON:', jsonText);
                }
                
                // Try to parse as JSON
                try {
                    const data = JSON.parse(jsonText);
                    console.log('Parsed JSON response:', data);
                    
                    if (data.success) {
                        responseMessage.style.color = 'white';
                        responseMessage.style.backgroundColor = '#4CAF50';
                        form.reset();
                    } else {
                        responseMessage.style.color = 'white';
                        responseMessage.style.backgroundColor = '#F44336';
                    }
                    
                    responseMessage.textContent = data.message;
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    responseMessage.style.color = 'black';
                    responseMessage.style.backgroundColor = '#d4edda';
                    responseMessage.innerHTML = 'Thank you for subscribing to our newsletter!';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                responseMessage.style.color = 'white';
                responseMessage.style.backgroundColor = '#F44336';
                responseMessage.textContent = 'Network error. Please try again later.';
            });
        });
    });
</script>

</body>
</html>