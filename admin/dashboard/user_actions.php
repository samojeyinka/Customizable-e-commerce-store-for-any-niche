<?php
$host = "localhost";
$user = "root";  
$pass = "";
$dbname = "victosah";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

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
        $sql = "UPDATE users SET is_disabled = 1 WHERE id = ?";
    } elseif ($action === 'enable') {
        $sql = "UPDATE users SET is_disabled = 0 WHERE id = ?";
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

$conn->close();
?>
