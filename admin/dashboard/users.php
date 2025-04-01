<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize filter conditions and search query variable
$conditions = [];
$search_query = '';

// Date filter handling for last login
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
if (isset($_GET['date'])) {
    // Validate date filter
    $allowed_filters = ['today', 'last7days', 'last28days', 'all'];
    if (in_array($date_filter, $allowed_filters)) {
        switch ($date_filter) {
            case 'today':
                $conditions[] = "DATE(users.last_login) = CURDATE()";
                break;
            case 'last7days':
                $conditions[] = "DATE(users.last_login) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last28days':
                $conditions[] = "DATE(users.last_login) >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
                break;
            // If 'all' is selected or no valid filter is selected, no condition is added
        }
    }
}

// Search functionality
if (!empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $search = $conn->real_escape_string($search_query);
    // Search by user details
    $conditions[] = "(users.email LIKE '%$search%' OR profiles.first_name LIKE '%$search%' OR profiles.last_name LIKE '%$search%' OR profiles.phone LIKE '%$search%')";
}

// Status filter (for disabled/enabled)
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $status = $conn->real_escape_string($_GET['status']);
    $conditions[] = "users.is_disabled = " . ($status == 'disabled' ? '1' : '0');
}

// Build WHERE clause
$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Base SQL query - Include JOIN with profiles and orders
$sql = "SELECT 
            users.id,
            users.email,
            users.last_login,
            users.is_disabled,
            profiles.first_name,
            profiles.last_name,
            profiles.phone,
            profiles.address,
            COUNT(DISTINCT orders.id) AS total_orders,
            MAX(orders.created_at) AS last_order_date
        FROM users
        LEFT JOIN profiles ON users.id = profiles.user_id
        LEFT JOIN orders ON users.id = orders.user_id
        $where_clause
        GROUP BY users.id
        ORDER BY users.last_login DESC";

// Get total count for pagination
$count_sql = "SELECT COUNT(DISTINCT users.id) AS total FROM users LEFT JOIN profiles ON users.id = profiles.user_id LEFT JOIN orders ON users.id = orders.user_id $where_clause";
$count_result = $conn->query($count_sql);
$total_count = $count_result->fetch_assoc()['total'];

// Pagination setup
$items_per_page = 10;
$total_pages = ceil($total_count / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $total_pages)) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Add pagination to main query
$sql .= " LIMIT $offset, $items_per_page";

// Execute query
$result = $conn->query($sql);
if (!$result) {
    // For debugging
    echo "Error in query: " . $conn->error;
    $users = [];
} else {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Date formatting for users
foreach ($users as &$user) {
    if (!empty($user['last_login'])) {
        try {
            $dt = new DateTime($user['last_login']);
            $user['formatted_login_date'] = $dt->format('d/m/Y');
            $user['formatted_login_time'] = $dt->format('h:ia');
        } catch (Exception $e) {
            $user['formatted_login_date'] = 'N/A';
            $user['formatted_login_time'] = 'N/A';
        }
    } else {
        $user['formatted_login_date'] = 'N/A';
        $user['formatted_login_time'] = 'N/A';
    }
    
    if (!empty($user['last_order_date'])) {
        try {
            $dt = new DateTime($user['last_order_date']);
            $user['formatted_order_date'] = $dt->format('d/m/Y');
            $user['formatted_order_time'] = $dt->format('h:ia');
        } catch (Exception $e) {
            $user['formatted_order_date'] = 'N/A';
            $user['formatted_order_time'] = 'N/A';
        }
    } else {
        $user['formatted_order_date'] = 'N/A';
        $user['formatted_order_time'] = 'N/A';
    }
}

// Get user statistics for the modal
$stats_query = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN users.is_disabled = 0 THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN users.is_disabled = 1 THEN 1 ELSE 0 END) as disabled_users,
    (SELECT COUNT(*) FROM users WHERE last_login >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)) as recent_users,
    (SELECT COUNT(*) FROM users WHERE last_login >= DATE_SUB(CURDATE(), INTERVAL 56 DAY) AND last_login < DATE_SUB(CURDATE(), INTERVAL 28 DAY)) as previous_period_users
FROM users";

$stats_result = $conn->query($stats_query);
$user_stats = $stats_result->fetch_assoc();

