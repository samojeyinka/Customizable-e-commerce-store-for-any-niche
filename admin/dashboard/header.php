<?php
if (!function_exists('db')) {
    require_once __DIR__ . '/../../config/config.php';
}
$admin_id = $_SESSION['admin_id'] ?? 0;
$admin_name = $_SESSION['admin_fullname'] ?? '';
$admin_email = $_SESSION['admin_email'] ?? '';
$admin_role = $_SESSION['admin_role'] ?? 'admin';

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

    // Include database connection
    include('../../config/connect.php');

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
require_once __DIR__ . '/../../includes/notifications.php';

// Include database connection
include('../../config/connect.php');

// Get unread count
$unread_count = get_unread_count($con, true);

// Get latest notifications for dropdown
$latest_notifications = get_notifications($con, true, null, 5, 0);
?>

<?php
// Resolve the current page title for the header
$page_titles = [
    'overview.php' => 'Overview',
    'products.php' => 'Products',
    'new-product.php' => 'New Product',
    'edit-product.php' => 'Edit Product',
    'orders.php' => 'Orders',
    'order-details.php' => 'Order Details',
    'users.php' => 'Users',
    'user-details.php' => 'User Details',
    'reviews.php' => 'Reviews',
    'issues.php' => 'Issues',
    'admin-returns.php' => 'Returns',
    'admin-view-return.php' => 'Return Details',
    'notifications.php' => 'Notifications',
];
$page_basename = basename($_SERVER['PHP_SELF'] ?? 'overview.php');
$page_title = $page_titles[$page_basename] ?? 'Dashboard';
?>

