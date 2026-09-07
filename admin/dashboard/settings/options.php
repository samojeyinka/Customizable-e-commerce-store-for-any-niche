<?php
session_start();
require_once "../../../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: ../../index.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'] ?? 'Admin User'; // Default if not set
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com'; // Default if not set
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Connect to the database
require_once "../../../config/servername.php";

$conn = db();

// Fetch admin details
$sql = "SELECT * FROM administrators WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Set profile photo path with fallback to default if not available
$profile_photo = "../assets/home/user.svg"; // Default image
if (!empty($admin['profile_photo'])) {
    $photo_path = "../../../uploads/profiles/" . $admin['profile_photo'];
    // Check if file exists
    if (file_exists($photo_path)) {
        $profile_photo = $photo_path;
    }
}

// Process notification status updates
if (isset($_GET['notification_action']) && isset($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    $action = $_GET['notification_action'];
    
    // Include database connection
    include('../../../config/connect.php');
    
    if ($action === 'read') {
        // Mark as read
        $update_query = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    } else if ($action === 'unread') {
        // Mark as unread
        $update_query = "UPDATE notifications SET is_read = 0 WHERE notification_id = ?";
    }
    
    if (isset($update_query)) {
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $notification_id);
        mysqli_stmt_execute($stmt);
        
        // Redirect back to remove GET parameters
        $redirect_url = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string
        header("Location: $redirect_url");
        exit;
    }
}

// Include notifications functions if not already included
$notifications_file = __DIR__ . '/../../../includes/notifications.php';
if (file_exists($notifications_file)) {
    require_once $notifications_file;
    
    // Include database connection
    if (!isset($con)) {
        include('../../../config/connect.php');
    }
    
    // Get unread count
    $unread_count = function_exists('get_unread_count') ? get_unread_count($con, true) : 0;
    
    // Get latest notifications for dropdown
    $latest_notifications = function_exists('get_notifications') ? get_notifications($con, true, null, 5, 0) : [];
} else {
    // Set defaults if notifications functionality isn't available
    $unread_count = 0;
    $latest_notifications = [];
}

// Close the main database connection

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Admin Settings - Victosah</title>
    <?php include '../../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="relative">

<header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
    <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

        <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
            <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
            </a>

            <i class="fa-solid fa-bars text-[24px] cursor-pointer" onclick="toggleNav()" alt="Search"></i>
            <h1 class="hidden md:block text-[16px] md:text-[20px] font-Onest font-semibold">Orders</h1>
        </div>


        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 border-[1px] border-[#F3F3F3] rounded-[25px] p-2">
                <i class="fa-solid fa-magnifying-glass text-[18px]" alt="Search"></i>
                <input type="text" placeholder="Search name, Order ID..." class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
            </div>

        </div>
        <div class="flex items-center gap-6 md:bg-[#F3F3F3] rounded-[4px] py-1 px-4">

            <span class="cursor-pointer relative" onclick="openNotification()">
                <i class="fa-regular fa-bell text-[20px]" alt="bag"></i>
                <?php if ($unread_count > 0): ?>
                <div class="w-[8px] h-[8px] bg-[#C2185B] rounded-full absolute top-[-.1rem] left-3"></div>
                <?php endif; ?>
            </span>

           
            <a href="./profile.php" class="flex items-center gap-2 cursor-pointer">
                <div class="w-[40px] h-[40px] md:w-[44px] md:h-[44px] rounded-[50%] overflow-hidden bg-gray-100">
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture" class="w-full h-full object-cover" 
                         onerror="this.src='../assets/home/user.svg';" />
                </div>

                <div class="hidden md:block flex flex-col gap-0">
                    <p class="text-[15px] md:text-[16px] font-Onest font-medium text-[#262626]"><?php echo htmlspecialchars($admin_name); ?></p>
                    <p class="text-[14px] md:text-[16px] font-Onest font-regular text-[#5B5B5B]"><?php echo htmlspecialchars($admin_email); ?></p>
                </div>
            </a>
        </div>
    </nav>

    <!-- The dropdowns -->
    <div id="notification" class="p-3 notification-content shadow-md bg-white rounded-[4px]">
        <!-- Notification content remains the same -->
        <div class="flex flex-col gap-2">

<div class="flex items-center justify-between">

<div class="flex items-center gap-1">
    <h1 class="text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Notifications</h1>
    <?php if ($unread_count > 0): ?>
        <div class="flex items-center justify-center bg-[<?php echo store_color('color_primary'); ?>] w-[20px] h-[20px] rounded-[50%]">
            <h1 class="text-white text-[11px] md:text-[12px] font-['Open Sans'] font-medium">
                <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
            </h1>
        </div>
    <?php endif; ?>
</div>

    <a href="../notifications.php" class="text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-[<?php echo store_color('color_primary'); ?>]">See all</a>


</div>

<div class="flex flex-col gap-2 h-[78vh] md:h-[75vh] overflow-y-auto">
    <div class="flex flex-col gap-2">

    <?php if (empty($latest_notifications)): ?>
                <div class="p-4 text-center text-[#6B7280]">No notifications</div>
            <?php else: ?>

                <?php foreach ($latest_notifications as $notification): ?>
        <div class="w-full flex flex-col gap-2 rounded-[4px] <?php echo $notification['is_read'] ? '' : 'bg-[#EEEEEE]'; ?>  p-2">
            <div class="flex items-center justify-between">
                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]"><?php echo htmlspecialchars($notification['title']); ?></h1>
                <div class="relative flex items-center gap-2">
                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"> <?php 
                                    $created_at = new DateTime($notification['created_at']);
                                    echo $created_at->format('d M, Y h:i A'); 
                                    ?></span>
                </div>
            </div>
            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><?php echo htmlspecialchars($notification['message']); ?></span>
        </div>
        <?php endforeach; ?>
                <div class="p-2 text-center">
                    <a href="../notifications.php" class="text-[14px] text-blue-600 hover:text-blue-800">View all notifications</a>
                </div>
            <?php endif; ?>

    </div>
</div>
</div>
    </div>
    </div>
</header>
<?php
// Include sidebar with proper path
include("../sidebar.php");
?>
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">

        <div class="w-full md:w-[70%] bg-white p-6 space-y-4">
            <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Settings</h1>
            <!-- Password Change Button -->
            <a href="../../forgotten-password.php" id="myvmBtn" class="hidden cursor-pointer w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Change password</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Two-factor Authentication Toggle -->
            <div class="flex items-center justify-between px-4 py-2 hidden">
                <span class="text-[#2C2C2C]">Enable Two-factor Authentication</span>
                <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" id="twoFAToggle">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
            </div>


            <a href="./profile.php" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Update Profile Details</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Login Status Check -->
            <button id="opencwyali" class="cursor-pointer w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150 hidden">
                <span class="text-[#2C2C2C]">Check where you are logged in</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

    </div>
    </div>

    <script type="text/javascript" src="../../functions/drop-select.js"></script>
    <script type="text/javascript" src="../../functions/order.js"></script>
    <script type="text/javascript" src="../../functions/dash.js"></script>
    <script type="text/javascript" src="../../functions/tab.js"></script>
    <script type="text/javascript" src="../../functions/overlay.js"></script>
    <script type="text/javascript" src="../../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../../functions/nav.js"></script>

</body>

</html>