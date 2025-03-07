<?php
// Start session
session_start();

// Include database connection
// include('../config/connect.php');
include(dirname(__DIR__) . '/config/connect.php');
include(dirname(__DIR__) . '/config/config.php');


// Include authentication utility
// require_once '../includes/auth/auth.php';
require_once __DIR__ . '/../includes/auth/auth.php';
// Check if user is authenticated
if (!isAuthenticated()) {
    // Return JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to your cart',
        // 'redirect' => '../includes/auth/login/signin.php'
        'redirect' => DOMAIN . '/includes/auth/login/signin.php'
    ]);
    exit();
}

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Check if it's an AJAX request with POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get product ID from POST data
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $variant_id = isset($_POST['variant_id']) ? intval($_POST['variant_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Validate product ID
    if ($product_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        exit();
    }
    
    // Validate quantity
    if ($quantity <= 0) {
        $quantity = 1; // Default to 1 if invalid quantity
    }
    
    // Check if product exists
    $check_product = "SELECT product_id FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($con, $check_product);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) == 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    // Check if variant exists if provided
    if ($variant_id) {
        $check_variant = "SELECT variant_id FROM product_variants WHERE variant_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($con, $check_variant);
        mysqli_stmt_bind_param($stmt, "ii", $variant_id, $product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) == 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Product variant not found'
            ]);
            exit();
        }
    }
    
    // Check if item already exists in cart
    $check_cart = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
    $stmt = mysqli_prepare($con, $check_cart);
    mysqli_stmt_bind_param($stmt, "iiii", $user_id, $product_id, $variant_id, $variant_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Item exists, update quantity
        mysqli_stmt_bind_result($stmt, $cart_id, $current_quantity);
        mysqli_stmt_fetch($stmt);
        
        $new_quantity = $current_quantity + $quantity;
        
        $update_cart = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?";
        $update_stmt = mysqli_prepare($con, $update_cart);
        mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            // Get updated cart count
            $count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
            $count_stmt = mysqli_prepare($con, $count_query);
            mysqli_stmt_bind_param($count_stmt, "i", $user_id);
            mysqli_stmt_execute($count_stmt);
            $count_result = mysqli_stmt_get_result($count_stmt);
            $cart_count = mysqli_fetch_assoc($count_result)['total'] ?? 0;
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Item quantity updated in cart',
                'cart_count' => $cart_count
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update cart: ' . mysqli_error($con)
            ]);
        }
    } else {
        // Item doesn't exist, add new item
        $add_cart = "INSERT INTO cart (user_id, product_id, variant_id, quantity, added_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($con, $add_cart);
        mysqli_stmt_bind_param($stmt, "iiii", $user_id, $product_id, $variant_id, $quantity);
        
        if (mysqli_stmt_execute($stmt)) {
            // Get updated cart count
            $count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
            $count_stmt = mysqli_prepare($con, $count_query);
            mysqli_stmt_bind_param($count_stmt, "i", $user_id);
            mysqli_stmt_execute($count_stmt);
            $count_result = mysqli_stmt_get_result($count_stmt);
            $cart_count = mysqli_fetch_assoc($count_result)['total'] ?? 0;
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart',
                'cart_count' => $cart_count
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add to cart: ' . mysqli_error($con)
            ]);
        }
    }
} else {
    // Not a POST request
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}