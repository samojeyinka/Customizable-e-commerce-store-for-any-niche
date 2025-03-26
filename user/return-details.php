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
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die(mysqli_error($conn));
}

// Get the return request ID from the URL
$return_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$return_id) {
    header('Location: ./returns.php');
    exit;
}

// Get the return request details
$sql = "SELECT r.id, r.order_id, r.order_item_id, r.product_id, r.return_reason, 
               r.return_details, r.return_quantity, r.status, r.admin_message, 
               r.created_at, r.updated_at,
               p.product_name, pv.size, pv.texture, p.colors, oi.price,
               (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
        FROM return_requests r
        JOIN products p ON r.product_id = p.product_id
        JOIN product_variants pv ON r.variant_id = pv.variant_id
        JOIN order_items oi ON r.order_item_id = oi.id
        WHERE r.id = ? AND r.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $return_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Return request not found or doesn't belong to this user
    header('Location: ./returns.php');
    exit;
}

$return = $result->fetch_assoc();
$image_path = isset($return['image_path']) ? "../assets/products/" . $return['image_path'] : "../assets/products/img1.svg";

// Status colors
// Updated status colors including 'Received'
$status_colors = [
    'Processing' => 'bg-[#E8B006]',
    'Received' => 'bg-[#1A237E]',    // Added Received status with navy blue color
    'Accepted' => 'bg-[#39D959]',
    'Rejected' => 'bg-red-500',
    'Completed' => 'bg-[#1A237E]'
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
    <title>VICTOSAH | Return Details</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />
</head>

<body>
    <main class="bg-[#FEFEFE]">
    <?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
    ?>

        <section class="w-full bg-[#FFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <a href="./returns.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">My Returns</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Return Details</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto bg-[#FFFFFF] py-5">
            <div class="w-full md:w-[80%] lg:w-[60%] mx-auto">
                <h1 class="text-[24px] md:text-[28px] text-[#2C2C2C] font-['Open Sans'] font-medium mb-6 text-center">Return Request Details</h1>
                
                <!-- Return Request Status Card -->
                <div class="status-card p-4 md:p-6 border-[1px] border-[#E1E1E1] rounded-[8px] mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-[18px] font-['Open Sans'] font-semibold">Return Request #<?php echo $return['id']; ?></h3>
                        <span class="py-1 px-4 <?php echo $status_color; ?> text-white text-[14px] font-['Open Sans'] rounded-[28px]"><?php echo $return['status']; ?></span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-[14px] text-[#777777] font-['Open Sans']">Return Date</p>
                            <p class="text-[14px] font-medium text-[#262626] font-['Open Sans']"><?php echo formatDate($return['created_at']); ?></p>
                        </div>
                        <div>
                            <p class="text-[14px] text-[#777777] font-['Open Sans']">Last Updated</p>
                            <p class="text-[14px] font-medium text-[#262626] font-['Open Sans']"><?php echo formatDate($return['updated_at'] ?? $return['created_at']); ?></p>
                        </div>
                        <div>
                            <p class="text-[14px] text-[#777777] font-['Open Sans']">Order ID</p>
                            <p class="text-[14px] font-medium text-[#262626] font-['Open Sans']">#<?php echo $return['order_id']; ?></p>
                        </div>
                        <div>
                            <p class="text-[14px] text-[#777777] font-['Open Sans']">Refund Amount</p>
                            <p class="text-[14px] font-medium text-[#262626] font-['Open Sans']">₦<?php echo number_format($return['price'] * $return['return_quantity']); ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($return['admin_message'])): ?>
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-700 p-3 rounded-lg">
                        <p class="text-[14px] text-[#1A237E] font-['Open Sans'] font-semibold">Message from Admin:</p>
                        <p class="text-[14px] text-[#262626] font-['Open Sans'] mt-1"><?php echo $return['admin_message']; ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Product Details -->
                <div class="product-details p-4 md:p-6 border-[1px] border-[#E1E1E1] rounded-[8px] mb-6">
                    <h3 class="text-[18px] font-['Open Sans'] font-semibold mb-4">Product Details</h3>
                    
                    <div class="flex gap-4">
                        <div class="w-[100px] h-[100px] rounded-[4px] overflow-hidden">
                            <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" alt="<?php echo $return['product_name']; ?>" />
                        </div>
                        <div>
                            <h4 class="text-[16px] font-['Open Sans'] font-medium"><?php echo $return['product_name']; ?></h4>
                            <p class="text-[14px] text-[#262626] font-['Open Sans']">Size: <?php echo $return['size']; ?> • Color: <?php echo $return['colors']; ?></p>
                            <p class="text-[14px] text-[#262626] font-['Open Sans']">Price: ₦<?php echo number_format($return['price']); ?></p>
                            <p class="text-[14px] text-[#262626] font-['Open Sans']">Quantity to Return: <?php echo $return['return_quantity']; ?></p>
                            <p class="text-[14px] text-[#262626] font-['Open Sans'] mt-2">Total Refund: ₦<?php echo number_format($return['price'] * $return['return_quantity']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Return Request Details -->
                <div class="return-details p-4 md:p-6 border-[1px] border-[#E1E1E1] rounded-[8px] mb-6">
                    <h3 class="text-[18px] font-['Open Sans'] font-semibold mb-4">Return Request Details</h3>
                    
                    <div class="mb-4">
                        <p class="text-[14px] text-[#777777] font-['Open Sans']">Reason for Return</p>
                        <p class="text-[14px] font-medium text-[#262626] font-['Open Sans']"><?php echo $return['return_reason']; ?></p>
                    </div>
                    
                    <?php if (!empty($return['return_details'])): ?>
                    <div>
                        <p class="text-[14px] text-[#777777] font-['Open Sans']">Additional Details</p>
                        <p class="text-[14px] text-[#262626] font-['Open Sans'] mt-1"><?php echo $return['return_details']; ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Return Policy Information -->
                <div class="mb-6 p-4 bg-gray-50 border-[1px] border-[#E1E1E1] rounded-[8px]">
                    <h3 class="text-[16px] font-['Open Sans'] font-semibold mb-2">Return Process Information</h3>
                    <ul class="list-disc list-inside space-y-1 text-[14px] text-[#262626] font-['Open Sans']">
                        <li>Your return request will be reviewed by our team within 1-2 business days</li>
                        <li>If approved, you will receive instructions for sending the item back</li>
                        <li>Refunds are processed 2-5 business days after we receive and inspect the returned item</li>
                        <li>Only the product cost will be refunded, not the shipping fee</li>
                    </ul>
                </div>
                
                <!-- Actions -->
                <div class="actions mt-6 flex justify-end">
                    <a href="./returns.php" class="py-2 px-4 bg-[#1A237E] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Returns</a>
                </div>
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