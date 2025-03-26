<?php
// Start session and include necessary files
session_start();
require_once '../../config/connect.php';
require_once '../../config/config.php';

// Admin Authentication Check
if (!isset($_SESSION['admin_id'])) {
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

// Sanitize and validate search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_term = mysqli_real_escape_string($con, $search);
    $search_condition = " AND (
        transaction_id LIKE '%$search_term%' 
        OR payment_reference LIKE '%$search_term%'
    )";
}

// Prepare base queries
$base_query = "FROM transaction_history WHERE 1=1 $search_condition";

// Status filter
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['Successful', 'Refunded']) 
    ? mysqli_real_escape_string($con, $_GET['status']) 
    : '';
if ($status_filter) {
    $base_query .= " AND status = '$status_filter'";
}

// Date filtering
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
$start_date = '';
$end_date = '';

switch ($date_filter) {
    case 'today':
        $start_date = date('Y-m-d 00:00:00');
        $end_date = date('Y-m-d 23:59:59');
        $base_query .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
        break;
    case 'last7days':
        $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $end_date = date('Y-m-d 23:59:59');
        $base_query .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
        break;
    case 'last28days':
        $start_date = date('Y-m-d 00:00:00', strtotime('-28 days'));
        $end_date = date('Y-m-d 23:59:59');
        $base_query .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
        break;
    case 'custom':
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $start_date = mysqli_real_escape_string($con, $_GET['start_date'] . ' 00:00:00');
            $end_date = mysqli_real_escape_string($con, $_GET['end_date'] . ' 23:59:59');
            $base_query .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
        }
        break;
}

