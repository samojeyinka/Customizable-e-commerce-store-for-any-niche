<?php
// Add this code to create a user/my-issues.php page where customers can view their reported issues

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


// Get user's reported issues
$issues_sql = "SELECT i.*, o.id as order_id, o.order_status 
               FROM order_issues i
               JOIN orders o ON i.order_id = o.id
               WHERE i.user_id = ?
               ORDER BY i.created_at DESC";

$stmt = $conn->prepare($issues_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$issues = $result->fetch_all(MYSQLI_ASSOC);

// Function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        case 'resolved':
            return 'bg-green-100 text-green-800';
        case 'closed':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Function to get order status badge class
function getOrderStatusBadgeClass($status) {
    switch ($status) {
        case 'Processing':
            return 'bg-[#E8B006] text-white';
        case 'Shipped':
            return 'bg-[' . store_color('color_primary') . '] text-white';
        case 'Delivered':
            return 'bg-[#39D959] text-white';
        case 'Cancelled':
            return 'bg-red-500 text-white';
        case 'Returned':
            return 'bg-[#9C27B0] text-white';
        default:
            return 'bg-gray-500 text-white';
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
    <title>GLOREFY | My Reported Issues</title>
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
                    <a href="./orders.php" class="text-[#5F5F5F] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">My Orders</a>
                    <i class="fa-solid fa-chevron-right text-[9px] text-[#262626]/20 leading-none"></i>
                    <span class="text-[<?php echo store_color('color_heading'); ?>] font-semibold">My Reported Issues</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] py-8 md:py-12">
            <div class="w-full max-w-[820px] mx-auto">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7 md:mb-10">
                    <h1 class="text-[<?php echo store_color('color_heading'); ?>] text-[30px] md:text-[38px] leading-[1.12] font-['Cormorant_Garamond'] font-medium">My Reported Issues</h1>
                    <a href="./orders.php" class="w-fit inline-flex items-center gap-2 py-3 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] md:text-[13px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-all duration-300">Back to Orders</a>
                </div>
                
                <?php if (empty($issues)): ?>
                <div class="flex flex-col items-center gap-5 text-center bg-white rounded-[20px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)] px-6 py-14">
                    <p class="text-[#6B6B6B] text-[15px] font-['Open_Sans']">You haven't reported any issues yet.</p>
                    <a href="./orders.php" class="inline-flex items-center gap-2 py-3 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[12px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-all duration-300">View My Orders</a>
                </div>
                <?php else: ?>
                <div class="space-y-5">
                    <?php foreach ($issues as $issue): 
                        $status_class = getStatusBadgeClass($issue['status']);
                        $order_status_class = getOrderStatusBadgeClass($issue['order_status']);
                    ?>
                    <div class="bg-white rounded-[20px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)] p-5 md:p-7">
                        <div class="flex flex-col md:flex-row justify-between gap-4 mb-5">
                            <div>
                                <h2 class="text-[19px] md:text-[21px] font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>] mb-2.5"><?php echo htmlspecialchars($issue['issue_type']); ?></h2>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-block px-3 py-1 text-[11px] tracking-[0.06em] uppercase font-['Montserrat'] font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo ucfirst($issue['status']); ?>
                                    </span>
                                    <span class="inline-block px-3 py-1 text-[11px] tracking-[0.06em] uppercase font-['Montserrat'] font-semibold rounded-full <?php echo $order_status_class; ?>">
                                        Order: <?php echo $issue['order_status']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-left md:text-right md:shrink-0">
                                <p class="text-[13px] text-[#6B6B6B] font-['Open_Sans']">Issue ID: <span class="font-medium text-[#262626]">#<?php echo $issue['issue_id']; ?></span></p>
                                <p class="text-[13px] text-[#6B6B6B] font-['Open_Sans']">Order ID: <span class="font-medium text-[#262626]">#<?php echo $issue['order_id']; ?></span></p>
                                <p class="text-[13px] text-[#6B6B6B] font-['Open_Sans']">Reported: <span class="font-medium text-[#262626]"><?php echo date('M d, Y', strtotime($issue['created_at'])); ?></span></p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-[12px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]/60 mb-2">Issue Description</h3>
                                <div class="p-4 bg-[#FBF9FA] rounded-[12px] border border-[#262626]/5">
                                    <p class="text-[14px] md:text-[15px] text-[#4A4A4A] font-['Open_Sans'] leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($issue['issue_description']); ?></p>
                                </div>
                            </div>
                            
                            <?php if (!empty($issue['admin_notes'])): ?>
                            <div>
                                <h3 class="text-[12px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_primary'); ?>] mb-2">Support Note</h3>
                                <div class="p-4 bg-[#EEF5FB] rounded-[12px] border border-[#262626]/5">
                                    <p class="text-[14px] md:text-[15px] text-[#4A4A4A] font-['Open_Sans'] leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($issue['admin_notes']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($issue['resolution'])): ?>
                            <div>
                                <h3 class="text-[12px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#1B7A3D] mb-2">Resolution</h3>
                                <div class="p-4 bg-[#ECF8F0] rounded-[12px] border border-[#262626]/5">
                                    <p class="text-[14px] md:text-[15px] text-[#4A4A4A] font-['Open_Sans'] leading-relaxed"><?php echo htmlspecialchars($issue['resolution']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex justify-end mt-5 pt-5 border-t border-[#262626]/[0.06]">
                            <a href="./track-order.php?id=<?php echo $issue['order_id']; ?>" class="inline-flex items-center gap-2 py-2.5 px-6 border border-[#262626]/15 text-[#262626] rounded-full text-[11px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold hover:border-[<?php echo store_color('color_primary'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">View Order</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
</body>

</html>