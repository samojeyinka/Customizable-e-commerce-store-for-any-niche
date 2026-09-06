<?php
// dashboard_stats.php - Include this at the top of your overview.php file

if (!function_exists('db')) {
    require_once __DIR__ . '/../../config/config.php';
}

// Database connection is already established from config.php
$conn = db();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get selected year (default to current year)
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get current date ranges for filtering
$today = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('-7 days'));
$last_28_days = date('Y-m-d', strtotime('-28 days'));
$last_56_days = date('Y-m-d', strtotime('-56 days'));

// Get the selected time period from URL parameter (default to last_28_days)
$time_period = isset($_GET['period']) ? $_GET['period'] : 'last_28_days';

// Set the date filter based on selected period
switch ($time_period) {
    case 'today':
        $date_filter = "DATE(created_at) = '$today'";
        $prev_date_filter = "DATE(created_at) = DATE_SUB('$today', INTERVAL 1 DAY)";
        break;
    case 'last_7_days':
        $date_filter = "DATE(created_at) >= '$last_7_days'";
        $prev_date_filter = "DATE(created_at) >= DATE_SUB('$last_7_days', INTERVAL 7 DAY) AND DATE(created_at) < '$last_7_days'";
        break;
    case 'last_28_days':
    default:
        $date_filter = "DATE(created_at) >= '$last_28_days'";
        $prev_date_filter = "DATE(created_at) >= '$last_56_days' AND DATE(created_at) < '$last_28_days'";
        break;
}

// User Statistics 
$user_stats_query = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as disabled_users,
    (SELECT COUNT(*) FROM users WHERE last_login >= '$last_28_days') as recent_users,
    (SELECT COUNT(*) FROM users WHERE last_login >= '$last_56_days' AND last_login < '$last_28_days') as previous_period_users
FROM users";

$user_stats_result = $conn->query($user_stats_query);
if (!$user_stats_result) {
    die("Error in user stats query: " . $conn->error);
}
$user_stats = $user_stats_result->fetch_assoc();

// Calculate user percentage change
$user_change = $user_stats['recent_users'] - $user_stats['previous_period_users'];
$user_percentage = $user_stats['previous_period_users'] != 0 
    ? round(($user_change / $user_stats['previous_period_users']) * 100) 
    : ($user_stats['recent_users'] > 0 ? 100 : 0);

// Check if the orders table exists
$table_check_query = "SHOW TABLES LIKE 'orders'";
$table_check_result = $conn->query($table_check_query);

