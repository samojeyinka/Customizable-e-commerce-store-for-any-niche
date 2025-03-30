<?php
// mark-notification.php
session_start();
require_once __DIR__ . './notifications.php';
require_once __DIR__ . '../config/connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Validate parameters
$notification_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$redirect_url = isset($_GET['redirect']) ? $_GET['redirect'] : 'notifications.php';

if ($notification_id > 0 && in_array($action, ['read', 'unread'])) {
    // Toggle notification read status
    if ($action === 'read') {
        // Mark as read
        mark_as_read($con, $notification_id);
    } else {
        // Mark as unread
        $query = "UPDATE notifications SET is_read = 0 WHERE notification_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $notification_id);
        mysqli_stmt_execute($stmt);
    }
}

// Redirect back to the original page
header('Location: ' . $redirect_url);
exit;