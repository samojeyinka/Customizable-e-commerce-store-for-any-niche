<?php
// Include database connection and auth

include(dirname(__DIR__) . '/config/connect.php');
include(dirname(__DIR__) . '/config/config.php');
require_once __DIR__ . '/../includes/auth/auth.php';

// Check if user is logged in
if (!isAuthenticated()) {
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        // 'redirect' => '../includes/auth/login/signin.php',
        'redirect' => DOMAIN . '/includes/auth/login/signin.php',
        'message' => 'Please login to add favorites'
    ]);
    exit;
}

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Check if product_id is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required'
    ]);
    exit;
}

$product_id = intval($_POST['product_id']);

// Check if this is a remove action
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($action === 'remove') {
    // Remove from favorites
    $query = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    $success = mysqli_stmt_execute($stmt);
    
    if ($success) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Product removed from favorites'
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error removing product from favorites: ' . mysqli_error($con)
        ]);
    }
    exit;
}

// Check if product is already in favorites
$check_query = "SELECT favorite_id FROM favorites WHERE user_id = ? AND product_id = ?";
$check_stmt = mysqli_prepare($con, $check_query);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $product_id);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($result) > 0) {
    // Product is already in favorites
    $favorite = mysqli_fetch_assoc($result);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'action' => 'exists',
        'favorite_id' => $favorite['favorite_id'],
        'message' => 'Product is already in favorites'
    ]);
    exit;
}

// Add product to favorites
$insert_query = "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)";
$insert_stmt = mysqli_prepare($con, $insert_query);
mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $product_id);
$success = mysqli_stmt_execute($insert_stmt);

if ($success) {
    $favorite_id = mysqli_insert_id($con);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'action' => 'added',
        'favorite_id' => $favorite_id,
        'message' => 'Product added to favorites'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error adding product to favorites: ' . mysqli_error($con)
    ]);
}