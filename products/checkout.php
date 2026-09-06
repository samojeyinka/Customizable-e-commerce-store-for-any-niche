<?php
// This first part is unchanged - just showing for context
// Start session to maintain user data
session_start();

require_once __DIR__ . "/../config/config.php";
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
    // Handle cart item removal
    if (isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['cart_id'])) {
        $cart_id = intval($_POST['cart_id']);
        
        $remove_query = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($con, $remove_query);
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to refresh the page after removal
            header("Location: checkout.php?removed=1");
            exit();
        }
    }
    
    // Process checkout form
    if (isset($_POST['checkout'])) {
        // Existing checkout logic remains unchanged
        // ...
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

    $cart_query = "SELECT DISTINCT c.cart_id, c.quantity, p.product_id, p.product_name, 
    i.image_path AS main_image, v.size, v.variant_id, v.status,
    COALESCE(v.discount_price, v.original_price) AS price,
    (SELECT MIN(COALESCE(pv.discount_price, pv.original_price)) 
     FROM product_variants pv 
     WHERE pv.product_id = p.product_id) AS min_variant_price,
    (SELECT pv_first.size 
     FROM product_variants pv_first 
     WHERE pv_first.product_id = p.product_id 
     ORDER BY pv_first.variant_id ASC 
     LIMIT 1) AS first_variant_size
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
    LEFT JOIN product_variants v ON c.variant_id = v.variant_id
    WHERE c.user_id = ?
    GROUP BY c.cart_id, c.quantity, p.product_id, p.product_name, 
    i.image_path, v.size, v.variant_id, v.status, v.discount_price, v.original_price";

$stmt = mysqli_prepare($con, $cart_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$subtotal = 0;

while ($item = mysqli_fetch_assoc($result)) {
    
    // Use price logic with fallback to minimum variant price
    if (!empty($item['price']) && $item['price'] > 0) {
        $price = floatval($item['price']);
    } else if (!empty($item['min_variant_price']) && $item['min_variant_price'] > 0) {
        // Fallback to min variant price if specific price is not available
        $price = floatval($item['min_variant_price']);
    } else {
        // Last resort fallback
        $price = 0;
    }
    
    // Calculate item total
    $item_total = $price * $item['quantity'];
    
    // Add calculated values to item
    $item['price'] = $price;
    $item['item_total'] = $item_total;
    
    // Add to cart items array and calculate subtotal
    $cart_items[] = $item;
    $subtotal += $item_total;
}

// For debugging - uncomment this to check for duplicate cart IDs
// echo "<pre>Cart IDs: " . print_r($cart_item_ids, true) . "</pre>";

// Alternative approach if the SQL GROUP BY isn't working
// This uses PHP to ensure unique cart items by cart_id
$unique_cart_items = [];
$unique_cart_ids = [];

foreach ($cart_items as $item) {
    if (!in_array($item['cart_id'], $unique_cart_ids)) {
        $unique_cart_ids[] = $item['cart_id'];
        $unique_cart_items[] = $item;
    }
}


 // Process size information - use first variant size as fallback if needed
 if (empty($item['size']) && !empty($item['first_variant_size'])) {
    $item['size'] = $item['first_variant_size'];
}
// Replace the original cart_items with the deduplicated array
$cart_items = $unique_cart_items;

// Update the subtotal based on deduplicated items
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['item_total'];
}




$product_count = count($cart_items);

// Base shipping fee per product
$base_shipping_fee = 2000; 

// Calculate shipping fee based on delivery method and product count
$shipping_fee = ($delivery_method === 'express') ? ($base_shipping_fee * $product_count) : 0;

// Set total with updated shipping fee
$total = $subtotal + $shipping_fee;

}


// Add this PHP code at the top of your checkout.php file to generate the necessary data
$paystack_data = [
    'key' => 'pk_test_ffbf13a1e6d967184705ae17a339b027b6ec459c', // Replace with your public key
    'user_email' => $user['email'] ?? '',
    'amount' => $total,
    'first_name' => $profile['first_name'] ?? '',
    'last_name' => $profile['last_name'] ?? '',
    'order_ref' => 'ORDER-' . time() . rand(1000, 9999)
];




