<?php
// order-success.php - Display confirmation after successful order

// Start session
session_start();

// Include database connection
include('../config/connect.php');
// Include authentication utility
require_once '../includes/auth/auth.php';

// Require authentication
requireAuth();

// Verify if order was completed
if (!isset($_SESSION['order_completed']) || $_SESSION['order_completed'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Get order ID
$order_id = $_SESSION['order_id'] ?? $_GET['order_id'] ?? '';

if (empty($order_id)) {
    header("Location: ../index.php");
    exit();
}

// Clear session variables
unset($_SESSION['order_completed']);
unset($_SESSION['order_id']);

// Get order details from database
$order_query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = mysqli_prepare($con, $order_query);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($order_result);

// If order not found, redirect
if (!$order) {
    header("Location: ../index.php");
    exit();
}

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Check if order belongs to current user
if ($order['user_id'] != $user_id) {
    header("Location: ../index.php");
    exit();
}

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = mysqli_prepare($con, $items_query);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
$order_items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $order_items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | Order Confirmed</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <main class="bg-[#FEFEFE] relative min-h-screen flex flex-col">
        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="../products/cart.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Cart</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="checkout.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Check Out</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-medium">Order Confirmed</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] md:w-[80%] lg:w-[70%] mx-auto flex flex-col gap-6 py-8 flex-grow">
            <!-- Success Message -->
            <div class="w-full rounded-lg border border-green-200 bg-green-50 p-6 text-center">
                <div class="flex justify-center">
                    <i class="fa-solid fa-circle-check text-[80px] text-[#C2185B] mb-4" alt="Success"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-medium text-gray-800 mb-2">Order Confirmed</h1>
                <p class="text-gray-600 mb-4">Your order has been placed successfully. A confirmation email has been sent to you.</p>
                <p class="font-medium text-gray-800">Order ID: <span class="text-[#C2185B]"><?php echo htmlspecialchars($order_id); ?></span></p>
            </div>
            
            <!-- Order Summary -->
            <div class="w-full">
                <h2 class="text-xl md:text-2xl font-medium text-gray-800 mb-4">Order Summary</h2>
                
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                    <!-- Order Details -->
                    <div class="bg-gray-50 p-4 border-b border-gray-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Order Date</p>
                                <p class="font-medium"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Order Status</p>
                                <p class="font-medium uppercase text-yellow-600"><?php echo htmlspecialchars($order['order_status']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Delivery Method</p>
                                <p class="font-medium capitalize"><?php echo htmlspecialchars($order['delivery_method']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment Reference</p>
                                <p class="font-medium"><?php echo htmlspecialchars($order['payment_reference']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="p-4">
                        <h3 class="font-medium text-gray-800 mb-3">Items</h3>
                        
                        <div class="space-y-4">
                            <?php foreach ($order_items as $item): ?>
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2 border-b border-gray-100">
                                <div class="flex items-start gap-3">
                                    <div class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                </div>
                                <div class="flex flex-col md:flex-row md:items-center md:gap-8 mt-2 md:mt-0">
                                    <div class="text-sm text-gray-600">
                                        <span>Size: <?php echo htmlspecialchars($item['size']); ?></span> |
                                        <span>Qty: <?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="font-medium">₦<?php echo number_format((float)$item['item_total']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="bg-gray-50 p-4 border-t border-gray-200">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">₦<?php echo number_format($order['order_total'] - $order['shipping_fee']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping Fee</span>
                                <span class="font-medium">₦<?php echo number_format((float)$order['shipping_fee']); ?></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <span class="text-gray-800 font-medium">Total</span>
                                <span class="font-medium text-lg text-[#C2185B]">₦<?php echo number_format((float)$order['order_total']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row gap-4 justify-center mt-4">
                <a href="../products/index.php" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg text-center font-medium hover:bg-gray-300 transition">
                    Continue Shopping
                </a>
                <a href="../account/orders.php" class="px-6 py-3 bg-[#C2185B] text-white rounded-lg text-center font-medium hover:bg-[#0c1450] transition">
                    View Your Orders
                </a>
            </div>
        </div>
    </main>
</body>
</html>