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
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">My Reported Issues</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] bg-[#FFFFFF] py-5">
            <div class="w-full md:w-[80%] lg:w-[70%] mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-[24px] md:text-[28px] font-['Open Sans'] font-bold">My Reported Issues</h1>
                    <a href="./orders.php" class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
                </div>
                
                <?php if (empty($issues)): ?>
                <div class="text-center py-10 border-[1px] border-[#E1E1E1] rounded-[8px]">
                    <p class="text-[16px] text-gray-600 mb-4">You haven't reported any issues yet.</p>
                    <a href="./orders.php" class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">View My Orders</a>
                </div>
                <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($issues as $issue): 
                        $status_class = getStatusBadgeClass($issue['status']);
                        $order_status_class = getOrderStatusBadgeClass($issue['order_status']);
                    ?>
                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6">
                        <div class="flex flex-col md:flex-row justify-between mb-4">
                            <div>
                                <h2 class="text-[18px] font-medium mb-1"><?php echo htmlspecialchars($issue['issue_type']); ?></h2>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <span class="inline-block px-2 py-1 text-sm rounded-full <?php echo $status_class; ?>">
                                        <?php echo ucfirst($issue['status']); ?>
                                    </span>
                                    <span class="inline-block px-2 py-1 text-sm rounded-full <?php echo $order_status_class; ?>">
                                        Order: <?php echo $issue['order_status']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[14px] text-gray-600">Issue ID: #<?php echo $issue['issue_id']; ?></p>
                                <p class="text-[14px] text-gray-600">Order ID: #<?php echo $issue['order_id']; ?></p>
                                <p class="text-[14px] text-gray-600">Reported: <?php echo date('M d, Y', strtotime($issue['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="text-[16px] font-medium mb-2">Issue Description</h3>
                            <div class="p-3 bg-gray-50 rounded-md">
                                <p class="text-[14px] text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($issue['issue_description']); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($issue['admin_notes'])): ?>
                        <div class="mb-4">
                            <h3 class="text-[16px] font-medium mb-2">Support Note</h3>
                            <div class="p-3 bg-blue-50 rounded-md">
                                <p class="text-[14px] text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($issue['admin_notes']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($issue['resolution'])): ?>
                        <div class="mb-4">
                            <h3 class="text-[16px] font-medium mb-2">Resolution</h3>
                            <div class="p-3 bg-green-50 rounded-md">
                                <p class="text-[14px] text-gray-800"><?php echo htmlspecialchars($issue['resolution']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-end">
                            <a href="./track-order.php?id=<?php echo $issue['order_id']; ?>" class="py-2 px-4 bg-[#E8E9F2] text-[#262626] text-center text-[16px] font-['Open Sans'] rounded-[4px]">View Order</a>
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