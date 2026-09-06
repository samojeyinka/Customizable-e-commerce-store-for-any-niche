<?php
require_once '../../config/config.php';

$conn = db();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if (!$userId || !$action) {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
        exit;
    }

    if ($action === 'delete') {
        $sql = "DELETE FROM users WHERE id = ?";
    } elseif ($action === 'disable') {
        $sql = "UPDATE users SET status = 'suspended' WHERE id = ?";
    } elseif ($action === 'enable') {
        $sql = "UPDATE users SET status = 'active' WHERE id = ?";
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => ucfirst($action) . " successful."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to execute action."]);
    }

    $stmt->close();
}


?>
