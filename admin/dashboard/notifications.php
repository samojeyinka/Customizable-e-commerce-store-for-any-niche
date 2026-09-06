<?php
// // Check if admin is logged in
// if (!isset($_SESSION['admin_id'])) {
//     // Redirect to login page
//     header("Location: ../index.php");
//     exit();
// }

// Connect to the database
require_once "../../config/servername.php";
include('../../config/connect.php');
require_once __DIR__ . '/../../includes/notifications.php';

// Define type mapping FIRST
$type_mapping = [
    'order' => 'orders',
    'order_confirmation' => 'orders',
    'issue' => 'issues',
    'return' => 'returns',
    'review' => 'reviews'
];

// Initialize type counts
$type_counts = [
    'orders' => 0,
    'issues' => 0,
    'returns' => 0,
    'reviews' => 0
];

// Process notification actions
if (isset($_GET['notification_action']) && isset($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    $action = $_GET['notification_action'];
    
    if ($action === 'read') {
        mark_as_read($con, $notification_id);
    } elseif ($action === 'unread') {
        $update_query = "UPDATE notifications SET is_read = 0 WHERE notification_id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $notification_id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'read_all') {
        mark_all_as_read($con);
    }
    
    $redirect_url = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $redirect_url");
    exit;
}

// Handle filters
$search_query = isset($_GET['search']) && !empty($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

// Date condition
$date_condition = '';
if ($date_filter === 'today') {
    $date_condition = "DATE(created_at) = CURDATE()";
} elseif ($date_filter === 'week') {
    $date_condition = "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($date_filter === 'month') {
    $date_condition = "created_at >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Build base SQL query
$sql = "SELECT * FROM notifications WHERE for_admin = 1";

// Add search condition
if (!empty($search_query)) {
    $sql .= " AND (title LIKE '%$search_query%' OR message LIKE '%$search_query%' OR reference_id LIKE '%$search_query%')";
}

// Add status filter
if ($status_filter === 'read') {
    $sql .= " AND is_read = 1";
} elseif ($status_filter === 'unread') {
    $sql .= " AND is_read = 0";
}

// Add date filter
if (!empty($date_condition)) {
    $sql .= " AND $date_condition";
}

// Add type filter based on tab
if ($current_tab !== 'all') {
    // Get all DB types that map to this tab
    $db_types = [];
    foreach ($type_mapping as $db_type => $tab_type) {
        if ($tab_type === $current_tab) {
            $db_types[] = $db_type;
        }
    }
    
    if (!empty($db_types)) {
        $escaped_types = array_map(function($type) use ($con) {
            return "'" . mysqli_real_escape_string($con, $type) . "'";
        }, $db_types);
        $sql .= " AND type IN (" . implode(',', $escaped_types) . ")";
    }
}

// Count total results
$count_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$count_result = mysqli_query($con, $count_sql);
if (!$count_result) {
    die("Database error: " . mysqli_error($con));
}
$total_count = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_count / $limit);

// Add ordering and limit
$sql .= " ORDER BY created_at DESC LIMIT $offset, $limit";

// Execute query
$result = mysqli_query($con, $sql);
if (!$result) {
    die("Database error: " . mysqli_error($con));
}

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}

// Get counts by type for tabs
$type_result = mysqli_query($con, "SELECT type, COUNT(*) as count FROM notifications WHERE for_admin = 1 GROUP BY type");
if ($type_result) {
    while ($row = mysqli_fetch_assoc($type_result)) {
        $db_type = $row['type'];
        if (isset($type_mapping[$db_type])) {
            $tab_type = $type_mapping[$db_type];
            $type_counts[$tab_type] += $row['count'];
        }
    }
}

// The rest of your HTML/PHP code remains the same...
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
    <title>Notifications - Admin Dashboard</title>
    <?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA] mt-20">
        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Notifications</h1>

            <div class="flex fex-col gap-2">
                <div class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                    <div class="flex items-center gap-0">
                        <form action="" method="GET" class="w-full">
                            <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                                <i class="fa-solid fa-magnifying-glass text-[18px]" alt="Search"></i>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                                <?php if ($current_tab !== 'all'): ?>
                                <input type="hidden" name="tab" value="<?php echo htmlspecialchars($current_tab); ?>" />
                                <?php endif; ?>
                                <?php if ($status_filter !== 'all'): ?>
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>" />
                                <?php endif; ?>
                                <?php if ($date_filter !== 'all'): ?>
                                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date_filter); ?>" />
                                <?php endif; ?>
                                <button type="submit" class="sr-only">Search</button>
                            </div>
                        </form>
                    </div>

                    <div class="w-full flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filter by:</span>

                            <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                                <div class="custom-dropdown">
                                    <div class="md:min-w-[85px] lg:min-w-[90px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                            <?php 
                                            echo ucfirst($status_filter);
                                            ?>
                                        </span>
                                        <i class="fa-solid fa-chevron-down arrow-down"></i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'all'])); ?>">All</a>
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'unread'])); ?>">Unread</a>
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'read'])); ?>">Read</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="custom-dropdown">
                                    <div class="md:min-w-[85px] lg:min-w-[90px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                            <?php 
                                            if ($date_filter === 'all') echo 'Date';
                                            else if ($date_filter === 'today') echo 'Today';
                                            else if ($date_filter === 'week') echo 'Last 7 days';
                                            else if ($date_filter === 'month') echo 'Last 28 days';
                                            else echo 'Custom date';
                                            ?>
                                        </span>
                                        <i class="fa-solid fa-chevron-down arrow-down"></i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['date' => 'today'])); ?>">Today</a>
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['date' => 'week'])); ?>">Last 7 days</a>
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['date' => 'month'])); ?>">Last 28 days</a>
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['date' => 'custom'])); ?>">Custom date</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($search_query) || $status_filter !== 'all' || $date_filter !== 'all'): ?>
                        <div class="flex items-center gap-1">
                            <a href="?tab=<?php echo $current_tab; ?>" class="flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-xmark text-[14px] text-[#262626]"></i>
                                <span class="text-[#262626] text-[14px] font-Onest font-regular">Clear filter</span>
                            </a>
                        </div>
                        <?php endif; ?>

                        <button class="hidden flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                            <i class="fa-solid fa-download text-[16px]"></i>
                            Export as
                        </button>

                        <?php if ($status_filter === 'unread' || $status_filter === 'all'): ?>
                        <a href="?notification_action=read_all" class="hidden flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg cursor-pointer">
                            <i class="fa-solid fa-check text-[16px]"></i>
                            Mark all as read
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="w-full justify-between overflow-x-auto flex border-b border-[#DDDDDD] mb-6" id="tabs">
                <a href="?tab=all<?php echo (!empty($search_query) ? '&search='.urlencode($search_query) : '').
                    ($status_filter !== 'all' ? '&status='.urlencode($status_filter) : '').
                    ($date_filter !== 'all' ? '&date='.urlencode($date_filter) : ''); ?>" 
                    class="text-nowrap px-4 py-2 <?php echo $current_tab === 'all' ? 'text-[#007F7F] border-b-2 border-[#007F7F]' : 'text-gray-600 hover:text-gray-900'; ?> tab-button">
                    All (<?php echo $total_count; ?>)
                </a>
                <a href="?tab=orders<?php echo (!empty($search_query) ? '&search='.urlencode($search_query) : '').
                    ($status_filter !== 'all' ? '&status='.urlencode($status_filter) : '').
                    ($date_filter !== 'all' ? '&date='.urlencode($date_filter) : ''); ?>" 
                    class="text-nowrap px-4 py-2 <?php echo $current_tab === 'orders' ? 'text-[#007F7F] border-b-2 border-[#007F7F]' : 'text-gray-600 hover:text-gray-900'; ?> tab-button">
                    Orders (<?php echo $type_counts['orders']; ?>)
                </a>
                <a href="?tab=issues<?php echo (!empty($search_query) ? '&search='.urlencode($search_query) : '').
                    ($status_filter !== 'all' ? '&status='.urlencode($status_filter) : '').
                    ($date_filter !== 'all' ? '&date='.urlencode($date_filter) : ''); ?>" 
                    class="text-nowrap px-4 py-2 <?php echo $current_tab === 'issues' ? 'text-[#007F7F] border-b-2 border-[#007F7F]' : 'text-gray-600 hover:text-gray-900'; ?> tab-button">
                    Issues (<?php echo $type_counts['issues']; ?>)
                </a>
                <a href="?tab=returns<?php echo (!empty($search_query) ? '&search='.urlencode($search_query) : '').
                    ($status_filter !== 'all' ? '&status='.urlencode($status_filter) : '').
                    ($date_filter !== 'all' ? '&date='.urlencode($date_filter) : ''); ?>" 
                    class="text-nowrap px-4 py-2 <?php echo $current_tab === 'returns' ? 'text-[#007F7F] border-b-2 border-[#007F7F]' : 'text-gray-600 hover:text-gray-900'; ?> tab-button">
                    Returns (<?php echo $type_counts['returns']; ?>)
                </a>
                <a href="?tab=reviews<?php echo (!empty($search_query) ? '&search='.urlencode($search_query) : '').
                    ($status_filter !== 'all' ? '&status='.urlencode($status_filter) : '').
                    ($date_filter !== 'all' ? '&date='.urlencode($date_filter) : ''); ?>" 
                    class="text-nowrap px-4 py-2 <?php echo $current_tab === 'reviews' ? 'text-[#007F7F] border-b-2 border-[#007F7F]' : 'text-gray-600 hover:text-gray-900'; ?> tab-button">
                    Reviews (<?php echo $type_counts['reviews']; ?>)
                </a>
            </div>

            <!-- Activity Feed Section -->
            <div class="tab-content">
                <div class="space-y-2">
                    <div class="flex flex-col gap-2">
                        <?php if (empty($notifications)): ?>
                            <div class="p-4 text-center text-gray-500">No notifications found</div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="w-full flex flex-col gap-2 rounded-[4px] <?php echo $notification['is_read'] ? 'bg-white' : 'bg-[#EEEEEE]'; ?> p-2">
                                    <div class="flex items-center justify-between">
                                        <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]"><?php echo htmlspecialchars($notification['title']); ?></h1>
                                        <div class="relative flex items-center gap-2">
                                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">
                                                <?php 
                                                $created_at = new DateTime($notification['created_at']);
                                                echo $created_at->format('M d, Y h:i A'); 
                                                ?>
                                            </span>
                                            <i class="fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer" onclick="openNotimenu(this)"></i>
                                            <!-- The dropdown menu -->
                                            <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                                <div class="flex flex-col gap-3">
                                                <?php if ($notification['type'] === 'order' && !empty($notification['reference_id'])): ?>
    <a href="./order-details.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