// Check if there's a successful order notification to display
if (isset($_GET['order_success']) && isset($_GET['order_id'])) {
    // Include notifications functions
    require_once '../includes/notifications.php';
    
    $order_id = intval($_GET['order_id']);
    
    // Get order details from database to create proper notification
    $order_query = "SELECT o.order_total, p.first_name, p.last_name 
                   FROM orders o 
                   LEFT JOIN profiles p ON o.user_id = p.user_id 
                   WHERE o.id = ?";
    
    $stmt = mysqli_prepare($con, $order_query);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($order_data = mysqli_fetch_assoc($result)) {
        $order_total = number_format((float)$order_data['order_total'], 2);
        $customer_name = trim($order_data['first_name'] . ' ' . $order_data['last_name']);
        if (empty($customer_name)) {
            $customer_name = "Customer #" . $user_id;
        }
        
        // Create notification for admin
        add_notification(
            $con,
            'order',
            "New Order #$order_id",
            "A new order has been placed by $customer_name for ₦$order_total",
            $order_id,
            'order',
            null, // null for_user_id means it's for all admins
            1     // 1 means it's for admin
        );
        
        // Create notification for the user too
        add_notification(
            $con,
            'order_confirmation',
            'Order Successfully Placed',
            "Your order #$order_id has been received and is being processed. Thank you for shopping with us!",
            $order_id,
            'order',
            $user_id, // specific user
            0         // 0 means it's not for admin
        );
    }
}

// Check if profile is complete
$requiredProfileFields = [
    'first_name', 'last_name', 'phone', 'country', 
    'address', 'state', 'city', 'zip_code'
];

$profileComplete = true;
$missingFields = [];

foreach ($requiredProfileFields as $field) {
    if (empty($profile[$field])) {
        $profileComplete = false;
        $missingFields[] = $field;
    }
}

// If billing is different, check those fields too
if (isset($profile['billing_same_as_delivery']) && !$profile['billing_same_as_delivery']) {
    $requiredBillingFields = [
        'billing_first_name', 'billing_last_name', 'billing_country',
        'billing_address', 'billing_state', 'billing_city', 'billing_zip_code'
    ];
    
    foreach ($requiredBillingFields as $field) {
        if (empty($profile[$field])) {
            $profileComplete = false;
            $missingFields[] = $field;
        }
    }
}


