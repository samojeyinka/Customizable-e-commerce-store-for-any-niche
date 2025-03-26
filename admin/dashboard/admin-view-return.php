<?php
// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Authentication check with admin role
requireAdminAuth();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die(mysqli_error($conn));
}

// Get the return request ID
$return_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($return_id <= 0) {
    header('Location: ./returns.php');
    exit;
}

// Get the return request details
$query = "SELECT r.id, r.order_id, r.user_id, r.product_id, r.return_reason, 
                 r.return_details, r.return_quantity, r.status, r.admin_message, 
                 r.created_at, r.updated_at, r.full_name, r.phone_number, r.contact_email,
                 p.product_name, pv.size, p.colors, oi.price, o.payment_reference,
                 (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
          FROM return_requests r
          JOIN products p ON r.product_id = p.product_id
          JOIN product_variants pv ON r.variant_id = pv.variant_id
          JOIN order_items oi ON r.order_item_id = oi.id
          JOIN orders o ON r.order_id = o.id
          WHERE r.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $return_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Return request not found
    header('Location: ./returns.php');
    exit;
}

$return = $result->fetch_assoc();
$image_path = isset($return['image_path']) ? "../assets/products/" . $return['image_path'] : "../assets/admin/img/product-placeholder.jpg";

// Process status updates
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $admin_message = $_POST['admin_message'] ?? '';
    
    // Update the return request status
    $sql = "UPDATE return_requests 
            SET status = ?, admin_message = ?, updated_at = NOW() 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $admin_message, $return_id);
    
    if ($stmt->execute()) {
        // Status updated successfully
        $message = "Return request status updated to '$status' successfully!";
        $message_type = 'success';
        
        // Refresh the return data
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $return_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $return = $result->fetch_assoc();
    } else {
        $message = "Error updating return request status: " . $stmt->error;
        $message_type = 'error';
    }
}

// Status colors
$status_colors = [
    'Processing' => 'bg-[#E8B006]',
    'Received' => 'bg-[#1A237E]',
    'Accepted' => 'bg-[#39D959]',
    'Rejected' => 'bg-red-500',
    'Completed' => 'bg-[#39D959]'
];

// Get the color for the current status
$status_color = isset($status_colors[$return['status']]) ? $status_colors[$return['status']] : 'bg-[#E8B006]';

