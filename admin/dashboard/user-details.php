<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";


// Database connection
$conn = db();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to users list if no ID is provided
    header('Location: users.php');
    exit;
}

$user_id = (int)$_GET['id'];

// Get user details with profile information
$user_sql = "SELECT 
users.id,
    users.email,
    users.last_login,
    users.status,
    profiles.first_name,
    profiles.last_name,
    profiles.phone,
    profiles.address,
    profiles.country,
    profiles.state,
    profiles.city,
    profiles.zip_code,
    (SELECT COUNT(*) FROM orders WHERE user_id = users.id) AS total_orders,
    (SELECT SUM(order_total) FROM orders WHERE user_id = users.id) AS total_spent,
    (SELECT MAX(created_at) FROM orders WHERE user_id = users.id) AS last_order_date
FROM users
LEFT JOIN profiles ON users.id = profiles.user_id
WHERE users.id = ?";

$stmt = $conn->prepare($user_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User not found, redirect to users list
    header('Location: users.php');
    exit;
}

$user = $result->fetch_assoc();

// Format dates
$last_login = !empty($user['last_login']) ? new DateTime($user['last_login']) : null;
$last_order_date = !empty($user['last_order_date']) ? new DateTime($user['last_order_date']) : null;

// Get user's recent orders
$orders_sql = "SELECT 
    orders.id AS order_id,
    orders.order_total AS amount,
    orders.delivery_method,
    orders.order_status,
    orders.created_at AS order_date
FROM orders
WHERE orders.user_id = ?
ORDER BY orders.created_at DESC
LIMIT 5";