require_once "../includes/auth/google.php";


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | Checkout</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="./paystack-checkout.js"></script>


    <?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[#FEFEFE] relative">
    <?php
     include('../includes/header.php');
    include('../includes/options.php');
    ?>
        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="../products/cart.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Cart</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-medium">Check Out</span>
                </div>
            </div>
        </section>

        <?php if (!$profileComplete): ?>
<div class="w-[90%] mt-[10rem] md:mt-0  mx-auto bg-red-50 border-l-4 border-red-500 p-4 md:mb-6 rounded-r">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
                Your profile is not fully set up
            </h3>
            <div class="mt-2 text-sm text-red-700">
                <p>
                    Please complete your profile information before checking out. 
                    Missing fields: <?php echo implode(', ', array_map(function($field) {
                        return str_replace('_', ' ', $field);
                    }, $missingFields)); ?>
                </p>
                <div class="mt-4">
                    <a href="../user/profile.php" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Complete Profile Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

        <div class="w-[95%] md:w-[90%] mx-auto max-w-[1440px] flex flex-col-reverse md:flex-row gap-3 py-5">
            <div class="w-full md:w-[55%] flex flex-col">
                <form method="POST" action="" class="flex flex-col gap-3">
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
                                    Tejuosho Main Complex Yaba.<br />Pickup is available from 8am-6pm
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

                        <div class="flex justify-end mt-3">
    <button 
        type="button" 
        id="update-profile-btn"
        class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-[#C2185B] text-[14px] font-['Open Sans'] flex items-center gap-1 cursor-pointer rounded-[4px] border border-[#E1E1E1]">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
        </svg>
        Save Address to Profile
    </button>
</div>
                    </div>

                    <div id="delivery-status" class="mt-4 hidden">
                        <section class="flex flex-col items-center w-full bg-[#ECFDEF] border-[1px] border-[#C3FACE] py-3 px-4 rounded">
                            <div class="flex items-center gap-2 mr-auto">
                                <i class="fa-solid fa-circle-check text-[24px] text-[#4CAF50] leading-none" alt="Success"></i>
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

         



                    <!-- Payment Method Section -->
<div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 mt-4">
   

    

    <div class="flex flex-col gap-3 pt-2">
        <!-- Payment method will be handled by Paystack -->
        <input type="hidden" id="email-address" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" />
        <input type="hidden" id="amount" name="amount" value="<?php echo $total; ?>" />
        <input type="hidden" id="first-name" name="first_name" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" />
        <input type="hidden" id="last-name" name="last_name" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" />
        <input type="hidden" id="order-ref" name="order_ref" value="ORDER-<?php echo time().rand(1000, 9999); ?>" />
    
    </div>
</div>

<div class="mt-4">
    <label class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#5B5B5B]">
        By proceeding with your purchase you agree to our Terms and Conditions and Privacy Policy
    </label>
    <!-- <button 
    type="button" 
    id="pay-button-desktop"
    class="w-full md:max-w-[377px] flex items-center justify-center gap-2 mt-4 py-2 px-4 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] hidden md:flex">
    Pay Now ₦<?php echo number_format((float)$total); ?>
</button> -->

<button 
    type="button" 
    id="pay-button-desktop"
    class="w-full md:max-w-[377px] flex items-center justify-center gap-2 mt-4 py-2 px-4 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] hidden md:flex <?php echo !$profileComplete ? 'opacity-50 cursor-not-allowed' : ''; ?>"
    <?php echo !$profileComplete ? 'disabled' : ''; ?>
>
    <?php echo $profileComplete ? 'Pay Now ₦' . number_format((float)$total) : 'Complete Profile to Checkout'; ?>
</button>
</div>
                </form>
            </div>

            <div class="w-full md:w-[45%] flex flex-col gap-3">
               <!-- Replace the existing mobile order summary div with this one -->
<div class="w-full flex flex-col gap-2 bg-[#E8E9F2] p-2 z-50 fixed top-[120px] right-0 md:hidden mobile-order-summary">
    <div class="w-[95%] mx-auto max-w-[1440px] flex flex-col gap-2 rounded-[4px] border-[1px] border-[#E1E1E1] p-2">
        <div class="flex items-center justify-between">
            <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Subtotal</p>
            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="mobile-subtotal"><?php echo number_format((float)$subtotal); ?></span></p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Shipping fee</p>
            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="mobile-shipping"><?php echo number_format((float)$shipping_fee); ?></span></p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Total</p>
            <p class="text-[#C2185B] text-[18px] md:text-[22px] font-['Open Sans'] font-bold">₦<span id="mobile-total"><?php echo number_format((float)$total); ?></span></p>
        </div>
    </div>
    <!-- <button 
    type="button" 
    id="pay-button-mobile"
    class="w-full md:max-w-[377px] flex items-center justify-center gap-2 mt-4 py-2 px-4 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
    Pay Now ₦<?php echo number_format((float)$total); ?>
</button> -->

<button 
    type="button" 
    id="pay-button-mobile"
    class="w-full md:max-w-[377px] flex items-center justify-center gap-2 mt-4 py-2 px-4 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] <?php echo !$profileComplete ? 'opacity-50 cursor-not-allowed' : ''; ?>"
    <?php echo !$profileComplete ? 'disabled' : ''; ?>
>
    <?php echo $profileComplete ? 'Pay Now ₦' . number_format((float)$total) : 'Complete Profile to Checkout'; ?>
</button>

</div>
                

             

      <div class="w-full flex flex-col gap-3 rounded-[4px] bg-[#E8E9F2] md:bg-[#EEEEEE] md:mt-[9rem] md:mt-0 p-2">
    <div class="flex items-center justify-between">
        <p class="text-[#262626] text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Your Order</p>
        <i class="fa-solid fa-chevron-down rotate-[180deg] text-[20px] text-[#262626] cursor-pointer md:hidden leading-none"></i>
    </div>
    
    <?php if (empty($cart_items)): ?>
        <div class="p-3 text-center">
            <p class="text-[#6b7280] text-[14px] md:text-[16px] font-['Open Sans']">Your cart is empty</p>
            <a href="../products/index.php" class="text-[#C2185B] text-[14px] font-['Open Sans'] underline">Continue Shopping</a>
        </div>
    <?php else: ?>
        <?php foreach ($cart_items as $item): ?>
            <div class="flex items-center justify-between border-b border-[#e5e7eb] pb-3 last:border-b-0">
                <div class="py-3 flex gap-2">
                    <div class="w-[80.64px] h-[48.73px] rounded-[4px] overflow-hidden">
                        <img src="<?php echo !empty($item['main_image']) ? '../assets/products/' . $item['main_image'] : '../assets/products/default.jpg'; ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($item['product_name']); ?>" />
                    </div>
                    <div class="flex flex-col gap-[2px]">
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium"><?php echo htmlspecialchars($item['product_name']); ?></p>
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: <?php echo htmlspecialchars(!empty($item['size']) ? $item['size'] : $item['first_variant_size']); ?></p>
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Quantity: <?php echo $item['quantity']; ?></p>
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Price: ₦<?php echo number_format((float)$item['price']); ?></p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">₦<?php echo number_format((float)$item['item_total']); ?></p>
                    <?php if (isset($item['cart_id'])): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" class="text-[12px] text-red-600 hover:text-red-800">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove
                                </span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div> 
<div class="w-full hidden md:flex flex-col gap-2 rounded-[4px] border-[1px] border-[#E1E1E1] p-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Subtotal</p>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="desktop-subtotal"><?php echo number_format((float)$subtotal); ?></span></p>
                    </div>

              

           <!-- Shipping fee display for desktop - only show breakdown for express delivery -->
<div class="flex items-center justify-between">
    <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
        <?php if ($delivery_method === 'express'): ?>
            Shipping fee (₦<?php echo number_format((float)$base_shipping_fee); ?> x <?php echo $product_count; ?> products)
        <?php else: ?>
            Shipping fee
        <?php endif; ?>
    </p>
    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="desktop-shipping"><?php echo number_format((float)$shipping_fee); ?></span></p>
</div>


                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Total</p>
                        <p class="text-[#C2185B] text-[18px] md:text-[22px] font-['Open Sans'] font-bold">₦<span id="desktop-total"><?php echo number_format((float)$total); ?></span></p>
                    </div>
                </div>

            </div>
        </div>

        <?php
        include(__DIR__ . '/../includes/footer.php');

?>
    </main>

    <!-- Payment success modal (hidden by default) -->
    <div id="paysuccess" class="payment ps" style="display: none;">
        <div class="payment-content pss overflow-hidden py-[4rem] flex flex-col gap-4 items-center">
            <div class="overflow-hidden flex flex-col items-center p-4">
                <i class="fa-solid fa-circle-check text-[120px] md:text-[150px] text-[#C2185B] mx-auto leading-none"></i>
                <p class="font-['Open Sans'] text-[19px] text-[24px] font-medium text-center">
                    Order Confirmed
                </p>
                <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                    Your order has been placed successfully. A confirmation email has been sent to you. Thank you for shopping with us
                </p>
                <a href="<?php echo DOMAIN; ?>/user/orders.php" class="w-[80%] text-center text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#C2185B] text-white rounded-[8px] mt-10 cursor-pointer" id="closepssucces">
                    View Order
                </a>
            </div>
        </div>
    </div>



    <!-- Add Paystack script as external file -->

<!-- Add your external JS file with nonce if needed -->

<script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const deliveryRadios = document.querySelectorAll('.delivery-method-radio');
    const deliverySection = document.getElementById('delivery-section');
    const pickupSection = document.getElementById('pickup-section');
    const billingSection = document.getElementById('billing-section');
    const billingCheckbox = document.getElementById('billing_same');
    const deliveryStatusSection = document.getElementById('delivery-status');
    const payButton = document.getElementById('pay-button');
    
    // Define shipping fee parameters
    const baseShippingFee = 2000;
    const productCount = <?php echo count($cart_items); ?>;
    
    // Initialize total
    let subtotal = <?php echo $subtotal; ?>;
    let total = subtotal;
    
    // Function to update totals

    function updateTotals(isExpress) {
    // Calculate shipping fee based on delivery method and product count
    const shippingFee = isExpress ? (baseShippingFee * productCount) : 0;
    
    // Update shipping fee displays
    document.getElementById('mobile-shipping').textContent = shippingFee.toLocaleString();
    document.getElementById('desktop-shipping').textContent = shippingFee.toLocaleString();
    
    // Update the shipping fee label text based on delivery method
    const shippingLabels = document.querySelectorAll('.shipping-fee-label');
    shippingLabels.forEach(label => {
        if (isExpress) {
            label.textContent = `Shipping fee (₦${baseShippingFee.toLocaleString()} x ${productCount} products)`;
        } else {
            label.textContent = 'Shipping fee';
        }
    });
    
    if (document.getElementById('delivery-fee')) {
        document.getElementById('delivery-fee').textContent = shippingFee.toLocaleString();
    }
    
    // Calculate new total
    total = subtotal + shippingFee;
    
    // Update total displays
    document.getElementById('mobile-total').textContent = total.toLocaleString();
    document.getElementById('desktop-total').textContent = total.toLocaleString();
    
    // Update both Pay Now buttons with the new total using their new IDs
    const mobileButton = document.getElementById('pay-button-mobile');
    const desktopButton = document.getElementById('pay-button-desktop');
    const buttonText = `Pay Now ₦${total.toLocaleString()}`;
    
    if (mobileButton) mobileButton.innerHTML = buttonText;
    if (desktopButton) desktopButton.innerHTML = buttonText;
    
    // Update the hidden amount field for Paystack
    const amountField = document.getElementById('amount');
    if (amountField) {
        amountField.value = total;
    }
}
    // Handle delivery method change
    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'express') {
                deliverySection.classList.remove('hidden');
                pickupSection.classList.add('hidden');
                deliveryStatusSection.classList.remove('hidden');
                updateTotals(true);
            } else {
                deliverySection.classList.add('hidden');
                pickupSection.classList.remove('hidden');
                deliveryStatusSection.classList.add('hidden');
                updateTotals(false);
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
    
    // Initialize the page based on current delivery method
    const currentMethod = document.querySelector('.delivery-method-radio:checked').value;
    if (currentMethod === 'express') {
        deliverySection.classList.remove('hidden');
        pickupSection.classList.add('hidden');
        deliveryStatusSection.classList.remove('hidden');
        updateTotals(true);
    } else {
        deliverySection.classList.add('hidden');
        pickupSection.classList.remove('hidden');
        deliveryStatusSection.classList.add('hidden');
        updateTotals(false);
    }
});