// Calculate percentage changes
$user_change = $user_stats['recent_users'] - $user_stats['previous_period_users'];
$user_percentage = $user_stats['previous_period_users'] != 0 
    ? round(($user_change / $user_stats['previous_period_users']) * 100) 
    : ($user_stats['recent_users'] > 0 ? 100 : 0);

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_export_'.date('Y-m-d').'.csv"');
    
    // Get all users without pagination for export
    $export_sql = "SELECT 
        users.id,
        users.email,
        users.last_login,
        users.is_disabled,
        profiles.first_name,
        profiles.last_name,
        profiles.phone,
        profiles.address,
        COUNT(DISTINCT orders.id) AS total_orders,
        MAX(orders.created_at) AS last_order_date
    FROM users
    LEFT JOIN profiles ON users.id = profiles.user_id
    LEFT JOIN orders ON users.id = orders.user_id
    $where_clause
    GROUP BY users.id
    ORDER BY users.last_login DESC";
    
    $export_result = $conn->query($export_sql);
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Address', 'Last Login', 'Status', 'Total Orders', 'Last Order Date']);
    
    // Add data rows
    while ($user = $export_result->fetch_assoc()) {
        $status = ($user['is_disabled'] == 0) ? 'Active' : 'Disabled';
        $name = $user['first_name'] . ' ' . $user['last_name'];
        
        fputcsv($output, [
            $user['id'],
            $name,
            $user['email'],
            $user['phone'],
            $user['address'],
            $user['last_login'],
            $status,
            $user['total_orders'],
            $user['last_order_date']
        ]);
    }
    
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Users</title>

    <style>
        .adminusersMenu{
            position: absolute;
            left: -10rem;
            min-width: 10rem;
            min-height: 10rem;
            height: 100%;
            z-index: 2;
            display: none;
        }
    </style>

</head>


<body class="relative">

