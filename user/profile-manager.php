<?php
// profile_manager.php - Place this in your user directory
require_once '../config/connect.php';

class ProfileManager {
    private $conn;
    private $uploadDir;

    public function __construct($connection) {
        $this->conn = $connection;
        // Set upload directory 
        $this->uploadDir = __DIR__ . '/../uploads/profile_images/';
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
            'profile_image' => null,
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
     * Upload and save profile image
     * @param int $userId User ID
     * @param array $fileUpload $_FILES['profile_image']
     * @return string|false Path to saved image or false on failure
     */
    public function uploadProfileImage($userId, $fileUpload) {
        // Validate file upload
        if (!isset($fileUpload) || $fileUpload['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Ensure upload directory exists
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($fileUpload['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Validate file type
        if (!in_array($fileExtension, $allowedTypes)) {
            return false;
        }

        // Validate file size (5MB max)
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($fileUpload['size'] > $maxFileSize) {
            return false;
        }

        // Generate a unique filename
        $newFilename = $userId . '_profile_' . uniqid() . '.' . $fileExtension;
        $uploadPath = $this->uploadDir . $newFilename;

        // Move uploaded file
        if (move_uploaded_file($fileUpload['tmp_name'], $uploadPath)) {
            // Delete old profile image if exists
            $this->deleteOldProfileImage($userId);

            // Update profile image path in database
            $relativePath = 'uploads/profile_images/' . $newFilename;
            $sql = "UPDATE profiles SET profile_image = ? WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $relativePath, $userId);
            $stmt->execute();

            return $relativePath;
        }

        return false;
    }

    /**
     * Delete old profile image
     * @param int $userId User ID
     */
    private function deleteOldProfileImage($userId) {
        // Fetch current profile image path
        $sql = "SELECT profile_image FROM profiles WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Delete physical file if exists
        if (!empty($row['profile_image'])) {
            $fullPath = __DIR__ . '/../' . $row['profile_image'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    /**
     * Get user's profile image
     * @param int $userId User ID
     * @return string|null Profile image path or null
     */
    public function getProfileImage($userId) {
        $sql = "SELECT profile_image FROM profiles WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['profile_image'] ?? null;
    }

    /**
     * Update user profile with enhanced security
     */
    public function updateProfile($userId, $profileData) {
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
        
        // Prepare SQL statement
        $sql = "INSERT INTO profiles (
            user_id, first_name, last_name, phone, country, 
            address, state, city, zip_code,
            billing_same_as_delivery,
            billing_first_name, billing_last_name, billing_country, 
            billing_address, billing_state, billing_city, billing_zip_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            phone = VALUES(phone),
            country = VALUES(country),
            address = VALUES(address),
            state = VALUES(state),
            city = VALUES(city),
            zip_code = VALUES(zip_code),
            billing_same_as_delivery = VALUES(billing_same_as_delivery),
            billing_first_name = VALUES(billing_first_name),
            billing_last_name = VALUES(billing_last_name),
            billing_country = VALUES(billing_country),
            billing_address = VALUES(billing_address),
            billing_state = VALUES(billing_state),
            billing_city = VALUES(billing_city),
            billing_zip_code = VALUES(billing_zip_code)";
        
        // Prepare and execute statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "isssssssssissssss", 
            $userId,
            $profileData['first_name'],
            $profileData['last_name'],
            $profileData['phone'],
            $profileData['country'],
            $profileData['address'],
            $profileData['state'],
            $profileData['city'],
            $profileData['zip_code'],
            $profileData['billing_same_as_delivery'],
            $profileData['billing_first_name'],
            $profileData['billing_last_name'],
            $profileData['billing_country'],
            $profileData['billing_address'],
            $profileData['billing_state'],
            $profileData['billing_city'],
            $profileData['billing_zip_code']
        );
        
        return $stmt->execute();
    }
}
?>