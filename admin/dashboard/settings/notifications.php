<?php
// Start session and include necessary files
session_start();
require_once '../../../config/connect.php';
require_once '../../../config/config.php';

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

// Get current notification settings
$settings = [];
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get settings from form
        $dashboard_new_orders = isset($_POST['dashboard_new_orders']) ? 1 : 0;
        $dashboard_transactions = isset($_POST['dashboard_transactions']) ? 1 : 0;
        $dashboard_cancellation_refunds = isset($_POST['dashboard_cancellation_refunds']) ? 1 : 0;
        $dashboard_security_alerts = isset($_POST['dashboard_security_alerts']) ? 1 : 0;
        $dashboard_stock_alerts = isset($_POST['dashboard_stock_alerts']) ? 1 : 0;
        $dashboard_chat = isset($_POST['dashboard_chat']) ? 1 : 0;
        
        $email_notifications_enabled = isset($_POST['email_notifications_enabled']) ? 1 : 0;
        $email_new_orders = isset($_POST['email_new_orders']) ? 1 : 0;
        $email_transactions = isset($_POST['email_transactions']) ? 1 : 0;
        $email_cancellation_refunds = isset($_POST['email_cancellation_refunds']) ? 1 : 0;
        $email_security_alerts = isset($_POST['email_security_alerts']) ? 1 : 0;
        $email_stock_alerts = isset($_POST['email_stock_alerts']) ? 1 : 0;
        $email_chat = isset($_POST['email_chat']) ? 1 : 0;
        
        // Check if the admin already has settings
        $check_sql = "SELECT id FROM admin_notification_settings WHERE admin_id = ?";
        $check_stmt = $con->prepare($check_sql);
        $check_stmt->bind_param("i", $admin_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing settings
            $sql = "UPDATE admin_notification_settings SET 
                    dashboard_new_orders = ?,
                    dashboard_transactions = ?,
                    dashboard_cancellation_refunds = ?,
                    dashboard_security_alerts = ?,
                    dashboard_stock_alerts = ?,
                    dashboard_chat = ?,
                    email_notifications_enabled = ?,
                    email_new_orders = ?,
                    email_transactions = ?,
                    email_cancellation_refunds = ?,
                    email_security_alerts = ?,
                    email_stock_alerts = ?,
                    email_chat = ?
                    WHERE admin_id = ?";
                    
            $stmt = $con->prepare($sql);
            $stmt->bind_param("iiiiiiiiiiiiii", 
                $dashboard_new_orders,
                $dashboard_transactions,
                $dashboard_cancellation_refunds,
                $dashboard_security_alerts,
                $dashboard_stock_alerts,
                $dashboard_chat,
                $email_notifications_enabled,
                $email_new_orders,
                $email_transactions,
                $email_cancellation_refunds,
                $email_security_alerts,
                $email_stock_alerts,
                $email_chat,
                $admin_id
            );
        } else {
            // Insert new settings
            $sql = "INSERT INTO admin_notification_settings (
                    admin_id,
                    dashboard_new_orders,
                    dashboard_transactions,
                    dashboard_cancellation_refunds,
                    dashboard_security_alerts,
                    dashboard_stock_alerts,
                    dashboard_chat,
                    email_notifications_enabled,
                    email_new_orders,
                    email_transactions,
                    email_cancellation_refunds,
                    email_security_alerts,
                    email_stock_alerts,
                    email_chat
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
            $stmt = $con->prepare($sql);
            $stmt->bind_param("iiiiiiiiiiiii", 
                $admin_id,
                $dashboard_new_orders,
                $dashboard_transactions,
                $dashboard_cancellation_refunds,
                $dashboard_security_alerts,
                $dashboard_stock_alerts,
                $dashboard_chat,
                $email_notifications_enabled,
                $email_new_orders,
                $email_transactions,
                $email_cancellation_refunds,
                $email_security_alerts,
                $email_stock_alerts,
                $email_chat
            );
        }
        
        // Execute the statement
        if ($stmt->execute()) {
            $success_message = "Notification settings updated successfully";
        } else {
            $error_message = "Failed to update notification settings";
        }
        
    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}

