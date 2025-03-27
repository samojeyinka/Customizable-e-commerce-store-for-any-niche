<?php
session_start();
require_once "../../../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . DOMAIN . "../../index.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'];
$admin_email = $_SESSION['admin_email'];
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Initialize messages
$success_message = "";
$error_message = "";

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Database connection
require_once "../../../config/servername.php";
$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process profile update including photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    
    // Validate inputs
    if (empty($full_name)) {
        $_SESSION['error_message'] = "Full name is required";
    } elseif (empty($email)) {
        $_SESSION['error_message'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format";
    } else {
        // Check if email exists for another admin
        $check_email = "SELECT admin_id FROM administrators WHERE email = ? AND admin_id != ?";
        $check_stmt = $conn->prepare($check_email);
        $check_stmt->bind_param("si", $email, $admin_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error_message'] = "Email is already in use by another administrator";
        } else {
            // Handle photo upload if a new photo was provided
            $new_filename = null;
            $photo_updated = false;
            
            if (isset($_FILES["profile_photo"]) && $_FILES["profile_photo"]["error"] != 4) { // 4 means no file was uploaded
                if ($_FILES["profile_photo"]["error"] == 0) {
                    $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
                    $filename = $_FILES["profile_photo"]["name"];
                    $filetype = $_FILES["profile_photo"]["type"];
                    $filesize = $_FILES["profile_photo"]["size"];
                    
                    // Verify file extension
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!array_key_exists($ext, $allowed)) {
                        $_SESSION['error_message'] = "Error: Please select a valid file format (JPG, JPEG, PNG, GIF)";
                    } else {
                        // Verify file size - 5MB maximum
                        $max_size = 5 * 1024 * 1024;
                        if ($filesize > $max_size) {
                            $_SESSION['error_message'] = "Error: File size must be less than 5MB";
                        } else {
                            // Verify MIME type of the file
                            if (in_array($filetype, $allowed)) {
                                // Create uploads directory if it doesn't exist
                                $upload_dir = "../../../uploads/profiles/";
                                if (!file_exists($upload_dir)) {
                                    mkdir($upload_dir, 0777, true);
                                }
                                
                                // Generate unique file name
                                $new_filename = uniqid() . "." . $ext;
                                $upload_path = $upload_dir . $new_filename;
                                
                                // Move the uploaded file
                                if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $upload_path)) {
                                    $photo_updated = true;
                                } else {
                                    $_SESSION['error_message'] = "Error uploading file";
                                }
                            } else {
                                $_SESSION['error_message'] = "Error: File type not allowed";
                            }
                        }
                    }
                } else {
                    $_SESSION['error_message'] = "Error with file upload: " . $_FILES["profile_photo"]["error"];
                }
            }
            
            // If there's no error, update profile
            if (!isset($_SESSION['error_message'])) {
                try {
                    // Start a transaction
                    $conn->begin_transaction();
                    
                    // Prepare the base SQL statement
                    if ($photo_updated) {
                        // Update with photo
                        $update_sql = "UPDATE administrators SET full_name = ?, email = ?, phone_number = ?, profile_photo = ? WHERE admin_id = ?";
                        $stmt = $conn->prepare($update_sql);
                        
                        // Bind parameters directly (pass by reference)
                        $stmt->bind_param("ssssi", $full_name, $email, $phone_number, $new_filename, $admin_id);
                    } else {
                        // Update without photo
                        $update_sql = "UPDATE administrators SET full_name = ?, email = ?, phone_number = ? WHERE admin_id = ?";
                        $stmt = $conn->prepare($update_sql);
                        
                        // Bind parameters directly (pass by reference)
                        $stmt->bind_param("sssi", $full_name, $email, $phone_number, $admin_id);
                    }
                    
                    // Execute the statement
                    if ($stmt->execute()) {
                        // Commit transaction
                        $conn->commit();
                        
                        // Update session data
                        $_SESSION['admin_fullname'] = $full_name;
                        $_SESSION['admin_email'] = $email;
                        
                        $_SESSION['success_message'] = "Profile updated successfully";
                    } else {
                        throw new Exception("Database error: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
                }
            }
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch admin details
$sql = "SELECT * FROM administrators WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Close the database connection
$conn->close();

// Handle profile photo path
$profile_photo_url = DOMAIN . "/assets/global/user.svg"; // Default image
if (!empty($admin['profile_photo'])) {
    $photo_path = DOMAIN . "/uploads/profiles/" . $admin['profile_photo'];
    $profile_photo_url = $photo_path;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/overlay.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/dropdown.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/graph.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/dash.css" />
    <title>Admin Profile - VICTOSAH</title>
</head>

<body class="relative">
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA] mt-16">
        <div class="w-[90%] mx-auto md:mx-0 md:w-[70%] pb-10 md:pb-0">
            <h1 class="text-[20px] font-Onest font-semibold mb-3 md:mb-5">Profile</h1>
            
            <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
            <?php endif; ?>
            
            <!-- Profile View -->
            <div id="profileView" class="space-y-6 <?php echo isset($_POST['update_profile']) ? '' : ''; ?>">
                <div class="flex justify-between items-start">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="<?php echo htmlspecialchars($profile_photo_url); ?>" 
                                 id="profileImageDisplay" 
                                 class="w-full h-full object-cover" 
                                 onerror="this.style.display='none'; document.getElementById('profileImagePlaceholder').style.display='block';">
                            <svg id="profileImagePlaceholder" class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <button id="editProfileBtn" class="px-4 py-2 bg-indigo-800 text-white rounded-md hover:bg-indigo-700 transition-colors">
                        Edit Profile
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full name</label>
                        <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
                            <?php echo htmlspecialchars($admin['full_name']); ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
                            <?php echo htmlspecialchars($admin['email']); ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone number</label>
                        <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
                            <?php echo htmlspecialchars($admin['phone_number'] ?? 'Not set'); ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
                            <?php echo htmlspecialchars(ucfirst($admin['role'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div id="editProfileForm" class="hidden space-y-6">
                <form method="POST" action="" class="space-y-6" enctype="multipart/form-data">
                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden relative group">
                            <img src="<?php echo htmlspecialchars($profile_photo_url); ?>" 
                                 id="editProfileImage" 
                                 class="w-full h-full object-cover" 
                                 onerror="this.style.display='none'; document.getElementById('editProfilePlaceholder').style.display='block';">
                            <svg id="editProfilePlaceholder" class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                 onclick="document.getElementById('photoInput').click()">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-indigo-800 font-medium cursor-pointer" onclick="document.getElementById('photoInput').click()">
                            Change Profile Photo
                        </p>
                        <input type="file" name="profile_photo" id="photoInput" class="hidden" accept="image/*">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full name</label>
                            <input type="text" id="full_name" name="full_name" class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
                                   value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                            <input type="email" id="email" name="email" class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
                                   value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                        </div>
                        
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone number</label>
                            <input type="text" id="phone_number" name="phone_number" class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
                                   value="<?php echo htmlspecialchars($admin['phone_number'] ?? ''); ?>">
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <input type="text" id="role" class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50" 
                                   value="<?php echo htmlspecialchars(ucfirst($admin['role'])); ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="flex space-x-4 pt-4">
                        <button type="button" id="cancelBtn" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <input type="hidden" name="update_profile" value="1">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-800 text-white rounded-md hover:bg-indigo-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if image exists and set placeholder accordingly
        const profileImageDisplay = document.getElementById('profileImageDisplay');
        const profileImagePlaceholder = document.getElementById('profileImagePlaceholder');
        
        profileImageDisplay.onload = function() {
            this.style.display = 'block';
            profileImagePlaceholder.style.display = 'none';
        };
        
        const editProfileImage = document.getElementById('editProfileImage');
        const editProfilePlaceholder = document.getElementById('editProfilePlaceholder');
        
        editProfileImage.onload = function() {
            this.style.display = 'block';
            editProfilePlaceholder.style.display = 'none';
        };
        
        // DOM Elements
        const profileView = document.getElementById('profileView');
        const editProfileForm = document.getElementById('editProfileForm');
        const editProfileBtn = document.getElementById('editProfileBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        // Show Edit Profile Form
        editProfileBtn.addEventListener('click', () => {
            profileView.classList.add('hidden');
            editProfileForm.classList.remove('hidden');
        });

        // Cancel Edit
        cancelBtn.addEventListener('click', () => {
            profileView.classList.remove('hidden');
            editProfileForm.classList.add('hidden');
        });
        
        // Image preview for file upload
        const photoInput = document.getElementById('photoInput');
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update both profile images (edit form and view)
                    editProfileImage.src = e.target.result;
                    editProfileImage.style.display = 'block';
                    editProfilePlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    });
    </script>
</body>
</html>