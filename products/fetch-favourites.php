<?php
require_once(dirname(__DIR__) . '/config/connect.php');
require_once(dirname(__DIR__) . '/config/config.php');
require_once __DIR__ . '/../includes/auth/auth.php';

if (!isAuthenticated()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'ids' => []]);
    exit;
}

$user = getCurrentUser();
$user_id = $user['id'];

$query = "SELECT product_id FROM favorites WHERE user_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$ids = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ids[] = (int)$row['product_id'];
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'ids' => $ids]);