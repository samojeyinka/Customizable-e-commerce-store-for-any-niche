<?php
// This file should be placed at admin/actions/mark-all-read.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include database connection
require_once "../../config/servername.php";
include('../../config/connect.php');
require_once "../../includes/notifications.php";

// Process request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = isset($_POST['type']) ? $_POST['type'] : 'all';
    $for_admin = true;
    
    // Mark all as read
    if ($type === 'all') {
        mark_all_as_read($con, $for_admin);
    } else {
        // Mark specific type as read
        $sql = "UPDATE notifications SET is_read = 1 WHERE for_admin = ? AND type = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "is", $for_admin, $type);
        mysqli_stmt_execute($stmt);
    }
    
    // Return success response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['success' => true]);
        exit;
    }
    
    // Redirect back to notifications page
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../notifications.php';
    header("Location: $redirect_url");
    exit;
}

// If not a POST request, redirect back
header("Location: ../notifications.php");
exit;