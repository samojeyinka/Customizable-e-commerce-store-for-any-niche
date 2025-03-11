<?php

// Get category and brand from URL parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : null;

// Set the number of products per page
// $products_per_page = 12;

$current_path = $_SERVER['REQUEST_URI'];

// Set products per page based on URL
$products_per_page = 12; // Default value for most pages

// Check if we're on the root/homepage
if (preg_match('#^/victosah/?$#', $current_path) || 
    preg_match('#^/victosah/index\.php$#', $current_path)) {
    $products_per_page = 4; // Smaller number for homepage
}

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
        c.category_title,
        b.brand_title,
        i.image_path AS main_image,
        MIN(v.original_price) AS min_price,
        MIN(v.discount_price) AS min_discount_price
    FROM 
        products p
    LEFT JOIN 
        categories c ON p.category_id = c.category_id
    LEFT JOIN 
        brands b ON p.brand_id = b.brand_id
    LEFT JOIN 
        product_images i ON p.product_id = i.product_id AND i.is_main = 1
    LEFT JOIN 
        product_variants v ON p.product_id = v.product_id
    WHERE 1=1
";

// Add filters for category and brand if they exist
if ($category_id) {
    $query .= " AND p.category_id = " . $category_id;
}

if ($brand_id) {
    $query .= " AND p.brand_id = " . $brand_id;
}

// Complete the query with GROUP BY and ORDER BY
$query .= "
    GROUP BY 
        p.product_id, p.product_name, p.is_featured, c.category_title, b.brand_title, i.image_path
    ORDER BY 
        p.date_added DESC
";

// Get total number of products with these filters (for calculating total pages)
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

// Get the category and brand names for display
$category_name = "All Products";
$brand_name = "";

if ($category_id) {
    $cat_query = "SELECT category_title FROM categories WHERE category_id = ?";
    $cat_stmt = mysqli_prepare($con, $cat_query);
    mysqli_stmt_bind_param($cat_stmt, "i", $category_id);
    mysqli_stmt_execute($cat_stmt);
    $cat_result = mysqli_stmt_get_result($cat_stmt);
    if ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $category_name = $cat_row['category_title'];
    }
}

if ($brand_id) {
    $brand_query = "SELECT brand_title FROM brands WHERE brand_id = ?";
    $brand_stmt = mysqli_prepare($con, $brand_query);
    mysqli_stmt_bind_param($brand_stmt, "i", $brand_id);
    mysqli_stmt_execute($brand_stmt);
    $brand_result = mysqli_stmt_get_result($brand_stmt);
    if ($brand_row = mysqli_fetch_assoc($brand_result)) {
        $brand_name = $brand_row['brand_title'];
    }
}

// Combined title for display
$page_title = $category_name;
if ($brand_name) {
    $page_title .= " - " . $brand_name;
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


// In your PHP section, fetch the cart items for the current user
$cart_items = [];
if ($user) {
    $cart_items_query = "SELECT product_id, cart_id FROM cart WHERE user_id = ?";
    $cart_items_stmt = mysqli_prepare($con, $cart_items_query);
    mysqli_stmt_bind_param($cart_items_stmt, "i", $user['id']);
    mysqli_stmt_execute($cart_items_stmt);
    $cart_items_result = mysqli_stmt_get_result($cart_items_stmt);
    
    while ($item = mysqli_fetch_assoc($cart_items_result)) {
        $cart_items[$item['product_id']] = $item['cart_id'];
    }
}



// Check if each product is in user's favorites
$favorites = [];
if ($user) {
    $favorites_query = "SELECT product_id FROM favorites WHERE user_id = ?";
    $favorites_stmt = mysqli_prepare($con, $favorites_query);
    mysqli_stmt_bind_param($favorites_stmt, "i", $user['id']);
    mysqli_stmt_execute($favorites_stmt);
    $favorites_result = mysqli_stmt_get_result($favorites_stmt);
    
    while ($fav = mysqli_fetch_assoc($favorites_result)) {
        $favorites[$fav['product_id']] = true;
    }
}

?>