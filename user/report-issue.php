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

// Conversational categories with icons + guided prompt text
$issue_categories = [
    'Wrong Item Received'  => ['icon' => 'fa-box-open',    'prompt' => 'What did you receive instead of what you ordered? Mention the product name and how it differs from what you expected.'],
    'Damaged Item'         => ['icon' => 'fa-cube',        'prompt' => 'How is the item damaged? Describe where and what the damage looks like (broken, torn, leaking, etc.) — a photo helps a lot.'],
    'Missing Item'         => ['icon' => 'fa-box',         'prompt' => 'Which item(s) are missing from your order? Tell us what you expected to receive.'],
    'Quality Issue'        => ['icon' => 'fa-circle-exclamation', 'prompt' => 'What\'s wrong with the quality? Describe the issue with the product\'s texture, scent, effectiveness, or packaging.'],
    'Item Not As Described'=> ['icon' => 'fa-tags',        'prompt' => 'How does the item differ from the description? Mention any differences in size, color, shade, scent, or what\'s in the box.'],
    'Delivery Problem'     => ['icon' => 'fa-truck',       'prompt' => 'What happened with delivery? Tell us about delays, wrong address, or anything that went wrong while your order was on the way.'],
    'Other'                => ['icon' => 'fa-comment-dots','prompt' => 'Tell us what happened. Any details you can share help us resolve it faster.'],
];

