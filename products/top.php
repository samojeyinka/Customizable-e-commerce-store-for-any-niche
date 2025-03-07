<?php
// Include database connection
include('../config/connect.php');
require_once '../includes/auth/auth.php';

$user = isAuthenticated() ? getCurrentUser() : null;


// Set the number of products per page
$products_per_page = 12;

// Get the current page from URL parameter, default to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Ensure current_page is at least 1
if ($current_page < 1) {
    $current_page = 1;
}

// Calculate the starting position for the SQL LIMIT clause
$start = ($current_page - 1) * $products_per_page;

// Base query for products with their main image and first variant price
$query = "
    SELECT 
        p.product_id,
        p.product_name, 
        p.is_featured,
        i.image_path AS main_image,
        MIN(v.original_price) AS min_price,
        MIN(v.discount_price) AS min_discount_price
    FROM 
        products p
    LEFT JOIN 
        product_images i ON p.product_id = i.product_id AND i.is_main = 1
    LEFT JOIN 
        product_variants v ON p.product_id = v.product_id
    GROUP BY 
        p.product_id, p.product_name, p.is_featured, i.image_path
    ORDER BY 
        p.date_added DESC
";

// Get total number of products (for calculating total pages)
$count_query = "SELECT COUNT(*) as total FROM (" . $query . ") as counted";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_products = $count_row['total'];

// Calculate total pages
$total_pages = ceil($total_products / $products_per_page);

// Add LIMIT clause for pagination
$query .= " LIMIT $start, $products_per_page";

// Execute the main query
$result = mysqli_query($con, $query);

// Check if query was successful
if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit;
}


// Get the user's cart count if they're logged in
$cart_count = 0;
if ($user) {
    $cart_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $cart_stmt = mysqli_prepare($con, $cart_query);
    mysqli_stmt_bind_param($cart_stmt, "i", $user['id']);
    mysqli_stmt_execute($cart_stmt);
    $cart_result = mysqli_stmt_get_result($cart_stmt);
    $cart_data = mysqli_fetch_assoc($cart_result);
    $cart_count = $cart_data['total'] ?? 0;
}
?>
