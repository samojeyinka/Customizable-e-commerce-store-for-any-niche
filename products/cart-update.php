<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/connect.php';
require_once dirname(__DIR__) . '/includes/auth/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$out = ['ok' => false, 'message' => '', 'cart_count' => 0];

if (!isAuthenticated()) {
    $out['message'] = 'Please sign in to manage your cart';
    echo json_encode($out);
    exit;
}

$user = getCurrentUser();
$user_id = (int)$user['id'];

$json = null;
$raw = file_get_contents('php://input');
if ($raw !== '' && $raw[0] === '{') {
    $json = json_decode($raw, true);
}
$data = is_array($json) ? $json : $_POST;

$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
$variant_id = isset($data['variant_id']) && $data['variant_id'] !== '' && $data['variant_id'] !== null ? (int)$data['variant_id'] : null;
if ($variant_id === 0) {
    $variant_id = null;
}
$action = isset($data['action']) ? (string)$data['action'] : 'set';
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

if ($product_id <= 0) {
    $out['message'] = 'Invalid product ID';
    echo json_encode($out);
    exit;
}

if ($action === 'remove') {
    $del = "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
    $stmt = mysqli_prepare($con, $del);
    mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $product_id, $variant_id, $variant_id);
    mysqli_stmt_execute($stmt);
} else {
    $quantity = max(0, $quantity);
    if ($quantity <= 0) {
        $del = "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
        $stmt = mysqli_prepare($con, $del);
        mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $product_id, $variant_id, $variant_id);
        mysqli_stmt_execute($stmt);
    } else {
        $find = "SELECT cart_id FROM cart WHERE user_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
        $stmt = mysqli_prepare($con, $find);
        mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $product_id, $variant_id, $variant_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $upd = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?";
            $s = mysqli_prepare($con, $upd);
            mysqli_stmt_bind_param($s, 'ii', $quantity, (int)$row['cart_id']);
            mysqli_stmt_execute($s);
        } else {
            $ins = "INSERT INTO cart (user_id, product_id, variant_id, quantity, added_at) VALUES (?, ?, ?, ?, NOW())";
            $s = mysqli_prepare($con, $ins);
            mysqli_stmt_bind_param($s, 'iiii', $user_id, $product_id, $variant_id, $quantity);
            mysqli_stmt_execute($s);
        }
    }
}

$count_query = "SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($con, $count_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$cart_count = (int)(mysqli_fetch_assoc($count_result)['total'] ?? 0);

$out['ok'] = true;
$out['message'] = 'Cart updated';
$out['cart_count'] = $cart_count;
echo json_encode($out, JSON_UNESCAPED_UNICODE);