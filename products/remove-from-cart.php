<?php

// Example implementation of remove-from-cart.php
require_once '../config/connect.php';
require_once '../includes/auth/auth.php';

$response = [
    'success' => false,
    'message' => '',
    'cart_count' => 0
];

// Check if user is authenticated
if (!isAuthenticated()) {
    $response['message'] = 'Please log in to manage your cart';
    $response['redirect'] = '../login.php';
    echo json_encode($response);
    exit;
}

// Check if cart_id is provided
if (!isset($_POST['cart_id']) || empty($_POST['cart_id'])) {
    $response['message'] = 'Cart item ID is required';
    echo json_encode($response);
    exit;
}

$cart_id = intval($_POST['cart_id']);
$user = getCurrentUser();
$user_id = $user['id'];

// Verify the cart item belongs to this user and delete it
$delete_query = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
$delete_stmt = mysqli_prepare($con, $delete_query);
mysqli_stmt_bind_param($delete_stmt, "ii", $cart_id, $user_id);
$result = mysqli_stmt_execute($delete_stmt);

if ($result) {
    // Get updated cart count
    $count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $count_stmt = mysqli_prepare($con, $count_query);
    mysqli_stmt_bind_param($count_stmt, "i", $user_id);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $count_data = mysqli_fetch_assoc($count_result);
    
    $response['success'] = true;
    $response['message'] = 'Item removed from cart';
    $response['cart_count'] = $count_data['total'] ?? 0;
} else {
    $response['message'] = 'Failed to remove item from cart';
}

echo json_encode($response);

?>