// Modified payment handler to support both mobile and desktop buttons
document.addEventListener('DOMContentLoaded', function() {
    // Get both pay buttons (mobile and desktop)
    const payButtons = document.querySelectorAll('#pay-button');
    
    if (payButtons.length > 0) {
        // Add event listener to each button
        payButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Add loading animation to the clicked button
                this.innerHTML = '<span class="spinner">Processing...</span>';
                
                // Dynamically get the current total from the display elements
                const totalText = document.getElementById('desktop-total').textContent;
                const amount = parseFloat(totalText.replace(/,/g, ''));
                
                // Get user data
                const email = '<?php echo htmlspecialchars($user['email']); ?>';
                const firstName = '<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>';
                const lastName = '<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>';
                const ref = 'ORDER-' + Date.now() + Math.floor(Math.random() * 10000);
                
                console.log('Processing payment for amount:', amount);
                
                let handler = PaystackPop.setup({
                    key: 'pk_test_ffbf13a1e6d967184705ae17a339b027b6ec459c',
                    email: email,
                    amount: amount * 100, // Convert to kobo
                    currency: "NGN",
                    ref: ref,
                    metadata: {
                        custom_fields: [
                            {
                                display_name: "First Name",
                                variable_name: "first_name",
                                value: firstName
                            },
                            {
                                display_name: "Last Name",
                                variable_name: "last_name",
                                value: lastName
                            }
                        ]
                    },
                    onClose: function() {
                        console.log('Payment window closed');
                        // Reset all button texts
                        resetButtonTexts();
                    },
                    callback: function(response) {
                        console.log('Payment complete! Reference:', response.reference);
                        processOrder(response.reference, response.transaction);
                    }
                });
                
                // Reset the clicked button text before opening Paystack iframe
                const buttonText = `Pay Now ₦${amount.toLocaleString()}`;
                this.innerHTML = buttonText;
                
                // Small delay to ensure DOM updates before opening Paystack
                setTimeout(() => {
                    handler.openIframe();
                }, 100);
            });
        });
    }
    
    // Function to reset all button texts
    function resetButtonTexts() {
        const totalText = document.getElementById('desktop-total').textContent;
        const buttonText = `Pay Now ₦${totalText}`;
        
        payButtons.forEach(button => {
            button.innerHTML = buttonText;
        });
    }
    
    function processOrder(paymentRef, transactionId) {
    // Create comprehensive FormData
    const formData = new FormData();
    
    // Payment details
    formData.append('payment_reference', paymentRef);
    formData.append('transaction_id', transactionId);
    
    // Collect delivery method
    const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;
    formData.append('delivery_method', deliveryMethod);
    
    // Basic order details
    formData.append('email', document.getElementById('email').value);
    formData.append('note', document.getElementById('note').value || '');
    
    // Pickup or delivery specifics
    if (deliveryMethod === 'pickup') {
        formData.append('pickup_location', 
            document.querySelector('input[name="pickup_location"]:checked').value
        );
    } else {
        // Shipping fields collection
        const shippingFields = [
            'country', 'first_name', 'last_name', 'phone', 
            'address', 'state', 'city', 'zip_code'
        ];
        
        shippingFields.forEach(field => {
            const element = document.getElementById(field);
            formData.append(field, element ? element.value : '');
        });
        
        // Billing details handling
        const billingCheckbox = document.getElementById('billing_same');
        formData.append('billing_same', billingCheckbox && billingCheckbox.checked ? '1' : '0');
        
        // If billing is different
        if (!billingCheckbox || !billingCheckbox.checked) {
            const billingFields = [
                'billing_country', 'billing_first_name', 'billing_last_name', 
                'billing_phone', 'billing_address', 'billing_state', 
                'billing_city', 'billing_zip_code'
            ];
            
            billingFields.forEach(field => {
                const element = document.getElementById(field);
                formData.append(field, element ? element.value : '');
            });
        }
    }
    
    // Enhanced fetch with comprehensive error handling
    fetch('process-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            console.log('Order processed successfully:', data);
            
            // Show success modal
            const successModal = document.getElementById('paysuccess');
            if (successModal) {
                successModal.style.display = 'block';
            }
            
            // Redirect to order success page after delay
            setTimeout(() => {
                window.location.href = data.redirect_url || 'order-success.php?ref=' + paymentRef;
            }, 3000);
        } else {
            throw new Error(data.message || 'Order processing failed');
        }
    })
    .catch(error => {
        console.error('Order Processing Error:', error);
        
        // Reset all button texts in case of error
        resetButtonTexts();
        
        // User-friendly error notification
        alert('Order Processing Failed: ' + error.message);
    });
}
    
    // Make this function available globally so other scripts can access it
    window.updatePayButtons = updatePayButtons;
});

