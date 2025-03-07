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

// Handle item removal via AJAX if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $cart_id = intval($_POST['cart_id']);
    
    // Delete specific item from cart
    $remove_query = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $remove_query);
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
    
    $response = ['success' => false];
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

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
    // ... (rest of the existing checkout processing code remains the same)
}

// Prepare data for checkout page
$cart_items = [];
$subtotal = 0;

// Check if coming from Buy Now button
if (isset($_GET['buy_now']) && isset($_GET['product_id']) && isset($_GET['variant_id'])) {
    // ... (existing buy now code remains the same)
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

<!-- Rest of the HTML remains the same until the order items section -->
<div class="w-full flex flex-col gap-3 rounded-[4px] bg-[#E8E9F2] md:bg-[#EEEEEE] mt-[9rem] md:mt-0 p-2">
    <div class="flex items-center justify-between">
        <p class="text-[#262626] text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Your Order</p>
        <img src="../assets/products/down2.svg" class="rotate-[180deg] cursor-pointer md:hidden" />
    </div>
    
    <?php foreach ($cart_items as $item): ?>
    <div class="cart-item flex items-center justify-between" data-cart-id="<?php echo $item['cart_id']; ?>">
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
        <div class="flex items-center gap-2">
            <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">₦<?php echo number_format($item['price'] * $item['quantity']); ?></p>
            <button class="remove-item-btn text-red-500 hover:text-red-700" data-cart-id="<?php echo $item['cart_id']; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</main>


<!-- Add script in the existing <script> section -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Existing JavaScript code (from previous artifact)...

        // Item removal functionality
        const removeItemButtons = document.querySelectorAll('.remove-item-btn');
        removeItemButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');
                const cartItemElement = this.closest('.cart-item');

                // Confirmation before removal
                if (!confirm('Are you sure you want to remove this item from your cart?')) {
                    return;
                }

                // Send AJAX request to remove item
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `remove_item=1&cart_id=${cartId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM
                        cartItemElement.remove();

                        // Recalculate subtotal
                        let newSubtotal = 0;
                        document.querySelectorAll('.cart-item').forEach(item => {
                            const price = parseFloat(item.querySelector('.font-medium').textContent.replace('₦', '').replace(/,/g, ''));
                            newSubtotal += price;
                        });

                        // Update subtotal displays
                        document.getElementById('mobile-subtotal').textContent = newSubtotal.toLocaleString();
                        document.getElementById('desktop-subtotal').textContent = newSubtotal.toLocaleString();

                        // Recalculate total (with current shipping fee)
                        const currentShippingFee = parseFloat(document.getElementById('mobile-shipping').textContent.replace(/,/g, ''));
                        const newTotal = newSubtotal + currentShippingFee;

                        // Update total displays
                        document.getElementById('mobile-total').textContent = newTotal.toLocaleString();
                        document.getElementById('desktop-total').textContent = newTotal.toLocaleString();

                        // Optionally, redirect to cart if no items left
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            window.location.href = '../products/cart.php';
                        }
                    } else {
                        alert('Failed to remove item. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    });
</script>
</body>
</html>