<?php
session_start();
// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: ../index.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'];
$admin_email = $_SESSION['admin_email'];
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Connect to the database for admin info
require_once "../../config/servername.php";
$admin_conn = db();

// Fetch admin details
$sql = "SELECT * FROM administrators WHERE admin_id = ?";
$stmt = $admin_conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin = $admin_result->fetch_assoc();

// Close the admin database connection


// Set profile photo path with fallback to default if not available
$profile_photo = "../assets/home/user.svg"; // Default image
if (!empty($admin['profile_photo'])) {
    $photo_path = "../../uploads/profiles/" . $admin['profile_photo'];
    // Check if file exists
    if (file_exists($photo_path)) {
        $profile_photo = $photo_path;
    }
}

// Process notification status updates
if (isset($_GET['notification_action']) && isset($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    $action = $_GET['notification_action'];
    
    if ($action === 'read') {
        // Mark as read
        $update_query = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    } else if ($action === 'unread') {
        // Mark as unread
        $update_query = "UPDATE notifications SET is_read = 0 WHERE notification_id = ?";
    }
    
    if (isset($update_query)) {
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $notification_id);
        mysqli_stmt_execute($stmt);
        
        // Redirect back to remove GET parameters
        $redirect_url = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string
        header("Location: $redirect_url");
        exit;
    }
}

// Include notifications functions if not already included
require_once __DIR__ . '/../../includes/notifications.php';


// Get unread count
// Make sure $conn is a mysqli connection before calling this
$conn = db();
$unread_count = get_unread_count($conn, true);

// Get latest notifications for dropdown
$latest_notifications = get_notifications($conn, true, null, 5, 0);

// Database connection for returns
$conn = db();
if (!$conn) {
    die(mysqli_error($conn));
}

// Process status updates
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $return_id = $_POST['return_id'];
    $status = $_POST['status'];
    $admin_message = $_POST['admin_message'] ?? '';
    
    // Update the return request status
    $sql = "UPDATE return_requests 
            SET status = ?, admin_message = ?, updated_at = NOW() 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $admin_message, $return_id);
    
    if ($stmt->execute()) {
        $message = "Return request #$return_id updated to $status successfully!";
        $message_type = 'success';
    } else {
        $message = "Error updating return request: " . $stmt->error;
        $message_type = 'error';
    }
}

// Get filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with filters - using direct contact information from return_requests table
$query = "SELECT r.id, r.order_id, r.user_id, r.product_id, r.return_reason, 
                 r.return_details, r.return_quantity, r.status, r.admin_message, r.created_at,
                 r.full_name, r.phone_number, r.contact_email, 
                 p.product_name, pv.size, p.colors, oi.price,
                 (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
          FROM return_requests r
          JOIN products p ON r.product_id = p.product_id
          JOIN product_variants pv ON r.variant_id = pv.variant_id
          JOIN order_items oi ON r.order_item_id = oi.id
          WHERE 1=1";

// Add filters if they exist
if (!empty($status_filter)) {
    $query .= " AND r.status = '" . $conn->real_escape_string($status_filter) . "'";
}

if (!empty($date_filter)) {
    $query .= " AND DATE(r.created_at) = '" . $conn->real_escape_string($date_filter) . "'";
}

$query .= " ORDER BY r.created_at DESC";

$result = $conn->query($query);
$return_requests = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $return_requests[] = $row;
    }
}

// Status colors
$status_colors = [
    'Processing' => 'bg-[#E8B006]',
    'Received' => 'bg-[#C2185B]',
    'Accepted' => 'bg-[#39D959]',
    'Rejected' => 'bg-red-500',
    'Completed' => 'bg-[#39D959]'
];

// Format date for display
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY Admin | Returns Management</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../tailwind-components.php'; ?>
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
                <div class="w-[8px] h-[8px] bg-[#C2185B] rounded-full absolute top-[-.1rem] left-3"></div>
            </span>

           
            <a href="./settings/profile.php" class="flex items-center gap-2 cursor-pointer">
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
        <div class="flex items-center justify-center bg-[#C2185B] w-[20px] h-[20px] rounded-[50%]">
            <h1 class="text-white text-[11px] md:text-[12px] font-['Open Sans'] font-medium">
                <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
            </h1>
        </div>
    <?php endif; ?>
</div>

    <a href="./notifications.php" class="text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-[#C2185B]">See all</a>


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
                    <a href="notifications.php" class="text-[14px] text-blue-600 hover:text-blue-800">View all notifications</a>
                </div>
            <?php endif; ?>

    </div>
</div>
</div>
    </div>
    </div>
</header>
<?php
include("./sidebar.php");
?>

