<?php
// Start session and include necessary files
session_start();
require_once '../config/connect.php';
require_once '../includes/auth/auth.php';
require_once './profile-manager.php';

// Set JSON response headers
header('Content-Type: application/json');

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Require authentication
    requireAuth();
    $user = getCurrentUser();
    $user_id = $user['id'];
    
    try {
        // Create ProfileManager instance
        $profileManager = new ProfileManager($con);
        
        // Collect profile data from POST
        $profileData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'country' => $_POST['country'] ?? 'Nigeria',
            'address' => $_POST['address'] ?? '',
            'state' => $_POST['state'] ?? '',
            'city' => $_POST['city'] ?? '',
            'zip_code' => $_POST['zip_code'] ?? '',
            'billing_same_as_delivery' => isset($_POST['billing_same']) && $_POST['billing_same'] == '1' ? 'on' : '',
            'billing_first_name' => $_POST['billing_first_name'] ?? '',
            'billing_last_name' => $_POST['billing_last_name'] ?? '',
            'billing_country' => $_POST['billing_country'] ?? 'Nigeria',
            'billing_address' => $_POST['billing_address'] ?? '',
            'billing_state' => $_POST['billing_state'] ?? '',
            'billing_city' => $_POST['billing_city'] ?? '',
            'billing_zip_code' => $_POST['billing_zip_code'] ?? ''
        ];
        
        // Update profile using ProfileManager
        $result = $profileManager->updateProfile($user_id, $profileData);
        
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update profile. Please try again.'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>