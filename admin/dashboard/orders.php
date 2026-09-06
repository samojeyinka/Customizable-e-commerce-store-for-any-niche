<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../config/config.php";

// Include authentication utility
require_once '../../includes/auth/auth.php';

// Database connection
$conn = db();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get valid columns from orders table
$available_columns = [];
$debug_result = $conn->query("SHOW COLUMNS FROM orders");
if ($debug_result) {
    while ($row = $debug_result->fetch_assoc()) {
        $available_columns[] = $row['Field'];
    }
}

// Determine date column with proper syntax
$date_column = 'id'; // Default fallback
if (in_array('created_at', $available_columns)) {
    $date_column = 'created_at';
} elseif (in_array('date_added', $available_columns)) {
    $date_column = 'date_added';
} elseif (in_array('created', $available_columns)) {
    $date_column = 'created';
}

// Initialize filter conditions and search query variable
$conditions = [];
$search_query = '';

// Date filter handling
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
if (isset($_GET['date'])) {
    // Validate date filter
    $allowed_filters = ['today', 'last7days', 'last28days', 'custom', 'all'];
    if (in_array($date_filter, $allowed_filters)) {
        switch ($date_filter) {
            case 'today':
                $conditions[] = "DATE(orders.$date_column) = CURDATE()";
                break;
            case 'last7days':
                $conditions[] = "DATE(orders.$date_column) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last28days':
                $conditions[] = "DATE(orders.$date_column) >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
                break;
            case 'custom':
                if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                    $start_date = $conn->real_escape_string($_GET['start_date']);
                    $end_date = $conn->real_escape_string($_GET['end_date']);
                    $conditions[] = "DATE(orders.$date_column) BETWEEN '$start_date' AND '$end_date'";
                }
                break;
            // If 'all' is selected or no valid filter is selected, no condition is added
        }
    }
}

// Search functionality
if (!empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $search = $conn->real_escape_string($search_query);
// Search by order ID and possibly by customer name if joined with profiles
    $conditions[] = "(orders.id LIKE '%$search%' OR CONCAT(profiles.first_name, ' ', profiles.last_name) LIKE '%$search%' OR users.email LIKE '%$search%')";
}

// Status filter
if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $conditions[] = "orders.order_status = '$status'";
}

// Build WHERE clause
$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Base SQL query - Include JOIN with users and profiles
$sql = "SELECT 
            orders.id AS order_id,
            orders.order_total AS amount,
            orders.delivery_method,
            orders.order_status,
            orders.$date_column AS order_date,
            users.email AS customer_email,
            profiles.first_name,
            profiles.last_name
        FROM orders
        LEFT JOIN users ON orders.user_id = users.id
        LEFT JOIN profiles ON orders.user_id = profiles.user_id
        $where_clause
        ORDER BY orders.$date_column DESC";

// Get total count for pagination with correct JOIN
$count_sql = "SELECT COUNT(*) AS total FROM orders LEFT JOIN users ON orders.user_id = users.id LEFT JOIN profiles ON orders.user_id = profiles.user_id $where_clause";
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
    $orders = [];
} else {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}

// Date formatting
foreach ($orders as &$order) {
    if (!empty($order['order_date'])) {
        try {
            $dt = new DateTime($order['order_date']);
            $order['formatted_date'] = $dt->format('d/m/Y');
            $order['formatted_time'] = $dt->format('h:ia');
        } catch (Exception $e) {
            $order['formatted_date'] = 'Invalid Date';
            $order['formatted_time'] = 'Invalid Time';
        }
    } else {
        $order['formatted_date'] = 'N/A';
        $order['formatted_time'] = 'N/A';
    }

    // Build a readable customer name with fallbacks
    $name = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''));
    $order['customer_name'] = $name !== ''
        ? $name
        : (!empty($order['customer_email']) ? $order['customer_email'] : 'Guest');
}