<div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">


    <div class="flex h-screen">

        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header - Include your admin header here -->
  
            
            <!-- Main Content -->
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Return Requests Management</h1>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <!-- Status Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <?php
                    // Get counts for each status
                    $counts = [
                        'Processing' => 0,
                        'Received' => 0,
                        'Accepted' => 0,
                        'Rejected' => 0,
                        'Completed' => 0
                    ];
                    
                    $countQuery = "SELECT status, COUNT(*) as count FROM return_requests GROUP BY status";
                    $countResult = $conn->query($countQuery);
                    if ($countResult) {
                        while ($row = $countResult->fetch_assoc()) {
                            if (isset($counts[$row['status']])) {
                                $counts[$row['status']] = $row['count'];
                            }
                        }
                    }
                    
                    // Display cards
                    foreach ($counts as $status => $count):
                        $color = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-500';
                        $isActive = $status_filter === $status;
                    ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?status=' . $status; ?>" 
                        class="bg-white p-4 rounded-lg shadow <?php echo $isActive ? 'ring-2 ring-blue-500' : ''; ?>">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase"><?php echo $status; ?></p>
                                <p class="text-2xl font-bold"><?php echo $count; ?></p>
                            </div>
                            <div class="w-3 h-3 rounded-full <?php echo $color; ?>"></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form action="" method="GET" class="flex flex-wrap items-center gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="border border-gray-300 rounded-md p-2 w-40">
                                <option value="">All Statuses</option>
                                <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Received" <?php echo $status_filter === 'Received' ? 'selected' : ''; ?>>Received</option>
                                <option value="Accepted" <?php echo $status_filter === 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                <option value="Rejected" <?php echo $status_filter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="date" name="date" value="<?php echo $date_filter; ?>" class="border border-gray-300 rounded-md p-2">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded-md hover:bg-blue-1000">Filter</button>
                            <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="ml-2 text-blue-600 hover:text-blue-800">Clear Filters</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Return Requests Table -->
                <div class="bg-white rounded-lg shadow overflow">
                    <?php if (empty($return_requests)): ?>
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No return requests found.</p>
                        <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                        <p class="mt-2">Try clearing your filters.</p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Request ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Return Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($return_requests as $request): 
                                $image_path = isset($request['image_path']) ? "../../assets/products/" . $request['image_path'] : "../assets/admin/img/product-placeholder.jpg";
                                $status_color = isset($status_colors[$request['status']]) ? $status_colors[$request['status']] : 'bg-[#E8B006]';
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?php echo $request['id']; ?></div>
                                    <div class="text-sm text-gray-500">Order #<?php echo $request['order_id']; ?></div>
                                    <div class="text-xs text-gray-500"><?php echo formatDate($request['created_at']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $request['full_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $request['contact_email']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $request['phone_number']; ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-md object-cover" src="<?php echo $image_path; ?>" alt="<?php echo $request['product_name']; ?>">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $request['product_name']; ?></div>
                                            <div class="text-sm text-gray-500">Size: <?php echo $request['size']; ?> • Color: <?php echo $request['colors']; ?></div>
                                            <div class="text-sm text-gray-500">Qty: <?php echo $request['return_quantity']; ?> • ₦<?php echo number_format((float)$request['price']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><span class="font-medium">Reason:</span> <?php echo $request['return_reason']; ?></div>
                                    <div class="text-sm text-gray-500 max-w-xs truncate"><?php echo $request['return_details']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_color; ?> text-white">
                                        <?php echo $request['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <!-- Main action - View details -->
                                    <a href="./admin-view-return.php?id=<?php echo $request['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">View Details</a>
                                    
                                    <!-- Quick status update buttons -->
                                    <?php if ($request['status'] === 'Processing'): ?>
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Received">Mark Received</button>
                                    <?php elseif ($request['status'] === 'Received'): ?>
                                    <button type="button" class="text-green-600 hover:text-green-900 mr-3 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Accepted">Accept</button>
                                    <button type="button" class="text-red-600 hover:text-red-900 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Rejected">Reject</button>
                                    <?php elseif ($request['status'] === 'Accepted'): ?>
                                    <button type="button" class="text-green-600 hover:text-green-900 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Completed">Complete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle">Update Return Request Status</h3>
                
                <form id="statusForm" method="POST" action="">
                    <input type="hidden" id="returnId" name="return_id" value="">
                    <input type="hidden" id="statusValue" name="status" value="">
                    
                    <div class="mb-4">
                        <label for="adminMessage" class="block text-sm font-medium text-gray-700 mb-1">Message to Customer</label>
                        <textarea id="adminMessage" name="admin_message" rows="4" class="w-full border border-gray-300 rounded-md p-2" placeholder="Enter message for the customer..."></textarea>
                        <p class="text-xs text-gray-500 mt-1" id="message-note"></p>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>
    
    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status Update Modal
            const statusModal = document.getElementById('statusModal');
            const statusForm = document.getElementById('statusForm');
            const returnIdInput = document.getElementById('returnId');
            const statusValueInput = document.getElementById('statusValue');
            const modalTitle = document.getElementById('modalTitle');
            const adminMessage = document.getElementById('adminMessage');
            const messageNote = document.getElementById('message-note');
            const cancelBtn = document.getElementById('cancelBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            // Status descriptions for modal title
            const statusDescriptions = {
                'Received': 'Mark as received when the physical product has been returned to you',
                'Accepted': 'Accept the return when product condition is good and approved for refund',
                'Rejected': 'Reject the return if product condition is unacceptable or against policy',
                'Completed': 'Mark as completed when refund has been processed'
            };
            
            // Update Status Button Click Events
            document.querySelectorAll('.quick-update-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const returnId = this.getAttribute('data-id');
                    const status = this.getAttribute('data-status');
                    
                    // Update form values
                    returnIdInput.value = returnId;
                    statusValueInput.value = status;
                    
                    // Update modal title and appearance
                    modalTitle.textContent = `Update Status to "${status}"`;
                    
                    // Reset message field
                    adminMessage.value = '';
                    
                    // Set appropriate button color
                    submitBtn.className = 'px-4 py-2 text-white rounded-md';
                    if (status === 'Received') {
                        submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                        messageNote.textContent = 'Include any notes about the received product condition';
                    } else if (status === 'Accepted') {
                        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        messageNote.textContent = 'Let the customer know about refund timeline';
                    } else if (status === 'Rejected') {
                        submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                        messageNote.textContent = 'A message is REQUIRED to explain rejection reason';
                        adminMessage.required = true;
                    } else if (status === 'Completed') {
                        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        messageNote.textContent = 'Include refund confirmation details';
                    }
                    
                    // Show modal
                    statusModal.classList.remove('hidden');
                });
            });
            
            // Cancel button event
            cancelBtn.addEventListener('click', function() {
                statusModal.classList.add('hidden');
            });
            
            // Form submission validation
            statusForm.addEventListener('submit', function(event) {
                const status = statusValueInput.value;
                const message = adminMessage.value.trim();
                
                // For rejected returns, require a message
                if (status === 'Rejected' && message === '') {
                    event.preventDefault();
                    alert('Please provide a reason for rejecting the return request.');
                    adminMessage.focus();
                }
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === statusModal) {
                    statusModal.classList.add('hidden');
                }
            });
        });


        // Add this script to your page
