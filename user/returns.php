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



// Get all return requests for the user
$sql = "SELECT r.id, r.order_id, r.order_item_id, r.product_id, r.return_reason, 
               r.return_details, r.return_quantity, r.status, r.admin_message, r.created_at,
               p.product_name, pv.size, p.colors, oi.price,
               (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
        FROM return_requests r
        JOIN products p ON r.product_id = p.product_id
        JOIN product_variants pv ON r.variant_id = pv.variant_id
        JOIN order_items oi ON r.order_item_id = oi.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$return_requests = $result->fetch_all(MYSQLI_ASSOC);

// Status colors
$status_colors = [
    'Processing' => 'bg-[#E8B006]',
    'Received' => 'bg-[' . store_color('color_primary') . ']',    // Added Received status
    'Accepted' => 'bg-[#39D959]',
    'Rejected' => 'bg-red-500',
    'Completed' => 'bg-[#39D959]'
];

// Status descriptions for users
$status_descriptions = [
    'Processing' => 'Your return request is being reviewed',
    'Received' => 'We have received your returned product',
    'Accepted' => 'Return approved, refund will be processed',
    'Rejected' => 'Return request was not approved',
    'Completed' => 'Return process completed'
];

// Format date for display
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

require_once "../includes/auth/google.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | My Returns</title>
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
                    <a href="./account.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">My Account</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">My Returns</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] bg-[<?php echo store_color('color_bg'); ?>] py-5">
            <div class="w-full md:w-[90%] lg:w-[80%] mx-auto">
                <h1 class="text-[24px] md:text-[28px] text-[#2C2C2C] font-['Open Sans'] font-medium mb-6 text-center">My Return Requests</h1>
                
                <?php if (empty($return_requests)): ?>
                <div class="text-center py-10">

                    <h3 class="text-[18px] font-['Open Sans'] font-medium text-[#262626] mb-2">No Return Requests Found</h3>
                    <p class="text-[14px] text-[#777777] font-['Open Sans'] mb-4">You haven't made any return requests yet.</p>
                    <a href="./orders.php" class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">View My Orders</a>
                </div>
                <?php else: ?>

                <!-- Return Status Guide -->
                <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
                    <h3 class="text-[16px] font-['Open Sans'] font-medium mb-2">Return Status Guide</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach($status_descriptions as $status => $description): 
                            $color = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-500';
                        ?>
                        <div class="flex items-center">
                            <span class="inline-block w-3 h-3 rounded-full <?php echo $color; ?> mr-2"></span>
                            <span class="text-[13px] font-medium"><?php echo $status; ?>:</span>
                            <span class="text-[13px] ml-1"><?php echo $description; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-[#F8F8F8]">
                                <th class="p-3 border-y-[1px] border-[#E1E1E1] text-left text-[14px] md:text-[16px] font-['Open Sans'] font-semibold">Product</th>
                                <th class="p-3 border-y-[1px] border-[#E1E1E1] text-left text-[14px] md:text-[16px] font-['Open Sans'] font-semibold">Return Details</th>
                                <th class="p-3 border-y-[1px] border-[#E1E1E1] text-center text-[14px] md:text-[16px] font-['Open Sans'] font-semibold">Status</th>
                                <th class="p-3 border-y-[1px] border-[#E1E1E1] text-center text-[14px] md:text-[16px] font-['Open Sans'] font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($return_requests as $request): 
                                $image_path = isset($request['image_path']) ? "../assets/products/" . $request['image_path'] : "../assets/products/img1.svg";
                                $status_color = isset($status_colors[$request['status']]) ? $status_colors[$request['status']] : 'bg-[#E8B006]';
                            ?>
                            <tr class="border-b-[1px] border-[#E1E1E1]">
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-[60px] h-[60px] rounded-[4px] overflow-hidden">
                                            <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" alt="<?php echo $request['product_name']; ?>" />
                                        </div>
                                        <div>
                                            <h4 class="text-[15px] font-['Open Sans'] font-medium"><?php echo $request['product_name']; ?></h4>
                                            <p class="text-[13px] text-[#262626] font-['Open Sans']">Size: <?php echo $request['size']; ?> • Color: <?php echo $request['colors']; ?></p>
                                            <p class="text-[13px] text-[#262626] font-['Open Sans']">Qty: <?php echo $request['return_quantity']; ?> • ₦<?php echo number_format((float)$request['price']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <p class="text-[14px] font-['Open Sans'] font-medium">Reason: <?php echo $request['return_reason']; ?></p>
                                    <p class="text-[13px] text-[#777777] font-['Open Sans'] mt-1"><?php echo formatDate($request['created_at']); ?></p>
                                    <p class="text-[13px] text-[#262626] font-['Open Sans'] mt-1">Order #<?php echo $request['order_id']; ?></p>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="inline-block py-1 px-3 <?php echo $status_color; ?> text-white text-[13px] font-['Open Sans'] rounded-[15px]"><?php echo $request['status']; ?></span>
                                        
                                        <?php if (!empty($request['admin_message'])): ?>
                                        <button class="view-message-btn mt-2 text-[13px] text-[<?php echo store_color('color_primary'); ?>] underline" data-message="<?php echo htmlspecialchars($request['admin_message']); ?>">View Message</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="./return-details.php?id=<?php echo $request['id']; ?>" class="inline-block py-1 px-3 bg-[#F3F3F3] text-[#262626] text-[13px] font-['Open Sans'] rounded-[4px]">View Details</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Message Modal -->
        <div id="messageModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
            <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
            <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-lg z-50 overflow-y-auto">
                <div class="modal-content py-4 text-left px-6">
                    <div class="flex justify-between items-center pb-3">
                        <p class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold">Admin Message</p>
                        <div class="modal-close cursor-pointer z-50">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="my-5">
                        <p id="modalMessage" class="text-[14px] text-[#262626] font-['Open Sans']"></p>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button class="modal-close px-4 bg-[<?php echo store_color('color_primary'); ?>] p-3 rounded-lg text-white hover:bg-blue-800">Close</button>
                    </div>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Message Modal functionality
        const messageModal = document.getElementById('messageModal');
        const messageButtons = document.querySelectorAll('.view-message-btn');
        const modalMessage = document.getElementById('modalMessage');
        const modalCloseButtons = document.querySelectorAll('.modal-close');
        
        messageButtons.forEach(button => {
            button.addEventListener('click', function() {
                const message = this.getAttribute('data-message');
                modalMessage.textContent = message;
                messageModal.classList.remove('hidden');
            });
        });
        
        modalCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                messageModal.classList.add('hidden');
            });
        });
        
        // Close modal when clicking outside
        messageModal.addEventListener('click', function(e) {
            if (e.target === messageModal || e.target.classList.contains('modal-overlay')) {
                messageModal.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html>