// Status classes configuration
$status_classes = [
    'Confirmed' => 'bg-[#1A7E79]',
    'Processing' => 'bg-[#E8B006]',
    'Shipped' => 'bg-[#C2185B]',
    'Delivered' => 'bg-[#39D959]',
    'Cancelled' => 'bg-red-500',
    'Returned' => 'bg-[#9C27B0]'
];


// Get order statistics for the modal
$stats_query = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN order_status = 'Delivered' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN order_status IN ('Processing', 'Shipped') THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN order_status IN ('Returned', 'Cancelled') THEN 1 ELSE 0 END) as returned_orders,
    (SELECT COUNT(*) FROM orders WHERE $date_column >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)) as last_28_days,
    (SELECT COUNT(*) FROM orders WHERE $date_column >= DATE_SUB(CURDATE(), INTERVAL 56 DAY) AND $date_column < DATE_SUB(CURDATE(), INTERVAL 28 DAY)) as previous_28_days
FROM orders";

$stats_result = $conn->query($stats_query);
$order_stats = $stats_result->fetch_assoc();

// Calculate percentage changes
$total_change = $order_stats['last_28_days'] - $order_stats['previous_28_days'];
$total_percentage = $order_stats['previous_28_days'] != 0 
    ? round(($total_change / $order_stats['previous_28_days']) * 100) 
    : ($order_stats['last_28_days'] > 0 ? 100 : 0);


    
// Add this at the top of your PHP code (before any HTML output)
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="orders_export_'.date('Y-m-d').'.xls"');
    
// Get all orders without pagination for export
    $export_sql = "SELECT 
        orders.id AS order_id,
        CONCAT(COALESCE(profiles.first_name, ''), ' ', COALESCE(profiles.last_name, '')) AS customer_name,
        users.email AS customer_email,
        orders.order_total AS amount,
        orders.order_status,
        orders.delivery_method,
        orders.$date_column AS order_date
    FROM orders
    LEFT JOIN users ON orders.user_id = users.id
    LEFT JOIN profiles ON orders.user_id = profiles.user_id
    $where_clause
    ORDER BY orders.$date_column DESC";
    
    $export_result = $conn->query($export_sql);
    
    $exported_orders = [];
    while ($export_order = $export_result->fetch_assoc()) {
        $export_name = trim($export_order['customer_name']);
        if ($export_name === '') {
            $export_name = !empty($export_order['customer_email']) ? $export_order['customer_email'] : 'Guest';
        }
        $export_order['customer_name'] = $export_name;
        $exported_orders[] = $export_order;
    }
    
    // Start Excel output
    echo "<table border='1'>";
    echo "<tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Delivery Method</th>
            <th>Order Date</th>
          </tr>";
    
foreach ($exported_orders as $order) {
        echo "<tr>
                <td>#".htmlspecialchars($order['order_id'])."</td>
                <td>".htmlspecialchars($order['customer_name'])."</td>
                <td>₦".number_format((float)$order['amount'])."</td>
                <td>".htmlspecialchars($order['order_status'])."</td>
                <td>".htmlspecialchars($order['delivery_method'])."</td>
                <td>".date('d/m/Y h:ia', strtotime($order['order_date']))."</td>
              </tr>";
    }
    echo "</table>";
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<title>Orders</title>

<?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>


<body class="relative">

<?php
include "./header.php";
include "./sidebar.php"
?>


  