// Get current notification settings
$sql = "SELECT * FROM admin_notification_settings WHERE admin_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $settings = $result->fetch_assoc();
} else {
    // Create default settings if none exist
    $sql = "INSERT INTO admin_notification_settings (admin_id) VALUES (?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    
    // Get the default settings
    $sql = "SELECT * FROM admin_notification_settings WHERE admin_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = $result->fetch_assoc();
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
    <title>Notification Settings | GLOREFY ADMIN</title>
    <?php include '../../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="relative">

    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        
        <!-- Success message display -->
        <?php if (!empty($success_message)): ?>
        <div class="bg-[#E0F8E9] shadow-lg mb-4 py-3 px-4 rounded relative" id="successAlert">
            <div class="h-full w-[5px] bg-[#28C76F] absolute left-0 top-0"></div>
            <div class="flex items-center justify-between">
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Success</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer text-[#777]" viewBox="0 0 20 20" fill="currentColor" onclick="document.getElementById('successAlert').style.display='none'">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Error message display -->
        <?php if (!empty($error_message)): ?>
        <div class="bg-[#FDECEC] shadow-lg mb-4 py-3 px-4 rounded relative" id="errorAlert">
            <div class="h-full w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <div class="flex items-center justify-between">
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] font-medium">Error</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer text-[#777]" viewBox="0 0 20 20" fill="currentColor" onclick="document.getElementById('errorAlert').style.display='none'">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-[14px] text-[#7F7F7F] mt-1"><?php echo $error_message; ?></p>
        </div>
        <?php endif; ?>
  
        <form method="POST" action="" class="w-full md:w-[70%] p-6 bg-white">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Notification Settings</h1>
            <h2 class="text-[16px] font-medium mb-6">Select your notification preference within the dashboard</h2>
            
            <!-- Dashboard Notifications -->
            <div class="space-y-4 mb-8">
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">New Orders</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_new_orders" class="sr-only peer" <?php echo $settings['dashboard_new_orders'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Transactions</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_transactions" class="sr-only peer" <?php echo $settings['dashboard_transactions'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Cancellation & Refund Alerts</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_cancellation_refunds" class="sr-only peer" <?php echo $settings['dashboard_cancellation_refunds'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Security Alerts</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_security_alerts" class="sr-only peer" <?php echo $settings['dashboard_security_alerts'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Stock & Quantity Alert</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_stock_alerts" class="sr-only peer" <?php echo $settings['dashboard_stock_alerts'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Chat</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_chat" class="sr-only peer" <?php echo $settings['dashboard_chat'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>
            </div>

            <!-- Email Notifications -->
            <div class="space-y-4 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-gray-700 block">Get notifications on your email</span>
                        <span class="text-gray-500 text-sm"><?php echo htmlspecialchars($admin_email); ?></span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="email_notifications_enabled" id="email_toggle" class="sr-only peer" <?php echo $settings['email_notifications_enabled'] ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                    </label>
                </div>

                <div id="email_settings" class="pl-6 space-y-4 <?php echo $settings['email_notifications_enabled'] ? '' : 'opacity-50'; ?>">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">New Orders</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_new_orders" class="sr-only peer email-setting" <?php echo $settings['email_new_orders'] ? 'checked' : ''; ?> <?php echo $settings['email_notifications_enabled'] ? '' : 'disabled'; ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Transactions</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_transactions" class="sr-only peer email-setting" <?php echo $settings['email_transactions'] ? 'checked' : ''; ?> <?php echo $settings['email_notifications_enabled'] ? '' : 'disabled'; ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Cancellation & Refund Alerts</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_cancellation_refunds" class="sr-only peer email-setting" <?php echo $settings['email_cancellation_refunds'] ? 'checked' : ''; ?> <?php echo $settings['email_notifications_enabled'] ? '' : 'disabled'; ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Security Alerts</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_security_alerts" class="sr-only peer email-setting" <?php echo $settings['email_security_alerts'] ? 'checked' : ''; ?> <?php echo $settings['email_notifications_enabled'] ? '' : 'disabled'; ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Stock & Quantity Alert</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_stock_alerts" class="sr-only peer email-setting" <?php echo $settings['email_stock_alerts'] ? 'checked' : ''; ?> <?php echo $settings['email_notifications_enabled'] ? '' : 'disabled'; ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Chat</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_chat" class="sr-only peer email-setting" <?php echo $settings['email_chat'] ? 'checked' : ''; ?> <?php echo $settings['email_notifications_enabled'] ? '' : 'disabled'; ?>>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#C2185B]"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Save Button -->
            <div class="mt-6">
                <button type="submit" class="w-full py-[12px] px-3 bg-[#C2185B] text-white text-[16px] font-medium cursor-pointer rounded-[8px] hover:bg-[#0e1442] transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <script type="text/javascript" src="../../functions/drop-select.js"></script>
    <script type="text/javascript" src="../../functions/order.js"></script>
    <script type="text/javascript" src="../../functions/dash.js"></script>
    <script type="text/javascript" src="../../functions/tab.js"></script>
    <script type="text/javascript" src="../../functions/overlay.js"></script>
    <script type="text/javascript" src="../../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../../functions/nav.js"></script>
    
    <script>
        // Toggle email notification settings based on the main toggle
        document.addEventListener('DOMContentLoaded', function() {
            const emailToggle = document.getElementById('email_toggle');
            const emailSettings = document.getElementById('email_settings');
            const emailInputs = document.querySelectorAll('.email-setting');
            
            emailToggle.addEventListener('change', function() {
                if (this.checked) {
                    emailSettings.classList.remove('opacity-50');
                    emailInputs.forEach(input => {
                        input.disabled = false;
                    });
                } else {
                    emailSettings.classList.add('opacity-50');
                    emailInputs.forEach(input => {
                        input.disabled = true;
                    });
                }
            });
        });
    </script>
</body>
</html>