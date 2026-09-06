<?php
// Include authentication utility
require_once '../includes/auth/auth.php';
require_once __DIR__ . "/../config/config.php";

// Authentication check
requireAuth();

// Get user data
$user = getCurrentUser();
$user_id = $user['id'];

$conn = db();



// Initialize variables
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success_message = '';
$error_message = '';
$order = null;
$order_items = [];

// Validate parameters
if (!$order_id) {
    $error_message = "Missing order ID.";
} else {
    // Get order details to verify it belongs to the user
    $order_sql = "SELECT o.*, 
                    DATE_FORMAT(o.created_at, '%M %d, %Y') as order_date,
                    (SELECT DATE_FORMAT(changed_at, '%M %d, %Y') FROM order_status_history 
                     WHERE order_id = o.id AND new_status = 'Delivered' 
                     ORDER BY changed_at DESC LIMIT 1) as delivery_date,
                    p.first_name, p.last_name, p.phone, p.address, p.city, p.state
                FROM orders o
                LEFT JOIN profiles p ON o.user_id = p.user_id
                WHERE o.id = ? AND o.user_id = ?";
                 
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $error_message = "Order not found or doesn't belong to you.";
    } else {
        $order = $result->fetch_assoc();
        
        // Get order items
        $items_sql = "SELECT oi.*, 
                        p.product_name, 
                        pv.size, 
                        pv.texture,
                        (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.product_id
                    JOIN product_variants pv ON oi.variant_id = pv.variant_id
                    WHERE oi.order_id = ?";
        
        $stmt = $conn->prepare($items_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items_result = $stmt->get_result();
        
        while ($item = $items_result->fetch_assoc()) {
            $order_items[] = $item;
        }
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_issue'])) {
    $issue_type = $conn->real_escape_string($_POST['issue_type']);
    $issue_description = $conn->real_escape_string($_POST['issue_description']);
    
    // Validate input
    if (empty($issue_type)) {
        $error_message = "Please select an issue type.";
    } else if (empty($issue_description)) {
        $error_message = "Please describe your issue.";
    } else if (strlen($issue_description) < 20) {
        $error_message = "Please provide more details about your issue (at least 20 characters).";
    } else {
// Insert the issue
        $insert_sql = "INSERT INTO order_issues (order_id, user_id, issue_type, issue_description) 
                       VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiss", $order_id, $user_id, $issue_type, $issue_description);
        
        if ($stmt->execute()) {
            $issue_id = $stmt->insert_id;
            
            // Handle image uploads if enabled
            $upload_success = true;
            if (!empty($_FILES['issue_images']['name'][0])) {
                $upload_dir = '../assets/issues/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                foreach ($_FILES['issue_images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['issue_images']['error'][$key] == 0) {
                        $filename = $issue_id . '_' . time() . '_' . $_FILES['issue_images']['name'][$key];
                        $filename = preg_replace('/\s+/', '_', $filename); // Replace spaces with underscores
                        $filepath = $upload_dir . $filename;
                        
                        if (move_uploaded_file($tmp_name, $filepath)) {
                            // Save image info to database
                            $image_sql = "INSERT INTO order_issue_images (issue_id, image_path) VALUES (?, ?)";
                            $istmt = $conn->prepare($image_sql);
                            $relative_path = 'issues/' . $filename;
                            $istmt->bind_param("is", $issue_id, $relative_path);
                            
                            if (!$istmt->execute()) {
                                $upload_success = false;
                            }
                            $istmt->close();
                        } else {
                            $upload_success = false;
                        }
                    }
                }
            }
            
            // Include notifications functions
            require_once '../includes/notifications.php';
            
            // Create notification for admin
            add_notification(
                $conn,
                'issue',
                "New Order Issue Reported",
                "A new issue has been reported for Order #$order_id. Issue type: $issue_type",
                $issue_id,
                'issue',
                null,
                1
            );
            
            // Create notification for the user too
            add_notification(
                $conn,
                'issue_confirmation',
                'Issue Report Received',
                "Your issue report for Order #$order_id has been received. We'll get back to you soon.",
                $issue_id,
                'issue',
                $user_id,
                0
            );
            
            $success_message = "Your issue has been submitted successfully. We'll get back to you soon.";
            
        } else {
            $error_message = "Error submitting your issue. Please try again.";
        }
    }
}