<!-- ======================== The header starts ======================== -->
<header class="w-full bg-white z-50 flex items-center justify-between px-4 md:px-6 lg:px-8 h-16 border-b border-gray-100 fixed top-0 left-0 shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
    <nav class="w-full flex items-center justify-between">

        <div class="flex items-center gap-4 md:gap-6">
            <a href="./overview.php" class="flex items-center">
                <img src="../assets/global/logo.png" alt="GLOREFY" class="w-[32px] md:w-[40px]" />
            </a>

            <i class="fa-solid fa-bars text-[22px] text-[#4B5563] cursor-pointer lg:hidden" onclick="toggleNav()"></i>
            <h1 class="hidden lg:block text-[18px] md:text-[20px] font-Onest font-semibold text-[#111827]"><?php echo htmlspecialchars($page_title); ?></h1>
        </div>

        <div class="hidden md:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-4 py-2 w-[220px] lg:w-[320px] focus-within:border-[#C2185B] focus-within:ring-2 focus-within:ring-[#C2185B]/10 transition-all">
            <i class="fa-solid fa-magnifying-glass text-[15px] text-gray-400"></i>
            <input type="text" placeholder="Search orders, users, products..." class="bg-transparent text-[14px] font-['Open Sans'] border-none outline-none placeholder:text-gray-400 flex-1" />
        </div>

        <div class="flex items-center gap-3 md:gap-5">
            <div class="relative">
                <button onclick="openNotification()" class="relative w-[40px] h-[40px] rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-regular fa-bell text-[18px] text-[#4B5563]"></i>
                    <?php if ($unread_count > 0): ?>
                    <span class="absolute -top-[2px] -right-[2px] min-w-[18px] h-[18px] px-1 rounded-full bg-[#C2185B] text-white text-[10px] font-bold flex items-center justify-center">
                        <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
                    </span>
                    <?php endif; ?>
                </button>

                <!-- Notification dropdown -->
                <div id="notification" class="notification-content p-0 bg-white border border-gray-100 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <h1 class="text-[16px] font-['Open Sans'] font-semibold text-[#111827]">Notifications</h1>
                            <?php if ($unread_count > 0): ?>
                            <span class="flex items-center justify-center bg-[#C2185B] min-w-[20px] h-[20px] px-1 rounded-full">
                                <span class="text-white text-[11px] font-semibold font-['Open Sans']">
                                    <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
                                </span>
                            </span>
                            <?php endif; ?>
                        </div>
                        <a href="./notifications.php" class="text-[14px] font-['Open Sans'] font-medium text-[#C2185B] hover:underline">See all</a>
                    </div>

                    <div class="flex flex-col max-h-[60vh] overflow-y-auto">
                        <?php if (empty($latest_notifications)): ?>
                        <div class="p-8 text-center text-gray-400 text-[14px] font-['Open Sans']">No notifications yet</div>
                        <?php else: ?>
                            <?php foreach ($latest_notifications as $notification): ?>
                        <div class="flex flex-col gap-1 px-4 py-3 border-b border-gray-50 <?php echo $notification['is_read'] ? '' : 'bg-[#FDF0F5] border-l-[3px] border-l-[#C2185B]'; ?>">
                            <div class="flex items-center justify-between gap-2">
                                <h1 class="text-[14px] font-['Open Sans'] font-semibold text-[#111827]"><?php echo htmlspecialchars($notification['title']); ?></h1>
                                <div class="relative flex items-center gap-2 shrink-0">
                                    <span class="text-[12px] font-['Open Sans'] text-gray-400">
                                        <?php
                                        try {
                                            $created_at = new DateTime($notification['created_at']);
                                            echo $created_at->format('d M, Y h:i A');
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($notification['created_at'] ?? '');
                                        }
                                        ?>
                                    </span>
                                    <i class="fa-solid fa-ellipsis-vertical text-[16px] cursor-pointer text-gray-400" onclick="openNotimenu(this)"></i>
                                    <div class="not-content bg-white border border-gray-100 shadow-lg rounded-lg p-2">
                                        <div class="flex flex-col gap-2">
                                        <?php if ($notification['type'] === 'order' && !empty($notification['reference_id'])): ?>
            <a href="./order-details.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[14px] font-medium text-[#262626] px-2 py-1 rounded hover:bg-gray-50">View Details</a>
        <?php elseif ($notification['type'] === 'return' && !empty($notification['reference_id'])): ?>
            <a href="./admin-view-return.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[14px] font-medium text-[#262626] px-2 py-1 rounded hover:bg-gray-50">View Details</a>
        <?php elseif ($notification['type'] === 'issue' && !empty($notification['reference_id'])): ?>
            <a href="./issues.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[14px] font-medium text-[#262626] px-2 py-1 rounded hover:bg-gray-50">View Details</a>
        <?php elseif ($notification['type'] === 'review' && !empty($notification['reference_id'])): ?>
            <a href="./reviews.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[14px] font-medium text-[#262626] px-2 py-1 rounded hover:bg-gray-50">View Details</a>
        <?php else: ?>
            <a href="#" class="text-[14px] font-medium text-[#262626] px-2 py-1 rounded hover:bg-gray-50">View Details</a>
        <?php endif; ?>

                                            <?php if ($notification['is_read']): ?>
                                                <a href="?notification_action=unread&notification_id=<?php echo $notification['notification_id']; ?>" class="text-[14px] font-medium text-[#E8B006] px-2 py-1 rounded hover:bg-amber-50">Mark as unread</a>
                                            <?php else: ?>
                                                <a href="?notification_action=read&notification_id=<?php echo $notification['notification_id']; ?>" class="text-[14px] font-medium text-[#E8B006] px-2 py-1 rounded hover:bg-amber-50">Mark as read</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-[13px] font-['Open Sans'] text-gray-500"><?php echo htmlspecialchars($notification['message']); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <div class="p-3 text-center border-t border-gray-100">
                            <a href="./notifications.php" class="text-[13px] font-['Open Sans'] font-medium text-[#C2185B] hover:underline">View all notifications</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <a href="./settings/profile.php" class="flex items-center gap-3 cursor-pointer">
                <div class="w-[40px] h-[40px] rounded-full overflow-hidden ring-2 ring-gray-100">
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture" class="w-full h-full object-cover"
                         onerror="this.src='../assets/home/user.svg';" />
                </div>
                <div class="hidden xl:block">
                    <p class="text-[14px] font-Onest font-semibold text-[#111827] leading-tight"><?php echo htmlspecialchars($admin_name); ?></p>
                    <p class="text-[12px] font-Onest font-regular text-gray-500"><?php echo htmlspecialchars($admin_email); ?></p>
                </div>
            </a>
        </div>
    </nav>
</header>
<!-- ======================== The header ends ======================== -->


<script>
    // Add this script to your page
document.addEventListener('DOMContentLoaded', function() {
    // Get the search input
    const searchInput = document.querySelector('header input[type="text"]');
    if (!searchInput) return;
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