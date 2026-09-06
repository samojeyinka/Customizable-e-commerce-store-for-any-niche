<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/connect.php';
require_once dirname(__DIR__) . '/includes/auth/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$out = ['ok' => false, 'message' => '', 'cart_count' => 0];

if (!isAuthenticated()) {
    $out['message'] = 'Please sign in to sync your cart';
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
$items = is_array($json) && isset($json['items']) && is_array($json['items']) ? $json['items'] : [];
if (empty($items) && isset($_POST['items'])) {
    $items = json_decode((string)$_POST['items'], true);
    if (!is_array($items)) {
        $items = [];
    }
}

foreach ($items as $it) {
    $pid = isset($it['product_id']) ? (int)$it['product_id'] : 0;
    $vid = isset($it['variant_id']) && $it['variant_id'] !== '' && $it['variant_id'] !== null ? (int)$it['variant_id'] : null;
    $qty = isset($it['quantity']) ? (int)$it['quantity'] : 1;
    if ($vid === 0) {
        $vid = null;
    }
    if ($pid <= 0 || $qty <= 0) {
        continue;
    }

    $find = "SELECT cart_id, quantity FROM cart
             WHERE user_id = ? AND product_id = ?
               AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
    $stmt = mysqli_prepare($con, $find);
    mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $pid, $vid, $vid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        $new_qty = (int)$row['quantity'] + $qty;
        $upd = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?";
        $s = mysqli_prepare($con, $upd);
        mysqli_stmt_bind_param($s, 'ii', $new_qty, (int)$row['cart_id']);
        mysqli_stmt_execute($s);
    } else {
        $ins = "INSERT INTO cart (user_id, product_id, variant_id, quantity, added_at) VALUES (?, ?, ?, ?, NOW())";
        $s = mysqli_prepare($con, $ins);
        mysqli_stmt_bind_param($s, 'iiii', $user_id, $pid, $vid, $qty);
        mysqli_stmt_execute($s);
    }
}

$count_query = "SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($con, $count_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$cart_count = (int)(mysqli_fetch_assoc($count_result)['total'] ?? 0);

$out['ok'] = true;
$out['message'] = 'Cart synced';
$out['cart_count'] = $cart_count;
echo json_encode($out, JSON_UNESCAPED_UNICODE);