// Function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Processing':
            return 'bg-[#E8B006]';
        case 'Shipped':
            return 'bg-[#C2185B]';
        case 'Delivered':
            return 'bg-[#39D959]';
        case 'Cancelled':
            return 'bg-red-500';
        case 'Returned':
            return 'bg-[#9C27B0]';
        default:
            return 'bg-gray-500';
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
    <title>GLOREFY | Report an Issue</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[#FEFEFE]">
        <?php
        include(__DIR__ . '/../includes/header.php');
        include(__DIR__ . '/../includes/options.php');
        ?>

        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="./orders.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">My Orders</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-medium">Report an Issue</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] bg-[#FFFFFF] py-5">
            <div class="w-full md:w-[80%] lg:w-[70%] mx-auto">
                <h1 class="text-[24px] md:text-[28px] font-['Open Sans'] font-bold mb-6">Report an Issue</h1>
                
                <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error_message; ?>
                </div>
                <div class="flex justify-center mt-6">
                    <a href="./orders.php" class="py-2 px-4 bg-[#C2185B] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
                </div>
                <?php elseif (!empty($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success_message; ?>
                </div>
                <div class="flex justify-center mt-6 space-x-4">
                    <a href="./orders.php" class="py-2 px-4 bg-[#C2185B] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
                    <a href="./track-order.php?id=<?php echo $order_id; ?>" class="py-2 px-4 bg-[#E8B006] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Track Order</a>
                </div>
                <?php elseif (!empty($order)): ?>
                <!-- Order Summary -->
                <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6 mb-6">
                    <h2 class="text-[18px] font-['Open Sans'] font-semibold mb-4">Order Summary</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-[14px] text-gray-600">Order ID: <span class="font-medium">#<?php echo $order['id']; ?></span></p>
                            <p class="text-[14px] text-gray-600">Order Date: <span class="font-medium"><?php echo $order['order_date']; ?></span></p>
                            <p class="text-[14px] text-gray-600">Status: 
                                <span class="inline-block py-1 px-2 text-xs text-white rounded-full <?php echo getStatusBadgeClass($order['order_status']); ?>">
                                    <?php echo $order['order_status']; ?>
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-[14px] text-gray-600">Delivery Method: <span class="font-medium"><?php echo ucfirst($order['delivery_method']); ?></span></p>
                            <?php if (!empty($order['delivery_date'])): ?>
                            <p class="text-[14px] text-gray-600">Delivery Date: <span class="font-medium"><?php echo $order['delivery_date']; ?></span></p>
                            <?php endif; ?>
                            <p class="text-[14px] text-gray-600">Total Amount: <span class="font-medium">₦<?php echo number_format((float)$order['order_total'], 2); ?></span></p>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h3 class="text-[16px] font-['Open Sans'] font-medium mb-2">Order Items</h3>
                    <div class="space-y-3 mb-4">
                        <?php foreach ($order_items as $item): 
                            $image_path = isset($item['image_path']) ? "../assets/products/" . $item['image_path'] : "../assets/products/img1.svg";
                        ?>
                        <div class="flex items-center gap-3 p-2 border border-gray-200 rounded-md">
                            <div class="w-[60px] h-[60px] rounded overflow-hidden">
                                <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <p class="text-[14px] font-medium"><?php echo $item['product_name']; ?></p>
                                <p class="text-[13px] text-gray-600">Size: <?php echo $item['size']; ?> <?php echo !empty($item['texture']) ? "• Texture: {$item['texture']}" : ''; ?></p>
                                <p class="text-[13px] text-gray-600">Qty: <?php echo $item['quantity']; ?> • ₦<?php echo number_format((float)$item['price'], 2); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Shipping Address -->
                    <h3 class="text-[16px] font-['Open Sans'] font-medium mb-2">Shipping Information</h3>
                    <div class="p-3 bg-gray-50 rounded-md">
                        <p class="text-[14px] text-gray-700"><?php echo "{$order['first_name']} {$order['last_name']}"; ?></p>
                        <p class="text-[14px] text-gray-700"><?php echo $order['address']; ?></p>
                        <p class="text-[14px] text-gray-700"><?php echo "{$order['city']}, {$order['state']}"; ?></p>
                        <p class="text-[14px] text-gray-700"><?php echo $order['phone']; ?></p>
                    </div>
                </div>
                
                <!-- Report Issue Form -->
                <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6">
                    <h2 class="text-[18px] font-['Open Sans'] font-semibold mb-4">What issue are you experiencing?</h2>
                    
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label for="issue_type" class="block text-[14px] font-medium mb-2">Issue Type*</label>
                            <select id="issue_type" name="issue_type" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#C2185B]" required>
                                <option value="">Select an issue type</option>
                                <option value="Wrong Item Received">Wrong Item Received</option>
                                <option value="Damaged Item">Damaged Item</option>
                                <option value="Missing Item">Missing Item</option>
                                <option value="Quality Issue">Quality Issue</option>
                                <option value="Item Not As Described">Item Not As Described</option>
                                <option value="Delivery Problem">Delivery Problem</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="issue_description" class="block text-[14px] font-medium mb-2">Describe Your Issue*</label>
                            <textarea 
                                id="issue_description" 
                                name="issue_description" 
                                rows="5" 
                                placeholder="Please provide details about your issue..."
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#C2185B]"
                                required
                                minlength="20"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimum 20 characters. Please include specific details to help us resolve your issue more efficiently.</p>
                        </div>
                        
                        <div>
                            <label for="issue_images" class="block text-[14px] font-medium mb-2">Upload Images (Optional)</label>
                            <input 
                                type="file" 
                                id="issue_images" 
                                name="issue_images[]" 
                                accept="image/*" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#C2185B]"
                                multiple
                            />
                            <p class="text-xs text-gray-500 mt-1">You can upload up to 3 images to help us understand your issue (JPEG, PNG, max 5MB each)</p>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="./orders.php" class="py-2 px-4 bg-[#F3F3F3] text-[#262626] text-center text-[16px] font-['Open Sans'] rounded-[4px]">Cancel</a>
                            <button type="submit" name="submit_issue" class="py-2 px-4 bg-[#C2185B] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Submit Report</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image file validation
        const fileInput = document.getElementById('issue_images');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const maxFileSize = 5 * 1024 * 1024; // 5MB
                const maxFiles = 3;
                
                if (this.files.length > maxFiles) {
                    alert(`You can only upload a maximum of ${maxFiles} images.`);
                    this.value = '';
                    return;
                }
                
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    
                    // Check file size
                    if (file.size > maxFileSize) {
                        alert(`File "${file.name}" exceeds maximum size of 5MB.`);
                        this.value = '';
                        return;
                    }
                    
                    // Check file type
                    if (!file.type.match('image/*')) {
                        alert(`File "${file.name}" is not an image.`);
                        this.value = '';
                        return;
                    }
                }
            });
        }
    });
    </script>
</body>

</html>