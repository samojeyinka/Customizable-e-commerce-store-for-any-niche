<?php
// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die(mysqli_error($conn));
}

// Process status updates
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $return_id = $_POST['return_id'];
    $status = $_POST['status'];
    $admin_message = $_POST['admin_message'] ?? '';
    
    // Update the return request status
    $sql = "UPDATE return_requests 
            SET status = ?, admin_message = ?, updated_at = NOW() 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $admin_message, $return_id);
    
    if ($stmt->execute()) {
        $message = "Return request #$return_id updated to $status successfully!";
        $message_type = 'success';
    } else {
        $message = "Error updating return request: " . $stmt->error;
        $message_type = 'error';
    }
}

// Get filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with filters - using direct contact information from return_requests table
$query = "SELECT r.id, r.order_id, r.user_id, r.product_id, r.return_reason, 
                 r.return_details, r.return_quantity, r.status, r.admin_message, r.created_at,
                 r.full_name, r.phone_number, r.contact_email, 
                 p.product_name, pv.size, p.colors, oi.price,
                 (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
          FROM return_requests r
          JOIN products p ON r.product_id = p.product_id
          JOIN product_variants pv ON r.variant_id = pv.variant_id
          JOIN order_items oi ON r.order_item_id = oi.id
          WHERE 1=1";

// Add filters if they exist
if (!empty($status_filter)) {
    $query .= " AND r.status = '$status_filter'";
}

if (!empty($date_filter)) {
    $query .= " AND DATE(r.created_at) = '$date_filter'";
}

$query .= " ORDER BY r.created_at DESC";

$result = $conn->query($query);
$return_requests = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $return_requests[] = $row;
    }
}

// Status colors
$status_colors = [
    'Processing' => 'bg-[#E8B006]',
    'Received' => 'bg-[#1A237E]',
    'Accepted' => 'bg-[#39D959]',
    'Rejected' => 'bg-red-500',
    'Completed' => 'bg-[#39D959]'
];

// Format date for display
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH Admin | Returns Management</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    
</head>

