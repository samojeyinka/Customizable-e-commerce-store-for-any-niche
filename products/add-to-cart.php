<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/connect.php';
require_once __DIR__ . '/../includes/auth/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$variant_id = isset($_POST['variant_id']) ? intval($_POST['variant_id']) : null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
if ($quantity <= 0) {
    $quantity = 1;
}
if ($variant_id === 0) {
    $variant_id = null;
}

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$check_product = "SELECT product_id FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($con, $check_product);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

if ($variant_id) {
    $check_variant = "SELECT variant_id FROM product_variants WHERE variant_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($con, $check_variant);
    mysqli_stmt_bind_param($stmt, "ii", $variant_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        echo json_encode(['success' => false, 'message' => 'Product variant not found']);
        exit;
    }
}

if (!isAuthenticated()) {
    echo json_encode([
        'success' => true,
        'mode' => 'guest',
        'message' => 'Item added to cart',
        'cart_count' => $quantity
    ]);
    exit;
}

$user = getCurrentUser();
$user_id = (int)$user['id'];

$check_cart = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
$stmt = mysqli_prepare($con, $check_cart);
mysqli_stmt_bind_param($stmt, "iiii", $user_id, $product_id, $variant_id, $variant_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $cart_id, $current_quantity);
    mysqli_stmt_fetch($stmt);
    $new_quantity = $current_quantity + $quantity;

    $update_cart = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?";
    $update_stmt = mysqli_prepare($con, $update_cart);
    mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $cart_count = cart_count_for($user_id);
        echo json_encode([
            'success' => true,
            'message' => 'Item quantity updated in cart',
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart: ' . mysqli_error($con)]);
    }
} else {
    $add_cart = "INSERT INTO cart (user_id, product_id, variant_id, quantity, added_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $add_cart);
    mysqli_stmt_bind_param($stmt, "iiii", $user_id, $product_id, $variant_id, $quantity);

    if (mysqli_stmt_execute($stmt)) {
        $cart_count = cart_count_for($user_id);
        echo json_encode([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add to cart: ' . mysqli_error($con)]);
    }
}

function cart_count_for($user_id)
{
    $count_query = "SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?";
    $count_stmt = mysqli_prepare($GLOBALS['con'], $count_query);
    mysqli_stmt_bind_param($count_stmt, "i", $user_id);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    return (int)(mysqli_fetch_assoc($count_result)['total'] ?? 0);
}