<?php
// profile_manager.php - Place this in your user directory
require_once '../config/connect.php';

class ProfileManager {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    /**
     * Get user profile by user ID
     */
    public function getProfileByUserId($userId) {
        $sql = "SELECT * FROM profiles WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            // Create empty profile if it doesn't exist
            $this->createEmptyProfile($userId);
            return $this->getDefaultProfile($userId);
        }
    }

    /**
     * Create empty profile record for a user
     */
    private function createEmptyProfile($userId) {
        $sql = "INSERT INTO profiles (user_id) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * Get default profile with user's email
     */
    private function getDefaultProfile($userId) {
        // Get user email
        $sql = "SELECT id, email FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Return default profile with email
        return [
            'id' => null,
            'user_id' => $userId,
            'email' => $user['email'] ?? '',
            'first_name' => '',
            'last_name' => '',
            'phone' => '',
            'country' => 'Nigeria',
            'address' => '',
            'state' => '',
            'city' => '',
            'zip_code' => '',
            'billing_same_as_delivery' => true,
            'billing_first_name' => '',
            'billing_last_name' => '',
            'billing_country' => 'Nigeria',
            'billing_address' => '',
            'billing_state' => '',
            'billing_city' => '',
            'billing_zip_code' => ''
        ];
    }

    /**
     * Update user profile - SIMPLIFIED VERSION WITHOUT BIND_PARAM
     */
    public function updateProfile($userId, $profileData) {
        // Check if profile exists
        $sql = "SELECT id FROM profiles WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = ($result->num_rows > 0);
        
        // Prepare billing data based on checkbox
        if (isset($profileData['billing_same_as_delivery']) && $profileData['billing_same_as_delivery'] == 'on') {
            $profileData['billing_first_name'] = $profileData['first_name'];
            $profileData['billing_last_name'] = $profileData['last_name'];
            $profileData['billing_country'] = $profileData['country'];
            $profileData['billing_address'] = $profileData['address'];
            $profileData['billing_state'] = $profileData['state'];
            $profileData['billing_city'] = $profileData['city'];
            $profileData['billing_zip_code'] = $profileData['zip_code'];
            $profileData['billing_same_as_delivery'] = 1;
        } else {
            $profileData['billing_same_as_delivery'] = 0;
        }
        
        // Use direct string concatenation instead of bind_param to avoid issues
        if ($exists) {
            // Update using direct query with proper escaping
            $sql = "UPDATE profiles SET 
                    first_name = '" . $this->conn->real_escape_string($profileData['first_name']) . "', 
                    last_name = '" . $this->conn->real_escape_string($profileData['last_name']) . "', 
                    phone = '" . $this->conn->real_escape_string($profileData['phone']) . "', 
                    country = '" . $this->conn->real_escape_string($profileData['country']) . "', 
                    address = '" . $this->conn->real_escape_string($profileData['address']) . "', 
                    state = '" . $this->conn->real_escape_string($profileData['state']) . "', 
                    city = '" . $this->conn->real_escape_string($profileData['city']) . "', 
                    zip_code = '" . $this->conn->real_escape_string($profileData['zip_code']) . "',
                    billing_same_as_delivery = " . intval($profileData['billing_same_as_delivery']) . ",
                    billing_first_name = '" . $this->conn->real_escape_string($profileData['billing_first_name']) . "', 
                    billing_last_name = '" . $this->conn->real_escape_string($profileData['billing_last_name']) . "', 
                    billing_country = '" . $this->conn->real_escape_string($profileData['billing_country']) . "', 
                    billing_address = '" . $this->conn->real_escape_string($profileData['billing_address']) . "', 
                    billing_state = '" . $this->conn->real_escape_string($profileData['billing_state']) . "', 
                    billing_city = '" . $this->conn->real_escape_string($profileData['billing_city']) . "', 
                    billing_zip_code = '" . $this->conn->real_escape_string($profileData['billing_zip_code']) . "'
                    WHERE user_id = " . intval($userId);
        } else {
            // Insert using direct query with proper escaping
            $sql = "INSERT INTO profiles (
                    user_id, first_name, last_name, phone, country, 
                    address, state, city, zip_code,
                    billing_same_as_delivery,
                    billing_first_name, billing_last_name, billing_country, 
                    billing_address, billing_state, billing_city, billing_zip_code
                ) VALUES (
                    " . intval($userId) . ",
                    '" . $this->conn->real_escape_string($profileData['first_name']) . "',
                    '" . $this->conn->real_escape_string($profileData['last_name']) . "',
                    '" . $this->conn->real_escape_string($profileData['phone']) . "',
                    '" . $this->conn->real_escape_string($profileData['country']) . "',
                    '" . $this->conn->real_escape_string($profileData['address']) . "',
                    '" . $this->conn->real_escape_string($profileData['state']) . "',
                    '" . $this->conn->real_escape_string($profileData['city']) . "',
                    '" . $this->conn->real_escape_string($profileData['zip_code']) . "',
                    " . intval($profileData['billing_same_as_delivery']) . ",
                    '" . $this->conn->real_escape_string($profileData['billing_first_name']) . "',
                    '" . $this->conn->real_escape_string($profileData['billing_last_name']) . "',
                    '" . $this->conn->real_escape_string($profileData['billing_country']) . "',
                    '" . $this->conn->real_escape_string($profileData['billing_address']) . "',
                    '" . $this->conn->real_escape_string($profileData['billing_state']) . "',
                    '" . $this->conn->real_escape_string($profileData['billing_city']) . "',
                    '" . $this->conn->real_escape_string($profileData['billing_zip_code']) . "'
                )";
        }
        
        // Execute the query directly
        $result = $this->conn->query($sql);
        return $result;
    }
}
?>