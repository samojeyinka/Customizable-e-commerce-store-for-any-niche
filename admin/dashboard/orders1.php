<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
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
$params = [];
$search_query = '';

// Date filter handling
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
if (isset($_GET['date'])) {
    // Validate date filter
    $allowed_filters = ['today', 'last7days', 'last28days', 'custom', 'all'];
    if (in_array($date_filter, $allowed_filters)) {
        switch ($date_filter) {
            case 'today':
                $conditions[] = "DATE($date_column) = CURDATE()";
                break;
            case 'last7days':
                $conditions[] = "DATE($date_column) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last28days':
                $conditions[] = "DATE($date_column) >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
                break;
            case 'custom':
                if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                    $start_date = $conn->real_escape_string($_GET['start_date']);
                    $end_date = $conn->real_escape_string($_GET['end_date']);
                    $conditions[] = "DATE($date_column) BETWEEN '$start_date' AND '$end_date'";
                }
                break;
        }
    }
}

// Search functionality
if (!empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $search = $conn->real_escape_string($search_query);
    // Modify this to only search columns that actually exist in the table
    $conditions[] = "(id LIKE '%$search%')";
    // Removed customer_name from search condition
}

// Status filter
if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $conditions[] = "order_status = '$status'";
}

// Build WHERE clause
$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Base SQL query - Removed customer_name from SELECT list
$sql = "SELECT 
            id AS order_id,
            order_total AS amount,
            delivery_method,
            order_status,
            $date_column AS order_date
        FROM orders
        $where_clause
        ORDER BY $date_column DESC";

// Get total count for pagination
$count_result = $conn->query("SELECT COUNT(*) AS total FROM orders $where_clause");
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
$orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

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
</head>

<body class="relative">
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Orders</h1>

            <div id="myBtn" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
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
                        </div>
                    </form>
                </div>

                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filer by:</span>
                        <img src="../assets/dash/filter-horizontal.svg" class="md:hidden" />

                        <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                           
                            <div class="custom-dropdown">
                                <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Date</span>
                                    <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                                </div>
                                <div class="dropdown-content">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                            <a href="?date=all" class="<?php echo $date_filter == 'all' ? 'font-bold' : ''; ?>">All time</a>
                                            <a href="?date=today" class="<?php echo $date_filter == 'today' ? 'font-bold' : ''; ?>">Today</a>
                                            <a href="?date=last7days" class="<?php echo $date_filter == 'last7days' ? 'font-bold' : ''; ?>">Last 7 days</a>
                                            <a href="?date=last28days" class="<?php echo $date_filter == 'last28days' ? 'font-bold' : ''; ?>">Last 28 days</a>
                                            <div onclick="showCustomDatePicker()">Custom date</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="?clear=1">
                            <img src="../assets/dash/Path.svg" />
                            <span class="text-[#262626] text-[14px] font-Onest font-regular">Clear filter</span>
                        </a>
                    </div>

                    <button class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                        <img src="../assets/dash/send-square.svg" />
                        Export as
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto mt-3 min-h-[20rem]">
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
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Enyesiobi Golibe</td>
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
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                        <!-- Order Menu (specific to this row) -->
                                        <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                            <div class="flex flex-col gap-3">
                                                <a href="../products/show.php?reorder=<?php echo $order['order_id']; ?>" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                                <a href="./track-order.php?id=<?php echo $order['order_id']; ?>" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                                <a href="../products/review.php?order=<?php echo $order['order_id']; ?>" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                                <a href="./report-issue.php?id=<?php echo $order['order_id']; ?>" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">Showing <?php echo count($orders); ?> results from <?php echo $total_count; ?></span>
            <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                <div class="flex items-center gap-2 cursor-pointer">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?>">
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
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer"><?php echo $total_pages; ?></a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-2 cursor-pointer">
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $date_filter != 'all' ? '&date=' . urlencode($date_filter) : ''; ?>">
                            <img src="../assets/products/next.svg" class="w-[6px] h-[11px]" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                        </a>
                    <?php else: ?>
                        <img src="../assets/products/next.svg" class="w-[6px] h-[11px] opacity-50" />
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular opacity-50">Next</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Function to toggle order menu
    function openOrdermenu(element) {
        // Close all other menus first
        const allMenus = document.querySelectorAll('.ordermenu-content');
        allMenus.forEach(menu => {
            if (menu !== element.nextElementSibling) {
                menu.classList.remove('showom');
            }
        });
        
        // Toggle the clicked menu
        element.nextElementSibling.classList.toggle('showom');
    }

    // Close all menus when clicking elsewhere
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.ordermenu-content') && !event.target.matches('[onclick="openOrdermenu(this)"]')) {
            const allMenus = document.querySelectorAll('.ordermenu-content');
            allMenus.forEach(menu => {
                menu.classList.remove('showom');
            });
        }
    });

    // Function to show custom date picker
    function showCustomDatePicker() {
        const today = new Date();
        const formattedToday = today.toISOString().split('T')[0];
        
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
        const formattedOneMonthAgo = oneMonthAgo.toISOString().split('T')[0];
        
        const startDate = prompt('Enter start date (YYYY-MM-DD):', formattedOneMonthAgo);
        if (startDate === null) return;
        
        const endDate = prompt('Enter end date (YYYY-MM-DD):', formattedToday);
        if (endDate === null) return;
        
        // Preserve any existing search parameter
        const searchParam = new URLSearchParams(window.location.search).get('search');
        const searchQueryString = searchParam ? `&search=${searchParam}` : '';
        
        window.location.href = `?date=custom&start_date=${startDate}&end_date=${endDate}${searchQueryString}`;
    }

    // Function to select dropdown option
    function selectOption(element) {
        const dropdownToggle = element.closest('.custom-dropdown').querySelector('.dropdown-toggle span');
        dropdownToggle.textContent = element.textContent;
        
        // Close the dropdown
        element.closest('.dropdown-content').classList.remove('show');
    }

    // Implement dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const dropdownContent = this.nextElementSibling;
                dropdownContent.classList.toggle('show');
            });
        });
        
        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.dropdown-toggle') && !event.target.matches('.dropdown-toggle *')) {
                const dropdowns = document.querySelectorAll('.dropdown-content');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
    });
    </script>
</body>
</html>