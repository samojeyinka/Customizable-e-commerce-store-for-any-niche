<?php
// Include authentication utility
require_once '../includes/auth/auth.php';
require_once __DIR__ . "/../config/config.php";

// Authentication check
requireAuth();

// Get user data
$user = getCurrentUser();
$user_id = $user['id'];


// Database connection
$conn = db();
if (!$conn) {
    die(mysqli_error($conn));
}



// Get the order item ID from the URL
$order_item_id = isset($_GET['item_id']) ? $_GET['item_id'] : null;
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_item_id || !$order_id) {
    header('Location: ./orders.php');
    exit;
}

// Get the order item details
$sql = "SELECT oi.id, oi.product_id, oi.variant_id, oi.quantity, oi.price,
              p.product_name, pv.size, pv.texture, p.colors,
              (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path,
              o.order_status
       FROM order_items oi
       JOIN products p ON oi.product_id = p.product_id
       JOIN product_variants pv ON oi.variant_id = pv.variant_id
       JOIN orders o ON oi.order_id = o.id
       WHERE oi.id = ? AND oi.order_id = ? AND o.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $order_item_id, $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Order item not found or doesn't belong to this user
    header('Location: ./orders.php');
    exit;
}

$item = $result->fetch_assoc();
$image_path = isset($item['image_path']) && $item['image_path'] !== '' ? product_image_url($item['image_path'], '../assets/products/') : "../assets/products/img1.svg";

// Get user's email from users table
$sql = "SELECT email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_email = $user_result->fetch_assoc()['email'];

