<?php
// Start session to maintain user data
session_start();

// Include database connection
include('../config/connect.php');
// Include authentication utility
require_once '../includes/auth/auth.php';

// Require authentication to access checkout
requireAuth();

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Get user profile if available
$profile_query = "SELECT * FROM profiles WHERE user_id = ?";
$stmt = mysqli_prepare($con, $profile_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$profile_result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($profile_result);

// Initialize variables
$delivery_method = $_POST['delivery_method'] ?? 'pickup';
$pickup_location = $_POST['pickup_location'] ?? '';
$shipping_fee = 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process checkout form
    if (isset($_POST['checkout'])) {
        // Save the order details
        $email = $_POST['email'] ?? $user['email'];
        $note = $_POST['note'] ?? '';
        $delivery_method = $_POST['delivery_method'] ?? 'pickup';
        $pickup_location = $_POST['pickup_location'] ?? '';
        
        // Delivery address (only needed for express delivery)
        $country = $_POST['country'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $state = $_POST['state'] ?? '';
        $city = $_POST['city'] ?? '';
        $zip_code = $_POST['zip_code'] ?? '';
        
        // Billing details
        $use_same_address = isset($_POST['billing_same']) ? true : false;
        $billing_country = $use_same_address ? $country : ($_POST['billing_country'] ?? '');
        $billing_first_name = $use_same_address ? $first_name : ($_POST['billing_first_name'] ?? '');
        $billing_last_name = $use_same_address ? $last_name : ($_POST['billing_last_name'] ?? '');
        $billing_phone = $use_same_address ? $phone : ($_POST['billing_phone'] ?? '');
        $billing_address = $use_same_address ? $address : ($_POST['billing_address'] ?? '');
        $billing_state = $use_same_address ? $state : ($_POST['billing_state'] ?? '');
        $billing_city = $use_same_address ? $city : ($_POST['billing_city'] ?? '');
        $billing_zip_code = $use_same_address ? $zip_code : ($_POST['billing_zip_code'] ?? '');
        
        // Calculate shipping fee based on delivery method
        $shipping_fee = ($delivery_method === 'express') ? 2000 : 0; // Example fee
        
        // Get cart items or buy now item
        $cart_items = [];
        $subtotal = 0;
        
        // For direct "Buy Now"
        if (isset($_SESSION['buy_now_item'])) {
            $item = $_SESSION['buy_now_item'];
            $cart_items[] = $item;
            $subtotal = $item['price'] * $item['quantity'];
        } 
        // Get from regular cart
        else {
            $cart_query = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, 
                          i.image_path AS main_image, v.size, v.variant_id, 
                          COALESCE(v.discount_price, v.original_price) AS price
                          FROM cart c
                          JOIN products p ON c.product_id = p.product_id
                          LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
                          LEFT JOIN product_variants v ON c.variant_id = v.variant_id
                          WHERE c.user_id = ?";
            
            $stmt = mysqli_prepare($con, $cart_query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($item = mysqli_fetch_assoc($result)) {
                $cart_items[] = $item;
                $subtotal += $item['price'] * $item['quantity'];
            }
        }
        
        $total = $subtotal + $shipping_fee;
        
        // Create order in database
        $order_query = "INSERT INTO orders (user_id, order_total, shipping_fee, subtotal, delivery_method, 
                        delivery_address, delivery_city, delivery_state, delivery_zip, delivery_country,
                        billing_address, billing_city, billing_state, billing_zip, billing_country,
                        pickup_location, notes, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $status = 'pending';
        $stmt = mysqli_prepare($con, $order_query);
        mysqli_stmt_bind_param($stmt, "iddssssssssssssss", 
                               $user_id, $total, $shipping_fee, $subtotal, $delivery_method,
                               $address, $city, $state, $zip_code, $country,
                               $billing_address, $billing_city, $billing_state, $billing_zip_code, $billing_country,
                               $pickup_location, $note, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($con);
            
            // Add order items
            foreach ($cart_items as $item) {
                $item_price = $item['price'];
                $item_total = $item['price'] * $item['quantity'];
                
                $item_query = "INSERT INTO order_items (order_id, product_id, variant_id, quantity, price, item_total)
                              VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($con, $item_query);
                mysqli_stmt_bind_param($stmt, "iiiddd", 
                                      $order_id, $item['product_id'], $item['variant_id'], 
                                      $item['quantity'], $item_price, $item_total);
                mysqli_stmt_execute($stmt);
            }
            
            // Clear cart or buy now item
            if (isset($_SESSION['buy_now_item'])) {
                unset($_SESSION['buy_now_item']);
            } else {
                $clear_cart = "DELETE FROM cart WHERE user_id = ?";
                $stmt = mysqli_prepare($con, $clear_cart);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
            }
            
            // Set success message
            $_SESSION['order_success'] = true;
            $_SESSION['order_id'] = $order_id;
            
            // Redirect to payment gateway
            header("Location: payment.php?order_id=" . $order_id);
            exit();
        }
    }
}

// Prepare data for checkout page
$cart_items = [];
$subtotal = 0;

// Check if coming from Buy Now button
if (isset($_GET['buy_now']) && isset($_GET['product_id']) && isset($_GET['variant_id'])) {
    $product_id = intval($_GET['product_id']);
    $variant_id = intval($_GET['variant_id']);
    $quantity = intval($_GET['quantity'] ?? 1);
    
    // Get product details
    $product_query = "SELECT p.product_id, p.product_name, i.image_path AS main_image, 
                     v.size, v.variant_id, COALESCE(v.discount_price, v.original_price) AS price
                     FROM products p
                     LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
                     LEFT JOIN product_variants v ON v.variant_id = ?
                     WHERE p.product_id = ?";
    
    $stmt = mysqli_prepare($con, $product_query);
    mysqli_stmt_bind_param($stmt, "ii", $variant_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($item = mysqli_fetch_assoc($result)) {
        $item['quantity'] = $quantity;
        $cart_items[] = $item;
        $subtotal = $item['price'] * $quantity;
        
        // Store buy now item in session
        $_SESSION['buy_now_item'] = $item;
    }
} 
// Otherwise get from cart
else {
    $cart_query = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, 
                  i.image_path AS main_image, v.size, v.variant_id, 
                  COALESCE(v.discount_price, v.original_price) AS price
                  FROM cart c
                  JOIN products p ON c.product_id = p.product_id
                  LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
                  LEFT JOIN product_variants v ON c.variant_id = v.variant_id
                  WHERE c.user_id = ?";
    
    $stmt = mysqli_prepare($con, $cart_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($item = mysqli_fetch_assoc($result)) {
        $cart_items[] = $item;
        $subtotal += $item['price'] * $item['quantity'];
    }
}

// Set initial total (will be updated based on delivery method)
$total = $subtotal;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Checkout</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/checkout.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/tabs.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/faq.css" />
</head>

<body>
    <main class="bg-[#FEFEFE] relative">
   
        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <a href="../products/cart.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Cart</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Check Out</span>
                </div>
            </div>
        </section>

        <div class="w-[95%] md:w-[90%] mx-auto flex flex-col-reverse md:flex-row gap-3 py-5">
            <div class="w-full md:w-[55%] flex flex-col gap-5 py-5">
                <form method="POST" action="">
                    <div class="w-[95%] md:w-[90%] lg:w-[80%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
                        <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                            How do you want to receive your product?
                        </p>

                        <div class="flex flex-col gap-2 pt-2">
                            <label for="pickup" class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" id="pickup" name="delivery_method" value="pickup" <?php echo ($delivery_method === 'pickup') ? 'checked' : ''; ?> class="delivery-method-radio" />
                                <p class="text-[15px] md:text-[16px] text-[#262626] w-full font-['Open Sans'] font-regular">
                                    Pick-Up
                                </p>
                            </label>
                            <label for="express" class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" id="express" name="delivery_method" value="express" <?php echo ($delivery_method === 'express') ? 'checked' : ''; ?> class="delivery-method-radio" />
                                <p class="text-[15px] md:text-[16px] text-[#262626] w-full font-['Open Sans'] font-regular">
                                    Express Delivery
                                </p>
                            </label>
                        </div>
                    </div>

                    <div id="pickup-section" class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 <?php echo ($delivery_method !== 'pickup') ? 'hidden' : ''; ?>">
                        <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                            Pickup Location
                        </p>

                        <div class="flex flex-col gap-2 pt-2">
                            <label for="lagos-store" class="flex items-start gap-2 cursor-pointer">
                                <input type="radio" id="lagos-store" name="pickup_location" value="Lagos Store" class="mt-2" checked />
                                <div class="flex flex-col gap-1">
                                    <p class="text-[15px] md:text-[16px] text-[#262626] w-full font-['Open Sans'] font-medium">
                                        Lagos Store
                                    </p>
                                    <p class="text-[13px] md:text-[14px] text-[#262626] w-full font-['Open Sans'] font-regular">
                                        Location of the company<br />Pickup is available from 8am-6pm
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="contact-section" class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
                        <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
                            <div class="flex flex-col items-center">
                                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                                    Contact Info
                                </p>
                                <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                    We'll use this email to send you details and updates about your order
                                </p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div class="flex flex-col gap-1">
                                    <label for="email" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Email Address
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="<?php echo htmlspecialchars($user['email']); ?>"
                                        placeholder="Enter your email address"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex flex-col gap-1">
                                    <label for="note" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Add a note to your order
                                    </label>
                                    <textarea 
                                        id="note"
                                        name="note"
                                        placeholder="Add note (Optional)" 
                                        class="w-full min-h-[88px] max-h-[88px] font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="delivery-section" class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 <?php echo ($delivery_method !== 'express') ? 'hidden' : ''; ?>">
                        <div class="flex flex-col gap-4">
                            <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
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
                                        <label for="country" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Country
                                        </label>
                                        <input
                                            type="text"
                                            id="country"
                                            name="country"
                                            value="<?php echo htmlspecialchars($profile['country'] ?? 'Nigeria'); ?>"
                                            placeholder="Nigeria"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>

                                    <div class="w-full flex items-center gap-3">
                                        <div class="w-full flex flex-col gap-1">
                                            <label for="first_name" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                First Name
                                            </label>
                                            <input
                                                type="text"
                                                id="first_name"
                                                name="first_name"
                                                value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>"
                                                placeholder="Enter first name"
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                        </div>

                                        <div class="w-full flex flex-col gap-1">
                                            <label for="last_name" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                Last Name
                                            </label>
                                            <input
                                                type="text"
                                                id="last_name"
                                                name="last_name"
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
                                            id="phone"
                                            name="phone"
                                            value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                                            placeholder="Enter phone number"
                                            class="w-full font-regular outline-none text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>

                                    <div class="flex flex-col gap-1">
                                        <label for="address" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Address
                                        </label>
                                        <input
                                            type="text"
                                            id="address"
                                            name="address"
                                            value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>"
                                            placeholder="Enter the address for us to deliver to"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>

                                    <div class="w-full flex flex-col md:flex-row items-center gap-2">
                                        <div class="w-full md:w-[60%] flex items-center gap-2">
                                            <div class="w-full flex flex-col gap-1">
                                                <label for="state" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                    State
                                                </label>
                                                <input
                                                    type="text"
                                                    id="state"
                                                    name="state"
                                                    value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>"
                                                    placeholder="Enter state"
                                                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                            </div>

                                            <div class="w-full flex flex-col gap-1">
                                                <label for="city" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                    City
                                                </label>
                                                <input
                                                    type="text"
                                                    id="city"
                                                    name="city"
                                                    value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>"
                                                    placeholder="Enter city"
                                                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                            </div>
                                        </div>

                                        <div class="w-full md:w-[40%] flex flex-col gap-1">
                                            <label for="zip_code" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                Zip Code
                                            </label>
                                            <input
                                                type="text"
                                                id="zip_code"
                                                name="zip_code"
                                                value="<?php echo htmlspecialchars($profile['zip_code'] ?? ''); ?>"
                                                placeholder="Enter zip code"
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <input type="checkbox" name="billing_same" id="billing_same" checked />
                                        <label for="billing_same" class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#5B5B5B] cursor-pointer">Use same address for billing</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="billing-section" class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2">
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
                                    <label for="billing_country" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Country
                                    </label>
                                    <input
                                        type="text"
                                        id="billing_country"
                                        name="billing_country"
                                        value="<?php echo htmlspecialchars($profile['billing_country'] ?? 'Nigeria'); ?>"
                                        placeholder="Nigeria"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex items-center gap-3">
                                    <div class="w-full flex flex-col gap-1">
                                        <label for="billing_first_name" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            First Name
                                        </label>
                                        <input
                                            type="text"
                                            id="billing_first_name"
                                            name="billing_first_name"
                                            value="<?php echo htmlspecialchars($profile['billing_first_name'] ?? ''); ?>"
                                            placeholder="Enter first name"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>

                                    <div class="w-full flex flex-col gap-1">
                                        <label for="billing_last_name" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Last Name
                                        </label>
                                        <input
                                            type="text"
                                            id="billing_last_name"
                                            name="billing_last_name"
                                            value="<?php echo htmlspecialchars($profile['billing_last_name'] ?? ''); ?>"
                                            placeholder="Enter last name"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label for="billing_address" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Address
                                    </label>
                                    <input
                                        type="text"
                                        id="billing_address"
                                        name="billing_address"
                                        value="<?php echo htmlspecialchars($profile['billing_address'] ?? ''); ?>"
                                        placeholder="Enter billing address"
                                        class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex flex-col md:flex-row items-center gap-2">
                                    <div class="w-full md:w-[60%] flex items-center gap-2">
                                        <div class="w-full flex flex-col gap-1">
                                            <label for="billing_state" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                State
                                            </label>
                                            <input
                                                type="text"
                                                id="billing_state"
                                                name="billing_state"
                                                value="<?php echo htmlspecialchars($profile['billing_state'] ?? ''); ?>"
                                                placeholder="Enter state"
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>

                                        <div class="w-full flex flex-col gap-1">
                                            <label for="billing_city" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                                City
                                            </label>
                                            <input
                                                type="text"
                                                id="billing_city"
                                                name="billing_city"
                                                value="<?php echo htmlspecialchars($profile['billing_city'] ?? ''); ?>"
                                                placeholder="Enter city"
                                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                        </div>
                                    </div>

                                    <div class="w-full md:w-[40%] flex flex-col gap-1">
                                        <label for="billing_zip_code" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            Zip Code
                                        </label>
                                        <input
                                            type="text"
                                            id="billing_zip_code"
                                            name="billing_zip_code"
                                            value="<?php echo htmlspecialchars($profile['billing_zip_code'] ?? ''); ?>"
                                            placeholder="Enter zip code"
                                            class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-0 md:gap-1 w-full font-Satoshi bg-transparent outline-none border-[1px] border-[#E1E1E1] rounded-[8px]">
                                    <div class="w-[210p ml-[1px] md:ml-1 pr-2 border-r-[2px] border-[#E1E1E1]">
                                        +234
                                    </div>
                                    <input
                                        type="text"
                                        name="billing_phone"
                                        id="billing_phone"
                                        value="<?php echo htmlspecialchars($profile['billing_phone'] ?? ''); ?>"
                                        placeholder="Enter phone number"
                                        class="w-full font-regular outline-none text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="delivery-status" class="mt-4 hidden">
                        <section class="flex flex-col items-center w-full bg-[#ECFDEF] border-[1px] border-[#C3FACE] py-3 px-4 rounded">
                            <div class="flex items-center gap-2 mr-auto">
                                <img src="../assets/global/infosuccess.svg" alt="Success" class="w-[24px]" />
                                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-Satoshi font-medium">
                                    Delivery Status
                                </p>
                            </div>
                            <div class="ml-4 w-full flex items-center justify-between">
                                <p class="w-[90%] text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 pr-3">
                                    We deliver to your location. This is the delivery fee:
                                </p>
                                <p class="text-[15px] md:text-[16px] text-[#262626] font-['Open Sans'] font-medium">
                                    ₦<span id="delivery-fee">2,000</span>
                                </p>
                            </div>
                        </section>
                    </div>

                    <div class="mt-4">
                        <label class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#5B5B5B]">
                            By proceeding with your purchase you agree to our Terms and Conditions and Privacy Policy
                        </label>
                        <button 
                            type="submit" 
                            name="checkout" 
                            value="1"
                            class="w-full md:max-w-[377px] flex items-center justify-center gap-2 mt-4 py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                            Continue to Pay
                        </button>
                    </div>
                </form>
            </div>

            <div class="w-full md:w-[45%] flex flex-col gap-3">
                <div class="w-full flex flex-col gap-2 bg-[#E8E9F2] p-2 z-0 fixed top-[120px] right-0 md:hidden">
                    <div class="w-[95%] mx-auto flex flex-col gap-2 rounded-[4px] border-[1px] border-[#E1E1E1] p-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Subtotal</p>
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="mobile-subtotal"><?php echo number_format($subtotal); ?></span></p>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Shipping fee</p>
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="mobile-shipping"><?php echo number_format($shipping_fee); ?></span></p>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Total</p>
                            <p class="text-[#484F98] text-[18px] md:text-[22px] font-['Open Sans'] font-bold">₦<span id="mobile-total"><?php echo number_format($total); ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="w-full flex flex-col gap-3 rounded-[4px] bg-[#E8E9F2] md:bg-[#EEEEEE] mt-[9rem] md:mt-0 p-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[#262626] text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Your Order</p>
                        <img src="../assets/products/down2.svg" class="rotate-[180deg] cursor-pointer md:hidden" />
                    </div>
                    
                    <?php foreach ($cart_items as $item): ?>
                    <div class="flex items-center justify-between">
                        <div class="py-3 flex gap-2">
                            <div class="w-[80.64px] h-[48.73px] rounded-[4px] overflow-hidden">
                                <img src="<?php echo !empty($item['main_image']) ? '../assets/products/' . $item['main_image'] : '../assets/products/default.jpg'; ?>" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: <?php echo htmlspecialchars($item['product_name']); ?></p>
                                <?php if (!empty($item['color'])): ?>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: <?php echo htmlspecialchars($item['color']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['size'])): ?>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                <?php endif; ?>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">₦<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="w-full hidden md:flex flex-col gap-2 rounded-[4px] border-[1px] border-[#E1E1E1] p-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Subtotal</p>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="desktop-subtotal"><?php echo number_format($subtotal); ?></span></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Shipping fee</p>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="desktop-shipping"><?php echo number_format($shipping_fee); ?></span></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Total</p>
                        <p class="text-[#484F98] text-[18px] md:text-[22px] font-['Open Sans'] font-bold">₦<span id="desktop-total"><?php echo number_format($total); ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Payment success modal (hidden by default) -->
    <div id="paysuccess" class="payment ps" style="display: none;">
        <div class="payment-content pss overflow-hidden py-[4rem] flex flex-col gap-4 items-center">
            <div class="overflow-hidden flex flex-col items-center p-4">
                <img src="../assets/global/success.svg" class="mx-auto w-[120px] md:w-[150px]" />
                <p class="font-['Open Sans'] text-[19px] text-[24px] font-medium text-center">
                    Order Confirmed
                </p>
                <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                    Your order has been placed successfully. A confirmation email has been sent to you. Thank you for shopping with us
                </p>
                <a href="./view-order.php" class="w-[80%] text-center text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer" id="closepssucces">
                    View Order
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements
            const deliveryRadios = document.querySelectorAll('.delivery-method-radio');
            const deliverySection = document.getElementById('delivery-section');
            const pickupSection = document.getElementById('pickup-section');
            const billingSection = document.getElementById('billing-section');
            const billingCheckbox = document.getElementById('billing_same');
            const deliveryStatusSection = document.getElementById('delivery-status');
            
            // Define shipping fee for express delivery
            const expressShippingFee = 2000;
            
            // Initialize total
            let subtotal = <?php echo $subtotal; ?>;
            let total = subtotal;
            
            // Function to update totals
            function updateTotals(shippingFee) {
                // Update shipping fee displays
                document.getElementById('mobile-shipping').textContent = shippingFee.toLocaleString();
                document.getElementById('desktop-shipping').textContent = shippingFee.toLocaleString();
                document.getElementById('delivery-fee').textContent = shippingFee.toLocaleString();
                
                // Calculate new total
                total = subtotal + shippingFee;
                
                // Update total displays
                document.getElementById('mobile-total').textContent = total.toLocaleString();
                document.getElementById('desktop-total').textContent = total.toLocaleString();
            }
            
            // Handle delivery method change
            deliveryRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'express') {
                        deliverySection.classList.remove('hidden');
                        pickupSection.classList.add('hidden');
                        deliveryStatusSection.classList.remove('hidden');
                        updateTotals(expressShippingFee);
                    } else {
                        deliverySection.classList.add('hidden');
                        pickupSection.classList.remove('hidden');
                        deliveryStatusSection.classList.add('hidden');
                        updateTotals(0);
                    }
                });
            });
            
            // Handle billing checkbox
            if (billingCheckbox) {
                billingCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        billingSection.classList.add('opacity-50');
                        
                        // Disable billing inputs
                        const inputs = billingSection.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.disabled = true;
                        });
                    } else {
                        billingSection.classList.remove('opacity-50');
                        
                        // Enable billing inputs
                        const inputs = billingSection.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.disabled = false;
                        });
                    }
                });
                
                // Trigger change event on load
                billingCheckbox.dispatchEvent(new Event('change'));
            }
            
            // Check if there's a buy-now item in session storage
            const buyNowData = sessionStorage.getItem('checkoutData');
            if (buyNowData) {
                try {
                    const productData = JSON.parse(buyNowData);
                    console.log('Buy Now Product:', productData);
                    
                    // You can use this data to populate the checkout form if needed
                    // This is already handled server-side in this implementation
                    
                    // Clear the session storage data once used
                    // sessionStorage.removeItem('checkoutData');
                } catch (e) {
                    console.error('Error parsing buy now data:', e);
                }
            }
            
            // Initialize the page based on current delivery method
            const currentMethod = document.querySelector('.delivery-method-radio:checked').value;
            if (currentMethod === 'express') {
                deliverySection.classList.remove('hidden');
                pickupSection.classList.add('hidden');
                deliveryStatusSection.classList.remove('hidden');
                updateTotals(expressShippingFee);
            } else {
                deliverySection.classList.add('hidden');
                pickupSection.classList.remove('hidden');
                deliveryStatusSection.classList.add('hidden');
                updateTotals(0);
            }
        });
    </script>
</body>
</html>