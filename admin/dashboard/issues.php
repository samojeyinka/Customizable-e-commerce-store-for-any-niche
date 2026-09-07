<?php
// Include authentication utility for admin
require_once __DIR__ . '/../../includes/auth/auth.php';
require_once __DIR__ . "/../../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: ../index.php");
    exit();
}



// Database connection
$conn = db();
if (!$conn) {
    die(mysqli_error($conn));
}

// Process issue status updates
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'])) {
    $issue_id = intval($_POST['issue_id']);
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $admin_notes = isset($_POST['admin_notes']) ? $conn->real_escape_string($_POST['admin_notes']) : '';
    $resolution = isset($_POST['resolution']) ? $conn->real_escape_string($_POST['resolution']) : '';
    
    if ($action === 'update_status') {
        $status = $_POST['status'];
        
        $sql = "UPDATE order_issues SET status = ?, admin_notes = ?, resolution = ? WHERE issue_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $status, $admin_notes, $resolution, $issue_id);
        
        if ($stmt->execute()) {
            $message = "Issue status updated successfully.";
        } else {
            $message = "Error updating issue status: " . $conn->error;
        }
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

// Prepare filter conditions
$conditions = [];
$params = [];
$param_types = '';

if ($status_filter !== 'all') {
    $conditions[] = "i.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if (!empty($search_query)) {
    $search_term = "%$search_query%";
    $conditions[] = "(i.issue_type LIKE ? OR i.issue_description LIKE ? OR o.id LIKE ? OR u.email LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= 'ssss';
}

// Build the WHERE clause
$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Set up sorting
$sort_clause = match($sort_by) {
    'status' => "ORDER BY i.status ASC, i.created_at DESC",
    'type' => "ORDER BY i.issue_type ASC, i.created_at DESC",
    'date_asc' => "ORDER BY i.created_at ASC",
    default => "ORDER BY i.created_at DESC" // date_desc is default
};

// Get paginated issues
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM order_issues i 
              JOIN orders o ON i.order_id = o.id
              JOIN users u ON i.user_id = u.id
              $where_clause";

if (!empty($params)) {
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $count_result = $stmt->get_result();
    $total_items = $count_result->fetch_assoc()['total'];
} else {
    $count_result = $conn->query($count_sql);
    $total_items = $count_result->fetch_assoc()['total'];
}

$total_pages = ceil($total_items / $items_per_page);

// Get issues
$issues_sql = "SELECT i.issue_id, i.order_id, i.issue_type, i.issue_description, i.status, 
                      i.admin_notes, i.resolution, i.created_at,
                      o.order_status, o.order_total,
                      CONCAT(p.first_name, ' ', p.last_name) as customer_name,
                      u.email as customer_email
               FROM order_issues i
               JOIN orders o ON i.order_id = o.id
               JOIN users u ON i.user_id = u.id
               JOIN profiles p ON u.id = p.user_id
               $where_clause
               $sort_clause
               LIMIT ? OFFSET ?";

// Add limit parameters
$params[] = $items_per_page;
$params[] = $offset;
$param_types .= 'ii';

// Execute query
$stmt = $conn->prepare($issues_sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$issues_result = $stmt->get_result();
$issues = $issues_result->fetch_all(MYSQLI_ASSOC);

// Get issue counts by status - MOVED THIS CODE BEFORE ENDING THE CONNECTION
$count_pending_sql = "SELECT COUNT(*) as count FROM order_issues WHERE status = 'pending'";
$count_inprogress_sql = "SELECT COUNT(*) as count FROM order_issues WHERE status = 'in_progress'";
$count_resolved_sql = "SELECT COUNT(*) as count FROM order_issues WHERE status = 'resolved'";
$count_closed_sql = "SELECT COUNT(*) as count FROM order_issues WHERE status = 'closed'";

$pending_count = $conn->query($count_pending_sql)->fetch_assoc()['count'];
$inprogress_count = $conn->query($count_inprogress_sql)->fetch_assoc()['count'];
$resolved_count = $conn->query($count_resolved_sql)->fetch_assoc()['count'];
$closed_count = $conn->query($count_closed_sql)->fetch_assoc()['count'];

// Function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        case 'resolved':
            return 'bg-green-100 text-green-800';
        case 'closed':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Function to get order status badge class
function getOrderStatusBadgeClass($status) {
    switch ($status) {
        case 'Processing':
            return 'bg-[#E8B006] text-white';
        case 'Shipped':
            return 'bg-[#C2185B] text-white';
        case 'Delivered':
            return 'bg-[#39D959] text-white';
        case 'Cancelled':
            return 'bg-red-500 text-white';
        case 'Returned':
            return 'bg-[#9C27B0] text-white';
        default:
            return 'bg-gray-500 text-white';
    }
}

// Format date for display
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M d, Y h:i A');
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
<title>Manage Customer Issues - Admin Dashboard</title>

<?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="relative">
<?php
include "./header.php";
include "./sidebar.php"
?>

<div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
    <div class="w-full rounded-[16px] bg-white mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-[20px] font-Onest font-semibold">Manage Customer Issues</h1>
        </div>

        <?php if (!empty($message)): ?>
        <div class="mb-4 p-3 rounded-md <?php echo strpos($message, 'Error') === false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="mb-6">
            <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by order ID, issue type, or email" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                
                <div class="w-full md:w-[200px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                
                <div class="w-full md:w-[200px]">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select id="sort" name="sort" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="date_desc" <?php echo $sort_by === 'date_desc' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="date_asc" <?php echo $sort_by === 'date_asc' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="status" <?php echo $sort_by === 'status' ? 'selected' : ''; ?>>By Status</option>
                        <option value="type" <?php echo $sort_by === 'type' ? 'selected' : ''; ?>>By Issue Type</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-[#C2185B] text-white rounded-md">Filter</button>
                </div>
            </form>
        </div>

        
        <!-- Issues Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="group flex items-start gap-4 px-5 py-4 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[10px] transition-colors hover:border-[#E2E2E2] hover:bg-white cursor-pointer" onclick="window.location='?status=pending'">
                <div class="w-[42px] h-[42px] rounded-[10px] bg-[#FFF4E6] flex items-center justify-center shrink-0">
                    <i class="fa-regular fa-clock text-[#C77E23] text-[18px]"></i>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[#8A8A8A] text-[12px] font-medium font-['Open Sans'] tracking-wide">Pending</span>
                    <div class="flex items-end gap-1.5">
                        <h2 class="text-[#262626] text-[24px] leading-none font-semibold font-['Open Sans']"><?php echo $pending_count; ?></h2>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D9903A] mb-1.5"></span>
                    </div>
                    <a href="?status=pending" class="text-[#262626] text-[12px] font-['Open Sans'] opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                </div>
            </div>

            <div class="group flex items-start gap-4 px-5 py-4 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[10px] transition-colors hover:border-[#E2E2E2] hover:bg-white cursor-pointer" onclick="window.location='?status=in_progress'">
                <div class="w-[42px] h-[42px] rounded-[10px] bg-[#EAF1FE] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-spinner text-[#3B6FD6] text-[18px]"></i>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[#8A8A8A] text-[12px] font-medium font-['Open Sans'] tracking-wide">In Progress</span>
                    <div class="flex items-end gap-1.5">
                        <h2 class="text-[#262626] text-[24px] leading-none font-semibold font-['Open Sans']"><?php echo $inprogress_count; ?></h2>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#3B6FD6] mb-1.5"></span>
                    </div>
                    <a href="?status=in_progress" class="text-[#262626] text-[12px] font-['Open Sans'] opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                </div>
            </div>

            <div class="group flex items-start gap-4 px-5 py-4 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[10px] transition-colors hover:border-[#E2E2E2] hover:bg-white cursor-pointer" onclick="window.location='?status=resolved'">
                <div class="w-[42px] h-[42px] rounded-[10px] bg-[#E7F7EE] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check text-[#2FA05A] text-[18px]"></i>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[#8A8A8A] text-[12px] font-medium font-['Open Sans'] tracking-wide">Resolved</span>
                    <div class="flex items-end gap-1.5">
                        <h2 class="text-[#262626] text-[24px] leading-none font-semibold font-['Open Sans']"><?php echo $resolved_count; ?></h2>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#2FA05A] mb-1.5"></span>
                    </div>
                    <a href="?status=resolved" class="text-[#262626] text-[12px] font-['Open Sans'] opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                </div>
            </div>

            <div class="group flex items-start gap-4 px-5 py-4 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[10px] transition-colors hover:border-[#E2E2E2] hover:bg-white cursor-pointer" onclick="window.location='?status=closed'">
                <div class="w-[42px] h-[42px] rounded-[10px] bg-[#F1F0F3] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-lock text-[#6B6B78] text-[18px]"></i>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[#8A8A8A] text-[12px] font-medium font-['Open Sans'] tracking-wide">Closed</span>
                    <div class="flex items-end gap-1.5">
                        <h2 class="text-[#262626] text-[24px] leading-none font-semibold font-['Open Sans']"><?php echo $closed_count; ?></h2>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#6B6B78] mb-1.5"></span>
                    </div>
                    <a href="?status=closed" class="text-[#262626] text-[12px] font-['Open Sans'] opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                </div>
            </div>
        </div>

        <!-- Issues List -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue ID</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Type</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($issues)): ?>
                    <tr>
                        <td colspan="8" class="py-4 px-4 text-center text-gray-500">No issues found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($issues as $issue): 
                        $status_class = getStatusBadgeClass($issue['status']);
                        $order_status_class = getOrderStatusBadgeClass($issue['order_status']);
                    ?>
                    <tr>
                        <td class="py-3 px-4 text-sm"><?php echo $issue['issue_id']; ?></td>
                        <td class="py-3 px-4">
                            <div>
                                <p class="text-sm font-medium">#<?php echo $issue['order_id']; ?></p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo $order_status_class; ?>">
                                    <?php echo $issue['order_status']; ?>
                                </span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-sm font-medium"><?php echo htmlspecialchars($issue['customer_name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($issue['customer_email']); ?></p>
                        </td>
                        <td class="py-3 px-4 text-sm"><?php echo htmlspecialchars($issue['issue_type']); ?></td>
                        <td class="py-3 px-4">
                            <p class="text-sm issue-excerpt"><?php echo htmlspecialchars($issue['issue_description']); ?></p>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo $status_class; ?>">
                                <?php echo ucfirst($issue['status']); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm"><?php echo formatDate($issue['created_at']); ?></td>
                        <td class="py-3 px-4">
                            <button 
                                onclick="openIssueModal(<?php echo $issue['issue_id']; ?>, '<?php echo htmlspecialchars(addslashes($issue['issue_type'])); ?>', '<?php echo htmlspecialchars(addslashes($issue['issue_description'])); ?>', '<?php echo $issue['status']; ?>', '<?php echo htmlspecialchars(addslashes($issue['admin_notes'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($issue['resolution'] ?? '')); ?>', <?php echo (int)$issue['order_id']; ?>, '<?php echo htmlspecialchars(addslashes($issue['customer_name'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($issue['customer_email'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($issue['order_status'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes(formatDate($issue['created_at']))); ?>')" 
                                class="text-blue-600 hover:text-blue-800"
                            >
                                View/Update
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center">
            <div class="pagination flex space-x-1">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_query); ?>&sort=<?php echo $sort_by; ?>" class="px-3 py-1 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Previous</a>
                <?php endif; ?>
                
                <?php
                // Show up to 5 page numbers
                $start_page = max(1, $page - 2);
                $end_page = min($start_page + 4, $total_pages);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_query); ?>&sort=<?php echo $sort_by; ?>" class="px-3 py-1 rounded-md <?php echo ($i == $page) ? 'active bg-[#C2185B] text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page + 1); ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_query); ?>&sort=<?php echo $sort_by; ?>" class="px-3 py-1 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Issue Detail Modal -->
<div id="issueModal" class="modal" style="display: none;">
    <div class="modal-content max-w-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-[36px] h-[36px] rounded-[10px] bg-[#FCE7F0] flex items-center justify-center">
                    <i class="fa-solid fa-circle-exclamation text-[#C2185B] text-[16px]"></i>
                </div>
                <div>
                    <h2 class="text-[16px] font-Onest font-semibold text-[#111827]">Issue Details</h2>
                    <p class="text-[12px] text-gray-400 font-['Open Sans']" id="modalSubtitle">ID #0000</p>
                </div>
            </div>
            <span class="close text-gray-400 text-[24px] leading-none cursor-pointer hover:text-gray-700" onclick="closeIssueModal()">&times;</span>
        </div>

        <form id="updateIssueForm" method="POST" action="" class="px-6 py-5 flex flex-col gap-5">
            <input type="hidden" id="issue_id" name="issue_id" value="">
            <input type="hidden" name="action" value="update_status">

            <!-- Status banner -->
            <div id="modalStatusBanner" class="flex items-center gap-2.5 rounded-[10px] px-4 py-3 bg-gray-50 border border-gray-100">
                <span id="modalStatusDot" class="w-2 h-2 rounded-full bg-gray-400"></span>
                <span class="text-[13px] font-medium text-gray-700">Current status:</span>
                <span id="modalStatusLabel" class="text-[13px] font-semibold text-gray-900 rounded-full px-2.5 py-0.5 bg-white border border-gray-200">Pending</span>
            </div>

            <!-- Issue context -->
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1 bg-[#FBFBFB] rounded-[10px] border border-[#EEEEEE] px-3.5 py-3">
                    <span class="text-[11px] text-gray-400 font-medium uppercase tracking-wide">Order ID</span>
                    <span id="modalOrderId" class="text-[14px] font-semibold text-[#262626]">—</span>
                </div>
                <div class="flex flex-col gap-1 bg-[#FBFBFB] rounded-[10px] border border-[#EEEEEE] px-3.5 py-3">
                    <span class="text-[11px] text-gray-400 font-medium uppercase tracking-wide">Order Status</span>
                    <span id="modalOrderStatus" class="text-[14px] font-medium text-[#262626]">—</span>
                </div>
                <div class="flex flex-col gap-1 bg-[#FBFBFB] rounded-[10px] border border-[#EEEEEE] px-3.5 py-3">
                    <span class="text-[11px] text-gray-400 font-medium uppercase tracking-wide">Customer</span>
                    <span id="modalCustomer" class="text-[14px] font-medium text-[#262626]">—</span>
                </div>
                <div class="flex flex-col gap-1 bg-[#FBFBFB] rounded-[10px] border border-[#EEEEEE] px-3.5 py-3">
                    <span class="text-[11px] text-gray-400 font-medium uppercase tracking-wide">Reported On</span>
                    <span id="modalIssueDate" class="text-[14px] font-medium text-[#262626]">—</span>
                </div>
            </div>

            <!-- Issue details -->
            <div class="flex flex-col gap-2">
                <span class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Issue</span>
                <div class="flex flex-col gap-3 rounded-[10px] border border-gray-100 px-4 py-3.5 bg-white">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-tag text-[13px] text-gray-400"></i>
                        <span id="issueType" class="inline-flex items-center text-[13px] font-medium text-[#C2185B] bg-[#FCE7F0] rounded-full px-3 py-1"></span>
                    </div>
                    <p id="issueDescription" class="text-[13px] leading-relaxed text-gray-600 whitespace-pre-line"></p>
                </div>
            </div>

            <!-- Update section -->
            <div class="flex flex-col gap-3 border-t border-gray-100 pt-4">
                <span class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Update Issue</span>

                <div class="flex flex-col gap-1.5">
                    <label for="status" class="text-[13px] font-medium text-gray-600">Status</label>
                    <select id="modal_status" name="status" class="admin-select">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="admin_notes" class="text-[13px] font-medium text-gray-600">Admin Notes</label>
                    <textarea id="admin_notes" name="admin_notes" rows="2" class="admin-input"></textarea>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="resolution" class="text-[13px] font-medium text-gray-600">Resolution</label>
                    <textarea id="resolution" name="resolution" rows="2" class="admin-input" placeholder="Enter details about how the issue was resolved"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeIssueModal()" class="admin-btn-outline"><i class="fa-solid fa-xmark"></i> Cancel</button>
                <button type="submit" class="admin-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update Issue</button>
            </div>
        </form>
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
    // Issue Modal Functions
    function openIssueModal(issueId, issueType, issueDescription, status, adminNotes, resolution, orderId, customer, orderStatus, issueDate) {
        document.getElementById('issue_id').value = issueId;
        document.getElementById('issueType').textContent = issueType;
        document.getElementById('issueDescription').textContent = issueDescription;
        document.getElementById('modal_status').value = status;
        document.getElementById('admin_notes').value = adminNotes || '';
        document.getElementById('resolution').value = resolution || '';

        document.getElementById('modalSubtitle').textContent = '#' + (issueId || 0).toString().padStart(4, '0');
        document.getElementById('modalOrderId').textContent = '#' + (orderId || '—');
        document.getElementById('modalCustomer').textContent = customer || '—';
        document.getElementById('modalOrderStatus').textContent = orderStatus ? orderStatus.replace(/_/g, ' ') : '—';
        document.getElementById('modalIssueDate').textContent = issueDate || '—';

        const statusMap = {
            pending:     { label: 'Pending',     color: '#C77E23', bg: '#FFF6EB' },
            in_progress: { label: 'In Progress', color: '#3B6FD6', bg: '#EDF3FF' },
            resolved:    { label: 'Resolved',    color: '#2FA05A', bg: '#E6F7EE' },
            closed:      { label: 'Closed',      color: '#6B6B78', bg: '#F1F0F3' }
        };
        const meta = statusMap[status] || statusMap.pending;
        const banner = document.getElementById('modalStatusBanner');
        const dot = document.getElementById('modalStatusDot');
        const label = document.getElementById('modalStatusLabel');
        banner.style.backgroundColor = meta.bg;
        banner.style.borderColor = meta.bg;
        dot.style.backgroundColor = meta.color;
        label.textContent = meta.label;
        label.style.color = meta.color;
        label.style.borderColor = meta.color;
        label.style.backgroundColor = '#ffffff';

        document.getElementById('issueModal').style.display = 'block';
    }
    
    function closeIssueModal() {
        document.getElementById('issueModal').style.display = 'none';
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('issueModal');
        if (event.target == modal) {
            closeIssueModal();
        }
    }
</script>


</body>
</html> 