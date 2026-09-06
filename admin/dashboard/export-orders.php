<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Database connection
$conn = db();
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
$search_query = '';

// Date filter handling
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'all';
if (isset($_GET['date'])) {
    // Validate date filter
    $allowed_filters = ['today', 'last7days', 'last28days', 'custom', 'all'];
    if (in_array($date_filter, $allowed_filters)) {
        switch ($date_filter) {
            case 'today':
                $conditions[] = "DATE(orders.$date_column) = CURDATE()";
                break;
            case 'last7days':
                $conditions[] = "DATE(orders.$date_column) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last28days':
                $conditions[] = "DATE(orders.$date_column) >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
                break;
            case 'custom':
                if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                    $start_date = $conn->real_escape_string($_GET['start_date']);
                    $end_date = $conn->real_escape_string($_GET['end_date']);
                    $conditions[] = "DATE(orders.$date_column) BETWEEN '$start_date' AND '$end_date'";
                }
                break;
            // If 'all' is selected or no valid filter is selected, no condition is added
        }
    }
}

// Search functionality
if (!empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $search = $conn->real_escape_string($search_query);
    // Search by order ID and customer name
    $conditions[] = "(orders.id LIKE '%$search%' OR CONCAT(profiles.first_name, ' ', profiles.last_name) LIKE '%$search%')";
}

// Status filter
if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $conditions[] = "orders.order_status = '$status'";
}

// Build WHERE clause
$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// SQL query for exporting all records
$sql = "SELECT 
            orders.id AS order_id,
            CONCAT(profiles.first_name, ' ', profiles.last_name) AS customer_name,
            orders.order_total AS amount,
            orders.delivery_method,
            orders.order_status,
            orders.$date_column AS order_date
        FROM orders
        LEFT JOIN profiles ON orders.user_id = profiles.user_id
        $where_clause
        ORDER BY orders.$date_column DESC";

// Execute query
$result = $conn->query($sql);
if (!$result) {
    // For debugging
    echo "Error in query: " . $conn->error;
    exit;
}

// Get all orders
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Format date for each order
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

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Orders_Export_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');

// Output Excel content
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Mode of Order</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>';

// Output each row
foreach ($orders as $order) {
    echo '<tr>';
    echo '<td>#' . htmlspecialchars($order['order_id']) . '</td>';
    echo '<td>' . htmlspecialchars($order['customer_name'] ?? '') . '</td>';
    echo '<td>₦' . number_format($order['amount'] ?? 0) . '</td>';
    echo '<td>' . htmlspecialchars($order['order_status'] ?? 'Processing') . '</td>';
    echo '<td>' . htmlspecialchars($order['delivery_method'] ?? 'Express Delivery') . '</td>';
    echo '<td>' . htmlspecialchars($order['formatted_date']) . '</td>';
    echo '<td>' . htmlspecialchars($order['formatted_time']) . '</td>';
    echo '</tr>';
}

echo '</tbody>
    </table>
</body>
</html>';
exit;