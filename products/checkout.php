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
    // $cart_query = "SELECT DISTINCT c.cart_id, c.quantity, p.product_id, p.product_name, 
    //           i.image_path AS main_image, v.size, v.variant_id, v.status,
    //           COALESCE(v.discount_price, v.original_price) AS price,
    //           (SELECT MIN(COALESCE(pv.discount_price, pv.original_price)) 
    //            FROM product_variants pv 
    //            WHERE pv.product_id = p.product_id) AS min_variant_price
    //           FROM cart c
    //           JOIN products p ON c.product_id = p.product_id
    //           LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
    //           LEFT JOIN product_variants v ON c.variant_id = v.variant_id
    //           WHERE c.user_id = ?
    //           GROUP BY c.cart_id";  // Use GROUP BY cart_id to ensure no duplicates

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
    GROUP BY c.cart_id";

$stmt = mysqli_prepare($con, $cart_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$subtotal = 0;

// Debug information to monitor the cart items
$cart_item_ids = [];

while ($item = mysqli_fetch_assoc($result)) {
    // Debug - save cart item IDs to check for duplicates
    $cart_item_ids[] = $item['cart_id'];
    
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
    'key' => 'pk_test_ed99e88c9f3e1caf961089161641b23813a8fc41', // Replace with your public key
    'user_email' => $user['email'] ?? '',
    'amount' => $total,
    'first_name' => $profile['first_name'] ?? '',
    'last_name' => $profile['last_name'] ?? '',
    'order_ref' => 'ORDER-' . time() . rand(1000, 9999)
];

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
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="./paystack-checkout.js"></script>


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

                        <div class="flex justify-end mt-3">
    <button 
        type="button" 
        id="update-profile-btn"
        class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-[#1A237E] text-[14px] font-['Open Sans'] flex items-center gap-1 cursor-pointer rounded-[4px] border border-[#E1E1E1]">
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

         

                    <!-- <div class="mt-4">
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
                    </div> -->


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
    <button 
        type="button" 
        id="pay-button"
        class="w-full md:max-w-[377px] flex items-center justify-center gap-2 mt-4 py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
        Pay Now ₦<?php echo number_format($total); ?>
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
    
    <?php if (empty($cart_items)): ?>
        <div class="p-3 text-center">
            <p class="text-[#6b7280] text-[14px] md:text-[16px] font-['Open Sans']">Your cart is empty</p>
            <a href="../products/index.php" class="text-[#1A237E] text-[14px] font-['Open Sans'] underline">Continue Shopping</a>
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
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Price: ₦<?php echo number_format($item['price']); ?></p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">₦<?php echo number_format($item['item_total']); ?></p>
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
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="desktop-subtotal"><?php echo number_format($subtotal); ?></span></p>
                    </div>

                    <!-- <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Shipping fee (₦<?php echo number_format($base_shipping_fee); ?> x <?php echo $total_item_count; ?> items)</p>
    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦<span id="desktop-shipping"><?php echo number_format($shipping_fee); ?></span></p>

                    </div> -->

           <!-- Shipping fee display for desktop - only show breakdown for express delivery -->
<div class="flex items-center justify-between">
    <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
        <?php if ($delivery_method === 'express'): ?>
            Shipping fee (₦<?php echo number_format($base_shipping_fee); ?> x <?php echo $product_count; ?> products)
        <?php else: ?>
            Shipping fee
        <?php endif; ?>
    </p>
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
                <a href="<?php echo DOMAIN; ?>/user/orders.php" class="w-[80%] text-center text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer" id="closepssucces">
                    View Order
                </a>
            </div>
        </div>
    </div>



    <!-- Add Paystack script as external file -->

<!-- Add your external JS file with nonce if needed -->

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const deliveryRadios = document.querySelectorAll('.delivery-method-radio');
    const deliverySection = document.getElementById('delivery-section');
    const pickupSection = document.getElementById('pickup-section');
    const billingSection = document.getElementById('billing-section');
    const billingCheckbox = document.getElementById('billing_same');
    const deliveryStatusSection = document.getElementById('delivery-status');
    
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
        updateTotals(true);
    } else {
        deliverySection.classList.add('hidden');
        pickupSection.classList.remove('hidden');
        deliveryStatusSection.classList.add('hidden');
        updateTotals(false);
    }
});




document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');
    
    if (payButton) {
        payButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Use paystackData passed from PHP
            const email = '<?php echo htmlspecialchars($user['email']); ?>';
            const amount = <?php echo $total; ?>;
            const firstName = '<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>';
            const lastName = '<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>';
            const ref = 'ORDER-' + Date.now() + Math.floor(Math.random() * 10000);
            
            let handler = PaystackPop.setup({
                key: 'pk_test_ed99e88c9f3e1caf961089161641b23813a8fc41',
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
                },
                callback: function(response) {
                    console.log('Payment complete! Reference:', response.reference);
                    processOrder(response.reference, response.transaction);
                }
            });
            
            handler.openIframe();
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
            if (!billingCheckbox.checked) {
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
            
            // User-friendly error notification
            alert('Order Processing Failed: ' + error.message);
        });
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

</script>
</body>
</html>