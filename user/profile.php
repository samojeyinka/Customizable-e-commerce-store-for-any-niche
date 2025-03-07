<?php
require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';

// Now require the profile manager
require_once './profile-manager.php';

// Authentication check
requireAuth();


// Get user data
$user = getCurrentUser();
$userId = $user['id'];

// Make sure $conn is defined before passing it to ProfileManager
if (!isset($conn)) {
    // Create a connection if not already defined
    $dbHost = "localhost";     
    $dbUsername = "root";      
    $dbPassword = "";          
    $dbName = "victosah";      
    
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Initialize profile manager with the database connection
$profileManager = new ProfileManager($conn);
$profile = $profileManager->getProfileByUserId($userId);


// Find where you define $successMessage (usually near the beginning of the file)
// Replace this:
$successMessage = '';
$errorMessage = '';

// With this:
$successMessage = '';
$errorMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message
}

// Find this section in your profilet.php file:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process profile data update
    $profileData = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'country' => $_POST['country'] ?? 'Nigeria',
        'address' => $_POST['address'] ?? '',
        'state' => $_POST['state'] ?? '',
        'city' => $_POST['city'] ?? '',
        'zip_code' => $_POST['zip_code'] ?? '',
        'billing_same_as_delivery' => isset($_POST['billing_same_as_delivery']) ? $_POST['billing_same_as_delivery'] : '',
        'billing_first_name' => $_POST['billing_first_name'] ?? '',
        'billing_last_name' => $_POST['billing_last_name'] ?? '',
        'billing_country' => $_POST['billing_country'] ?? 'Nigeria',
        'billing_address' => $_POST['billing_address'] ?? '',
        'billing_state' => $_POST['billing_state'] ?? '',
        'billing_city' => $_POST['billing_city'] ?? '',
        'billing_zip_code' => $_POST['billing_zip_code'] ?? ''
    ];
    
    $result = $profileManager->updateProfile($userId, $profileData);
    
    if ($result) {
        // Set a success message in the session
        $_SESSION['success_message'] = "Profile updated successfully!";
        
        // Redirect back to the same page, but as a GET request
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $errorMessage = "Error updating profile. Please try again.";
    }
}

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | My Profile</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />
    <style>
        body {
            overflow-x: hidden;
        }
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
<?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
        ?>
    <main class="bg-[#FEFEFE]">
        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Profile</span>
                </div>
            </div>
        </section>

        <div class="w-[95%] md:w-[90%] mx-auto flex flex-col gap-3 py-5">
            <?php if(!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <div class="flex items-center gap-2">
                <div class="w-[50px] h-[50px] md:w-[60px] md:h-[60px] rounded-[50%]">
                    <img src="<?php echo DOMAIN; ?>/assets/user/avatar.svg" alt="Profile Picture" class="w-full h-full" />
                </div>

                <h1 class="text-[15px] md:text-[16px] font-Onest font-medium"><?php echo htmlspecialchars($user['email']); ?></h1>
            </div>

            <section class="flex flex-col items-center w-full bg-[#EEE7FF] py-3 px-4 rounded">
                <div class="flex items-center gap-2 mr-auto">
                    <img src="<?php echo DOMAIN; ?>/assets/user/info.svg" alt="Profile Picture" class="w-[24px]" />
                    <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                        Need for your information
                    </p>
                </div>
                <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[4rem] pr-3 md:pr-0">
                    For a smoother checkout experience, your billing and contact address will be automatically filled based on your saved details. You can update or change them if needed during checkout
                </p>
            </section>

            <form method="POST" action="">
                <div class="w-full md:w-[95%] lg:w-[70%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 mb-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex md:items-center gap-2 md:gap-0 flex-col-reverse md:flex-row justify-between">
                            <div class="flex flex-col items-center">
                                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                                    Contact Info
                                </p>
                                <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                    We'll use this email to send you details and updates about your order
                                </p>
                            </div>
                            <button type="submit" class="w-[fit-content] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                                Save Changes
                            </button>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label
                                for="email"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Email address*
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="<?php echo htmlspecialchars($user['email']); ?>"
                                placeholder="Enter your email address"
                                readonly
                                class="w-full md:w-[65%] font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#DCDCDC] font-regular text-[#2C2C2C] placeholder:text-[#CCCCCC] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-[90%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
                    <button type="submit" class="w-[fit-content] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                        Save Changes
                    </button>

                    <div class="flex items-center flex-col md:flex-row gap-4">
                        <!-- DELIVERY ADDRESS SECTION -->
                        <div class="w-full md:w-[50%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
                            <div class="flex flex-col items-center">
                                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                                    Delivery
                                </p>
                                <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                    Enter the address where you want your order delivered
                                </p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div class="flex flex-col gap-1">
                                    <label
                                        for="country"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Country
                                    </label>
                                    <input
                                        type="text"
                                        name="country"
                                        id="country"
                                        value="<?php echo htmlspecialchars($profile['country'] ?? 'Nigeria'); ?>"
                                        placeholder="Nigeria"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex items-center gap-3">
                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            for="first_name"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            First Name
                                        </label>
                                        <input
                                            type="text"
                                            name="first_name"
                                            id="first_name"
                                            value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>"
                                            placeholder="Enter first name"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>

                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            for="last_name"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Last Name
                                        </label>
                                        <input
                                            type="text"
                                            name="last_name"
                                            id="last_name"
                                            value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>"
                                            placeholder="Enter last name"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-0 md:gap-1 w-full font-Satoshi bg-transparent outline-none border-[1px] border-[#E1E1E1] rounded-[8px]">
                                    <div class="w-[210p ml-[1px] md:ml-1 pr-2 border-r-[2px] border-[#E1E1E1]">
                                        +234
                                    </div>
                                    <input
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                                        placeholder="Enter phone number"
                                        class="w-full font-regular outline-none text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label
                                        for="address"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Address
                                    </label>
                                    <input
                                        type="text"
                                        name="address"
                                        id="address"
                                        value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>"
                                        placeholder="Enter the address for us to deliver too"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex flex-col md:flex-row items-center gap-2">
                                    <div class="w-full md:w-[60%] flex items-center gap-2">
                                        <div class="w-full flex flex-col gap-1">
                                            <label
                                                for="state"
                                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                State
                                            </label>
                                            <input
                                                type="text"
                                                name="state"
                                                id="state"
                                                value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>"
                                                placeholder=""
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>

                                        <div class="w-full flex flex-col gap-1">
                                            <label
                                                for="city"
                                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                City
                                            </label>
                                            <input
                                                type="text"
                                                name="city"
                                                id="city"
                                                value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>"
                                                placeholder=""
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>
                                    </div>

                                    <div class="w-full md:w-[40%] flex flex-col gap-1">
                                        <label
                                            for="zip_code"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Zip Code
                                        </label>
                                        <input
                                            type="text"
                                            name="zip_code"
                                            id="zip_code"
                                            value="<?php echo htmlspecialchars($profile['zip_code'] ?? ''); ?>"
                                            placeholder=""
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-1">
                                    <input 
                                        type="checkbox" 
                                        name="billing_same_as_delivery" 
                                        id="billing_same_as_delivery"
                                        <?php echo (isset($profile['billing_same_as_delivery']) && $profile['billing_same_as_delivery']) ? 'checked' : ''; ?> />
                                    <label for="billing_same_as_delivery" 
                                        class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#5B5B5B] cursor-pointer">
                                        Use same address for billing
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- BILLING ADDRESS SECTION -->
                        <div id="billing-section" class="w-full md:w-[50%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
                            <div class="flex flex-col items-center">
                                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                                    Billing Address
                                </p>
                                <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                    We use your billing address to verify your payment, and ensure a secure and seamless checkout experience
                                </p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div class="flex flex-col gap-1">
                                    <label
                                        for="billing_country"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Country
                                    </label>
                                    <input
                                        type="text"
                                        name="billing_country"
                                        id="billing_country"
                                        value="<?php echo htmlspecialchars($profile['billing_country'] ?? 'Nigeria'); ?>"
                                        placeholder="Nigeria"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex items-center gap-3">
                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            for="billing_first_name"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            First Name
                                        </label>
                                        <input
                                            type="text"
                                            name="billing_first_name"
                                            id="billing_first_name"
                                            value="<?php echo htmlspecialchars($profile['billing_first_name'] ?? ''); ?>"
                                            placeholder="Enter first name"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>

                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            for="billing_last_name"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Last Name
                                        </label>
                                        <input
                                            type="text"
                                            name="billing_last_name"
                                            id="billing_last_name"
                                            value="<?php echo htmlspecialchars($profile['billing_last_name'] ?? ''); ?>"
                                            placeholder="Enter last name"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label
                                        for="billing_address"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Address
                                    </label>
                                    <input
                                        type="text"
                                        name="billing_address"
                                        id="billing_address"
                                        value="<?php echo htmlspecialchars($profile['billing_address'] ?? ''); ?>"
                                        placeholder="Enter billing address"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex flex-col md:flex-row items-center gap-2">
                                    <div class="w-full md:w-[60%] flex items-center gap-2">
                                        <div class="w-full flex flex-col gap-1">
                                            <label
                                                for="billing_state"
                                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                State
                                            </label>
                                            <input
                                                type="text"
                                                name="billing_state"
                                                id="billing_state"
                                                value="<?php echo htmlspecialchars($profile['billing_state'] ?? ''); ?>"
                                                placeholder=""
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>

                                        <div class="w-full flex flex-col gap-1">
                                            <label
                                                for="billing_city"
                                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                City
                                            </label>
                                            <input
                                                type="text"
                                                name="billing_city"
                                                id="billing_city"
                                                value="<?php echo htmlspecialchars($profile['billing_city'] ?? ''); ?>"
                                                placeholder=""
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>
                                    </div>

                                    <div class="w-full md:w-[40%] flex flex-col gap-1">
                                        <label
                                            for="billing_zip_code"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Zip Code
                                        </label>
                                        <input
                                            type="text"
                                            name="billing_zip_code"
                                            id="billing_zip_code"
                                            value="<?php echo htmlspecialchars($profile['billing_zip_code'] ?? ''); ?>"
                                            placeholder=""
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php
        include(__DIR__ . '/../includes/footer.php');
        ?>
    </main>

    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>
    
    <!-- Profile specific JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const billingCheckbox = document.getElementById('billing_same_as_delivery');
            const billingSection = document.getElementById('billing-section');
            
            // Function to toggle billing section visibility
            function toggleBillingSection() {
                if (billingCheckbox.checked) {
                    billingSection.style.opacity = '0.5';
                    // Disable all inputs in billing section
                    const inputs = billingSection.querySelectorAll('input');
                    inputs.forEach(input => {
                        input.disabled = true;
                    });
                } else {
                    billingSection.style.opacity = '1';
                    // Enable all inputs in billing section
                    const inputs = billingSection.querySelectorAll('input');
                    inputs.forEach(input => {
                        input.disabled = false;
                    });
                }
            }
            
            // Set initial state
            toggleBillingSection();
            
            // Add event listener
            billingCheckbox.addEventListener('change', toggleBillingSection);
        });
    </script>
</body>
</html>