document.addEventListener('DOMContentLoaded', function() {
    // Get the search input
    const searchInput = document.querySelector('input[type="text"]');
    const searchContainer = searchInput.closest('div');
    
    // Create suggestions container
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'bg-white border border-gray-200 rounded shadow-lg absolute left-0 right-0 z-50 hidden';
    suggestionsContainer.style.top = '60px'; // Position below header
    suggestionsContainer.style.maxWidth = '550px';
    suggestionsContainer.style.width = '100%';
    searchContainer.style.position = 'relative';
    searchContainer.appendChild(suggestionsContainer);
    
    // Listen for input in the search field
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (query.length === 0) {
            hideSuggestions();
            return;
        }
        
        // Display search suggestions based directly on what the user typed
        displaySuggestions(query);
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            hideSuggestions();
        }
    });
    
    // Function to display suggestions
    function displaySuggestions(query) {
        suggestionsContainer.innerHTML = '';
        
        // Define categories - now including Users
        const categories = [
            { 
                key: 'order', 
                title: `Order with ID: ${query}`,
                subtitle: 'Click to view order details',
                labelText: 'Order',
                labelClass: 'bg-green-100 text-green-800',
                url: './order-details.php?id='
            },
            { 
                key: 'reviews', 
                title: `Reviews with "${query}"`,
                subtitle: '',
                labelText: '',
                labelClass: '',
                url: './reviews.php?q='
            },
            { 
                key: 'review', 
                title: `Review with Product ID: ${query}`,
                subtitle: 'Click to view product reviews',
                labelText: 'Review',
                labelClass: 'bg-yellow-100 text-yellow-800',
                url: './reviews.php?product_id='
            },
            { 
                key: 'user', 
                title: `User with ID: ${query}`,
                subtitle: 'Click to view user details',
                labelText: 'User',
                labelClass: 'bg-blue-100 text-blue-800',
                url: './user-details.php?id='
            }
        ];
        
        // For each category, create a suggestion item with styling from the screenshot
        categories.forEach((category, index) => {
            const itemContainer = document.createElement('div');
            
            // Apply different styles based on category type
            if (category.key === 'reviews') {
                itemContainer.className = 'p-4 border-t border-b border-gray-200 bg-gray-50';
                itemContainer.innerHTML = `<div class="font-medium text-gray-700">${category.title}</div>`;
            } else {
                itemContainer.className = 'p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
                
                let labelHtml = '';
                if (category.labelText) {
                    labelHtml = `<span class="text-sm rounded-full px-3 py-1 ${category.labelClass}">${category.labelText}</span>`;
                }
                
                itemContainer.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">${category.title}</div>
                            <div class="text-sm text-gray-500">${category.subtitle}</div>
                        </div>
                        ${labelHtml}
                    </div>
                `;
                
                // Add click handler to navigate to the appropriate page
                itemContainer.addEventListener('click', function() {
                    window.location.href = `${category.url}${query}`;
                });
            }
            
            suggestionsContainer.appendChild(itemContainer);
        });
        
        // Show the suggestions container
        suggestionsContainer.classList.remove('hidden');
    }
    
    // Function to hide suggestions
    function hideSuggestions() {
        suggestionsContainer.classList.add('hidden');
    }
});

    </script>
</body>
</html>