// Format date for display
function formatDate($date) {
    if (!$date) return "Not yet";
    return date('M d, Y h:i A', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH Admin | Return Request Details</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/admin/styles/admin.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar - Include your admin sidebar here -->
        <?php include(__DIR__ . '/includes/sidebar.php'); ?>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Header - Include your admin header here -->
            <?php include(__DIR__ . '/includes/header.php'); ?>
            
            <!-- Main Content -->
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Return Request Details</h1>
                        <p class="text-gray-600">Request #<?php echo $return['id']; ?></p>
                    </div>
                    <a href="./returns.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md">
                        Back to Returns
                    </a>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <!-- Status Bar -->
                <div class="bg-white p-4 rounded-lg shadow mb-6 flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="mr-2 text-gray-700">Status:</span>
                        <span class="px-3 py-1 <?php echo $status_color; ?> text-white text-sm rounded-full">
                            <?php echo $return['status']; ?>
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2 text-gray-700">Created:</span>
                        <span class="text-gray-900"><?php echo formatDate($return['created_at']); ?></span>
                        <span class="mx-2 text-gray-400">|</span>
                        <span class="mr-2 text-gray-700">Updated:</span>
                        <span class="text-gray-900"><?php echo formatDate($return['updated_at'] ?? $return['created_at']); ?></span>
                    </div>
                </div>
                
                <!-- Return Request Content -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Left Column - Customer Info -->
                    <div class="md:col-span-1">
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h2>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Full Name</p>
                                    <p class="font-medium"><?php echo $return['full_name']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium"><?php echo $return['contact_email']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phone Number</p>
                                    <p class="font-medium"><?php echo $return['phone_number']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">User ID</p>
                                    <p class="font-medium">#<?php echo $return['user_id']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Order Information</h2>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Order ID</p>
                                    <p class="font-medium">#<?php echo $return['order_id']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Reference</p>
                                    <p class="font-medium"><?php echo $return['payment_reference']; ?></p>
                                </div>
                                <div class="pt-2">
                                    <a href="../orders/view.php?id=<?php echo $return['order_id']; ?>" class="text-blue-600 hover:underline">
                                        View Original Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Return Details -->
                    <div class="md:col-span-2">
                        <!-- Product Information -->
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Product Information</h2>
                            <div class="flex">
                                <div class="mr-4">
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo $return['product_name']; ?>" class="w-24 h-24 object-cover rounded-md">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-lg"><?php echo $return['product_name']; ?></h3>
                                    <p class="text-gray-600">Size: <?php echo $return['size']; ?> • Color: <?php echo $return['colors']; ?></p>
                                    <p class="text-gray-600">Price: ₦<?php echo number_format($return['price']); ?></p>
                                    <p class="text-gray-600">Return Quantity: <?php echo $return['return_quantity']; ?></p>
                                    <p class="font-medium text-lg mt-2">Total Refund: ₦<?php echo number_format($return['price'] * $return['return_quantity']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Return Details -->
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Return Request Details</h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500">Reason for Return</p>
                                    <p class="font-medium"><?php echo $return['return_reason']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Customer's Additional Details</p>
                                    <p class="bg-gray-50 p-3 rounded-md">
                                        <?php echo nl2br($return['return_details'] ?? 'No additional details provided.'); ?>
                                    </p>
                                </div>
                                
                                <?php if (!empty($return['admin_message'])): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Admin Message to Customer</p>
                                    <p class="bg-blue-50 p-3 rounded-md border-l-4 border-blue-500">
                                        <?php echo nl2br($return['admin_message']); ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Update Status Form -->
                        <?php if ($return['status'] !== 'Completed'): ?>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Update Return Status</h2>
                            <form method="POST" action="" id="statusForm">
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="status" name="status" class="w-full border border-gray-300 rounded-md p-2">
                                        <option value="Processing" <?php echo $return['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Received" <?php echo $return['status'] === 'Received' ? 'selected' : ''; ?>>Received</option>
                                        <option value="Accepted" <?php echo $return['status'] === 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                        <option value="Rejected" <?php echo $return['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        <option value="Completed" <?php echo $return['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <b>Processing</b>: Initial request state<br>
                                        <b>Received</b>: When product has been returned to you<br>
                                        <b>Accepted</b>: Product condition is good, approved for refund<br>
                                        <b>Rejected</b>: Request denied due to product condition or policy<br>
                                        <b>Completed</b>: Refund has been processed and return is closed
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="admin_message" class="block text-sm font-medium text-gray-700 mb-1">Message to Customer</label>
                                    <textarea id="admin_message" name="admin_message" rows="4" class="w-full border border-gray-300 rounded-md p-2" placeholder="Enter message for the customer (required for rejected returns)..."><?php echo $return['admin_message']; ?></textarea>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
                                        Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Return Progress Timeline -->
                <div class="bg-white p-6 rounded-lg shadow mt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Return Progress Timeline</h2>
                    
                    <div class="relative">
                        <!-- Timeline container -->
                        <div class="ml-6 space-y-8 relative before:absolute before:inset-0 before:h-full before:w-[2px] before:bg-gray-200 before:left-[7px]">
                            <!-- Processing Step -->
                            <div class="relative">
                                <div class="flex items-center">
                                    <div class="z-10 flex items-center justify-center w-6 h-6 rounded-full <?php echo ($return['status'] !== 'Processing') ? 'bg-gray-200' : $status_colors['Processing']; ?> shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium">Processing</h3>
                                        <p class="text-sm text-gray-500">Initial review of the return request</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Received Step -->
                            <div class="relative">
                                <div class="flex items-center">
                                    <div class="z-10 flex items-center justify-center w-6 h-6 rounded-full <?php echo (in_array($return['status'], ['Received', 'Accepted', 'Completed'])) ? $status_colors['Received'] : 'bg-gray-200'; ?> shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium">Received</h3>
                                        <p class="text-sm text-gray-500">Product has been returned to us</p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($return['status'] !== 'Rejected'): ?>
                            <!-- Accepted Step -->
                            <div class="relative">
                                <div class="flex items-center">
                                    <div class="z-10 flex items-center justify-center w-6 h-6 rounded-full <?php echo (in_array($return['status'], ['Accepted', 'Completed'])) ? $status_colors['Accepted'] : 'bg-gray-200'; ?> shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium">Accepted</h3>
                                        <p class="text-sm text-gray-500">Return approved, refund will be processed</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Completed Step -->
                            <div class="relative">
                                <div class="flex items-center">
                                    <div class="z-10 flex items-center justify-center w-6 h-6 rounded-full <?php echo ($return['status'] === 'Completed') ? $status_colors['Completed'] : 'bg-gray-200'; ?> shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium">Completed</h3>
                                        <p class="text-sm text-gray-500">Refund has been processed</p>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <!-- Rejected Step (alternative flow) -->
                            <div class="relative">
                                <div class="flex items-center">
                                    <div class="z-10 flex items-center justify-center w-6 h-6 rounded-full <?php echo $status_colors['Rejected']; ?> shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium">Rejected</h3>
                                        <p class="text-sm text-gray-500">Return request denied</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('statusForm');
            const statusSelect = document.getElementById('status');
            const adminMessageTextarea = document.getElementById('admin_message');
            
            if (form) {
                form.addEventListener('submit', function(event) {
                    const status = statusSelect.value;
                    const message = adminMessageTextarea.value.trim();
                    
                    // Require message for rejected status
                    if (status === 'Rejected' && message === '') {
                        event.preventDefault();
                        alert('Please provide a reason for rejecting the return request.');
                        adminMessageTextarea.focus();
                    }
                    
                    // Recommend message for other status changes
                    if (status !== 'Rejected' && message === '' && !confirm('You haven\'t provided a message to the customer. Continue anyway?')) {
                        event.preventDefault();
                        adminMessageTextarea.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>