// Paystack payment handler - standalone script
// Final solution for handling both mobile and desktop payment buttons
document.addEventListener('DOMContentLoaded', function() {
    // Get both pay buttons (mobile and desktop) with unique IDs
    const mobilePayButton = document.getElementById('pay-button-mobile');
    const desktopPayButton = document.getElementById('pay-button-desktop');
    const payButtons = [mobilePayButton, desktopPayButton].filter(button => button !== null);
    
    if (payButtons.length > 0) {
        // Add event listener to each button
        payButtons.forEach(button => {
            button.addEventListener('click', handlePaymentClick);
        });
    }
    
    function handlePaymentClick(e) {
        e.preventDefault();
        
        // Add loading animation to the clicked button
        this.innerHTML = '<span class="spinner">Processing...</span>';
        
        // Dynamically get the current total from the display elements
        const totalText = document.getElementById('desktop-total').textContent;
        const amount = parseFloat(totalText.replace(/,/g, ''));
        
        // Get user data
        const email = '<?php echo htmlspecialchars($user['email']); ?>';
        const firstName = '<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>';
        const lastName = '<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>';
        const ref = 'ORDER-' + Date.now() + Math.floor(Math.random() * 10000);
        
        console.log('Processing payment for amount:', amount);
        
        let handler = PaystackPop.setup({
            key: 'pk_test_ffbf13a1e6d967184705ae17a339b027b6ec459c',
            email: email,
            amount: amount * 100, // Convert to kobo
            currency: "NGN",
            ref: ref,
            metadata: {
                custom_fields: [
                    {
                        display_name: "First Name",
                        variable_name: "first_name",
                        value: firstName
                    },
                    {
                        display_name: "Last Name",
                        variable_name: "last_name",
                        value: lastName
                    }
                ]
            },
            onClose: function() {
                console.log('Payment window closed');
                // Reset button texts
                resetButtonTexts();
            },
            callback: function(response) {
                console.log('Payment complete! Reference:', response.reference);
                processOrder(response.reference, response.transaction);
            }
        });
        
        // Reset the clicked button text before opening Paystack iframe
        const clickedButton = this;
        const buttonText = `Pay Now ₦${amount.toLocaleString()}`;
        
        // Small delay to ensure DOM updates before opening Paystack
        setTimeout(() => {
            clickedButton.innerHTML = buttonText;
            handler.openIframe();
        }, 100);
    }
    
    // Function to reset all button texts
    function resetButtonTexts() {
        const totalText = document.getElementById('desktop-total').textContent;
        const buttonText = `Pay Now ₦${totalText}`;
        
        payButtons.forEach(button => {
            button.innerHTML = buttonText;
        });
    }
    
    function processOrder(paymentRef, transactionId) {
        // Create comprehensive FormData
        const formData = new FormData();
        
        // Payment details
        formData.append('payment_reference', paymentRef);
        formData.append('transaction_id', transactionId);
        
        // Collect delivery method
        const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;
        formData.append('delivery_method', deliveryMethod);
        
        // Basic order details
        formData.append('email', document.getElementById('email').value);
        formData.append('note', document.getElementById('note').value || '');
        
        // Pickup or delivery specifics
        if (deliveryMethod === 'pickup') {
            formData.append('pickup_location', 
                document.querySelector('input[name="pickup_location"]:checked').value
            );
        } else {
            // Shipping fields collection
            const shippingFields = [
                'country', 'first_name', 'last_name', 'phone', 
                'address', 'state', 'city', 'zip_code'
            ];
            
            shippingFields.forEach(field => {
                const element = document.getElementById(field);
                formData.append(field, element ? element.value : '');
            });
            
            // Billing details handling
            const billingCheckbox = document.getElementById('billing_same');
            formData.append('billing_same', billingCheckbox && billingCheckbox.checked ? '1' : '0');
            
            // If billing is different
            if (!billingCheckbox || !billingCheckbox.checked) {
                const billingFields = [
                    'billing_country', 'billing_first_name', 'billing_last_name', 
                    'billing_phone', 'billing_address', 'billing_state', 
                    'billing_city', 'billing_zip_code'
                ];
                
                billingFields.forEach(field => {
                    const element = document.getElementById(field);
                    formData.append(field, element ? element.value : '');
                });
            }
        }
        
        // Enhanced fetch with comprehensive error handling
        fetch('process-order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Log response details for debugging
            console.log('Response Status:', response.status);
            
            // Try to parse response as JSON
            return response.json().then(data => {
                if (!response.ok) {
                    // Throw error with message from server
                    throw new Error(data.message || 'Order processing failed');
                }
                return data;
            });
        })
        .then(data => {
            console.log('Order processed successfully:', data);
            
            // Show success modal
            const successModal = document.getElementById('paysuccess');
            if (successModal) {
                successModal.style.display = 'block';
            }
            
            // Redirect to order success page
            setTimeout(() => {
                window.location.href = 'order-success.php?ref=' + paymentRef;
            }, 3000);
        })
        .catch(error => {
            console.error('Order Processing Error:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            
            // Reset all button texts in case of error
            resetButtonTexts();
            
            // User-friendly error notification
            // alert('Order Processing Failed: ' + error.message);
        });
    }
    
    // Function to update both pay buttons when total changes
    function updatePayButtons(total) {
        const formattedTotal = total.toLocaleString();
        const buttonText = `Pay Now ₦${formattedTotal}`;
        
        if (mobilePayButton) mobilePayButton.innerHTML = buttonText;
        if (desktopPayButton) desktopPayButton.innerHTML = buttonText;
    }
    
    // Make this function available for the updateTotals function
    window.updatePayButtons = updatePayButtons;
    
    // Modified updateTotals function to update both buttons
    const originalUpdateTotals = window.updateTotals;
    if (typeof originalUpdateTotals === 'function') {
        window.updateTotals = function(isExpress) {
            // Call the original function
            originalUpdateTotals(isExpress);
            
            // Also update the pay buttons
            if (window.total) {
                updatePayButtons(window.total);
            }
        };
    }
});

