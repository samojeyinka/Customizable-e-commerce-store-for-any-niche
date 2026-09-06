<?php
require_once(dirname(__DIR__) . '/config/connect.php');
require_once(dirname(__DIR__) . '/config/config.php');
require_once __DIR__ . '/../includes/auth/auth.php';

if (!isAuthenticated()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user = getCurrentUser();
$user_id = $user['id'];

$items_raw = isset($_POST['items']) ? $_POST['items'] : '';
if (empty($items_raw)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'synced' => 0]);
    exit;
}

$items = json_decode($items_raw, true);
if (!is_array($items)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid items']);
    exit;
}

$synced = 0;
$stmt = mysqli_prepare($con, "INSERT IGNORE INTO favorites (user_id, product_id) VALUES (?, ?)");
foreach ($items as $pid) {
    $pid = intval($pid);
    if ($pid <= 0) continue;
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $pid);
    if (mysqli_stmt_execute($stmt)) $synced++;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'synced' => $synced]);