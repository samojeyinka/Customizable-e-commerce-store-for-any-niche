<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Order ID is required");
}

$order_id = intval($_GET['id']);

// Get order details
$order_sql = "SELECT 
                orders.id AS order_id,
                orders.order_total,
                orders.delivery_method,
                orders.order_status,
                orders.created_at AS order_date,
                CONCAT(profiles.first_name, ' ', profiles.last_name) AS customer_name
            FROM orders
            LEFT JOIN profiles ON orders.user_id = profiles.user_id
            WHERE orders.id = $order_id";

$order_result = $conn->query($order_sql);
if (!$order_result || $order_result->num_rows === 0) {
    die("Error: Order not found");
}

$order = $order_result->fetch_assoc();

// Format order date
if (!empty($order['order_date'])) {
    try {
        $dt = new DateTime($order['order_date']);
        $order['formatted_date'] = $dt->format('d/m/Y');
        $order['formatted_time'] = $dt->format('h:ia');
    } catch (Exception $e) {
        $order['formatted_date'] = 'Invalid Date';
        $order['formatted_time'] = 'Invalid Time';
    }
} else {
    $order['formatted_date'] = 'N/A';
    $order['formatted_time'] = 'N/A';
}

// Get order items with product details
$items_sql = "SELECT 
                oi.id AS item_id,
                oi.product_id,
                oi.variant_id,
                oi.quantity,
                oi.price AS item_price,
                p.product_name,
                pv.size,
                pv.texture,
                pi.image_path AS main_image,
                pi.image_id
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.product_id
            LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id
            LEFT JOIN (
                SELECT product_id, image_path, image_id
                FROM product_images 
                WHERE is_main = 1
            ) pi ON oi.product_id = pi.product_id
            WHERE oi.order_id = $order_id";

$items_result = $conn->query($items_sql);
if (!$items_result) {
    die("Error in query: " . $conn->error);
}

$order_items = $items_result->fetch_all(MYSQLI_ASSOC);

// Define a domain constant like in cart.php if not already defined
if (!defined('DOMAIN')) {
    define('DOMAIN', '../../'); // Adjust this based on your actual domain setup
}

// Calculate total quantity and amount
$total_quantity = 0;
$total_amount = 0;

foreach ($order_items as &$item) {
    $item['total_price'] = $item['quantity'] * $item['item_price'];
    $total_quantity += $item['quantity'];
    $total_amount += $item['total_price'];
    
    // Handle product image path - adjust based on admin dashboard location
    // If image_path is stored as relative path in DB
    if (!empty($item['image_path'])) {
        // For admin/dashboard location, need to go up two levels
        $item['image_url'] = "../../assets/products/" . basename($item['image_path']);
    } else {
        // Default image if none found
        $item['image_url'] = "../../assets/products/default.svg";
    }
}

// Status classes configuration (same as in orders.php)
$status_classes = [
    'Confirmed' => 'bg-[#1A7E79]',
    'Processing' => 'bg-[#E8B006]',
    'Shipped' => 'bg-[#1A237E]',
    'Dispatched' => 'bg-[#D51E5E]',
    'Delivered' => 'bg-[#39D959]',
    'Refunded' => 'bg-[#D93939]',
    'Cancelled' => 'bg-red-500'
];

// Get status class
$status_class = $status_classes[$order['order_status']] ?? 'bg-[#E8B006]';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <title>Order #<?php echo $order_id; ?> Details</title>
</head>

<body class="relative">
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <!-- Order Summary Card -->
        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
                <div>
                    <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">Order #<?php echo htmlspecialchars($order['order_id']); ?></h1>
                    <p class="text-[14px] text-gray-500">Placed on <?php echo htmlspecialchars($order['formatted_date'] . ' at ' . $order['formatted_time']); ?></p>
                </div>
                <div class="mt-2 md:mt-0">
                    <span class="py-1 px-4 <?php echo $status_class; ?> text-white text-[16px] font-['Open Sans'] rounded-[28px]">
                        <?php echo htmlspecialchars($order['order_status']); ?>
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <h2 class="text-[16px] font-semibold mb-1">Customer</h2>
                    <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
                </div>
                <div>
                    <h2 class="text-[16px] font-semibold mb-1">Delivery Method</h2>
                    <p><?php echo htmlspecialchars($order['delivery_method']); ?></p>
                </div>
                <div>
                    <h2 class="text-[16px] font-semibold mb-1">Total Amount</h2>
                    <p>₦<?php echo number_format($order['order_total']); ?></p>
                </div>
            </div>
            
            <a href="./orders.php" class="inline-flex items-center gap-2 text-blue-700 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to Orders
            </a>
        </div>

        <!-- Order Items Table -->
        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <h2 class="text-[18px] font-Onest font-semibold mb-3">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table cols="" class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th class="p-2">Image</th>
                        <th class="p-2">Product</th>
                        <th class="p-2">Variant</th>
                        <th class="p-2">Price</th>
                        <th class="p-2">Quantity</th>
                        <th class="p-2">Total</th>
                    </thead>
                    <tbody>
                        <?php if (empty($order_items)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No items found for this order</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($order_items as $item): ?>
                                <tr class="border-b border-gray-200">
                                    <td class="p-3">
                                        <img src="<?php echo !empty($item['main_image']) ? DOMAIN . '/assets/products/' . $item['main_image'] : DOMAIN . '/assets/products/default.svg'; ?>" 
                                            alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                            class="w-16 h-16 object-cover rounded-md">
                                    </td>
                                    <td class="p-3">
                                        <p class="text-[15px] font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="text-[13px] text-gray-500">ID: <?php echo htmlspecialchars($item['product_id']); ?></p>
                                    </td>
                                    <td class="p-3">
                                        <p class="text-[14px]">Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                        <p class="text-[14px]">Texture: <?php echo htmlspecialchars($item['texture']); ?></p>
                                    </td>
                                    <td class="p-3">₦<?php echo number_format($item['item_price']); ?></td>
                                    <td class="p-3"><?php echo $item['quantity']; ?></td>
                                    <td class="p-3 font-semibold">₦<?php echo number_format($item['total_price']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- Summary row -->
                            <tr class="bg-gray-50">
                                <td colspan="4" class="p-3 text-right">Order Summary:</td>
                                <td class="p-3 font-semibold"><?php echo $total_quantity; ?></td>
                                <td class="p-3 font-semibold">₦<?php echo number_format($total_amount); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="w-full rounded-[16px] bg-white mx-auto p-3 flex flex-wrap gap-3">
            <a href="../products/show.php?reorder=<?php echo $order['order_id']; ?>" class="py-2 px-4 bg-blue-700 text-white rounded-md hover:bg-blue-800">Re-Order</a>
            <a href="./track-order.php?id=<?php echo $order['order_id']; ?>" class="py-2 px-4 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Track Order</a>
            <a href="../products/review.php?order=<?php echo $order['order_id']; ?>" class="py-2 px-4 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Leave a Review</a>
            <a href="./report-issue.php?id=<?php echo $order['order_id']; ?>" class="py-2 px-4 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Report an Issue</a>
        </div>
    </div>
</body>
</html>