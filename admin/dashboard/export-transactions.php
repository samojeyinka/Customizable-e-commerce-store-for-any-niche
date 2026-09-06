<?php
// Start session and include necessary files
session_start();
require_once '../../config/connect.php';
require_once '../../includes/auth/auth.php';

// Authentication and user validation
requireAuth();
$user = getCurrentUser();
$user_id = $user['id'];

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="transactions-export-' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// CSV headers
fputcsv($output, [
    'Customer Name',
    'Email',
    'Transaction ID',
    'Payment Reference',
    'Order ID',
    'Amount',
    'Status',
    'Payment Method',
    'Currency',
    'Delivery Method',
    'Transaction Date',
    'Notes'
], ',', '"', '\\');

// Determine which transactions to export
if (isset($_GET['ids']) && !empty($_GET['ids'])) {
    // Export specific transactions
    $ids = explode(',', $_GET['ids']);
    $id_list = '';
    foreach ($ids as $id) {
        $id_list .= intval($id) . ',';
    }
    $id_list = rtrim($id_list, ',');
    
    $sql = "SELECT * FROM transaction_history WHERE id IN ($id_list)";
} else {
    // Export based on filters
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';
    $start_date = '';
    $end_date = '';

    // Process date filter
    if ($date_filter) {
        switch ($date_filter) {
            case 'today':
                $start_date = date('Y-m-d 00:00:00');
                $end_date = date('Y-m-d 23:59:59');
                break;
            case 'week':
                $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
                $end_date = date('Y-m-d 23:59:59');
                break;
            case 'month':
                $start_date = date('Y-m-d 00:00:00', strtotime('-28 days'));
                $end_date = date('Y-m-d 23:59:59');
                break;
            case 'custom':
                $start_date = isset($_GET['start_date']) ? $_GET['start_date'] . ' 00:00:00' : '';
                $end_date = isset($_GET['end_date']) ? $_GET['end_date'] . ' 23:59:59' : '';
                break;
        }
    }

    // Base SQL for transactions
    $sql = "SELECT * FROM transaction_history WHERE 1=1";

    // Add search condition if search term provided
    if (!empty($search)) {
        $search_term = mysqli_real_escape_string($con, "%$search%");
        $sql .= " AND (customer_name LIKE '$search_term' 
                  OR email LIKE '$search_term' 
                  OR transaction_id LIKE '$search_term' 
                  OR payment_reference LIKE '$search_term')";
    }

    // Add date filter if provided
    if (!empty($start_date) && !empty($end_date)) {
        $sql .= " AND transaction_date BETWEEN '$start_date' AND '$end_date'";
    }

    // Order by date
    $sql .= " ORDER BY transaction_date DESC";
}

// Execute query
$result = mysqli_query($con, $sql);

// Check for errors
if (!$result) {
    die("Error exporting transactions: " . mysqli_error($con));
}

// Write data to CSV
while ($row = mysqli_fetch_assoc($result)) {
    // Format date
    $row['transaction_date'] = date('Y-m-d H:i:s', strtotime($row['transaction_date']));
    
    // Write row to CSV
    fputcsv($output, [
        $row['customer_name'],
        $row['email'],
        $row['transaction_id'],
        $row['payment_reference'],
        $row['order_id'],
        $row['amount'],
        $row['status'],
        $row['payment_method'],
        $row['currency'],
        $row['delivery_method'],
        $row['transaction_date'],
        $row['notes']
    ], ',', '"', '\\');
}

// Close database connection
mysqli_close($con);

// Exit to prevent any additional output
exit();