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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[<?php echo store_color('color_bg'); ?>]">
    <?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
    ?>

        <section class="w-full pt-7 pb-3 border-b border-[#262626]/[0.05]">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-2 text-[12px] font-['Montserrat'] font-medium tracking-[0.03em]">
                    <a href="../index.php" class="text-[#5F5F5F] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">Home</a>
                    <i class="fa-solid fa-chevron-right text-[9px] text-[#262626]/20 leading-none"></i>
                    <a href="./account.php" class="text-[#5F5F5F] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">My Account</a>
                    <i class="fa-solid fa-chevron-right text-[9px] text-[#262626]/20 leading-none"></i>
                    <span class="text-[<?php echo store_color('color_heading'); ?>] font-semibold">My Returns</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] py-8 md:py-12">
            <div class="w-full max-w-[900px] mx-auto">
                <h1 class="text-[<?php echo store_color('color_heading'); ?>] text-[30px] md:text-[38px] leading-[1.12] font-['Cormorant_Garamond'] font-medium text-center mb-8 md:mb-10">My Return Requests</h1>
                
                <?php if (empty($return_requests)): ?>
                <div class="flex flex-col items-center gap-5 text-center bg-white rounded-[20px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)] px-6 py-14">
                    <div class="w-16 h-16 rounded-full bg-[#F8F0F4] flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left text-[22px] leading-none" style="color:var(--glor-primary)" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h3 class="text-[20px] font-['Montserrat'] font-semibold text-[#262626]">No Return Requests Found</h3>
                        <p class="text-[14px] text-[#777777] font-['Open_Sans'] mt-1">You haven't made any return requests yet.</p>
                    </div>
                    <a href="./orders.php" class="inline-flex items-center gap-2 py-3 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-all duration-300">View My Orders</a>
                </div>
                <?php else: ?>

                <!-- Return Status Guide -->
                <div class="mb-6 p-5 md:p-6 bg-white rounded-[16px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)]">
                    <h3 class="text-[12px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#262626] mb-4">Return Status Guide</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                        <?php foreach($status_descriptions as $status => $description): 
                            $color = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-500';
                        ?>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-block w-2.5 h-2.5 rounded-full <?php echo $color; ?> shrink-0"></span>
                            <span class="text-[12px] font-['Montserrat'] font-semibold text-[#262626]"><?php echo $status; ?>:</span>
                            <span class="text-[12px] md:text-[13px] text-[#6B6B6B] font-['Open_Sans']"><?php echo $description; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="overflow-x-auto bg-white rounded-[20px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)]">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-[#FBF9FA]">
                                <th class="p-4 border-b border-[#262626]/[0.05] text-left text-[11px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#6B6B6B]">Product</th>
                                <th class="p-4 border-b border-[#262626]/[0.05] text-left text-[11px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#6B6B6B]">Return Details</th>
                                <th class="p-4 border-b border-[#262626]/[0.05] text-center text-[11px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#6B6B6B]">Status</th>
                                <th class="p-4 border-b border-[#262626]/[0.05] text-center text-[11px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#6B6B6B]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($return_requests as $request): 
                                $image_path = isset($request['image_path']) && $request['image_path'] !== '' ? product_image_url($request['image_path'], '../assets/products/') : "../assets/products/img1.svg";
                                $status_color = isset($status_colors[$request['status']]) ? $status_colors[$request['status']] : 'bg-[#E8B006]';
                            ?>
                            <tr class="border-b border-[#262626]/[0.06] last:border-b-0">
                                <td class="p-4 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="w-[72px] h-[72px] rounded-[12px] overflow-hidden shrink-0">
                                            <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" alt="<?php echo $request['product_name']; ?>" />
                                        </div>
                                        <div>
                                            <h4 class="text-[15px] font-['Montserrat'] font-semibold text-[#262626]"><?php echo $request['product_name']; ?></h4>
                                            <p class="text-[12px] md:text-[13px] text-[#5F5F5F] font-['Open_Sans'] mt-0.5">Size: <?php echo $request['size']; ?> • Color: <?php echo $request['colors']; ?></p>
                                            <p class="text-[12px] md:text-[13px] text-[#5F5F5F] font-['Open_Sans'] mt-0.5">Qty: <?php echo $request['return_quantity']; ?> • ₦<?php echo number_format((float)$request['price']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-top">
                                    <p class="text-[13px] md:text-[14px] font-['Montserrat'] font-semibold text-[#262626]">Reason: <?php echo $request['return_reason']; ?></p>
                                    <p class="text-[12px] text-[#8A8A8A] font-['Open_Sans'] mt-1"><?php echo formatDate($request['created_at']); ?></p>
                                    <p class="text-[12px] md:text-[13px] text-[#5F5F5F] font-['Open_Sans'] mt-1">Order #<?php echo $request['order_id']; ?></p>

                                    <?php if (!empty($request['admin_message'])): ?>
                                    <div class="mt-3 rounded-[12px] border-l-[3px] p-4 bg-[<?php echo store_color('color_tint'); ?>]" style="border-color:<?php echo store_color('color_primary'); ?>">
                                        <p class="text-[11px] tracking-[0.12em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_primary'); ?>]">Update on your return</p>
                                        <p class="text-[13px] text-[#262626] font-['Open_Sans'] mt-1 leading-relaxed"><?php echo nl2br(htmlspecialchars($request['admin_message'])); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-center align-middle">
                                    <span class="inline-block px-3 py-1.5 <?php echo $status_color; ?> text-white text-[10px] tracking-[0.1em] uppercase font-['Montserrat'] font-semibold rounded-full whitespace-nowrap"><?php echo $request['status']; ?></span>
                                </td>
                                <td class="p-4 text-center align-middle">
                                    <a href="./return-details.php?id=<?php echo $request['id']; ?>" class="inline-block px-4 py-2 border border-[#262626]/15 text-[#262626] text-[11px] tracking-[0.1em] uppercase font-['Montserrat'] font-semibold rounded-full hover:border-[<?php echo store_color('color_primary'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200 whitespace-nowrap">View Details</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
    <script src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>
</body>
</html>