// Process form submission
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $return_reason = isset($_POST['return_reason']) ? $_POST['return_reason'] : '';
    $return_details = isset($_POST['return_details']) ? $_POST['return_details'] : '';
    $return_quantity = isset($_POST['return_quantity']) ? intval($_POST['return_quantity']) : 0;
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
    $contact_email = isset($_POST['contact_email']) ? $_POST['contact_email'] : '';
    
    // Validate inputs
    if (empty($return_reason)) {
        $error = "Please select a return reason";
    } else if ($return_quantity <= 0 || $return_quantity > $item['quantity']) {
        $error = "Please enter a valid return quantity";
    } else if (empty($full_name)) {
        $error = "Please enter your full name";
    } else if (empty($phone_number)) {
        $error = "Please enter your phone number";
    } else if (empty($contact_email)) {
        $error = "Please enter your email address";
    } else if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Insert return request into database with contact information
        $sql = "INSERT INTO return_requests (user_id, order_id, order_item_id, product_id, variant_id, 
                                          return_reason, return_details, return_quantity, status, 
                                          full_name, phone_number, contact_email,
                                          created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Processing', ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiissssss", $user_id, $order_id, $order_item_id, $item['product_id'], 
                            $item['variant_id'], $return_reason, $return_details, $return_quantity,
                            $full_name, $phone_number, $contact_email);
        
        if ($stmt->execute()) {
            $success = "Return request submitted successfully!";
            // Redirect to returns list page after 2 seconds

            // Add this after the successful return request submission (where $success is set)
if ($stmt->execute()) {
    $return_request_id = $stmt->insert_id; // Get the ID of the newly created return request
    $success = "Return request submitted successfully!";
    
    // Include notifications functions
    require_once '../includes/notifications.php';
    
    // Get product name for better notification
    $product_name = $item['product_name'];
    
    // Create notification for admin
    add_notification(
        $conn,
        'return',
        "New Return Request",
        "A new return request has been submitted for $product_name. Quantity: $return_quantity",
        $return_request_id,
        'return',
        null, // null for_user_id means it's for all admins
        1     // 1 means it's for admin
    );
    
    // Create notification for the user too
    add_notification(
        $conn,
        'return_confirmation',
        'Return Request Submitted',
        "Your return request for $product_name has been received and is being processed. We'll notify you of updates.",
        $return_request_id,
        'return',
        $user_id, // specific user
        0         // 0 means it's not for admin
    );
    
    // Redirect to returns list page after 2 seconds
    header("refresh:2;url=./returns.php");
} else {
    $error = "Error submitting return request: " . $stmt->error;
}
            header("refresh:2;url=./returns.php");
        } else {
            $error = "Error submitting return request: " . $stmt->error;
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
    <title>GLOREFY | Request Return</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[<?php echo store_color('color_bg'); ?>]">

    <?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
    ?>

        <section class="w-full bg-[<?php echo store_color('color_bg'); ?>] py-1">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="./orders.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">My Orders</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="./track-order.php?id=<?php echo $order_id; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Track Order</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">Request Return</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] bg-[<?php echo store_color('color_bg'); ?>] py-5">
            <div class="w-full md:w-[80%] lg:w-[60%] mx-auto">
                <h1 class="text-[24px] md:text-[28px] text-[#2C2C2C] font-['Open Sans'] font-medium mb-6 text-center">Request Product Return</h1>
                
                <?php if (!empty($error)): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-700 p-4 rounded-lg">
                    <p class="text-red-700"><?php echo $error; ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-700 p-4 rounded-lg">
                    <p class="text-green-700"><?php echo $success; ?></p>
                </div>
                <?php endif; ?>

                <!-- Return Policy Information -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-700 rounded-lg">
                    <h3 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold mb-2">Return Policy Information</h3>
                    <ul class="list-disc list-inside space-y-1 text-[14px] md:text-[15px] text-[#262626] font-['Open Sans']">
                        <li>Only the product cost will be refunded, not the shipping fee</li>
                        <li>Products must be returned in their original condition</li>
                        <li>If the product is found damaged upon inspection, you will need to collect it or pay for redelivery</li>
                        <li>Return requests are subject to approval</li>
                    </ul>
                </div>

                <!-- Product Details -->
                <div class="mb-6 p-4 border-[1px] border-[#E1E1E1] rounded-[8px]">
                    <h3 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold mb-3">Product Details</h3>
                    <div class="flex gap-4">
                        <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                            <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" alt="<?php echo $item['product_name']; ?>" />
                        </div>
                        <div>
                            <h4 class="text-[16px] font-['Open Sans'] font-medium"><?php echo $item['product_name']; ?></h4>
                            <p class="text-[14px] text-[#262626] font-['Open Sans']">Size: <?php echo $item['size']; ?> • Color: <?php echo $item['colors']; ?></p>
                            <p class="text-[14px] text-[#262626] font-['Open Sans']">Price: ₦<?php echo number_format((float)$item['price']); ?></p>
                            <p class="text-[14px] text-[#262626] font-['Open Sans']">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Return Request Form -->
                <form method="POST" action="" class="p-4 border-[1px] border-[#E1E1E1] rounded-[8px]">
                    <h3 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold mb-4">Return Request Form</h3>
                    
                    <!-- Contact Information Section -->
                    <div class="mb-6 border-b pb-4">
                        <h4 class="text-[16px] font-['Open Sans'] font-medium mb-3">Contact Information</h4>
                        
                        <div class="mb-4">
                            <label for="full_name" class="block text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mb-1">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" class="w-full p-2 border-[1px] border-[#E1E1E1] rounded-[4px]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone_number" class="block text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mb-1">Phone Number *</label>
                            <input type="tel" id="phone_number" name="phone_number" class="w-full p-2 border-[1px] border-[#E1E1E1] rounded-[4px]" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="contact_email" class="block text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mb-1">Email Address *</label>
                            <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($user_email); ?>" class="w-full p-2 border-[1px] border-[#E1E1E1] rounded-[4px]" required>
                        </div>
                    </div>
                    
                    <!-- Return Details Section -->
                    <div class="mb-4">
                        <label for="return_quantity" class="block text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mb-1">Quantity to Return *</label>
                        <select id="return_quantity" name="return_quantity" class="w-full p-2 border-[1px] border-[#E1E1E1] rounded-[4px]" required>
                            <?php for ($i = 1; $i <= $item['quantity']; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="return_reason" class="block text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mb-1">Reason for Return *</label>
                        <select id="return_reason" name="return_reason" class="w-full p-2 border-[1px] border-[#E1E1E1] rounded-[4px]" required>
                            <option value="">-- Select a reason --</option>
                            <option value="Wrong Item">Wrong Item Received</option>
                            <option value="Defective">Product Defective or Damaged</option>
                            <option value="Quality Issue">Quality Not as Expected</option>
                            <option value="Size Issue">Size/Fit Issue</option>
                            <option value="Changed Mind">Changed My Mind</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="return_details" class="block text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mb-1">Additional Details</label>
                        <textarea id="return_details" name="return_details" rows="4" class="w-full p-2 border-[1px] border-[#E1E1E1] rounded-[4px]" placeholder="Please provide any additional details about your return request..."></textarea>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <a href="./track-order.php?id=<?php echo $order_id; ?>" class="mr-3 py-2 px-4 bg-[#F3F3F3] text-[#262626] text-center text-[16px] font-['Open Sans'] rounded-[4px]">Cancel</a>
                        <button type="submit" class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Submit Return Request</button>
                    </div>
                </form>
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
    <script src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>
</body>
</html>