// Profile update functionality
document.addEventListener('DOMContentLoaded', function() {
    const updateProfileBtn = document.getElementById('update-profile-btn');
    
    if (updateProfileBtn) {
        updateProfileBtn.addEventListener('click', function() {
            // Collect all form data
            const formData = new FormData();
            
            // Collect delivery method
            const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;
            formData.append('delivery_method', deliveryMethod);
            
            // Only proceed if express delivery is selected (has address fields)
            if (deliveryMethod === 'express') {
                // Shipping details
                const shippingFields = [
                    'country', 'first_name', 'last_name', 'phone', 
                    'address', 'state', 'city', 'zip_code'
                ];
                
                shippingFields.forEach(field => {
                    const element = document.getElementById(field);
                    formData.append(field, element ? element.value : '');
                });
                
                // Billing same as shipping checkbox
                const billingCheckbox = document.getElementById('billing_same');
                formData.append('billing_same', billingCheckbox && billingCheckbox.checked ? '1' : '0');
                
                // Billing details
                const billingFields = [
                    'billing_country', 'billing_first_name', 'billing_last_name', 
                    'billing_phone', 'billing_address', 'billing_state', 
                    'billing_city', 'billing_zip_code'
                ];
                
                billingFields.forEach(field => {
                    const element = document.getElementById(field);
                    formData.append(field, element ? element.value : '');
                });
                
                // Send the profile update request
                fetch('../user/update-checkout-profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Show success message
                        alert('Address information saved to your profile!');
                    } else {
                        // Show error message
                        alert(data.message || 'Failed to update profile. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Profile Update Error:', error);
                    alert('An error occurred while updating your profile.');
                });
            } else {
                alert('Please select Express Delivery to save a delivery address to your profile.');
            }
        });
    }
});



// Script to handle the sticky order summary on mobile
document.addEventListener('DOMContentLoaded', function() {
    // Get the order summary element
    const orderSummary = document.querySelector('.fixed.top-\\[120px\\]');
    
    if (orderSummary) {
        // Initial position - we want to start at 120px, then go to 0
        const initialTopPosition = 120;
        
        // Function to update position based on scroll
        function updatePosition() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > initialTopPosition) {
                // Once scrolled past initial position, stick to top
                orderSummary.style.top = '0px';
                orderSummary.classList.add('shadow-md');
            } else {
                // Otherwise, keep initial position
                orderSummary.style.top = (initialTopPosition - scrollTop) + 'px';
                orderSummary.classList.remove('shadow-md');
            }
        }
        
        // Update immediately on load
        updatePosition();
        
        // Update on scroll
        window.addEventListener('scroll', updatePosition);
    }
});

</script>
</body>
</html>