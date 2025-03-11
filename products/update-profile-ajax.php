<?php
// Start session and include necessary files
session_start();
require_once '../config/connect.php';
require_once '../includes/auth/auth.php';

// Set JSON response headers
header('Content-Type: application/json');

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Check if this is a POST request with the right action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    // Require authentication
    requireAuth();
    $user = getCurrentUser();
    $user_id = $user['id'];
    
    try {
        // Collect and sanitize the data
        $profileData = [
            'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
            'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'country' => sanitizeInput($_POST['country'] ?? 'Nigeria'),
            'address' => sanitizeInput($_POST['address'] ?? ''),
            'state' => sanitizeInput($_POST['state'] ?? ''),
            'city' => sanitizeInput($_POST['city'] ?? ''),
            'zip_code' => sanitizeInput($_POST['zip_code'] ?? ''),
            'billing_same_as_delivery' => isset($_POST['billing_same_as_delivery']) && $_POST['billing_same_as_delivery'] == '1' ? 1 : 0
        ];
        
        // Add billing details if they're different from shipping
        if (!$profileData['billing_same_as_delivery']) {
            $profileData['billing_first_name'] = sanitizeInput($_POST['billing_first_name'] ?? '');
            $profileData['billing_last_name'] = sanitizeInput($_POST['billing_last_name'] ?? '');
            $profileData['billing_country'] = sanitizeInput($_POST['billing_country'] ?? 'Nigeria');
            $profileData['billing_address'] = sanitizeInput($_POST['billing_address'] ?? '');
            $profileData['billing_state'] = sanitizeInput($_POST['billing_state'] ?? '');
            $profileData['billing_city'] = sanitizeInput($_POST['billing_city'] ?? '');
            $profileData['billing_zip_code'] = sanitizeInput($_POST['billing_zip_code'] ?? '');
            $profileData['billing_phone'] = sanitizeInput($_POST['billing_phone'] ?? '');
        } else {
            // Copy shipping details to billing
            $profileData['billing_first_name'] = $profileData['first_name'];
            $profileData['billing_last_name'] = $profileData['last_name'];
            $profileData['billing_country'] = $profileData['country'];
            $profileData['billing_address'] = $profileData['address'];
            $profileData['billing_state'] = $profileData['state'];
            $profileData['billing_city'] = $profileData['city'];
            $profileData['billing_zip_code'] = $profileData['zip_code'];
            $profileData['billing_phone'] = $profileData['phone'];
        }
        
        // Check if profile exists
        $check_query = "SELECT id FROM profiles WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Profile exists - UPDATE
            $update_query = "UPDATE profiles SET 
                first_name = ?, last_name = ?, phone = ?, country = ?, 
                address = ?, state = ?, city = ?, zip_code = ?,
                billing_same_as_delivery = ?, 
                billing_first_name = ?, billing_last_name = ?, billing_country = ?,
                billing_address = ?, billing_state = ?, billing_city = ?, 
                billing_zip_code = ?, billing_phone = ?,
                updated_at = NOW()
                WHERE user_id = ?";
                
            $stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($stmt, "sssssssssssssssssi", 
                $profileData['first_name'], $profileData['last_name'], 
                $profileData['phone'], $profileData['country'],
                $profileData['address'], $profileData['state'], 
                $profileData['city'], $profileData['zip_code'],
                $profileData['billing_same_as_delivery'],
                $profileData['billing_first_name'], $profileData['billing_last_name'], 
                $profileData['billing_country'], $profileData['billing_address'], 
                $profileData['billing_state'], $profileData['billing_city'], 
                $profileData['billing_zip_code'], $profileData['billing_phone'],
                $user_id
            );
        } else {
            // Profile doesn't exist - INSERT
            $insert_query = "INSERT INTO profiles (
                user_id, first_name, last_name, phone, country, 
                address, state, city, zip_code,
                billing_same_as_delivery, 
                billing_first_name, billing_last_name, billing_country,
                billing_address, billing_state, billing_city, 
                billing_zip_code, billing_phone,
                created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
            )";
                
            $stmt = mysqli_prepare($con, $insert_query);
            mysqli_stmt_bind_param($stmt, "issssssssssssssss", 
                $user_id,
                $profileData['first_name'], $profileData['last_name'], 
                $profileData['phone'], $profileData['country'],
                $profileData['address'], $profileData['state'], 
                $profileData['city'], $profileData['zip_code'],
                $profileData['billing_same_as_delivery'],
                $profileData['billing_first_name'], $profileData['billing_last_name'], 
                $profileData['billing_country'], $profileData['billing_address'], 
                $profileData['billing_state'], $profileData['billing_city'], 
                $profileData['billing_zip_code'], $profileData['billing_phone']
            );
        }
        
        // Execute the query
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            // Return success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            // Return error response
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update profile: ' . mysqli_error($con)
            ]);
        }
        
    } catch (Exception $e) {
        // Return exception response
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    // Invalid request
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}