<?php
    // Shared page parameter string used by pagination links
    $page_params = http_build_query(array_filter([
        'search' => $search_query ?: null,
        'date' => $date_filter != 'all' ? $date_filter : null,
        'start_date' => ($date_filter == 'custom' && isset($_GET['start_date'])) ? $_GET['start_date'] : null,
        'end_date' => ($date_filter == 'custom' && isset($_GET['end_date'])) ? $_GET['end_date'] : null,
    ]));
    $page_link = function($page) use ($page_params) {
        return '?page=' . (int)$page . ($page_params !== '' ? '&' . $page_params : '');
    };
    ?>

    <div id="main" class="md:p-4 flex flex-col gap-4 bg-[#FAFAFA]">

        <!-- Page heading -->
        <div class="px-1 md:px-2 mt-2 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-[22px] md:text-[26px] font-Onest font-bold text-[#111827]">Orders</h1>
                <p class="text-[14px] font-['Open Sans'] text-gray-500 mt-1">Manage and track all customer orders</p>
            </div>
            <button id="openOrdersModal" class="w-fit md:w-auto flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-[14px] font-medium text-[#262626] hover:bg-gray-50 cursor-pointer transition-colors shrink-0">
                <i class="fa-solid fa-chart-pie text-[14px] text-[#C2185B]"></i>
                Orders Overview
            </button>
        </div>

        <!-- Toolbar + table card -->
        <div class="w-full rounded-xl bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)] border border-gray-100 mx-auto overflow-hidden">
            <div id="myBtn" class="w-full p-4 flex flex-col md:flex-row md:items-center gap-3 md:gap-4 justify-between border-b border-gray-100">
                <form action="" method="GET" class="w-full md:max-w-[340px]">
                    <div class="w-full flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus-within:border-[#C2185B] focus-within:ring-2 focus-within:ring-[#C2185B]/10 transition-all">
                        <i class="fa-solid fa-magnifying-glass text-[15px] text-gray-400" alt="Search"></i>
                        <input type="text" name="search" placeholder="Search order ID or customer..." value="<?php echo htmlspecialchars($search_query); ?>" class="w-full text-[14px] font-['Open Sans'] bg-transparent border-none outline-none placeholder:text-gray-400" />
                        <?php if($date_filter != 'all'): ?>
                            <input type="hidden" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                        <?php endif; ?>
                    </div>
                </form>

                <div class="flex items-center gap-3 flex-wrap">
                    <form action="" method="GET" class="flex items-center gap-2">
                        <?php if(!empty($search_query)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <?php endif; ?>
                        <select name="date" id="dateFilter" onchange="this.form.submit()" class="rounded-lg border border-gray-200 bg-white py-2 px-3 text-[14px] font-['Open Sans'] text-[#262626] focus:ring-2 focus:ring-[#C2185B]/10 focus:border-[#C2185B] focus:outline-none">
                            <option value="all" <?php echo $date_filter == 'all' ? 'selected' : ''; ?>>All time</option>
                            <option value="today" <?php echo $date_filter == 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="last7days" <?php echo $date_filter == 'last7days' ? 'selected' : ''; ?>>Last 7 days</option>
                            <option value="last28days" <?php echo $date_filter == 'last28days' ? 'selected' : ''; ?>>Last 28 days</option>
                        </select>
                    </form>

                    <?php if ($date_filter != 'all' || !empty($search_query)): ?>
                    <a href="?date=all" class="flex items-center gap-1 text-[14px] font-medium text-gray-500 hover:text-[#D93939] transition-colors">
                        <i class="fa-solid fa-xmark text-[13px]"></i>
                        Clear filter
                    </a>
                    <?php endif; ?>

                    <button onclick="exportToExcel()" class="flex items-center gap-2 px-4 py-2 bg-[#111827] hover:bg-[#1F2937] text-white text-[14px] font-medium rounded-lg cursor-pointer transition-colors shrink-0">
                        <i class="fa-solid fa-download text-[14px]"></i>
                        Export
                    </button>
                </div>
            </div>

            
<div class="overflow-x-auto">
                <table class="w-full min-w-[760px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 text-[12px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">Order ID</th>
                            <th class="text-left px-4 py-3 text-[12px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">Customer</th>
                            <th class="text-left px-4 py-3 text-[12px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">Amount</th>
                            <th class="text-left px-4 py-3 text-[12px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">Status</th>
                            <th class="text-left px-4 py-3 text-[12px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">Mode of Order</th>
                            <th class="text-left px-4 py-3 text-[12px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">Date</th>
                            <th class="text-right px-4 py-3"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-400 font-['Open Sans'] text-[14px]">No orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="border-b border-gray-50 hover:bg-[#FBF5F8] transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-[14px] font-semibold font-['Open Sans'] text-[#262626]">#<?php echo htmlspecialchars($order['order_id']); ?></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-[14px] font-['Open Sans'] text-[#262626]"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                                        <?php if (!empty($order['customer_email']) && $order['customer_email'] !== $order['customer_name']): ?>
                                            <span class="text-[12px] text-gray-400 font-['Open Sans'] block"><?php echo htmlspecialchars($order['customer_email']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-[14px] font-semibold font-['Open Sans'] text-[#262626]">₦<?php echo number_format($order['amount'] ?? 0); ?></td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <?php
                                        $status = $order['order_status'] ?? 'Processing';
                                        $status_class = $status_classes[$status] ?? 'bg-[#E8B006]';
                                        ?>
                                        <span class="inline-flex px-3 py-1 <?php echo $status_class; ?> text-white text-[12px] font-medium font-['Open Sans'] rounded-full"><?php echo htmlspecialchars($status); ?></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-[14px] font-['Open Sans'] text-[#262626]"><?php echo htmlspecialchars(ucfirst($order['delivery_method'] ?? 'Express')); ?></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-[14px] font-['Open Sans'] text-gray-500"><?php echo htmlspecialchars($order['formatted_date'] . ' ' . $order['formatted_time']); ?></td>
                                    <td class="px-4 py-4 text-right relative">
                                        <i class="fa-solid fa-ellipsis-vertical text-[17px] text-gray-400 cursor-pointer hover:text-[#262626]" onclick="openAdminOrderMenu(this)"></i>

                                        <!-- The menu for each order  starts ---->
                                        <div class="adminordersMenu bg-white border border-gray-100 shadow-lg rounded-lg p-2">
                                            <div class="flex flex-col gap-1">
                                                <a href="./order-details.php?id=<?php echo $order['order_id']; ?>" class="px-3 py-2 text-[14px] font-medium text-[#262626] rounded-md hover:bg-gray-50">View Details</a>
                                                <a href="./order-details.php?id=<?php echo $order['order_id']; ?>&tab=update-status" class="px-3 py-2 text-[14px] font-medium text-[#262626] rounded-md hover:bg-gray-50">Update Order Status</a>

                                                <a href="./order-details.php?id=<?php echo $order['order_id']; ?>&tab=update-status" class="px-3 py-2 text-[14px] font-medium text-[#D93939] rounded-md hover:bg-red-50">Cancel Order</a>
                                            </div>
                                        </div>

                                        <!-- The menu for each order  ends ---->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

<?php if ($total_pages > 1): ?>
            <div class="w-full px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 border-t border-gray-100">
                <span class="text-[13px] text-gray-500 font-['Open Sans']">Showing <?php echo count($orders); ?> of <?php echo $total_count; ?> orders</span>
                <div class="flex items-center gap-1">

                    <div class="flex items-center gap-1">
                        <?php if ($current_page > 1): ?>
                            <a href="<?php echo $page_link($current_page - 1); ?>" class="px-3 py-1.5 text-[13px] font-['Open Sans'] text-gray-600 rounded-lg hover:bg-gray-100 flex items-center gap-1"><i class="fa-solid fa-chevron-left text-[11px]"></i> Prev</a>
                        <?php else: ?>
                            <span class="px-3 py-1.5 text-[13px] font-['Open Sans'] text-gray-300 flex items-center gap-1"><i class="fa-solid fa-chevron-left text-[11px]"></i> Prev</span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-1">
                        <?php
                        $start_page = max(1, min($current_page - 2, $total_pages - 4));
                        $end_page = min($total_pages, max($current_page + 2, 5));
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <?php if ($i == $current_page): ?>
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#C2185B] text-white text-[13px] font-medium"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo $page_link($i); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-['Open Sans'] text-gray-600 hover:bg-gray-100"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <span class="px-1 text-gray-400 font-['Open Sans']">...</span>
                            <a href="<?php echo $page_link($total_pages); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-['Open Sans'] text-gray-600 hover:bg-gray-100"><?php echo $total_pages; ?></a>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-1">
                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?php echo $page_link($current_page + 1); ?>" class="px-3 py-1.5 text-[13px] font-['Open Sans'] text-gray-600 rounded-lg hover:bg-gray-100 flex items-center gap-1">Next <i class="fa-solid fa-chevron-right text-[11px]"></i></a>
                        <?php else: ?>
                            <span class="px-3 py-1.5 text-[13px] font-['Open Sans'] text-gray-300 flex items-center gap-1">Next <i class="fa-solid fa-chevron-right text-[11px]"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- The modals starts -->
    <div id="ordersModal" class="modal reg">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
        <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Order Overview</h1>
        <i class="fa-solid fa-xmark text-[24px] cursor-pointer absolute top-4 right-4" id="closeOrdersModal" alt="close"></i>

        <div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-4 mt-2">
            <!-- Total Orders Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Orders</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((float)$order_stats['total_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid <?php echo $total_change >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?> text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $total_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo abs($total_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Completed Orders Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Completed Orders</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((float)$order_stats['completed_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-[#39D959]">+12%</span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Orders Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Pending Orders</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((float)$order_stats['pending_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-down text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-[#D93939]">+12%</span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Returned Orders Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Returned Orders</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((float)$order_stats['returned_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-[#39D959]">+12%</span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- The modals ends -->






    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>

<script>
function openAdminOrderMenu(element) {
  const menuContainer = element.closest('td').querySelector('.adminordersMenu');
  const isOpen = menuContainer.style.display === "block";

  document.querySelectorAll('.adminordersMenu').forEach(menu => {
      menu.style.display = "none";
  });

  if (isOpen) return;

  const rect = element.getBoundingClientRect();
  const menuWidth = menuContainer.offsetWidth || 190;
  const menuHeight = menuContainer.offsetHeight || 100;

  let left = rect.right - menuWidth;
  if (left < 8) left = 8;

  let top = rect.bottom + 6;
  if (top + menuHeight > window.innerHeight) {
      top = rect.top - menuHeight - 6;
  }

  menuContainer.style.position = "fixed";
  menuContainer.style.left = left + "px";
  menuContainer.style.top = top + "px";
  menuContainer.style.width = menuWidth + "px";
  menuContainer.style.height = "auto";
  menuContainer.style.minHeight = "auto";
  menuContainer.style.margin = "0";
  menuContainer.style.display = "block";
}

function closeAdminOrderMenus() {
    document.querySelectorAll('.adminordersMenu').forEach(menu => {
        menu.style.display = "none";
    });
}

// Add this to hide menus when clicking outside
document.addEventListener('click', function(event) {
    // Check if the click was outside any menu or menu trigger
    if (!event.target.closest('.adminordersMenu') && !event.target.closest('[onclick="openAdminOrderMenu(this)"]')) {
        closeAdminOrderMenus();
    }
});

window.addEventListener('scroll', closeAdminOrderMenus, true);
window.addEventListener('resize', closeAdminOrderMenus);

// Make sure menus are hidden initially
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.adminordersMenu').forEach(menu => {
        menu.style.display = "none";
    });
});





const openOrdersModal = document.getElementById("openOrdersModal");
const closeOrdersModal = document.getElementById("closeOrdersModal");
const ordersModal = document.getElementById("ordersModal");

openOrdersModal.onclick = function () {
    ordersModal.style.display = "block";
}


closeOrdersModal.onclick = function () {
    ordersModal.style.display = "none";
}


function exportToExcel() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    
    // Redirect to the export URL
    window.location.href = 'orders.php?' + params.toString();
}

</script>

</body>

</html>