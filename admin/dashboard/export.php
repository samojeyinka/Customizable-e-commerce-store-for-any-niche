<?php
// Database connection
$host = "localhost";
$user = "root";  // Change if needed
$pass = "";
$dbname = "victosah";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check the correct date column from orders table
$order_date_column = "created_at"; // Change this if your column name is different

// Fetch user data with total orders and last order date
$sql = "SELECT users.email, profiles.first_name, profiles.last_name, profiles.phone, profiles.address, users.last_login, 
               COUNT(orders.id) AS total_orders, 
               MAX(orders.$order_date_column) AS last_order
        FROM users
        JOIN profiles ON users.id = profiles.user_id
        LEFT JOIN orders ON users.id = orders.user_id
        GROUP BY users.id";

$result = $conn->query($sql);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="users_data.csv"');

$output = fopen("php://output", "w");

// Add column headers
fputcsv($output, ['First Name', 'Last Name', 'Email', 'Phone', 'Address', 'Last Login', 'Total Orders', 'Last Order Date']);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'],
            $row['address'],
            $row['last_login'],
            $row['total_orders'],
            $row['last_order'] ?? 'No Orders'  // Handle users with no orders
        ]);
    }
}

fclose($output);
$conn->close();
exit();
?>
