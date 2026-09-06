<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/connect.php';
require_once dirname(__DIR__) . '/includes/auth/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$out = ['ok' => false, 'count' => 0, 'subtotal' => 0, 'items' => []];

function product_snapshot($con, $product_id, $variant_id = null, $quantity = 1)
{
    $sql = "SELECT p.product_id, p.product_name, p.product_slug, p.product_status,
                   i.image_path AS main_image,
                   v.variant_id, v.size, v.texture,
                   COALESCE(v.discount_price, v.original_price) AS price,
                   (SELECT MIN(COALESCE(pv.discount_price, pv.original_price))
                    FROM product_variants pv WHERE pv.product_id = p.product_id) AS min_price
            FROM products p
            LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
            LEFT JOIN product_variants v ON v.variant_id = ?
            WHERE p.product_id = ?";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        return null;
    }
    if ($variant_id !== null) {
        mysqli_stmt_bind_param($stmt, 'ii', $variant_id, $product_id);
    } else {
        $n = null;
        mysqli_stmt_bind_param($stmt, 'ii', $n, $product_id);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    if (!$row) {
        return null;
    }

    if ($variant_id !== null && ($row['price'] === null || $row['price'] == 0)) {
        $row['price'] = $row['min_price'];
    }
    if ($variant_id === null) {
        $row['price'] = $row['min_price'];
    }

    $price = (float)($row['price'] ?? 0);
    $quantity = max(1, (int)$quantity);

    return [
        'product_id' => (int)$row['product_id'],
        'product_name' => $row['product_name'],
        'slug' => $row['product_slug'],
        'product_status' => $row['product_status'],
        'image' => $row['main_image'],
        'variant_id' => $variant_id !== null ? (int)$variant_id : null,
        'size' => $row['size'],
        'texture' => $row['texture'],
        'price' => $price,
        'quantity' => $quantity,
        'line_total' => round($price * $quantity, 2),
    ];
}

function build_payload($items)
{
    global $out;
    $count = 0;
    $subtotal = 0;
    foreach ($items as $it) {
        $count += (int)$it['quantity'];
        $subtotal += (float)$it['line_total'];
    }
    $out['count'] = $count;
    $out['subtotal'] = round($subtotal, 2);
    $out['items'] = $items;
    $out['ok'] = true;
}

$json = null;
$raw = file_get_contents('php://input');
if ($raw !== '' && $raw[0] === '{') {
    $json = json_decode($raw, true);
}
$posted_items = is_array($json) && isset($json['items']) && is_array($json['items']) ? $json['items'] : [];
if (empty($posted_items) && isset($_POST['items'])) {
    $posted_items = json_decode((string)$_POST['items'], true);
    if (!is_array($posted_items)) {
        $posted_items = [];
    }
}

if (isAuthenticated()) {
    $user = getCurrentUser();
    $user_id = (int)$user['id'];

    $sql = "SELECT c.quantity, c.variant_id, c.product_id
            FROM cart c
            WHERE c.user_id = ?
            ORDER BY c.added_at DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $items = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $snap = product_snapshot($con, (int)$row['product_id'], $row['variant_id'] !== null ? (int)$row['variant_id'] : null, (int)$row['quantity']);
        if ($snap) {
            $items[] = $snap;
        }
    }
    build_payload($items);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

$items = [];
foreach ($posted_items as $it) {
    $pid = isset($it['product_id']) ? (int)$it['product_id'] : 0;
    $vid = isset($it['variant_id']) && $it['variant_id'] !== '' && $it['variant_id'] !== null ? (int)$it['variant_id'] : null;
    $qty = isset($it['quantity']) ? (int)$it['quantity'] : 1;
    if ($vid === 0) {
        $vid = null;
    }
    if ($pid <= 0) {
        continue;
    }
    $snap = product_snapshot($con, $pid, $vid, $qty);
    if ($snap) {
        $items[] = $snap;
    }
}
build_payload($items);
echo json_encode($out, JSON_UNESCAPED_UNICODE);