// Default to the full set; filtered down below based on the order's delivery status.
$available_categories = $issue_categories;

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

        // Smart: only offer issue types relevant to the order's delivery status.
        // Item-based issues (wrong/damaged/missing/quality/not-as-described) only make
        // sense once the order has been delivered. Before that, delivery-type issues apply.
        if ($order['order_status'] === 'Processing' || $order['order_status'] === 'Confirmed' || $order['order_status'] === 'Shipped') {
            $available_categories = array_intersect_key($issue_categories, array_flip(['Delivery Problem', 'Other']));
        } else {
            $available_categories = $issue_categories;
        }
        
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
            return 'bg-[' . store_color('color_primary') . ']';
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
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">Report an Issue</span>
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
                    <a href="./orders.php" class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
                </div>
                <?php elseif (!empty($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success_message; ?>
                </div>
                <div class="flex justify-center mt-6 space-x-4">
                    <a href="./orders.php" class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
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
                
                <!-- Report Issue Form (conversational wizard) -->
                <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6">
                    <h2 class="text-[18px] font-['Open Sans'] font-semibold mb-1">Let's sort this out</h2>
                    <p class="text-[13px] text-gray-500 mb-4">A few quick questions — this helps our team resolve your issue faster.</p>

                    <!-- Stepper -->
                    <div class="flex items-center gap-1 mb-6" id="wizardStepper">
                        <?php
                        $steps = [1 => 'What happened', 2 => 'Details', 3 => 'Photos', 4 => 'Confirm'];
                        $i = 0;
                        foreach ($steps as $num => $label): $i++; ?>
                            <?php if ($num > 1): ?><div class="flex-1 h-[2px] bg-gray-200 rounded-full wizard-line" data-line="<?php echo $num; ?>"></div><?php endif; ?>
                            <div class="flex flex-col items-center gap-1">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-semibold transition-colors wizard-dot" data-step="<?php echo $num; ?>"><?php echo $num; ?></span>
                                <span class="text-[11px] text-gray-400 font-['Open Sans'] hidden sm:inline wizard-label" data-step="<?php echo $num; ?>"><?php echo $label; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data" id="issueWizardForm">
                        <!-- Step 1: What happened -->
                        <div class="wizard-step" data-step="1">
                            <p class="text-[15px] font-['Open Sans'] font-medium text-gray-700 mb-3">What's the issue with this order?</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <?php foreach ($available_categories as $cat => $meta): ?>
                                <label class="issue-cat relative flex items-center gap-3 p-3 border-[1px] border-[#E1E1E1] rounded-[8px] cursor-pointer transition-colors hover:border-[<?php echo store_color('color_primary'); ?>]">
                                    <input type="radio" name="issue_type" value="<?php echo htmlspecialchars($cat); ?>" class="hidden issue-cat-input" <?php echo $cat === 'Other' ? '' : ''; ?> required>
                                    <span class="w-9 h-9 rounded-[8px] bg-[<?php echo store_color('color_tint'); ?>] flex items-center justify-center shrink-0"><i class="fa-solid <?php echo $meta['icon']; ?> text-[<?php echo store_color('color_primary'); ?>]"></i></span>
                                    <span class="text-[14px] font-['Open Sans'] font-medium text-[#262626]"><?php echo htmlspecialchars($cat); ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex items-center justify-between mt-5">
                                <a href="./orders.php" class="py-2 px-4 bg-[#F3F3F3] text-[#262626] text-center text-[14px] font-['Open Sans'] rounded-[4px]">Cancel</a>
                                <button type="button" class="wizard-next py-2 px-5 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[4px] font-['Open Sans'] text-[14px]">Continue</button>
                            </div>
                        </div>

                        <!-- Step 2: Details -->
                        <div class="wizard-step hidden" data-step="2">
                            <label for="issue_description" class="block text-[15px] font-['Open Sans'] font-medium text-gray-700 mb-2">Can you give us a bit more detail?</label>
                            <div id="issuePrompt" class="mb-2 p-3 rounded-[8px] bg-[<?php echo store_color('color_tint'); ?>] border-l-4 border-[<?php echo store_color('color_primary'); ?>] text-[14px] text-gray-700"></div>
                            <textarea
                                id="issue_description"
                                name="issue_description"
                                rows="5"
                                placeholder="Type your answer here..."
                                class="w-full p-3 border-[1px] border-[#E1E1E1] rounded-[8px] focus:outline-none focus:ring-2 focus:ring-[<?php echo store_color('color_primary'); ?>]"
                                required
                                minlength="20"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimum 20 characters. The more detail you share, the faster we can help.</p>
                            <div class="flex items-center justify-between mt-5">
                                <button type="button" class="wizard-prev py-2 px-5 bg-[#F3F3F3] text-[#262626] rounded-[4px] font-['Open Sans'] text-[14px]">Back</button>
                                <button type="button" class="wizard-next py-2 px-5 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[4px] font-['Open Sans'] text-[14px]">Continue</button>
                            </div>
                        </div>

                        <!-- Step 3: Photos -->
                        <div class="wizard-step hidden" data-step="3">
                            <label for="issue_images" class="block text-[15px] font-['Open Sans'] font-medium text-gray-700 mb-2">Care to add a photo? <span class="text-gray-400 font-normal">(optional)</span></label>
                            <p class="text-[13px] text-gray-500 mb-3">A picture of the item or packaging can really speed things up.</p>
                            <input
                                type="file"
                                id="issue_images"
                                name="issue_images[]"
                                accept="image/*"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[<?php echo store_color('color_primary'); ?>]"
                                multiple
                            />
                            <p class="text-xs text-gray-500 mt-1">You can upload up to 3 images (JPEG, PNG, max 5MB each).</p>
                            <div class="flex items-center justify-between mt-5">
                                <button type="button" class="wizard-prev py-2 px-5 bg-[#F3F3F3] text-[#262626] rounded-[4px] font-['Open Sans'] text-[14px]">Back</button>
                                <button type="button" class="wizard-next py-2 px-5 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[4px] font-['Open Sans'] text-[14px]">Continue</button>
                            </div>
                        </div>

                        <!-- Step 4: Confirm -->
                        <div class="wizard-step hidden" data-step="4">
                            <p class="text-[15px] font-['Open Sans'] font-medium text-gray-700 mb-3">Does this look right?</p>
                            <div class="flex flex-col gap-3 bg-gray-50 rounded-[8px] p-4">
                                <div class="flex items-start gap-3">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"><i class="fa-solid fa-tag text-[16px] text-[<?php echo store_color('color_primary'); ?>]"></i></span>
                                    <div>
                                        <p class="text-[12px] text-gray-400 font-['Open Sans']">Issue</p>
                                        <p id="confirmType" class="text-[14px] font-['Open Sans'] font-medium text-[#262626]">—</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"><i class="fa-solid fa-comment text-[16px] text-[<?php echo store_color('color_primary'); ?>]"></i></span>
                                    <div>
                                        <p class="text-[12px] text-gray-400 font-['Open Sans']">What you told us</p>
                                        <p id="confirmDesc" class="text-[14px] font-['Open Sans'] text-[#262626] whitespace-pre-line">—</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"><i class="fa-solid fa-image text-[16px] text-[<?php echo store_color('color_primary'); ?>]"></i></span>
                                    <div>
                                        <p class="text-[12px] text-gray-400 font-['Open Sans']">Photos attached</p>
                                        <p id="confirmPhotos" class="text-[14px] font-['Open Sans'] text-[#262626]">None</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-5">
                                <button type="button" class="wizard-prev py-2 px-5 bg-[#F3F3F3] text-[#262626] rounded-[4px] font-['Open Sans'] text-[14px]">Back</button>
                                <button type="submit" name="submit_issue" class="py-2 px-5 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[4px] font-['Open Sans'] text-[14px]">Submit Report</button>
                            </div>
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
        // ---- Conversational wizard ----
        const steps = document.querySelectorAll('.wizard-step');
        const dots = document.querySelectorAll('.wizard-dot');
        const labels = document.querySelectorAll('.wizard-label');
        const lines = document.querySelectorAll('.wizard-line');
        const primary = '<?php echo store_color('color_primary'); ?>';
        const tint = '<?php echo store_color('color_tint'); ?>';
        const promptEl = document.getElementById('issuePrompt');
        const catInputs = document.querySelectorAll('.issue-cat-input');
        const issueDesc = document.getElementById('issue_description');

        const CATEGORIES = {
            <?php foreach ($available_categories as $cat => $meta): ?>
            "<?php echo addslashes($cat); ?>": "<?php echo addslashes($meta['prompt']); ?>",
            <?php endforeach; ?>
        };

        // Pre-select "Other" is not needed; leave unselected.

        catInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                document.querySelectorAll('.issue-cat').forEach(function(l) {
                    l.classList.remove('border-[#E8B006]', 'bg-[' + tint + ']');
                    l.style.borderColor = '#E1E1E1';
                });
                const card = input.closest('.issue-cat');
                card.style.borderColor = primary;
                card.classList.add('bg-[' + tint + ']');
                if (promptEl) {
                    promptEl.textContent = CATEGORIES[input.value] || '';
                }
            });
        });

        function goTo(step) {
            steps.forEach(function(s) {
                s.classList.toggle('hidden', parseInt(s.getAttribute('data-step')) !== step);
            });
            dots.forEach(function(d) {
                const n = parseInt(d.getAttribute('data-step'));
                const done = n < step;
                const active = n === step;
                d.style.background = done || active ? primary : '#E8E8E8';
                d.style.color = done || active ? '#fff' : '#9CA3AF';
            });
            labels.forEach(function(l) {
                l.style.color = parseInt(l.getAttribute('data-step')) <= step ? '#374151' : '#9CA3AF';
            });
            lines.forEach(function(l) {
                l.style.background = parseInt(l.getAttribute('data-line')) <= step ? primary : '#E8E8E8';
            });
        }

        document.querySelectorAll('.wizard-next').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const current = btn.closest('.wizard-step');
                const step = parseInt(current.getAttribute('data-step'));
                if (step === 1) {
                    const selected = document.querySelector('.issue-cat-input:checked');
                    if (!selected) { alert('Please choose the type of issue first.'); return; }
                }
                if (step === 2) {
                    if (!issueDesc || issueDesc.value.trim().length < 20) {
                        alert('Please add a little more detail (at least 20 characters).');
                        issueDesc && issueDesc.focus();
                        return;
                    }
                }
                goTo(step + 1);
            });
        });

        document.querySelectorAll('.wizard-prev').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const step = parseInt(btn.closest('.wizard-step').getAttribute('data-step'));
                goTo(step - 1);
            });
        });

        // Fill confirmation summary when submitting
        document.getElementById('issueWizardForm').addEventListener('submit', function() {
            const type = document.querySelector('.issue-cat-input:checked');
            document.getElementById('confirmType').textContent = type ? type.value : '—';
            document.getElementById('confirmDesc').textContent = issueDesc ? issueDesc.value : '—';
            const files = document.getElementById('issue_images').files;
            document.getElementById('confirmPhotos').textContent = files.length > 0 ? files.length + (files.length === 1 ? ' photo' : ' photos') : 'None';
        });

        goTo(1);

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