<?php
session_start();
require_once "../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: ./index.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'];
$admin_email = $_SESSION['admin_email'];
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Connect to the database
require_once "../config/servername.php";

$conn = db();

// Fetch admin details
$sql = "SELECT * FROM administrators WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Close the database connection

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Dashboard</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include 'tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-100">
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        ☰
    </button>

    <div class="sidebar" id="sidebar">
        <div class="flex items-center justify-center py-5 border-b border-white/10">
            <img src="<?php echo DOMAIN; ?>/assets/global/logo.png" alt="GLOREFY" class="w-[31.35px] md:w-[41.35px]" />
        </div>
        
        <ul class="sidebar-menu mt-5">
            <li class="active">
                <a href="dashboard.php">Dashboard</a>
            </li>
            <li>
                <a href="#">Products</a>
            </li>
            <li>
                <a href="#">Orders</a>
            </li>
            <li>
                <a href="#">Customers</a>
            </li>
            <li>
                <a href="#">Reports</a>
            </li>
            <li>
                <a href="#">Settings</a>
            </li>
            <li class="border-t border-white/10 mt-5 pt-5">
                <a href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold font-['Open Sans']">Dashboard</h1>
            <div class="dropdown relative">
                <button class="flex items-center space-x-2 bg-white px-3 py-2 rounded-lg shadow">
                    <span class="font-medium"><?php echo htmlspecialchars($admin_name); ?></span>
                    <span>▼</span>
                </button>
                <!-- Dropdown menu would go here -->
            </div>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <p><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <p><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="admin-profile">
            <h2 class="text-xl font-bold mb-4">Admin Profile</h2>
            <div class="profile-header">
                <img 
                    src="<?php echo DOMAIN; ?>/assets/global/<?php echo htmlspecialchars($admin['profile_photo'] ?? 'default.jpg'); ?>" 
                    alt="Profile" 
                    class="profile-image"
                    onerror="this.src='<?php echo DOMAIN; ?>/assets/global/default.jpg'"
                >
                <div>
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
                    <p class="text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
                    <p class="text-sm"><?php echo htmlspecialchars($admin_email); ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Phone Number</p>
                    <p><?php echo htmlspecialchars($admin['phone_number'] ?? 'Not set'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Last Login</p>
                    <p><?php echo isset($admin['last_login']) ? date('F j, Y, g:i a', strtotime($admin['last_login'])) : 'First login'; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Account Status</p>
                    <p>
                        <span class="inline-block px-2 py-1 text-xs rounded <?php echo $admin['account_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ucfirst(htmlspecialchars($admin['account_status'] ?? 'Unknown')); ?>
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Account Created</p>
                    <p><?php echo isset($admin['created_at']) ? date('F j, Y', strtotime($admin['created_at'])) : 'Unknown'; ?></p>
                </div>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3 class="text-gray-500">Total Products</h3>
                <div class="stat-value">120</div>
                <p class="text-sm text-green-500">+5% from last month</p>
            </div>
            
            <div class="stat-card">
                <h3 class="text-gray-500">Total Orders</h3>
                <div class="stat-value">435</div>
                <p class="text-sm text-green-500">+12% from last month</p>
            </div>
            
            <div class="stat-card">
                <h3 class="text-gray-500">Total Customers</h3>
                <div class="stat-value">298</div>
                <p class="text-sm text-green-500">+8% from last month</p>
            </div>
            
            <div class="stat-card">
                <h3 class="text-gray-500">Revenue</h3>
                <div class="stat-value">$12,435</div>
                <p class="text-sm text-green-500">+15% from last month</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 border-b text-left">Date</th>
                            <th class="py-3 px-4 border-b text-left">Action</th>
                            <th class="py-3 px-4 border-b text-left">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3 px-4 border-b"><?php echo date('Y-m-d H:i:s'); ?></td>
                            <td class="py-3 px-4 border-b">Login</td>
                            <td class="py-3 px-4 border-b">Admin logged in successfully</td>
                        </tr>
                        <!-- More activity rows would go here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        });
    </script>
</body>
</html>