// Count total records
$count_query = "SELECT COUNT(*) as total $base_query";
$count_result = mysqli_query($con, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Fetch transactions
$query = "SELECT * $base_query ORDER BY transaction_date DESC LIMIT $offset, $limit";
$result = mysqli_query($con, $query);

// Transaction statistics
$stats_query = "SELECT 
    COUNT(*) as total_transactions,
    SUM(CASE WHEN status = 'Successful' THEN amount ELSE 0 END) as successful_amount,
    SUM(CASE WHEN status = 'Refunded' THEN amount ELSE 0 END) as refunded_amount,
    COUNT(CASE WHEN status = 'Successful' THEN 1 END) as successful_count,
    COUNT(CASE WHEN status = 'Refunded' THEN 1 END) as refunded_count
FROM transaction_history 
WHERE 1=1 $search_condition";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Status update handler
if (isset($_GET['update_status']) && isset($_GET['transaction_id'])) {
    $transaction_id = mysqli_real_escape_string($con, $_GET['transaction_id']);
    $new_status = mysqli_real_escape_string($con, $_GET['update_status']);
    
    if (in_array($new_status, ['Successful', 'Refunded'])) {
        $update_sql = "UPDATE transaction_history SET status = '$new_status' WHERE id = '$transaction_id'";
        mysqli_query($con, $update_sql);
        
        // Redirect to prevent form resubmission
        $redirect_params = $_GET;
        unset($redirect_params['update_status']);
        unset($redirect_params['transaction_id']);
        header("Location: transactions.php?" . http_build_query($redirect_params));
        exit();
    }
}

// Export functionality
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="transactions_export_'.date('Y-m-d').'.xls"');
    
    $export_query = "SELECT 
        transaction_id AS 'Transaction ID', 
        payment_reference AS 'Payment Reference', 
        amount AS 'Amount', 
        status AS 'Status', 
        transaction_date AS 'Date'
        FROM transaction_history 
        $base_query 
        ORDER BY transaction_date DESC";
    $export_result = mysqli_query($con, $export_query);
    
    echo "<table border='1'>";
    echo "<tr>";
    $first_row = mysqli_fetch_assoc($export_result);
    foreach (array_keys($first_row) as $column) {
        echo "<th>" . htmlspecialchars($column) . "</th>";
    }
    echo "</tr>";
    
    // Output first row
    echo "<tr>";
    foreach ($first_row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
    
    // Output remaining rows
    while ($row = mysqli_fetch_assoc($export_result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    exit;
}

// Status classes configuration
$status_classes = [
    'Successful' => 'bg-[#39D959]',
    'Refunded' => 'bg-[#1A237E]'
];
?>


<!-- The rest of the HTML remains largely the same as in the previous file, 
     with modifications to match the new requirements -->

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
    <title>Transactions</title>
</head>
<body class="relative">
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Transactions</h1>

            <div id="openTransactionsModal" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-Onest font-regular">Transactions Overview</h1>
                <img src="../assets/dash/Vector 6905.svg" />
            </div>
        </div>

        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <form method="GET" action="" id="filterForm">
                <div class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                    <div class="flex items-center gap-0">
                        <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                            <img src="../assets/dash/search-normal (1).svg" alt="Search" class="w-[18px]" />
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search Transaction ID" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                        </div>
                    </div>

                    <div class="w-full flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filter by:</span>
                            
                            <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                                <!-- Date Filter Dropdown -->
                                <div class="custom-dropdown">
                                    <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                            <?php 
                                            switch($date_filter) {
                                                case 'today': echo 'Today'; break;
                                                case 'last7days': echo 'Last 7 days'; break;
                                                case 'last28days': echo 'Last 28 days'; break;
                                                case 'custom': echo 'Custom'; break;
                                                default: echo 'All time';
                                            }
                                            ?>
                                        </span>
                                        <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                            <div onclick="selectDateFilter('all')">All time</div>
                                            <div onclick="selectDateFilter('today')">Today</div>
                                            <div onclick="selectDateFilter('last7days')">Last 7 days</div>
                                            <div onclick="selectDateFilter('last28days')">Last 28 days</div>
                                            <div onclick="showCustomDateModal()">Custom</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Filter Dropdown -->
                                <div class="custom-dropdown">
                                    <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                            <?php echo $status_filter ?: 'Status'; ?>
                                        </span>
                                        <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                            <div onclick="selectStatusFilter('Successful')">Successful</div>
                                            <div onclick="selectStatusFilter('Refunded')">Refunded</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="?date=all" class="flex items-center gap-1">
                                <img src="../assets/dash/Path.svg" />
                                <span class="text-[#262626] text-[14px] font-Onest font-regular cursor-pointer">Clear filter</span>
                            </a>

                            <button type="button" onclick="exportTransactions()" class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer ml-2">
                                <img src="../assets/dash/send-square.svg" />
                                Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for filters -->
                <input type="hidden" name="date" id="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>">
                <input type="hidden" name="status" id="status_filter" value="<?php echo htmlspecialchars($status_filter); ?>">
                <input type="hidden" name="start_date" id="start_date" value="<?php echo htmlspecialchars(substr($start_date, 0, 10)); ?>">
                <input type="hidden" name="end_date" id="end_date" value="<?php echo htmlspecialchars(substr($end_date, 0, 10)); ?>">
                <input type="hidden" name="page" id="page_input" value="<?php echo $page; ?>">
            </form>

            <div class="overflow-x-auto mt-3 min-h-[20rem]">
                <table class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <tr>
                            <th class="text-nowrap p-2">Transaction ID</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Payment Reference</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($transaction = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans'] p-2">
                                        <?php echo htmlspecialchars($transaction['transaction_id']); ?>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php echo htmlspecialchars($transaction['payment_reference']); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="py-1 px-4 <?php echo $status_classes[$transaction['status']]; ?> text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">
                                            <?php echo htmlspecialchars($transaction['status']); ?>
                                        </button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        ₦<?php echo number_format($transaction['amount'], 2); ?>
                                    </td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">
                                        <?php echo date('d/m/Y h:ia', strtotime($transaction['transaction_date'])); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-[#262626] text-[15px] font-['Open Sans']">
                                    No transactions found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
                            $active_class = ($i == $page) 
                                ? 'text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#1A237E]' 
                                : 'text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer';
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

                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px] mt-4">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/Frame 1171276632 (3).svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Revenue</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">
                                ₦<?php echo number_format($stats['successful_amount'], 2); ?>
                            </h2>
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
                            <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Successful Transactions</span>
                            <div class="flex items-center gap-2">
                                <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">
                                    <?php echo number_format($stats['successful_count']); ?>
                                </h2>
                            </div>
                            <div class="flex items-center gap-1">
                                <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                                <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                    <span class="text-[#39D959]">+12%</span> from last 28 days
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                        <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                            <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                        </div>

                        <div class="flex flex-col gap-[1px]">
                            <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Refunded Transactions</span>
                            <div class="flex items-center gap-2">
                                <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">
                                    <?php echo number_format($stats['refunded_count']); ?>
                                </h2>
                            </div>
                            <div class="flex items-center gap-1">
                                <img src="../assets/dash/decrease.svg" class="w-[20px] h-[20px]" />
                                <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                    <span class="text-[#D93939]">+12%</span> from last 28 days
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
        <script>
            // Date filter selection
            function selectDateFilter(filter) {
                document.getElementById('date_filter').value = filter;
                document.getElementById('filterForm').submit();
            }

            // Status filter selection
            function selectStatusFilter(status) {
                document.getElementById('status_filter').value = status;
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

            // Pagination
            function changePage(page) {
                document.getElementById('page_input').value = page;
                document.getElementById('filterForm').submit();
            }

            // Export transactions
            function exportTransactions() {
                // Add export parameter to the form
                const form = document.getElementById('filterForm');
                const exportInput = document.createElement('input');
                exportInput.type = 'hidden';
                exportInput.name = 'export';
                exportInput.value = 'excel';
                form.appendChild(exportInput);
                
                // Submit the form
                form.submit();
            }

            // Modal handling
            document.getElementById('openTransactionsModal').addEventListener('click', function() {
                document.getElementById('myModal').style.display = 'block';
            });

            document.getElementById('closeauth').addEventListener('click', function() {
                document.getElementById('myModal').style.display = 'none';
            });
        </script>
    </div>
</body>
</html>