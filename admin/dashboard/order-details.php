<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";



// Database connection
$conn = db();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Order ID is required");
}

$order_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $status_notes = isset($_POST['status_notes']) ? $conn->real_escape_string($_POST['status_notes']) : '';
    $dispatcher_details = isset($_POST['dispatcher_details']) ? $conn->real_escape_string($_POST['dispatcher_details']) : '';
    
    // First get the current order to check current status
    $get_sql = "SELECT order_status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($get_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $status_message = "Error: Order not found";
    } else {
        $current_order = $result->fetch_assoc();
        $current_status = $current_order['order_status'];
        
        // Don't update if status hasn't changed
        if ($current_status == $new_status) {
            $status_message = "Status is already set to " . htmlspecialchars($new_status);
        } else {
            // Prepare timestamp column name based on status
            $timestamp_column = '';
switch ($new_status) {
                case 'Processing':
                    $timestamp_column = 'processed_at';
                    break;
                case 'Confirmed':
                    $timestamp_column = 'confirmed_at';
                    break;
                case 'Shipped':
                    $timestamp_column = 'shipped_at';
                    break;
                case 'Delivered':
                    $timestamp_column = 'delivered_at';
                    break;
                case 'Cancelled':
                    $timestamp_column = 'cancelled_at';
                    break;
                case 'Returned':
                    $timestamp_column = 'returned_at';
                    break;
                default:
                    $timestamp_column = ''; // Invalid status
            }
            $now = date('Y-m-d H:i:s');
            
            // Check if the columns exist in the orders table
            $check_columns_sql = "SHOW COLUMNS FROM orders LIKE '$timestamp_column'";
            $check_result = $conn->query($check_columns_sql);
            $column_exists = ($check_result && $check_result->num_rows > 0);
            
            if ($column_exists) {
                // Update order status with the specific timestamp column
                $update_sql = "UPDATE orders SET 
                                order_status = ?, 
                                $timestamp_column = ?, 
                                updated_at = ?, 
                                status_notes = ?, 
                                dispatcher_details = ? 
                              WHERE id = ?";
                
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("sssssi", $new_status, $now, $now, $status_notes, $dispatcher_details, $order_id);
            } else {
                // Fall back to the old update method if timestamp columns don't exist
                $update_sql = "UPDATE orders SET 
                                order_status = ?, 
                                updated_at = ?, 
                                status_notes = ?, 
                                dispatcher_details = ? 
                              WHERE id = ?";
                
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ssssi", $new_status, $now, $status_notes, $dispatcher_details, $order_id);
            }
            
            if ($stmt->execute()) {
                $status_message = "Order status successfully updated to " . htmlspecialchars($new_status);
            } else {
                $status_message = "Error updating order status: " . $conn->error;
            }
            $stmt->close();
            
// Log status update to order_status_history table (if you want to keep a history)
            $history_sql = "INSERT INTO order_status_history (order_id, old_status, new_status, notes, dispatcher_details, changed_by, changed_at) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            // Assuming you have user authentication and can get the current user ID
            $current_user = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;
            
            $stmt = $conn->prepare($history_sql);
            $stmt->bind_param("isssis", $order_id, $current_status, $new_status, $status_notes, $dispatcher_details, $current_user);
            
            // This is optional - if the table doesn't exist, it will just fail silently
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Get order details
$order_sql = "SELECT 
                orders.id AS order_id,
                orders.order_total,
                orders.delivery_method,
                orders.order_status,
                orders.status_notes,
                orders.dispatcher_details,
                orders.created_at AS order_date,
                CONCAT(profiles.first_name, ' ', profiles.last_name) AS customer_name,
                profiles.phone,
                profiles.address,
                profiles.city,
                profiles.state,
                profiles.zip_code,
                profiles.country,
                users.email
            FROM orders
            LEFT JOIN profiles ON orders.user_id = profiles.user_id
            LEFT JOIN users ON orders.user_id = users.id
            WHERE orders.id = ?";

$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if (!$order_result || $order_result->num_rows === 0) {
    die("Error: Order not found");
}

$order = $order_result->fetch_assoc();
$stmt->close();

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
            WHERE oi.order_id = ?";

$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

if (!$items_result) {
    die("Error in query: " . $conn->error);
}

$order_items = $items_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Define a domain constant if not already defined
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
    if (!empty($item['main_image'])) {
        // For admin/dashboard location, need to go up two levels
        $item['image_url'] = product_image_url($item['main_image'], "../../assets/products/");
    } else {
        // Default image if none found
        $item['image_url'] = "../../assets/products/default.svg";
    }
}

// Status classes configuration (same as in orders.php)
$status_classes = [
    'Processing' => 'bg-[#E8B006]',
    'Confirmed' => 'bg-[#1A7E79]',
    'Shipped' => 'bg-[#C2185B]',
    'Delivered' => 'bg-[#39D959]',
    'Cancelled' => 'bg-red-500',
    'Returned' => 'bg-[#9C27B0]'
];

// Get status class
$status_class = $status_classes[$order['order_status']] ?? 'bg-[#E8B006]';

// Determine active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'customer-details';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<title>Order #<?php echo $order_id; ?> Details</title>

<?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="relative">
<?php
include "./header.php";
include "./sidebar.php"
?>

<div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
    <div class="w-full rounded-[16px] bg-white mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-[20px] font-Onest font-semibold">Order #<?php echo $order_id; ?> Details</h1>
            <a href="./orders.php" class="cursor-pointer">
                <i class="fa-solid fa-xmark text-[24px]" alt="close"></i>
            </a>
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mb-6">
            <button class="tab-button py-3 px-4 <?php echo ($active_tab == 'customer-details') ? 'active' : ''; ?>" data-tab="customer-details">
                Customer Details
            </button>
            <button class="tab-button py-3 px-4 <?php echo ($active_tab == 'update-status') ? 'active' : ''; ?>" data-tab="update-status">
                Update Status
            </button>
            <button class="tab-button py-3 px-4 <?php echo ($active_tab == 'order-items') ? 'active' : ''; ?>" data-tab="order-items">
                Order Items
            </button>
        </div>

        <!-- Customer Details Tab -->
        <div id="customer-details" class="tab-content <?php echo ($active_tab == 'customer-details') ? 'active' : ''; ?>">
            <h2 class="text-[18px] font-Onest font-medium mb-4">Customer Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-[14px] text-gray-500 mb-1">Personal Information</h3>
                        <div class="space-y-2">
                            <div>
                                <p class="text-[14px] text-gray-500">Full Name</p>
                                <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                            </div>
                            <div>
                                <p class="text-[14px] text-gray-500">Email</p>
                                <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <p class="text-[14px] text-gray-500">Phone Number</p>
                                <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-[14px] text-gray-500 mb-1">Order Information</h3>
                        <div class="space-y-2">
                            <div>
                                <p class="text-[14px] text-gray-500">Order Date</p>
                                <p class="text-[16px] font-medium"><?php echo $order['formatted_date'] . ' ' . $order['formatted_time']; ?></p>
                            </div>
                            <div>
                                <p class="text-[14px] text-gray-500">Delivery Method</p>
                                <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['delivery_method'] ?? 'Standard Delivery'); ?></p>
                            </div>
                            <div>
                                <p class="text-[14px] text-gray-500">Current Status</p>
                                <span class="inline-block py-1 px-4 mt-1 <?php echo $status_class; ?> text-white text-[14px] rounded-[28px]">
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-[14px] text-gray-500 mb-1">Personal & Shipping Details</h3>
                    <div class="p-4 border rounded-md">
                        <div class="space-y-3">
                            <h4 class="text-[15px] font-medium border-b pb-1">Personal Information</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[14px] text-gray-500">Full Name</p>
                                    <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                </div>
                                <div>
                                    <p class="text-[14px] text-gray-500">Email</p>
                                    <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                                </div>
                                <div>
                                    <p class="text-[14px] text-gray-500">Phone</p>
                                    <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            
                            <h4 class="text-[15px] font-medium border-b pb-1 mt-3">Shipping Address</h4>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-[14px] text-gray-500">Address</p>
                                    <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[14px] text-gray-500">City</p>
                                        <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['city'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-[14px] text-gray-500">State</p>
                                        <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['state'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[14px] text-gray-500">Zip Code</p>
                                        <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['zip_code'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-[14px] text-gray-500">Country</p>
                                        <p class="text-[16px] font-medium"><?php echo htmlspecialchars($order['country'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status Tab -->
        <div id="update-status" class="tab-content <?php echo ($active_tab == 'update-status') ? 'active' : ''; ?>">
            <h2 class="text-[18px] font-Onest font-medium mb-4">Update Order Status</h2>
            
            <?php if(isset($status_message)): ?>
            <div class="mb-4 p-3 rounded-md <?php echo strpos($status_message, 'Error') === false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                <?php echo $status_message; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="?id=<?php echo $order_id; ?>&tab=update-status" class="max-w-md space-y-4">
                <div class="space-y-2">
                    <label class="block text-[14px] text-gray-700">Current Status</label>
                    <div class="inline-block py-1 px-4 <?php echo $status_class; ?> text-white rounded-[28px]">
                        <?php echo htmlspecialchars($order['order_status']); ?>
                    </div>
                </div>

              

                <div class="space-y-2">
    <label for="new_status" class="block text-[14px] text-gray-700">New Status</label>
<select name="new_status" id="new_status" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="Processing" <?php echo $order['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
        <option value="Confirmed" <?php echo $order['order_status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
        <option value="Shipped" <?php echo $order['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
        <option value="Delivered" <?php echo $order['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
        <option value="Cancelled" <?php echo $order['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
        <option value="Returned" <?php echo $order['order_status'] === 'Returned' ? 'selected' : ''; ?>>Returned</option>
    </select>
</div>

                <div class="space-y-2">
                    <label for="status_notes" class="block text-[14px] text-gray-700">Status Update Notes (Optional)</label>
                    <textarea name="status_notes" id="status_notes" rows="4" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

<div id="dispatcherDetailsSection" class="space-y-2 hidden">
                    <label for="dispatcher_details" class="block text-[14px] text-gray-700">Please enter the dispatcher details or shipping information (only required if order status is "Shipped")</label>
                    <textarea name="dispatcher_details" id="dispatcher_details" rows="4" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit" name="update_status" class="px-4 py-2 bg-[#C2185B] text-white rounded-md hover:bg-blue-800 transition duration-200">
                    Update Status
                </button>
            </form>
            
            <!-- Order Timeline -->
            <div class="mt-8">
                <h2 class="text-[18px] font-Onest font-medium mb-4">Order Timeline</h2>
                
                <div class="relative pl-8 border-l-2 border-gray-200">
                    <div class="mb-6 relative">
                        <div class="absolute -left-[25px] top-0 w-4 h-4 rounded-full bg-[#C2185B]"></div>
                        <div class="mb-1">
                            <span class="text-[16px] font-medium"><?php echo htmlspecialchars($order['order_status']); ?></span>
                            <span class="text-[14px] text-gray-500 ml-2"><?php echo $order['formatted_date'] . ' ' . $order['formatted_time']; ?></span>
                        </div>
                        <?php if(!empty($order['status_notes']) || !empty($order['dispatcher_details'])): ?>
                            <?php if(!empty($order['status_notes'])): ?>
                                <p class="text-[14px] text-gray-600 mb-1">
                                    <span class="font-medium">Notes:</span> <?php echo htmlspecialchars($order['status_notes']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if(!empty($order['dispatcher_details']) && ($order['order_status'] === 'Shipped')): ?>
                                <p class="text-[14px] text-blue-600">
                                    <span class="font-medium">Shipping Info:</span> <?php echo htmlspecialchars($order['dispatcher_details']); ?>
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-[14px] text-gray-600">Current order status</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-6 relative">
                        <div class="absolute -left-[25px] top-0 w-4 h-4 rounded-full bg-[#C2185B]"></div>
                        <div class="mb-1">
                            <span class="text-[16px] font-medium">Order Placed</span>
                            <span class="text-[14px] text-gray-500 ml-2"><?php echo $order['formatted_date'] . ' ' . $order['formatted_time']; ?></span>
                        </div>
                        <p class="text-[14px] text-gray-600">Order #<?php echo $order_id; ?> was placed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items Tab -->
        <div id="order-items" class="tab-content <?php echo ($active_tab == 'order-items') ? 'active' : ''; ?>">
            <h2 class="text-[18px] font-Onest font-medium mb-4">Order Items</h2>
            
            <?php if (empty($order_items)): ?>
                <p class="text-center p-4 border rounded-md">No items found for this order</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($order_items as $item): ?>
                    <div class="p-4 border rounded-md">
                        <div class="flex items-center">
                            <div class="w-16 h-16 mr-4 rounded overflow-hidden">
                                <img src="<?php echo !empty($item['main_image']) ? product_image_url($item['main_image'], '../../assets/products/') : '../../assets/products/default.svg'; ?>" 
                                    alt="<?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?>" 
                                    class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-[16px] font-medium">Name: <?php echo htmlspecialchars($item['product_name'] ?? 'Product Name'); ?></h3>
                                <?php if (!empty($item['texture'])): ?>
                                <p class="text-[14px]">Texture: <?php echo htmlspecialchars($item['texture']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['size'])): ?>
                                <p class="text-[14px]">Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                <?php endif; ?>
                                <p class="text-[14px]">Quantity: <?php echo intval($item['quantity']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[18px] font-medium">₦<?php echo number_format((float)$item['total_price']); ?></p>
                                <p class="text-[14px] text-gray-500">₦<?php echo number_format((float)$item['item_price']); ?> each</p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Summary -->
                <div class="mt-8 border-t pt-4">
                    <h2 class="text-[18px] font-Onest font-medium mb-4">Order Summary</h2>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[16px]">Total Items:</span>
                            <span class="text-[16px] font-medium"><?php echo $total_quantity; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[16px]">Subtotal:</span>
                            <span class="text-[16px] font-medium">₦<?php echo number_format((float)$order['order_total']); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[16px]">Delivery Fee:</span>
                            <span class="text-[16px] font-medium">₦0</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t mt-2">
                            <span class="text-[18px] font-medium">Total:</span>
                            <span class="text-[18px] font-bold">₦<?php echo number_format((float)$order['order_total']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Method -->
                <div class="mt-8">
                    <h2 class="text-[18px] font-Onest font-medium mb-2">Delivery Method</h2>
                    <p class="text-[16px]"><?php echo htmlspecialchars($order['delivery_method'] ?? 'Standard Delivery'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        const statusSelect = document.getElementById('new_status');
        const dispatcherSection = document.getElementById('dispatcherDetailsSection');
        const dispatcherField = document.getElementById('dispatcher_details');

        // Tab switching
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to current tab and content
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
                
                // Update URL with tab parameter
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.pushState({}, '', url);
            });
        });

        // Show/hide dispatcher details based on status
        if (statusSelect) {
            // Check initial value to set initial visibility
            if (statusSelect.value === 'Shipped') {
                dispatcherSection.classList.remove('hidden');
                dispatcherField.setAttribute('required', 'required');
            }
            
            // Add change event listener
            statusSelect.addEventListener('change', function() {
                console.log("Status changed to: " + this.value); // Debug
                if (this.value === 'Shipped') {
                    dispatcherSection.classList.remove('hidden');
                    dispatcherField.setAttribute('required', 'required');
                } else {
                    dispatcherSection.classList.add('hidden');
                    dispatcherField.removeAttribute('required');
                }
            });
        }
    });
</script>



</body>
</html>