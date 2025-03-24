<?php
// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'];
$admin_email = $_SESSION['admin_email'];
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Connect to the database
$servername = "localhost";
$dbname = 'victosah';
$username = 'root';
$dbpassword = '';

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

// Set profile photo path with fallback to default if not available
$profile_photo = "../assets/home/user.svg"; // Default image
if (!empty($admin['profile_photo'])) {
    $photo_path = "../../uploads/profiles/" . $admin['profile_photo'];
    // Check if file exists
    if (file_exists($photo_path)) {
        $profile_photo = $photo_path;
    }
}
?>
    
<!-- ========================  The header  starts ======================== -->
<header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
    <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

        <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
            <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                <img src="../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                <h1 class="hidden md:block text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
            </a>

            <img onclick="toggleNav()" src="../assets/home/menu.svg" alt="Search" class="w-[28px] cursor-pointer" />
            <h1 class="hidden md:block text-[16px] md:text-[20px] font-Onest font-semibold">Orders</h1>
        </div>


        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 border-[1px] border-[#F3F3F3] rounded-[25px] p-2">
                <img src="../assets/global/search-normal.svg" alt="Search" class="w-[18px]" />
                <input type="text" placeholder="Search name, Order ID..." class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
            </div>

        </div>
        <div class="flex items-center gap-6 md:bg-[#F3F3F3] rounded-[4px] py-1 px-4">

            <span class="cursor-pointer relative" onclick="openNotification()">
                <img src="../assets/global/bell.svg" class="w-[18px] md:w-[20px]" alt="bag" />
                <div class="w-[8px] h-[8px] bg-[#1A237E] rounded-full absolute top-[-.1rem] left-3"></div>
            </span>

           
            <a href="./settings/profile.php" class="flex items-center gap-2 cursor-pointer">
                <div class="w-[40px] h-[40px] md:w-[44px] md:h-[44px] rounded-[50%] overflow-hidden bg-gray-100">
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture" class="w-full h-full object-cover" 
                         onerror="this.src='../assets/home/user.svg';" />
                </div>

                <div class="hidden md:block flex flex-col gap-0">
                    <p class="text-[15px] md:text-[16px] font-Onest font-medium text-[#262626]"><?php echo htmlspecialchars($admin_name); ?></p>
                    <p class="text-[14px] md:text-[16px] font-Onest font-regular text-[#5B5B5B]"><?php echo htmlspecialchars($admin_email); ?></p>
                </div>
            </a>
        </div>
    </nav>

    <!-- The dropdowns -->
    <div id="notification" class="p-3 notification-content shadow-md bg-white rounded-[4px]">
        <!-- Notification content remains the same -->
        <div class="flex flex-col gap-2">
            <!-- Notification header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <h1 class="text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Notificatons</h1>
                    <div class="flex items-center justify-center bg-[#1A237E] w-[20px] h-[20px] rounded-[50%]">
                        <h1 class="text-white text-[11px] md:text-[12px] font-['Open Sans'] font-medium">9</h1>
                    </div>
                </div>
                <a href="./notifications.php" class="text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-[#1A237E]">See all</a>
            </div>

            <!-- Notification content - remaining content stays the same -->
            <div class="flex flex-col gap-2 h-[78vh] md:h-[75vh] overflow-y-auto">
                <!-- Notification items - all remaining content -->
                <!-- I'm keeping this part unchanged -->
            </div>
        </div>
    </div>
</header>
<!-- ========================  The header  ends ======================== -->