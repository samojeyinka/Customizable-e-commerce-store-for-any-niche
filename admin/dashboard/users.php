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

// Search and filter logic
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Determine the date range for filtering using last_login
$dateCondition = "";
if ($filter == "today") {
    $dateCondition = "AND users.last_login >= CURDATE()";
} elseif ($filter == "last_week") {
    $dateCondition = "AND users.last_login >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter == "last_28_days") {
    $dateCondition = "AND users.last_login >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
}

// Base SQL query to fetch users, profiles, and order details
$sql = "SELECT users.id, users.email, users.last_login, users.is_disabled, 
               profiles.first_name, profiles.last_name, profiles.phone, profiles.address,
               COUNT(orders.id) AS total_orders, 
               MAX(orders.created_at) AS last_order_date
        FROM users
        JOIN profiles ON users.id = profiles.user_id
        LEFT JOIN orders ON users.id = orders.user_id
        WHERE 1 $dateCondition";


// Apply search filter
if (!empty($search)) {
    $sql .= " AND (profiles.first_name LIKE '%$search%' 
                   OR profiles.last_name LIKE '%$search%' 
                   OR profiles.phone LIKE '%$search%' 
                   OR users.email LIKE '%$search%') ";
}

$sql .= " GROUP BY users.id";  // Group by user to avoid duplicate rows
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .search-box, .filter-box { margin-bottom: 10px; padding: 5px; width: 200px; }
    </style>
</head>
<body>

    <h2>Users List</h2>

    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by name, email, phone..." 
               value="<?php echo htmlspecialchars($search); ?>" class="search-box">
        
        <select name="filter" class="filter-box">
            <option value="">-- Filter by Last Login --</option>
            <option value="today" <?php echo ($filter == "today") ? "selected" : ""; ?>>Today</option>
            <option value="last_week" <?php echo ($filter == "last_week") ? "selected" : ""; ?>>Last Week</option>
            <option value="last_28_days" <?php echo ($filter == "last_28_days") ? "selected" : ""; ?>>Last 28 Days</option>
        </select>

        <button type="submit">Apply</button>
        <a href="users.php"><button type="button">Clear Filter</button></a>
    </form>

    <table>
        <tr>
            <th>Customer Number</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Last Login</th>
            <th>Total Orders</th>
            <th>Last Order Date</th>
            <th>Actions</th>

        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['last_login']; ?></td>
                    <td><?php echo $row['total_orders']; ?></td>
                    <td>
                        <?php echo ($row['last_order_date']) ? $row['last_order_date'] : "No Orders"; ?>
                    </td>
                    <td>
    <button onclick="confirmAction(<?php echo $row['id']; ?>, 'delete')">Delete</button>
    <?php if ($row['is_disabled'] == 0): ?>
        <button onclick="confirmAction(<?php echo $row['id']; ?>, 'disable')">Disable</button>
    <?php else: ?>
        <button onclick="confirmAction(<?php echo $row['id']; ?>, 'enable')">Enable</button>
    <?php endif; ?>
</td>

                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No users found.</td></tr>
        <?php endif; ?>
    </table>

    <form method="GET" action="export.php">
    <button type="submit">Export to CSV</button>
</form>


<div id="confirmationModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);">
    <h3 id="modalMessage">Are you sure?</h3>
    <input type="hidden" id="userId">
    <input type="hidden" id="actionType">
    <button onclick="performAction()">Yes</button>
    <button onclick="closeModal()">Cancel</button>
</div>


<script>
function confirmAction(userId, action) {
    let message = action === 'delete' ? "Are you sure you want to delete this user?" :
                 action === 'disable' ? "Are you sure you want to disable this user?" :
                 "Are you sure you want to enable this user?";

    if (confirm(message)) {
        fetch('user_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); 
            if (data.success) {
                location.reload(); // Reload page to update UI
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>



</body>
</html>

<?php $conn->close(); ?>
