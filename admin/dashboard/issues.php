<?php
// Include authentication utility for admin
require_once __DIR__ . '/../../includes/auth/auth.php';
require_once __DIR__ . "/../../config/config.php";

// Check if admin

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
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
            return 'bg-[#1A237E] text-white';
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
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <title>Manage Customer Issues - Admin Dashboard</title>

    <style>
        .pagination a.active {
            background-color: #1A237E;
            color: white;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 700px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        
        .issue-excerpt {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
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
                    <button type="submit" class="px-4 py-2 bg-[#1A237E] text-white rounded-md">Filter</button>
                </div>
            </form>
        </div>

        
        <!-- Issues Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">            
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <h3 class="text-yellow-800 font-semibold">Pending</h3>
                <p class="text-2xl font-bold"><?php echo $pending_count; ?></p>
                <a href="?status=pending" class="text-blue-700 text-sm hover:underline">View all</a>
            </div>


            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="text-blue-800 font-semibold">In Progress</h3>
                <p class="text-2xl font-bold"><?php echo $inprogress_count; ?></p>
                <a href="?status=in_progress" class="text-blue-700 text-sm hover:underline">View all</a>
            </div>

         
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <h3 class="text-green-800 font-semibold">Resolved</h3>
                <p class="text-2xl font-bold"><?php echo $resolved_count; ?></p>
                <a href="?status=resolved" class="text-green-700 text-sm hover:underline">View all</a>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-gray-800 font-semibold">Closed</h3>
                <p class="text-2xl font-bold"><?php echo $closed_count; ?></p>
                <a href="?status=closed" class="text-gray-700 text-sm hover:underline">View all</a>
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
                                onclick="openIssueModal(<?php echo $issue['issue_id']; ?>, '<?php echo htmlspecialchars(addslashes($issue['issue_type'])); ?>', '<?php echo htmlspecialchars(addslashes($issue['issue_description'])); ?>', '<?php echo $issue['status']; ?>', '<?php echo htmlspecialchars(addslashes($issue['admin_notes'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($issue['resolution'] ?? '')); ?>')" 
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
                <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search_query); ?>&sort=<?php echo $sort_by; ?>" class="px-3 py-1 rounded-md <?php echo ($i == $page) ? 'active bg-[#1A237E] text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>"><?php echo $i; ?></a>
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
<div id="issueModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeIssueModal()">&times;</span>
        <h2 class="text-xl font-semibold mb-4">Issue Details</h2>
        
        <form id="updateIssueForm" method="POST" action="">
            <input type="hidden" id="issue_id" name="issue_id" value="">
            <input type="hidden" name="action" value="update_status">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Issue Type</label>
                <div id="issueType" class="p-2 bg-gray-50 rounded-md text-gray-800"></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Issue Description</label>
                <div id="issueDescription" class="p-2 bg-gray-50 rounded-md text-gray-800 whitespace-pre-line"></div>
            </div>
            
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="modal_status" name="status" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                <textarea id="admin_notes" name="admin_notes" rows="3" class="w-full p-2 border border-gray-300 rounded-md"></textarea>
            </div>
            
            <div class="mb-4">
                <label for="resolution" class="block text-sm font-medium text-gray-700 mb-1">Resolution</label>
                <textarea id="resolution" name="resolution" rows="3" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter details about how the issue was resolved"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="closeIssueModal()" class="mr-2 px-4 py-2 bg-gray-200 text-gray-800 rounded-md">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#1A237E] text-white rounded-md">Update Issue</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Issue Modal Functions
    function openIssueModal(issueId, issueType, issueDescription, status, adminNotes, resolution) {
        document.getElementById('issue_id').value = issueId;
        document.getElementById('issueType').textContent = issueType;
        document.getElementById('issueDescription').textContent = issueDescription;
        document.getElementById('modal_status').value = status;
        document.getElementById('admin_notes').value = adminNotes || '';
        document.getElementById('resolution').value = resolution || '';
        
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