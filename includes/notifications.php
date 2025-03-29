<?php
// File: includes/notifications.php

/**
 * Add a new notification to the system
 *
 * @param string $type Notification type (order, return, etc.)
 * @param string $title Short notification title
 * @param string $message Full notification message
 * @param string $reference_id ID of the related entity (order_id, etc.)
 * @param string $reference_type Type of the related entity (order, product, etc.)
 * @param int|null $for_user_id Specific user ID or NULL for all admins
 * @param bool $for_admin Whether this is an admin notification
 * @return int|bool The notification ID if successful, false otherwise
 */
function add_notification($con, $type, $title, $message, $reference_id = null, $reference_type = null, $for_user_id = null, $for_admin = true) {
    $query = "INSERT INTO notifications 
              (type, title, message, reference_id, reference_type, for_user_id, for_admin) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssssii", $type, $title, $message, $reference_id, $reference_type, $for_user_id, $for_admin);
    
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($con);
    }
    
    return false;
}

/**
 * Get notifications for admin or specific user
 *
 * @param bool $for_admin Whether to get admin notifications
 * @param int|null $user_id Specific user ID or NULL for admin
 * @param int $limit Number of notifications to retrieve
 * @param int $offset Pagination offset
 * @param bool $unread_only Whether to get only unread notifications
 * @return array Notifications array
 */
function get_notifications($con, $for_admin = true, $user_id = null, $limit = 10, $offset = 0, $unread_only = false) {
    $query = "SELECT * FROM notifications WHERE for_admin = ?";
    
    if ($user_id !== null) {
        $query .= " AND for_user_id = ?";
    }
    
    if ($unread_only) {
        $query .= " AND is_read = 0";
    }
    
    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($user_id !== null) {
        mysqli_stmt_bind_param($stmt, "iiii", $for_admin, $user_id, $limit, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $for_admin, $limit, $offset);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    
    return $notifications;
}

/**
 * Get count of unread notifications
 *
 * @param bool $for_admin Whether to count admin notifications
 * @param int|null $user_id Specific user ID or NULL for admin
 * @return int Count of unread notifications
 */
function get_unread_count($con, $for_admin = true, $user_id = null) {
    $query = "SELECT COUNT(*) as count FROM notifications WHERE for_admin = ? AND is_read = 0";
    
    if ($user_id !== null) {
        $query .= " AND for_user_id = ?";
    }
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($user_id !== null) {
        mysqli_stmt_bind_param($stmt, "ii", $for_admin, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $for_admin);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['count'];
}

/**
 * Mark notification as read
 *
 * @param int $notification_id Notification ID
 * @return bool Success status
 */
function mark_as_read($con, $notification_id) {
    $query = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $notification_id);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Mark all notifications as read
 *
 * @param bool $for_admin Whether to mark admin notifications
 * @param int|null $user_id Specific user ID or NULL for admin
 * @return bool Success status
 */
function mark_all_as_read($con, $for_admin = true, $user_id = null) {
    $query = "UPDATE notifications SET is_read = 1 WHERE for_admin = ?";
    
    if ($user_id !== null) {
        $query .= " AND for_user_id = ?";
    }
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($user_id !== null) {
        mysqli_stmt_bind_param($stmt, "ii", $for_admin, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $for_admin);
    }
    
    return mysqli_stmt_execute($stmt);
}
?>