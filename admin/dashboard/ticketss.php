<?php
// Start session and include necessary files
session_start();
require_once '../../config/connect.php';
require_once '../../config/config.php';

// Admin Authentication Check
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: ../index.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'] ?? '';
$admin_email = $_SESSION['admin_email'] ?? '';
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Set default values
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';
$start_date = '';
$end_date = '';

// Process date filter
if ($date_filter) {
    switch ($date_filter) {
        case 'today':
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
            break;
        case 'week':
            $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $end_date = date('Y-m-d 23:59:59');
            break;
        case 'month':
            $start_date = date('Y-m-d 00:00:00', strtotime('-28 days'));
            $end_date = date('Y-m-d 23:59:59');
            break;
        case 'custom':
            // Custom dates would come from additional parameters
            $start_date = isset($_GET['start_date']) ? $_GET['start_date'] . ' 00:00:00' : '';
            $end_date = isset($_GET['end_date']) ? $_GET['end_date'] . ' 23:59:59' : '';
            break;
    }
}

// Base SQL for transactions
$sql = "SELECT * FROM transaction_history WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM transaction_history WHERE 1=1";

// Add search condition if search term provided
if (!empty($search)) {
    $search_term = mysqli_real_escape_string($con, "%$search%");
    $sql .= " AND (customer_name LIKE '$search_term' 
               OR email LIKE '$search_term' 
               OR transaction_id LIKE '$search_term' 
               OR payment_reference LIKE '$search_term')";
    $count_sql .= " AND (customer_name LIKE '$search_term' 
                   OR email LIKE '$search_term' 
                   OR transaction_id LIKE '$search_term' 
                   OR payment_reference LIKE '$search_term')";
}

// Add date filter if provided
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
    $count_sql .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
}

// Count total results for pagination
$count_result = mysqli_query($con, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Add pagination
$sql .= " ORDER BY transaction_date DESC LIMIT $offset, $limit";

// Execute query
$result = mysqli_query($con, $sql);

// Check for errors
if (!$result) {
    $error = mysqli_error($con);
    // Handle error (could log or display to user)
}

// Fetch transaction statistics for overview modal
$stats = fetchTransactionStats($con, $start_date, $end_date);

// Admin authorization check function - can be used to check specific permissions
function checkAdminPermission($required_role = 'admin') {
    $admin_role = $_SESSION['admin_role'] ?? '';
    
    // If superadmin, allow access to everything
    if ($admin_role === 'superadmin') {
        return true;
    }
    
    // For regular admin, check if they have the required role
    if ($required_role === 'admin' && $admin_role === 'admin') {
        return true;
    }
    
    // Additional role checks can be added here
    
    return false;
}

/**
 * Function to fetch transaction statistics for the overview modal
 */
function fetchTransactionStats($con, $start_date = '', $end_date = '') {
    $stats = [];
    
    // Date condition for all queries
    $date_condition = "";
    if (!empty($start_date) && !empty($end_date)) {
        $date_condition = " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
    }
    
    // Total revenue (all completed transactions)
    $revenue_sql = "SELECT SUM(amount) as total FROM transaction_history 
                   WHERE status = 'completed'" . $date_condition;
    $revenue_result = mysqli_query($con, $revenue_sql);
    $stats['total_revenue'] = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;
    
    // Refunded amount
    $refund_sql = "SELECT SUM(amount) as total FROM transaction_history 
                  WHERE status = 'refunded'" . $date_condition;
    $refund_result = mysqli_query($con, $refund_sql);
    $stats['refunded'] = mysqli_fetch_assoc($refund_result)['total'] ?? 0;
    
    // Failed transactions
    $failed_sql = "SELECT SUM(amount) as total FROM transaction_history 
                  WHERE status = 'failed'" . $date_condition;
    $failed_result = mysqli_query($con, $failed_sql);
    $stats['failed'] = mysqli_fetch_assoc($failed_result)['total'] ?? 0;
    
    // Completed transactions
    $completed_sql = "SELECT COUNT(*) as count, SUM(amount) as total FROM transaction_history 
                     WHERE status = 'completed'" . $date_condition;
    $completed_result = mysqli_query($con, $completed_sql);
    $completed_data = mysqli_fetch_assoc($completed_result);
    $stats['completed_count'] = $completed_data['count'] ?? 0;
    $stats['completed_amount'] = $completed_data['total'] ?? 0;
    
    // Pending transactions
    $pending_sql = "SELECT COUNT(*) as count, SUM(amount) as total FROM transaction_history 
                   WHERE status = 'pending'" . $date_condition;
    $pending_result = mysqli_query($con, $pending_sql);
    $pending_data = mysqli_fetch_assoc($pending_result);
    $stats['pending_count'] = $pending_data['count'] ?? 0;
    $stats['pending_amount'] = $pending_data['total'] ?? 0;
    
    return $stats;
}

// Format money values
function formatAmount($amount) {
    return '₦' . number_format($amount, 0, '.', ',');
}

// Format date values
function formatDate($date) {
    return date('d/m/Y g:ia', strtotime($date));
}

// Get status class/color
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'completed':
            return 'bg-[#39D959]';
        case 'failed':
            return 'bg-[#D93939]';
        case 'pending':
            return 'bg-[#F5A623]';
        case 'refunded':
            return 'bg-[#1A237E]';
        default:
            return 'bg-[#CCCCCC]';
    }
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
    <title>Transactions</title>