<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- Sidebar - Include your admin sidebar here -->

        
        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Header - Include your admin header here -->
  
            
            <!-- Main Content -->
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Return Requests Management</h1>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <!-- Status Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <?php
                    // Get counts for each status
                    $counts = [
                        'Processing' => 0,
                        'Received' => 0,
                        'Accepted' => 0,
                        'Rejected' => 0,
                        'Completed' => 0
                    ];
                    
                    $countQuery = "SELECT status, COUNT(*) as count FROM return_requests GROUP BY status";
                    $countResult = $conn->query($countQuery);
                    if ($countResult) {
                        while ($row = $countResult->fetch_assoc()) {
                            if (isset($counts[$row['status']])) {
                                $counts[$row['status']] = $row['count'];
                            }
                        }
                    }
                    
                    // Display cards
                    foreach ($counts as $status => $count):
                        $color = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-500';
                        $isActive = $status_filter === $status;
                    ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?status=' . $status; ?>" 
                        class="bg-white p-4 rounded-lg shadow <?php echo $isActive ? 'ring-2 ring-blue-500' : ''; ?>">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase"><?php echo $status; ?></p>
                                <p class="text-2xl font-bold"><?php echo $count; ?></p>
                            </div>
                            <div class="w-3 h-3 rounded-full <?php echo $color; ?>"></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form action="" method="GET" class="flex flex-wrap items-center gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="border border-gray-300 rounded-md p-2 w-40">
                                <option value="">All Statuses</option>
                                <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Received" <?php echo $status_filter === 'Received' ? 'selected' : ''; ?>>Received</option>
                                <option value="Accepted" <?php echo $status_filter === 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                <option value="Rejected" <?php echo $status_filter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="date" name="date" value="<?php echo $date_filter; ?>" class="border border-gray-300 rounded-md p-2">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Filter</button>
                            <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="ml-2 text-blue-600 hover:text-blue-800">Clear Filters</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Return Requests Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <?php if (empty($return_requests)): ?>
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No return requests found.</p>
                        <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                        <p class="mt-2">Try clearing your filters.</p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Request ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Return Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($return_requests as $request): 
                                $image_path = isset($request['image_path']) ? "../../assets/products/" . $request['image_path'] : "../assets/admin/img/product-placeholder.jpg";
                                $status_color = isset($status_colors[$request['status']]) ? $status_colors[$request['status']] : 'bg-[#E8B006]';
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?php echo $request['id']; ?></div>
                                    <div class="text-sm text-gray-500">Order #<?php echo $request['order_id']; ?></div>
                                    <div class="text-xs text-gray-500"><?php echo formatDate($request['created_at']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $request['full_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $request['contact_email']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $request['phone_number']; ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-md object-cover" src="<?php echo $image_path; ?>" alt="<?php echo $request['product_name']; ?>">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $request['product_name']; ?></div>
                                            <div class="text-sm text-gray-500">Size: <?php echo $request['size']; ?> • Color: <?php echo $request['colors']; ?></div>
                                            <div class="text-sm text-gray-500">Qty: <?php echo $request['return_quantity']; ?> • ₦<?php echo number_format($request['price']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><span class="font-medium">Reason:</span> <?php echo $request['return_reason']; ?></div>
                                    <div class="text-sm text-gray-500 max-w-xs truncate"><?php echo $request['return_details']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_color; ?> text-white">
                                        <?php echo $request['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <!-- Main action - View details -->
                                    <a href="./view-return.php?id=<?php echo $request['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">View Details</a>
                                    
                                    <!-- Quick status update buttons -->
                                    <?php if ($request['status'] === 'Processing'): ?>
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Received">Mark Received</button>
                                    <?php elseif ($request['status'] === 'Received'): ?>
                                    <button type="button" class="text-green-600 hover:text-green-900 mr-3 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Accepted">Accept</button>
                                    <button type="button" class="text-red-600 hover:text-red-900 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Rejected">Reject</button>
                                    <?php elseif ($request['status'] === 'Accepted'): ?>
                                    <button type="button" class="text-green-600 hover:text-green-900 quick-update-btn" data-id="<?php echo $request['id']; ?>" data-status="Completed">Complete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle">Update Return Request Status</h3>
                
                <form id="statusForm" method="POST" action="">
                    <input type="hidden" id="returnId" name="return_id" value="">
                    <input type="hidden" id="statusValue" name="status" value="">
                    
                    <div class="mb-4">
                        <label for="adminMessage" class="block text-sm font-medium text-gray-700 mb-1">Message to Customer</label>
                        <textarea id="adminMessage" name="admin_message" rows="4" class="w-full border border-gray-300 rounded-md p-2" placeholder="Enter message for the customer..."></textarea>
                        <p class="text-xs text-gray-500 mt-1" id="message-note"></p>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status Update Modal
            const statusModal = document.getElementById('statusModal');
            const statusForm = document.getElementById('statusForm');
            const returnIdInput = document.getElementById('returnId');
            const statusValueInput = document.getElementById('statusValue');
            const modalTitle = document.getElementById('modalTitle');
            const adminMessage = document.getElementById('adminMessage');
            const messageNote = document.getElementById('message-note');
            const cancelBtn = document.getElementById('cancelBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            // Status descriptions for modal title
            const statusDescriptions = {
                'Received': 'Mark as received when the physical product has been returned to you',
                'Accepted': 'Accept the return when product condition is good and approved for refund',
                'Rejected': 'Reject the return if product condition is unacceptable or against policy',
                'Completed': 'Mark as completed when refund has been processed'
            };
            
            // Update Status Button Click Events
            document.querySelectorAll('.quick-update-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const returnId = this.getAttribute('data-id');
                    const status = this.getAttribute('data-status');
                    
                    // Update form values
                    returnIdInput.value = returnId;
                    statusValueInput.value = status;
                    
                    // Update modal title and appearance
                    modalTitle.textContent = `Update Status to "${status}"`;
                    
                    // Reset message field
                    adminMessage.value = '';
                    
                    // Set appropriate button color
                    submitBtn.className = 'px-4 py-2 text-white rounded-md';
                    if (status === 'Received') {
                        submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                        messageNote.textContent = 'Include any notes about the received product condition';
                    } else if (status === 'Accepted') {
                        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        messageNote.textContent = 'Let the customer know about refund timeline';
                    } else if (status === 'Rejected') {
                        submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                        messageNote.textContent = 'A message is REQUIRED to explain rejection reason';
                        adminMessage.required = true;
                    } else if (status === 'Completed') {
                        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        messageNote.textContent = 'Include refund confirmation details';
                    }
                    
                    // Show modal
                    statusModal.classList.remove('hidden');
                });
            });
            
            // Cancel button event
            cancelBtn.addEventListener('click', function() {
                statusModal.classList.add('hidden');
            });
            
            // Form submission validation
            statusForm.addEventListener('submit', function(event) {
                const status = statusValueInput.value;
                const message = adminMessage.value.trim();
                
                // For rejected returns, require a message
                if (status === 'Rejected' && message === '') {
                    event.preventDefault();
                    alert('Please provide a reason for rejecting the return request.');
                    adminMessage.focus();
                }
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === statusModal) {
                    statusModal.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>