if ($table_check_result->num_rows > 0) {
    // Table exists, now check columns
    $columns_query = "SHOW COLUMNS FROM orders";
    $columns_result = $conn->query($columns_query);
    
    $has_status_column = false;
    $has_order_status_column = false;
    $has_delivery_method_column = false;
    $has_order_type_column = false;
    $has_total_amount_column = false;
    $has_order_total_column = false;
    $has_created_at_column = false;
    
    while ($column = $columns_result->fetch_assoc()) {
        if ($column['Field'] == 'status') $has_status_column = true;
        if ($column['Field'] == 'order_status') $has_order_status_column = true;
        if ($column['Field'] == 'delivery_method') $has_delivery_method_column = true;
        if ($column['Field'] == 'order_type') $has_order_type_column = true;
        if ($column['Field'] == 'total_amount') $has_total_amount_column = true;
        if ($column['Field'] == 'order_total') $has_order_total_column = true;
        if ($column['Field'] == 'created_at') $has_created_at_column = true;
    }
    
    // Determine which amount column to use
    $amount_column = $has_order_total_column ? 'order_total' : ($has_total_amount_column ? 'total_amount' : null);
    
    // If created_at doesn't exist, modify the filters
    if (!$has_created_at_column) {
        $date_filter = "1=1";
        $prev_date_filter = "1=1";
    }
    
    // Order Statistics
    $order_stats_query = "SELECT COUNT(*) as total_orders";
    
    if ($has_status_column) {
        $order_stats_query .= ",
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_orders";
    } else if ($has_order_status_column) {
        $order_stats_query .= ",
        SUM(CASE WHEN order_status = 'Delivered' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN order_status = 'Ongoing' THEN 1 ELSE 0 END) as ongoing_orders";
    } else {
        $order_stats_query .= ",
        0 as completed_orders,
        0 as ongoing_orders";
    }
    
    $order_stats_query .= ",
        COUNT(CASE WHEN $date_filter THEN 1 END) as current_period_orders,
        COUNT(CASE WHEN $prev_date_filter THEN 1 END) as previous_period_orders
    FROM orders";
    
    $order_stats_result = $conn->query($order_stats_query);
    if (!$order_stats_result) {
        die("Error in order stats query: " . $conn->error);
    }
    $order_stats = $order_stats_result->fetch_assoc();
    
    // Calculate order percentage change
    $order_change = $order_stats['current_period_orders'] - $order_stats['previous_period_orders'];
    $order_percentage = $order_stats['previous_period_orders'] != 0 
        ? round(($order_change / $order_stats['previous_period_orders']) * 100)
        : ($order_stats['current_period_orders'] > 0 ? 100 : 0);
    
    // Sales Statistics (Revenue)
    if ($amount_column) {
        // Determine status condition
        $status_condition = "";
        if ($has_status_column) {
            $status_condition = "status = 'delivered'";
        } else if ($has_order_status_column) {
            $status_condition = "order_status = 'Delivered'";
        }
        
        // Total revenue query
        $total_revenue_query = "SELECT SUM($amount_column) as total_revenue FROM orders";
        if ($status_condition) {
            $total_revenue_query .= " WHERE $status_condition";
        }
        
        $total_revenue_result = $conn->query($total_revenue_query);
        $total_revenue = $total_revenue_result ? $total_revenue_result->fetch_assoc() : ['total_revenue' => 0];
        
        // Period revenue query
        $sales_stats_query = "SELECT 
            SUM(CASE WHEN $date_filter THEN $amount_column ELSE 0 END) as current_period_revenue,
            SUM(CASE WHEN $prev_date_filter THEN $amount_column ELSE 0 END) as previous_period_revenue
        FROM orders";
        
        if ($status_condition) {
            $sales_stats_query .= " WHERE $status_condition";
        }
        
        $sales_stats_result = $conn->query($sales_stats_query);
        if ($sales_stats_result) {
            $sales_stats = $sales_stats_result->fetch_assoc();
            $sales_stats['total_revenue'] = $total_revenue['total_revenue'] ?? 0;
            
            // Calculate percentage change
            $sales_change = $sales_stats['current_period_revenue'] - $sales_stats['previous_period_revenue'];
            $sales_percentage = $sales_stats['previous_period_revenue'] != 0 
                ? round(($sales_change / $sales_stats['previous_period_revenue']) * 100)
                : ($sales_stats['current_period_revenue'] > 0 ? 100 : 0);
        } else {
            $sales_stats = [
                'total_revenue' => $total_revenue['total_revenue'] ?? 0,
                'current_period_revenue' => 0,
                'previous_period_revenue' => 0
            ];
            $sales_percentage = 0;
        }
    } else {
        $sales_stats = [
            'total_revenue' => 0,
            'current_period_revenue' => 0,
            'previous_period_revenue' => 0
        ];
        $sales_percentage = 0;
    }
    
    // Delivery method stats
    if ($has_delivery_method_column || $has_order_type_column) {
        $delivery_stats_query = "SELECT 
            COUNT(CASE WHEN " . ($has_delivery_method_column ? "delivery_method = 'express'" : "order_type = 'Express Delivery'") . " THEN 1 END) as express_delivery,
            COUNT(CASE WHEN " . ($has_delivery_method_column ? "delivery_method = 'pickup'" : "order_type = 'PickUp'") . " THEN 1 END) as pickup_delivery,
            COUNT(*) as total_delivery_orders
        FROM orders";
        
        // Add date filters if created_at exists
        if ($has_created_at_column) {
            $delivery_stats_query .= " WHERE $date_filter";
        }
        
        $delivery_stats_result = $conn->query($delivery_stats_query);
        if ($delivery_stats_result) {
            $delivery_stats = $delivery_stats_result->fetch_assoc();
        } else {
            $delivery_stats = [
                'express_delivery' => 0,
                'pickup_delivery' => 0,
                'total_delivery_orders' => 0
            ];
        }
    } else {
        $delivery_stats = [
            'express_delivery' => 0,
            'pickup_delivery' => 0,
            'total_delivery_orders' => 0
        ];
    }
    
    // Monthly Revenue Data for Last 6 Months
    if ($has_created_at_column && $amount_column) {
        // Get current date and calculate 6 months back
        $currentDate = new DateTime();
        $months = [];
        $monthNames = [];
        $monthlyRevenue = [];
        
        // Prepare data structure for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = clone $currentDate;
            $date->modify("-$i months");
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('M');
            
            $months[] = $monthKey;
            $monthNames[] = $monthName;
            $monthlyRevenue[$monthKey] = 0;
        }
        
        // Query to get revenue for each of the last 6 months
        $revenueQuery = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM($amount_column) as monthly_revenue
        FROM orders
        WHERE 
            created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            AND order_status = 'Delivered'
        GROUP BY month
        ORDER BY month";
        
        $revenueResult = $conn->query($revenueQuery);
        
        if ($revenueResult) {
            while ($row = $revenueResult->fetch_assoc()) {
                if (isset($monthlyRevenue[$row['month']])) {
                    $monthlyRevenue[$row['month']] = (float)$row['monthly_revenue'];
                }
            }
        }
        
        // Prepare data in correct order for chart
        $chartData = [];
        foreach ($months as $month) {
            $chartData[] = $monthlyRevenue[$month];
        }
        
        $monthly_revenue_json = json_encode($chartData);
        $monthly_labels_json = json_encode($monthNames);
    } else {
        // Default empty data if required columns don't exist
        $currentMonth = date('M');
        $monthNames = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthNames[] = date('M', strtotime("-$i months"));
        }
        $monthly_revenue_json = json_encode(array_fill(0, 6, 0));
        $monthly_labels_json = json_encode($monthNames);
    }
} else {
    // Orders table doesn't exist - set default values
    $order_stats = [
        'total_orders' => 0,
        'completed_orders' => 0,
        'ongoing_orders' => 0,
        'current_period_orders' => 0,
        'previous_period_orders' => 0
    ];
    $order_percentage = 0;
    
    $sales_stats = [
        'total_revenue' => 0,
        'current_period_revenue' => 0,
        'previous_period_revenue' => 0
    ];
    $sales_percentage = 0;
    
    $delivery_stats = [
        'express_delivery' => 0,
        'pickup_delivery' => 0,
        'total_delivery_orders' => 0
    ];
    
    // Default empty data for chart
    $currentMonth = date('M');
    $monthNames = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthNames[] = date('M', strtotime("-$i months"));
    }
    $monthly_revenue_json = json_encode(array_fill(0, 6, 0));
    $monthly_labels_json = json_encode($monthNames);
}
?>