</head>

<body class="relative">
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Transactions</h1>

            <div id="myBtn" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-Onest font-regular">Transactions Overview</h1>
                <img src="../assets/dash/Vector 6905.svg" />
            </div>
        </div>

        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <form method="GET" action="" id="filterForm">
                <div id="myBtn" class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                    <div class="flex items-center gap-0">
                        <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                            <img src="../assets/dash/search-normal (1).svg" alt="Search" class="w-[18px]" />
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                        </div>
                    </div>

                    <div class="w-full flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filter by:</span>
                            <img src="../assets/dash/filter-horizontal.svg" class="md:hidden" />

                            <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                                <div class="custom-dropdown">
                                    <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Date</span>
                                        <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                                <div onclick="selectDateFilter('today')">Today</div>
                                                <div onclick="selectDateFilter('week')">Last 7 days</div>
                                                <div onclick="selectDateFilter('month')">Last 28 days</div>
                                                <div onclick="showCustomDateModal()">Custom date</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1" onclick="clearFilters()">
                            <img src="../assets/dash/Path.svg" />
                            <span class="text-[#262626] text-[14px] font-Onest font-regular cursor-pointer">Clear filter</span>
                        </div>

                        <button type="button" class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer" onclick="exportTransactions()">
                            <img src="../assets/dash/send-square.svg" />
                            Export as
                        </button>
                    </div>
                </div>
                
                <!-- Hidden inputs for filters -->
                <input type="hidden" name="date_filter" id="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>">
                <input type="hidden" name="start_date" id="start_date" value="<?php echo htmlspecialchars(substr($start_date, 0, 10)); ?>">
                <input type="hidden" name="end_date" id="end_date" value="<?php echo htmlspecialchars(substr($end_date, 0, 10)); ?>">
                <input type="hidden" name="page" id="page_input" value="<?php echo $page; ?>">
                <input type="hidden" name="limit" value="<?php echo $limit; ?>">
            </form>

            <div class="overflow-x-auto mt-3 min-h-[20rem]">
                <table cols="" class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th class="text-nowrap p-2 flex items-center gap-2">
                            <input type="checkbox" id="selectAll" />
                            <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Customer Name</span>
                        </th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Email</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Transaction ID</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Payment Reference</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>
                    </thead>

                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($transaction = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="transaction-checkbox border-[#E1E1E1]" data-id="<?php echo $transaction['id']; ?>" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']"><?php echo htmlspecialchars($transaction['customer_name']); ?></span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($transaction['email']); ?></td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($transaction['payment_reference']); ?></td>
                                    <td>
                                        <button type="button" class="py-1 px-4 <?php echo getStatusClass($transaction['status']); ?> text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">
                                            <?php echo ucfirst(htmlspecialchars($transaction['status'])); ?>
                                        </button>
                                    </td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo formatDate($transaction['transaction_date']); ?></td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo formatAmount($transaction['amount']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-[#262626] text-[15px] font-['Open Sans']">
                                    No transactions found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">
                Showing <?php echo min($limit, $total_records); ?> results from <?php echo number_format($total_records); ?>
            </span>
            <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                <div class="flex items-center gap-2 cursor-pointer" onclick="changePage(<?php echo max(1, $page - 1); ?>)">
                    <img src="../assets/products/prev.svg" class="w-[6px] h-[11px]" />
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                </div>

                <div class="w-full flex items-center justify-between md:gap-6">
                    <?php
                    // Calculate pagination range
                    $start_page = max(1, min($page - 2, $total_pages - 4));
                    $end_page = min($total_pages, max($page + 2, 5));
                    
                    if ($start_page > 1) {
                        echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer" onclick="changePage(1)">1</span>';
                        if ($start_page > 2) {
                            echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                        }
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active_class = ($i == $page) ? 'text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#1A237E]' : 'text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer';
                        echo '<span class="' . $active_class . '" onclick="changePage(' . $i . ')">' . $i . '</span>';
                    }
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                        }
                        echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer" onclick="changePage(' . $total_pages . ')">' . $total_pages . '</span>';
                    }
                    ?>
                </div>

                <div class="flex items-center gap-2 cursor-pointer" onclick="changePage(<?php echo min($total_pages, $page + 1); ?>)">
                    <img src="../assets/products/next.svg" class="w-[6px] h-[11px]" />
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Overview Modal -->
    <div id="myModal" class="modal reg thisone">
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Transaction Overview</h1>
            <img src="../assets/global/close-circle.svg" alt="close" id="closeauth" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />

            <div class="flex items-center gap-3 my-4">
                <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Sort by:</span>

                <div class="flex items-center gap-2 md:gap-3 lg:gap-4">
                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">2025</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                    <div onclick="selectOption(this)">2025</div>
                                    <div onclick="selectOption(this)">2024</div>
                                    <div onclick="selectOption(this)">2023</div>
                                    <div onclick="selectOption(this)">2022</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Last 28 days</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                    <div onclick="selectOption(this)">Today</div>
                                    <div onclick="selectOption(this)">Last 7 days</div>
                                    <div onclick="selectOption(this)">Last 28 days</div>
                                    <div onclick="selectOption(this)">Custom date</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (3).svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Revenue</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo formatAmount($stats['total_revenue']); ?></h2>
                    </div>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-[#39D959]">+12%</span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-4 mt-2">
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/Frame 1171276632 (3).svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Refunded</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo formatAmount($stats['completed_amount']); ?></h2>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/decrease.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                <span class="text-[#D93939]">+12%</span> from last 28 days
                            </p>
                        </div>
                    </div>
                </div>

                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Pending Transactions</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo formatAmount($stats['pending_amount']); ?></h2>
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

    <!-- Custom Date Modal -->
    <div id="customDateModal" class="modal reg" style="display: none;">
        <div class="modal-content overflow-hidden p-4" style="max-width: 500px;">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Custom Date Range</h1>
            <img src="../assets/global/close-circle.svg" alt="close" id="closeCustomDate" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />

            <div class="my-6 flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-[14px] text-[#262626] font-Onest font-medium">Start Date</label>
                    <input type="date" id="custom_start_date" class="w-full p-2 border border-[#E1E1E1] rounded-md" value="<?php echo substr($start_date, 0, 10); ?>" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-[14px] text-[#262626] font-Onest font-medium">End Date</label>
                    <input type="date" id="custom_end_date" class="w-full p-2 border border-[#E1E1E1] rounded-md" value="<?php echo substr($end_date, 0, 10); ?>" />
                </div>
                <button type="button" onclick="applyCustomDates()" class="mt-2 py-2 px-4 bg-blue-900 text-white rounded-lg cursor-pointer">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>
    
    <script>
        // Handle date filter selection
        function selectDateFilter(filter) {
            document.getElementById('date_filter').value = filter;
            document.getElementById('filterForm').submit();
        }
        
        // Show custom date modal
        function showCustomDateModal() {
            const modal = document.getElementById('customDateModal');
            modal.style.display = 'block';
        }
        
        // Close custom date modal
        document.getElementById('closeCustomDate').addEventListener('click', function() {
            document.getElementById('customDateModal').style.display = 'none';
        });
        
        // Apply custom dates
        function applyCustomDates() {
            const startDate = document.getElementById('custom_start_date').value;
            const endDate = document.getElementById('custom_end_date').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }
            
            document.getElementById('date_filter').value = 'custom';
            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
            document.getElementById('filterForm').submit();
        }
        
        // Clear all filters
        function clearFilters() {
            document.querySelector('input[name="search"]').value = '';
            document.getElementById('date_filter').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('page_input').value = '1';
            document.getElementById('filterForm').submit();
        }
        
        // Change page for pagination
        function changePage(page) {
            document.getElementById('page_input').value = page;
            document.getElementById('filterForm').submit();
        }
        
        // Select all checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.transaction-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Export transactions
        function exportTransactions() {
            // Get selected transaction IDs
            const selectedIds = [];
            const checkboxes = document.querySelectorAll('.transaction-checkbox:checked');
            
            checkboxes.forEach(checkbox => {
                selectedIds.push(checkbox.getAttribute('data-id'));
            });
            
            // If none selected, export all filtered results
            if (selectedIds.length === 0) {
                window.location.href = 'export-transactions.php?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString();
            } else {
                // Export only selected transactions
                window.location.href = 'export-transactions.php?ids=' + selectedIds.join(',');
            }
        }
    </script>
</body>