<?php elseif ($notification['type'] === 'return' && !empty($notification['reference_id'])): ?>
    <a href="./admin-view-return.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
<?php elseif ($notification['type'] === 'issue' && !empty($notification['reference_id'])): ?>
    <a href="./issues.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
<?php elseif ($notification['type'] === 'review' && !empty($notification['reference_id'])): ?>
    <a href="./reviews.php?id=<?php echo htmlspecialchars($notification['reference_id']); ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
<?php else: ?>
    <a href="#" class="text-[16px] font-medium text-[#262626]">View Details</a>
<?php endif; ?>
                                                    
                                                    <?php if ($notification['is_read']): ?>
                                                        <a href="?notification_action=unread&notification_id=<?php echo $notification['notification_id']; ?>&<?php echo http_build_query(array_filter([
                                                            'tab' => $current_tab !== 'all' ? $current_tab : null,
                                                            'search' => !empty($search_query) ? $search_query : null,
                                                            'status' => $status_filter !== 'all' ? $status_filter : null,
                                                            'date' => $date_filter !== 'all' ? $date_filter : null,
                                                            'page' => $page > 1 ? $page : null
                                                        ])); ?>" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                                    <?php else: ?>
                                                        <a href="?notification_action=read&notification_id=<?php echo $notification['notification_id']; ?>&<?php echo http_build_query(array_filter([
                                                            'tab' => $current_tab !== 'all' ? $current_tab : null,
                                                            'search' => !empty($search_query) ? $search_query : null,
                                                            'status' => $status_filter !== 'all' ? $status_filter : null,
                                                            'date' => $date_filter !== 'all' ? $date_filter : null,
                                                            'page' => $page > 1 ? $page : null
                                                        ])); ?>" class="text-[16px] font-medium text-[#E8B006]">Mark as read</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">
                                        <?php 
                                        if ($notification['type'] === 'order' && !empty($notification['reference_id'])) {
                                            echo '<span class="underline">Order #' . htmlspecialchars($notification['reference_id']) . '</span> ';
                                        }
                                        echo htmlspecialchars($notification['message']); 
                                        ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_count > 0): ?>
                <div class="w-full py-2 mx-auto">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">
                        Showing <?php echo min($limit, count($notifications)); ?> results from <?php echo $total_count; ?>
                    </span>
                    <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                        <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-[12px]"></i>
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                        </a>
                        <?php else: ?>
                        <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                            <i class="fa-solid fa-chevron-left text-[12px]"></i>
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                        </div>
                        <?php endif; ?>

                        <div class="w-full flex items-center justify-between md:gap-6">
                            <?php
                            // Calculate which page numbers to show
                            $start_page = max(1, min($page - 2, $total_pages - 4));
                            $end_page = min($total_pages, max(5, $page + 2));
                            
                            // Always show first page
                            if ($start_page > 1) {
                                echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">1</a>';
                                if ($start_page > 2) {
                                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                                }
                            }
                            
                            // Show page numbers
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo '<span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#C2185B]">' . $i . '</span>';
                                } else {
                                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $i . '</a>';
                                }
                            }
                            
                            // Always show last page
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                                }
                                echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $total_pages . '</a>';
                            }
                            ?>
                        </div>

                        <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="flex items-center gap-2 cursor-pointer">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                            <i class="fa-solid fa-chevron-right text-[12px]"></i>
                        </a>
                        <?php else: ?>
                        <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                            <i class="fa-solid fa-chevron-right text-[12px]"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="./drop-select.js"></script>
    <script type="text/javascript" src="./notification.js"></script>
 

</body>
</html>