<?php
include "./header.php";
include "./sidebar.php"
?>

    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Users</h1>

            <div id="openUsersModal" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-Onest font-regular">Users Overview</h1>
                <img src="../assets/dash/Vector 6905.svg" />
            </div>
        </div>

        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <div id="myBtn" class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                <div class="flex items-center gap-0">
                    <form action="" method="GET" class="w-full flex">
                        <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                            <img src="../assets/dash/search-normal (1).svg" alt="Search" class="w-[18px]" />
                            <input type="text" name="search" placeholder="Search by name, email, phone..." value="<?php echo htmlspecialchars($search_query); ?>" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                            <!-- Preserve date filter when searching -->
                            <?php if($date_filter != 'all'): ?>
                                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filter by:</span>
                        <img src="../assets/dash/filter-horizontal.svg" class="md:hidden" />

                        <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                           <!-- Filter dropdowns -->
                           <div class="flex items-center gap-2">
                               <form id="dateFilterForm" action="" method="GET" class="flex items-center">
                                   <!-- Preserve search parameter if it exists -->
                                   <?php if(!empty($search_query)): ?>
                                   <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                                   <?php endif; ?>
                                   
                                   <select name="date" id="dateFilter" onchange="this.form.submit()" class="md:min-w-[120px] rounded-[4px] border-[1px] border-[#C5C5C5] py-1 px-2 text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular focus:outline-none">
                                       <option value="all" <?php echo $date_filter == 'all' ? 'selected' : ''; ?>>All time</option>
                                       <option value="today" <?php echo $date_filter == 'today' ? 'selected' : ''; ?>>Today</option>
                                       <option value="last7days" <?php echo $date_filter == 'last7days' ? 'selected' : ''; ?>>Last 7 days</option>
                                       <option value="last28days" <?php echo $date_filter == 'last28days' ? 'selected' : ''; ?>>Last 28 days</option>
                                   </select>
                               </form>
                               
                               <!-- Status filter dropdown -->
                               <form id="statusFilterForm" action="" method="GET" class="flex items-center">
                                   <!-- Preserve existing parameters -->
                                   <?php if(!empty($search_query)): ?>
                                   <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                                   <?php endif; ?>
                                   
                                   <?php if($date_filter != 'all'): ?>
                                   <input type="hidden" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                                   <?php endif; ?>
                                   
                                   <select name="status" id="statusFilter" onchange="this.form.submit()" class="md:min-w-[120px] rounded-[4px] border-[1px] border-[#C5C5C5] py-1 px-2 text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular focus:outline-none">
                                       <option value="">All users</option>
                                       <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                       <option value="disabled" <?php echo isset($_GET['status']) && $_GET['status'] == 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                                   </select>
                               </form>
                           </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <img src="../assets/dash/Path.svg" />
                        <a href="?">
                            <span class="text-[#262626] text-[14px] font-Onest font-regular">Clear filter</span>
                        </a>
                    </div>

                    <button onclick="exportToCSV()" class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer shrink-0">
                        <img src="../assets/dash/send-square.svg" />
                        Export
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto mt-3 h-[33rem]">
                <table cols="" class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th class="text-nowrap p-2 flex items-center gap-2">
                            <input type="checkbox" />
                            <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Customer Name</span>
                        </th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Email</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Phone</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Address</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Last Login</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Total Orders</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Last Order</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                            <img src="../assets/dash/column.svg" class="min-w-[24px] min-h-[24px]" />
                        </th>
                    </thead>

                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">
                                            <?php 
                                            $firstName = $user['first_name'] ?? '';
                                            $lastName = $user['last_name'] ?? '';
                                            $fullName = trim($firstName . ' ' . $lastName);
                                            
                                            echo !empty($fullName) ? htmlspecialchars($fullName) : 'No profile yet'; 
                                            ?>
                                        </span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : '..'; ?>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : '..'; ?>
                                    </td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php 
                                        $loginDate = ($user['formatted_login_date'] ?? '') . ' ' . ($user['formatted_login_time'] ?? '');
                                        echo htmlspecialchars(trim($loginDate));
                                        ?>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php echo $user['total_orders']; ?>
                                    </td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php 
                                        if (!empty($user['last_order_date'])) {
                                            $orderDate = ($user['formatted_order_date'] ?? '') . ' ' . ($user['formatted_order_time'] ?? '');
                                            echo htmlspecialchars(trim($orderDate));
                                        } else {
                                            echo 'No Orders';
                                        }
                                        ?>
                                    </td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openAdminUserMenu(this)" />

                                        <!-- The menu for each user starts -->
                                        <div class="adminusersMenu h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                            <div class="flex flex-col gap-3">
                                                <a href="./user-details.php?id=<?php echo $user['id']; ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                                <?php if ($user['is_disabled'] == 0): ?>
                                                    <a href="javascript:void(0)" onclick="confirmAction(<?php echo $user['id']; ?>, 'disable')" class="text-[16px] font-medium text-[#E8B006]">Disable User</a>
                                                <?php else: ?>
                                                    <a href="javascript:void(0)" onclick="confirmAction(<?php echo $user['id']; ?>, 'enable')" class="text-[16px] font-medium text-[#39D959]">Enable User</a>
                                                <?php endif; ?>
                                                <a href="javascript:void(0)" onclick="confirmAction(<?php echo $user['id']; ?>, 'delete')" class="text-[16px] font-medium text-[#D93939]">Delete User</a>
                                            </div>
                                        </div>
                                        <!-- The menu for each user ends -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">Showing <?php echo count($users); ?> results from <?php echo $total_count; ?></span>
                <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                    <div class="flex items-center gap-2 cursor-pointer">
                        <?php if ($current_page > 1): ?>
                            <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="flex items-center">
                                <img src="../assets/products/prev.svg" class="w-[6px] h-[11px]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                            </a>
                        <?php else: ?>
                            <div class="flex items-center opacity-50">
                                <img src="../assets/products/prev.svg" class="w-[6px] h-[11px]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="w-full flex items-center justify-between md:gap-6">
                        <?php
                        $start_page = max(1, min($current_page - 2, $total_pages - 4));
                        $end_page = min($total_pages, max($current_page + 2, 5));
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <?php if ($i == $current_page): ?>
                                <span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#1A237E]"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>
                            <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer"><?php echo $total_pages; ?></a>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2 cursor-pointer shrink-0">
                        <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>" class="shrink-0 text-nowrap flex items-center">
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                                <img src="../assets/products/next.svg" class="w-[6px] h-[11px]" />
                            </a>
                        <?php else: ?>
                            <div class="flex items-center opacity-50">
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                                <img src="../assets/products/next.svg" class="w-[6px] h-[11px]" />
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Overview Modal -->
    <div id="usersModal" class="modal reg" style="display: none;">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Users Overview</h1>
            <img src="../assets/global/close-circle.svg" alt="close" id="closeUsersModal" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />

            <div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-4 mt-2">
                <!-- Total Users Card -->
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>
                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Users</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($user_stats['total_users']); ?></h2>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/<?php echo $user_change >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                <span class="text-<?php echo $user_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                    <?php echo abs($user_percentage); ?>%
                                </span> from last 28 days
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Active Users Card -->
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>
                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Active Users</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($user_stats['active_users']); ?></h2>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                <span class="text-[#39D959]">
                                    <?php echo round(($user_stats['active_users'] / max(1, $user_stats['total_users'])) * 100); ?>%
                                </span> of total users
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Users Card -->
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>
                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Recent Users (28 Days)</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($user_stats['recent_users']); ?></h2>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/<?php echo $user_change >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                <span class="text-<?php echo $user_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                    <?php echo abs($user_percentage); ?>%
                                </span> from previous period
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Disabled Users Card -->
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                    </div>
                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Disabled Users</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($user_stats['disabled_users']); ?></h2>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/<?php echo $user_stats['disabled_users'] > 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                <span class="text-<?php echo $user_stats['disabled_users'] > 0 ? '[#D93939]' : '[#39D959]'; ?>">
                                    <?php echo round(($user_stats['disabled_users'] / max(1, $user_stats['total_users'])) * 100); ?>%
                                </span> of total users
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); z-index: 1000;">
        <h3 id="modalMessage">Are you sure?</h3>
        <input type="hidden" id="userId">
        <input type="hidden" id="actionType">
        <div class="flex justify-end gap-3 mt-4">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded">Cancel</button>
            <button onclick="performAction()" class="px-4 py-2 bg-blue-900 text-white rounded">Confirm</button>
        </div>
    </div>

    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>

    

    <!-- <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script> -->

    <script>
        // User menu functionality
        function openAdminUserMenu(element) {
            // Find the closest parent td and then find the menu inside it
            const menuContainer = element.closest('td').querySelector('.adminusersMenu');
            
            // Toggle the display of the menu
            if (menuContainer.style.display === "block") {
                menuContainer.style.display = "none";
            } else {
                // First, close all other open menus
                document.querySelectorAll('.adminusersMenu').forEach(menu => {
                    menu.style.display = "none";
                });
                
                // Then open the clicked menu
                menuContainer.style.display = "block";
            }
        }

        // Hide menus when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.adminusersMenu') && !event.target.matches('img[onclick="openAdminUserMenu(this)"]')) {
                document.querySelectorAll('.adminusersMenu').forEach(menu => {
                    menu.style.display = "none";
                });
            }
        });

        // Make sure menus are hidden initially
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.adminusersMenu').forEach(menu => {
                menu.style.display = "none";
            });
        });

        // Modal functionality
        const openUsersModal = document.getElementById("openUsersModal");
        const closeUsersModal = document.getElementById("closeUsersModal");
        const usersModal = document.getElementById("usersModal");

        openUsersModal.onclick = function() {
            usersModal.style.display = "block";
        }

        closeUsersModal.onclick = function() {
            usersModal.style.display = "none";
        }

        // Export to CSV functionality
        function exportToCSV() {
            // Get current filter parameters
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            
            // Redirect to the export URL
            window.location.href = 'users.php?' + params.toString();
        }

        // User action confirmation functionality
        function confirmAction(userId, action) {
            let message = action === 'delete' ? "Are you sure you want to delete this user?" :
                        action === 'disable' ? "Are you sure you want to disable this user?" :
                        "Are you sure you want to enable this user?";
            
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('userId').value = userId;
            document.getElementById('actionType').value = action;
            
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        function performAction() {
            const userId = document.getElementById('userId').value;
            const action = document.getElementById('actionType').value;
            
            // Send AJAX request to user_actions.php
            fetch('user_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `user_id=${userId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); 
                if (data.success) {
                    location.reload(); // Reload page to update UI
                }
            })
            .catch(error => console.error('Error:', error));
            
            closeModal();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === usersModal) {
                usersModal.style.display = "none";
            }
            if (event.target === document.getElementById('confirmationModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>