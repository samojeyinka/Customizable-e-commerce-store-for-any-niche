<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication utility




// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

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
    $conditions[] = "(orders.id LIKE '%$search%' OR CONCAT(profiles.first_name, ' ', profiles.last_name) LIKE '%$search%')";
}

// Status filter
if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $conditions[] = "orders.order_status = '$status'";
}

// Build WHERE clause
$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Base SQL query - Include JOIN with profiles
$sql = "SELECT 
            orders.id AS order_id,
            orders.order_total AS amount,
            orders.delivery_method,
            orders.order_status,
            orders.$date_column AS order_date,
            profiles.first_name,
            profiles.last_name
        FROM orders
        LEFT JOIN profiles ON orders.user_id = profiles.user_id
        $where_clause
        ORDER BY orders.$date_column DESC";

// Get total count for pagination with correct JOIN
$count_sql = "SELECT COUNT(*) AS total FROM orders LEFT JOIN profiles ON orders.user_id = profiles.user_id $where_clause";
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
}

// Status classes configuration
$status_classes = [
    'Confirmed' => 'bg-[#1A7E79]',
    'Processing' => 'bg-[#E8B006]',
    'Shipped' => 'bg-[#1A237E]',
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
        CONCAT(profiles.first_name, ' ', profiles.last_name) AS customer_name,
        orders.order_total AS amount,
        orders.order_status,
        orders.delivery_method,
        orders.$date_column AS order_date
    FROM orders
    LEFT JOIN profiles ON orders.user_id = profiles.user_id
    $where_clause
    ORDER BY orders.$date_column DESC";
    
    $export_result = $conn->query($export_sql);
    
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
    
    while ($order = $export_result->fetch_assoc()) {
        echo "<tr>
                <td>#".htmlspecialchars($order['order_id'])."</td>
                <td>".htmlspecialchars($order['customer_name'])."</td>
                <td>₦".number_format($order['amount'])."</td>
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
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Orders</title>

    <style>
        .adminordersMenu{
            position: absolute;
            left: -10rem;
         min-width: 10rem;
         min-height: 10rem;
           height: 100%;
           z-index: 2;
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
            <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Orders</h1>

            <div id="openOrdersModal" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-Onest font-regular">Orders Overview</h1>
                <img src="../assets/dash/Vector 6905.svg" />
            </div>
        </div>



     
        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <div id="myBtn" class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                <div class="flex items-center gap-0">
                    <form action="" method="GET" class="w-full flex">
                        <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                            <img src="../assets/dash/search-normal (1).svg" alt="Search" class="w-[18px]" />
                            <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                            <!-- Preserve date filter when searching -->
                            <?php if($date_filter != 'all'): ?>
                                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                                <?php if($date_filter == 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])): ?>
                                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date']); ?>">
                                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date']); ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filer by:</span>
                        <img src="../assets/dash/filter-horizontal.svg" class="md:hidden" />

                        <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                           <!-- Replace custom dropdown with standard select -->
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
                               
                             
                           </div>
                        </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                    <img src="../assets/dash/Path.svg" />
                        <a href="?date=all">
                            <span class="text-[#262626] text-[14px] font-Onest font-regular">Clear filter</span>
                        </a>
                    </div>

                    <button onclick="exportToExcel()" class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer shrink-0">
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
                            <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Order ID</span>
                        </th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Customer Name</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Mode of Order</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                            <img src="../assets/dash/column.svg" class="min-w-[24px] min-h-[24px]" />
                        </th>
                    </thead>

                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">#<?php echo htmlspecialchars($order['order_id']); ?></span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                    <?php 
                                    $firstName = $order['first_name'] ?? '';
                                    $lastName = $order['last_name'] ?? '';
                                    echo htmlspecialchars(trim("$firstName $lastName")); 
                                    ?>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦<?php echo number_format($order['amount'] ?? 0); ?></td>
                                    <td>
                                        <?php
                                        $status = $order['order_status'] ?? 'Processing';
                                        $status_class = $status_classes[$status] ?? 'bg-[#E8B006]';
                                        ?>
                                        <button type="submit" class="py-1 px-4 <?php echo $status_class; ?> text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]"><?php echo htmlspecialchars($status); ?></button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($order['delivery_method'] ?? 'Express Delivery'); ?></td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($order['formatted_date'] . ' ' . $order['formatted_time']); ?></td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openAdminOrderMenu(this)" />

                                        <!-- The menu for each order  starts ---->
                                        <div class="adminordersMenu h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
    <div class="flex flex-col gap-3">
        <a href="./order-details.php?id=<?php echo $order['order_id']; ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
        <a href="./order-details.php?id=<?php echo $order['order_id']; ?>&tab=update-status" class="text-[16px] font-medium text-[#262626]">Update Order Status</a>

        <a href="./order-details.php?id=<?php echo $order['order_id']; ?>&tab=update-status" class="text-[16px] font-medium text-[#D93939]">Cancel Order</a>
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

            <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">Showing <?php echo count($orders); ?> results from <?php echo $total_count; ?></span>
            <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                <div class="flex items-center gap-2 cursor-pointer">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo ($date_filter == 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])) ? '&start_date=' . urlencode($_GET['start_date']) . '&end_date=' . urlencode($_GET['end_date']) : ''; ?>">
                            <img src="../assets/products/prev.svg" class="w-[6px] h-[11px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                        </a>
                    <?php else: ?>
                        <img src="../assets/products/prev.svg" class="w-[6px] h-[11px] opacity-50" />
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular opacity-50">Prev</span>
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
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo ($date_filter == 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])) ? '&start_date=' . urlencode($_GET['start_date']) . '&end_date=' . urlencode($_GET['end_date']) : ''; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo ($date_filter == 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])) ? '&start_date=' . urlencode($_GET['start_date']) . '&end_date=' . urlencode($_GET['end_date']) : ''; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer"><?php echo $total_pages; ?></a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-2 cursor-pointer shrink-0">
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?><?php echo ($date_filter == 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])) ? '&start_date=' . urlencode($_GET['start_date']) . '&end_date=' . urlencode($_GET['end_date']) : ''; ?>" class="shrink-0 text-nowrap">
                            <img src="../assets/products/next.svg" class="w-[6px] h-[11px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                        </a>
                    <?php else: ?>
                        
                        <img src="../assets/products/next.svg" class="w-[6px] h-[11px] opacity-50 " />
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular opacity-50">Next</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
     
    </div>
    </div>



    <!-- The modals starts -->
    <div id="ordersModal" class="modal reg">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
        <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Order Overview</h1>
        <img src="../assets/global/close-circle.svg" alt="close" id="closeOrdersModal" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />

        <div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-4 mt-2">
            <!-- Total Orders Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Orders</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($order_stats['total_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/<?php echo $total_change >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
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
                        <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($order_stats['completed_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
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
                        <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($order_stats['pending_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/decrease.svg" class="w-[20px] h-[20px]" />
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
                        <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format($order_stats['returned_orders']); ?></h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
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
    // Find the closest parent td and then find the menu inside it
  const menuContainer = element.closest('td').querySelector('.adminordersMenu');
    
    // Toggle the display of the menu
    if (menuContainer.style.display === "block") {
        menuContainer.style.display = "none";
    } else {
        // First, close all other open menus
        document.querySelectorAll('.adminordersMenu').forEach(menu => {
            menu.style.display = "none";
        });
        
        // Then open the clicked menu
        menuContainer.style.display = "block";
    }
}

// Add this to hide menus when clicking outside
document.addEventListener('click', function(event) {
    // Check if the click was outside any menu or menu trigger
    if (!event.target.closest('.adminordersMenu') && !event.target.matches('img[onclick="openAdminOrderMenu(this)"]')) {
        document.querySelectorAll('.adminordersMenu').forEach(menu => {
            menu.style.display = "none";
        });
    }
});

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