$stmt = $conn->prepare($orders_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$recent_orders = $orders_result->fetch_all(MYSQLI_ASSOC);

// Status classes configuration (for order status colors)
$status_classes = [
    'Confirmed' => 'bg-[#1A7E79]',
    'Processing' => 'bg-[#E8B006]',
    'Shipped' => 'bg-[#C2185B]',
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
<title>User Details</title>
    <?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="relative">

<?php
include "./header.php";
include "./sidebar.php"
?>

    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <!-- Breadcrumb navigation -->
        <div class="w-full flex items-center gap-2 mb-4">
            <a href="users.php" class="text-[#C2185B] text-[14px] md:text-[16px] font-Onest font-medium">Users</a>
            <span class="text-[#262626] text-[14px] md:text-[16px] font-Onest font-regular">></span>
            <span class="text-[#262626] text-[14px] md:text-[16px] font-Onest font-regular">User Details</span>
        </div>

        <!-- User header section -->
        <div class="w-full rounded-[16px] bg-white mx-auto p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <div class="w-[60px] h-[60px] md:w-[80px] md:h-[80px] rounded-full bg-[#C2185B] text-white flex items-center justify-center text-[24px] md:text-[32px] font-bold">
                    <?php 
                    $initials = '';
                    if (!empty($user['first_name'])) {
                        $initials .= strtoupper(substr($user['first_name'], 0, 1));
                    }
                    if (!empty($user['last_name'])) {
                        $initials .= strtoupper(substr($user['last_name'], 0, 1));
                    }
                    echo !empty($initials) ? htmlspecialchars($initials) : 'U'; 
                    ?>
                </div>
                <div class="flex flex-col">
                    <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold text-[#262626]">
                        <?php 
                        $firstName = $user['first_name'] ?? '';
                        $lastName = $user['last_name'] ?? '';
                        $fullName = trim($firstName . ' ' . $lastName);
                        
                        echo !empty($fullName) ? htmlspecialchars($fullName) : 'No profile name'; 
                        ?>
                    </h1>
                    <p class="text-[14px] md:text-[16px] font-Onest font-regular text-[#666666]">
                        User ID: #<?php echo $user['id']; ?>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="users.php" class="px-4 py-2 border border-[#C2185B] text-[#C2185B] rounded-lg">
                    Back to Users
                </a>
<?php if ($user['status'] != 'suspended'): ?>
                    <button onclick="confirmAction(<?php echo $user['id']; ?>, 'disable')" class="px-4 py-2 bg-[#E8B006] text-white rounded-lg">
                        Disable User
                    </button>
                <?php else: ?>
                    <button onclick="confirmAction(<?php echo $user['id']; ?>, 'enable')" class="px-4 py-2 bg-[#39D959] text-white rounded-lg">
                        Enable User
                    </button>
                <?php endif; ?>
                <button onclick="confirmAction(<?php echo $user['id']; ?>, 'delete')" class="px-4 py-2 bg-[#D93939] text-white rounded-lg">
                    Delete User
                </button>
            </div>
        </div>

        <!-- User information grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- Basic information card -->
            <div class="w-full rounded-[16px] bg-white mx-auto p-4">
                <h2 class="text-[18px] font-Onest font-semibold mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Email</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Phone</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'No profile yet'; ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Address</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'No profile yet'; ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Location</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            <?php 
                            $location = [];
                            if (!empty($user['city'])) $location[] = htmlspecialchars($user['city']);
                            if (!empty($user['state'])) $location[] = htmlspecialchars($user['state']);
                            if (!empty($user['country'])) $location[] = htmlspecialchars($user['country']);
                            if (!empty($user['zip_code'])) $location[] = htmlspecialchars($user['zip_code']);
                            
                            echo !empty($location) ? implode(', ', $location) : 'No location data';
                            ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Status</span>
<span class="inline-block mt-1 px-3 py-1 rounded-full text-white text-[14px] font-Onest font-medium <?php echo $user['status'] != 'suspended' ? 'bg-[#39D959]' : 'bg-[#D93939]'; ?>">
                            <?php echo $user['status'] != 'suspended' ? 'Active' : 'Disabled'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Account statistics card -->
            <div class="w-full rounded-[16px] bg-white mx-auto p-4">
                <h2 class="text-[18px] font-Onest font-semibold mb-4">Account Statistics</h2>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Last Login</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            <?php echo $last_login ? $last_login->format('d/m/Y h:ia') : 'Never'; ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">User ID</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            #<?php echo $user['id']; ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Total Orders</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            <?php echo number_format((float)$user['total_orders']); ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[14px] font-Onest font-medium text-[#666666]">Total Spent</span>
                        <span class="text-[16px] font-Onest font-regular text-[#262626]">
                            ₦<?php echo number_format($user['total_spent'] ?? 0); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent orders section -->
        <div class="w-full rounded-[16px] bg-white mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-[18px] font-Onest font-semibold">Recent Orders</h2>
              
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th class="p-2 text-[13px] md:text-[15px] font-medium">Order ID</th>
                        <th class="p-2 text-[13px] md:text-[15px] font-medium">Amount</th>
                        <th class="p-2 text-[13px] md:text-[15px] font-medium">Status</th>
                        <th class="p-2 text-[13px] md:text-[15px] font-medium">Delivery Method</th>
                        <th class="p-2 text-[13px] md:text-[15px] font-medium">Date</th>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">No orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td class="p-3 text-[13px] md:text-[14px] font-regular font-['Open Sans']">
                                        <a href="../orders/order-details.php?id=<?php echo $order['order_id']; ?>" class="text-[#C2185B]">
                                            #<?php echo $order['order_id']; ?>
                                        </a>
                                    </td>
                                    <td class="px-2 text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                                        ₦<?php echo number_format($order['amount'] ?? 0); ?>
                                    </td>
                                    <td class="px-2">
                                        <?php
                                        $status = $order['order_status'] ?? 'Processing';
                                        $status_class = $status_classes[$status] ?? 'bg-[#E8B006]';
                                        ?>
                                        <span class="inline-block py-1 px-4 <?php echo $status_class; ?> text-white text-[14px] font-['Open Sans'] rounded-[28px]">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td class="px-2 text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                                        <?php echo htmlspecialchars($order['delivery_method'] ?? 'Standard Delivery'); ?>
                                    </td>
                                    <td class="px-2 text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                                        <?php 
                                        if (!empty($order['order_date'])) {
                                            $date = new DateTime($order['order_date']);
                                            echo $date->format('d/m/Y h:ia');
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); z-index: 1000; min-width: 300px;">
        <h3 id="modalMessage" class="text-[18px] font-Onest font-medium mb-4">Are you sure?</h3>
        <input type="hidden" id="userId">
        <input type="hidden" id="actionType">
        <div class="flex justify-end gap-3 mt-4">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded">Cancel</button>
            <button onclick="performAction()" class="px-4 py-2 bg-blue-900 text-white rounded">Confirm</button>
        </div>
    </div>

    <script>
        // User action confirmation functionality
        function confirmAction(userId, action) {
            let message = action === 'delete' ? "Are you sure you want to delete this user? This action cannot be undone." :
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
                    if (action === 'delete') {
                        window.location.href = 'users.php'; // Redirect to users list after deletion
                    } else {
                        location.reload(); // Reload page to update UI for enable/disable
                    }
                }
            })
            .catch(error => console.error('Error:', error));
            
            closeModal();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === document.getElementById('confirmationModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>