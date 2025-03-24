<!DOCTYPE html>
<html>
<head>
    <title>Add Administrator</title>
</head>
<body>
    <h2>Add New Administrator</h2>
    
    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="error"><?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
    <?php endif; ?>
    
    <form action="/admin/add-administrator.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="email">Email Address: *</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div>
            <label for="full_name">Full Name: *</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        
        <div>
            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number">
        </div>
        
        <div>
            <label for="role">Role: *</label>
            <select id="role" name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Administrator</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
                <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                <option value="superadmin">Super Administrator</option>
                <?php endif; ?>
            </select>
        </div>
        
        <div>
            <label for="profile_photo">Profile Photo:</label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png">
        </div>
        
        <button type="submit">Add Administrator</button>
    </form>
</body>
</html>