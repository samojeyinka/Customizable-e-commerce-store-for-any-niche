<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function requireAuth($redirect_url = '../includes/auth/login/signin.php') {
    if (!isAuthenticated()) {
        header("Location: $redirect_url");
        exit();
    }
}

function requireGuest($redirect_url = 'dashboard.php') {
    if (isAuthenticated()) {
        header("Location: $redirect_url");
        exit();
    }
}

function getCurrentUser() {
    if (!isAuthenticated()) {
        return false;
    }

    $con = db();
    $user_id = $_SESSION['user_id'];
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false;
    }

    return $result->fetch_assoc();
}

function logout($redirect_url = null) {
    if ($redirect_url === null) {
        $redirect_url = DOMAIN . '/includes/auth/login/signin.php';
    }
    session_unset();
    session_destroy();
    header("Location: $redirect_url");
    exit();
}
