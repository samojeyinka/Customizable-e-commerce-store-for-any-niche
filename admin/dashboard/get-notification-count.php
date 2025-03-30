<?php
// File: admin/includes/get-notification-count.php
// This file returns the current unread notification count for AJAX requests

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Check if this is an AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

// Include database connection
require_once "../../config/servername.php";
include('../../config/connect.php');
require_once "../../includes/notifications.php";

// Get unread notification count
$count = get_unread_count($con, true);

// Return as JSON
header('Content-Type: application/json');
echo json_encode(['count' => $count]);
exit;