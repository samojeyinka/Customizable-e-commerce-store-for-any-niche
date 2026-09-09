<?php
session_start();
include('../../config/connect.php');
require_once "../../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: ../index.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'];
$admin_email = $_SESSION['admin_email'];
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Connect to the database for admin info
require_once "../../config/servername.php";
$admin_conn = db();

// Fetch admin details
$sql = "SELECT * FROM administrators WHERE admin_id = ?";
$stmt = $admin_conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin = $admin_result->fetch_assoc();

// Close the admin database connection


// Set profile photo path with fallback to default if not available
$profile_photo = "../assets/home/user.svg"; // Default image
if (!empty($admin['profile_photo'])) {
    $photo_path = "../../uploads/profiles/" . $admin['profile_photo'];
    // Check if file exists
    if (file_exists($photo_path)) {
        $profile_photo = $photo_path;
    }
}

// Process notification status updates
if (isset($_GET['notification_action']) && isset($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    $action = $_GET['notification_action'];
    
    if ($action === 'read') {
        // Mark as read
        $update_query = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
    } else if ($action === 'unread') {
        // Mark as unread
        $update_query = "UPDATE notifications SET is_read = 0 WHERE notification_id = ?";
    }
    
    if (isset($update_query)) {
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $notification_id);
        mysqli_stmt_execute($stmt);
        
        // Redirect back to remove GET parameters
        $redirect_url = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string
        header("Location: $redirect_url");
        exit;
    }
}

// Include notifications functions if not already included
require_once __DIR__ . '/../../includes/notifications.php';

// Get unread count
$unread_count = get_unread_count($con, true);

// Get latest notifications for dropdown
$latest_notifications = get_notifications($con, true, null, 5, 0);

// Get filter parameters from URL
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination variables
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $per_page;

// Process category deletion if requested
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];

    // Get image filename before deleting
    $select_image = "SELECT category_image FROM categories WHERE category_id = '$category_id'";
    $image_result = mysqli_query($con, $select_image);
    if ($image_result && mysqli_num_rows($image_result) > 0) {
        $row = mysqli_fetch_assoc($image_result);
        if (!empty($row['category_image'])) {
            $image_path = "../../assets/categories/" . $row['category_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    // Delete category
    $delete_query = "DELETE FROM categories WHERE category_id = '$category_id'";
    $result_delete = mysqli_query($con, $delete_query);
    if ($result_delete) {
        echo "<script>alert('Category deleted successfully!');</script>";
        echo "<script>window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error deleting category: " . mysqli_error($con) . "');</script>";
    }
}

// Process export to Excel
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="products_export_'.date('Y-m-d').'.xls"');
    
    // Fetch all products without pagination for export
    $export_query = buildProductQuery($con, '', '', '', '', true);
    $export_result = mysqli_query($con, $export_query);
    
    echo "<table border='1'>";
    echo "<tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Quantity</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date Added</th>
          </tr>";
    
    while ($product = mysqli_fetch_assoc($export_result)) {
        $status_text = getStatusText($product['total_quantity']);
        echo "<tr>
                <td>".htmlspecialchars($product['product_name'])."</td>
                <td>".htmlspecialchars($product['sku'])."</td>
                <td>".number_format((float)$product['total_quantity'])."</td>
                <td>".htmlspecialchars($product['category_title'])."</td>
                <td>₦".number_format((float)$product['min_price'])."</td>
                <td>".$status_text."</td>
                <td>".date('d/m/Y h:i a', strtotime($product['date_added']))."</td>
              </tr>";
    }
    echo "</table>";
    exit;
}

// Function to build product query with filters
function buildProductQuery($con, $category_filter, $status_filter, $date_filter, $search_query, $all = false) {
    $query = "
        SELECT 
p.product_id,
            p.product_name,
            p.product_slug,
            p.sku,
            p.date_added,
            c.category_title,
            i.image_path AS main_image,
            (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) AS total_quantity,
            (SELECT MIN(original_price) FROM product_variants WHERE product_id = p.product_id) AS min_price
        FROM 
            products p
        LEFT JOIN 
            categories c ON p.category_id = c.category_id
        LEFT JOIN 
            product_images i ON p.product_id = i.product_id AND i.is_main = 1
        WHERE 1=1";
    
    // Apply category filter
    if (!empty($category_filter)) {
        $category_filter = mysqli_real_escape_string($con, $category_filter);
        $query .= " AND c.category_title = '$category_filter'";
    }
    
    // Apply status filter
    if (!empty($status_filter)) {
        switch ($status_filter) {
            case 'inStock':
                $query .= " AND (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) > 0";
                break;
            case 'outOfStock':
                $query .= " AND (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) <= 0";
                break;
            case 'lowStock':
                $query .= " AND (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) BETWEEN 1 AND 9";
                break;
        }
    }
    
    // Apply date filter
    if (!empty($date_filter)) {
        switch ($date_filter) {
            case 'today':
                $query .= " AND DATE(p.date_added) = CURDATE()";
                break;
            case 'last7':
                $query .= " AND p.date_added >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last28':
                $query .= " AND p.date_added >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
                break;
        }
    }
    
    // Apply search filter
    if (!empty($search_query)) {
        $search_query = mysqli_real_escape_string($con, $search_query);
        $query .= " AND (p.product_name LIKE '%$search_query%' OR p.sku LIKE '%$search_query%')";
    }
    
    $query .= " ORDER BY p.date_added DESC";
    
    // Add pagination if not exporting all
    if (!$all) {
        global $start, $per_page;
        $query .= " LIMIT $start, $per_page";
    }
    
    return $query;
}

// Function to get status text
function getStatusText($quantity) {
    if ($quantity <= 0) return "Out of Stock";
    if ($quantity < 10) return "Low Stock";
    return "In Stock";
}

// Get total products count for pagination
function getTotalProducts($con, $category_filter, $status_filter, $date_filter, $search_query) {
    $count_query = "
        SELECT COUNT(*) as total 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE 1=1";
    
    if (!empty($category_filter)) {
        $category_filter = mysqli_real_escape_string($con, $category_filter);
        $count_query .= " AND c.category_title = '$category_filter'";
    }
    
    if (!empty($status_filter)) {
        switch ($status_filter) {
            case 'inStock':
                $count_query .= " AND (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) > 0";
                break;
            case 'outOfStock':
                $count_query .= " AND (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) <= 0";
                break;
            case 'lowStock':
                $count_query .= " AND (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) BETWEEN 1 AND 9";
                break;
        }
    }
    
    if (!empty($date_filter)) {
        switch ($date_filter) {
            case 'today':
                $count_query .= " AND DATE(p.date_added) = CURDATE()";
                break;
            case 'last7':
                $count_query .= " AND p.date_added >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last28':
                $count_query .= " AND p.date_added >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)";
                break;
        }
    }
    
    if (!empty($search_query)) {
        $search_query = mysqli_real_escape_string($con, $search_query);
        $count_query .= " AND (p.product_name LIKE '%$search_query%' OR p.sku LIKE '%$search_query%')";
    }
    
    $result = mysqli_query($con, $count_query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Get all categories for filter dropdown
$categories_query = "SELECT category_title FROM categories ORDER BY category_title";
$categories_result = mysqli_query($con, $categories_query);
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row['category_title'];
}

// Process product deletion if requested
if (isset($_GET['delete_product']) && is_numeric($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];
    
    // Start transaction
    mysqli_begin_transaction($con);
    try {
        // Delete variants
        $delete_variants = "DELETE FROM product_variants WHERE product_id = ?";
        $stmt = mysqli_prepare($con, $delete_variants);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        
        // Get images to delete files
        $get_images = "SELECT image_path FROM product_images WHERE product_id = ?";
        $stmt = mysqli_prepare($con, $get_images);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $image_result = mysqli_stmt_get_result($stmt);
        
        // Delete actual image files
        while ($image = mysqli_fetch_assoc($image_result)) {
            if (!is_external_image_url($image['image_path'])) {
                $image_path = "../../assets/products/" . $image['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // Delete images from database
        $delete_images = "DELETE FROM product_images WHERE product_id = ?";
        $stmt = mysqli_prepare($con, $delete_images);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        
        // Delete the product
        $delete_product = "DELETE FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($con, $delete_product);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        
        // Commit the transaction
        mysqli_commit($con);
        
        echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        mysqli_rollback($con);
        echo "<script>alert('Error deleting product: " . mysqli_error($con) . "');</script>";
    }
}

// Get total number of products for pagination
$total_products = getTotalProducts($con, $category_filter, $status_filter, $date_filter, $search_query);
$total_pages = ceil($total_products / $per_page);

// Build and execute the main product query
$query = buildProductQuery($con, $category_filter, $status_filter, $date_filter, $search_query);
$result = mysqli_query($con, $query);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<title>Document</title>

<?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>


<body class="relative">


<header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
    <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

        <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
            <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
            </a>

            <i class="fa-solid fa-bars text-[24px] cursor-pointer" onclick="toggleNav()" alt="Search"></i>
            <h1 class="hidden md:block text-[16px] md:text-[20px] font-Onest font-semibold">Orders</h1>
        </div>


        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 border-[1px] border-[#F3F3F3] rounded-[25px] p-2">
                <i class="fa-solid fa-magnifying-glass text-[18px]" alt="Search"></i>
                <input type="text" placeholder="Search name, Order ID..." class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
            </div>

        </div>
        <div class="flex items-center gap-6 md:bg-[#F3F3F3] rounded-[4px] py-1 px-4">

            <span class="cursor-pointer relative" onclick="openNotification()">
                <i class="fa-regular fa-bell text-[20px]" alt="bag"></i>
                <div class="w-[8px] h-[8px] bg-[#C2185B] rounded-full absolute top-[-.1rem] left-3"></div>
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

<div class="flex items-center justify-between">

<div class="flex items-center gap-1">
    <h1 class="text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Notifications</h1>
    <?php if ($unread_count > 0): ?>
        <div class="flex items-center justify-center bg-[<?php echo store_color('color_primary'); ?>] w-[20px] h-[20px] rounded-[50%]">
            <h1 class="text-white text-[11px] md:text-[12px] font-['Open Sans'] font-medium">
                <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
            </h1>
        </div>
    <?php endif; ?>
</div>

    <a href="./notifications.php" class="text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-[<?php echo store_color('color_primary'); ?>]">See all</a>


</div>

<div class="flex flex-col gap-2 h-[78vh] md:h-[75vh] overflow-y-auto">
    <div class="flex flex-col gap-2">

    <?php if (empty($latest_notifications)): ?>
                <div class="p-4 text-center text-[#6B7280]">No notifications</div>
            <?php else: ?>

                <?php foreach ($latest_notifications as $notification): ?>
        <div class="w-full flex flex-col gap-2 rounded-[4px] <?php echo $notification['is_read'] ? '' : 'bg-[#EEEEEE]'; ?>  p-2">
            <div class="flex items-center justify-between">
                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]"><?php echo htmlspecialchars($notification['title']); ?></h1>
                <div class="relative flex items-center gap-2">
                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"> <?php 
                                    $created_at = new DateTime($notification['created_at']);
                                    echo $created_at->format('d M, Y h:i A'); 
                                    ?></span>
                </div>
            </div>
            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><?php echo htmlspecialchars($notification['message']); ?></span>
        </div>
        <?php endforeach; ?>
                <div class="p-2 text-center">
                    <a href="notifications.php" class="text-[14px] text-blue-600 hover:text-blue-800">View all notifications</a>
                </div>
            <?php endif; ?>

    </div>
</div>
</div>
    </div>
    </div>
</header>
<?php

include(__DIR__ . "/sidebar.php");
?>

    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA] mt-20">

        <div class="w-full flex flex-col md:flex-row justify-between md:items-center">
            <div class="w-full md:w-[fit-content] rounded-[16px] bg-white mx-auto md:mx-0 p-2">
                <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Products</h1>

                <div id="myBtn" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                    <h1 class="text-[16px] font-Onest font-regular">Product Overview</h1>
<i class="fa-solid fa-ellipsis-vertical text-[20px]"></i>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <div class="flex  items-center gap-3">
                    <button id="openCategory" class="ml-4 md:ml-0 w-[fit-content] text-[#C2185B] shrink-0 flex items-center gap-2 px-4 py-2 border-[1px] border-[#C2185B] rounded-lg cursor-pointer">
                        Add Category
                    </button>
                    <button id="openBrand" class="ml-4 md:ml-0 w-[fit-content] text-[#C2185B] shrink-0 flex items-center gap-2 px-4 py-2 border-[1px] border-[#C2185B] rounded-lg cursor-pointer">
                        Add Tag
                    </button>
                </div>
                <a href="./new-product.php" class="ml-4 md:ml-0 w-[fit-content] shrink-0 flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
<i class="fa-solid fa-plus text-[20px]"></i>
                    Add New Product
                </a>
            </div>
        </div>



        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
          <div class="w-full rounded-[16px] bg-white mx-auto p-3">
    <form method="get" action="products.php" class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
        <div class="flex items-center gap-0">
            <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                <i class="fa-solid fa-magnifying-glass text-[18px]" alt="Search"></i>
                <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
            </div>
        </div>

        <div class="w-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filter by:</span>
                <i class="fa-solid fa-filter md:hidden"></i>

                <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo !empty($category_filter) ? $category_filter : 'Category'; ?></span>
                            <i class="fa-solid fa-chevron-down arrow-down"></i>
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="setFilter('category', '')">All Categories</div>
                                    <?php foreach ($categories as $category): ?>
                                        <div onclick="setFilter('category', '<?php echo htmlspecialchars($category); ?>')"><?php echo htmlspecialchars($category); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                <?php 
                                    switch($status_filter) {
                                        case 'inStock': echo 'In Stock'; break;
                                        case 'outOfStock': echo 'Out of Stock'; break;
                                        case 'lowStock': echo 'Low Stock'; break;
                                        default: echo 'Status';
                                    }
                                ?>
                            </span>
                            <i class="fa-solid fa-chevron-down arrow-down"></i>
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="setFilter('status', '')">All Status</div>
                                    <div onclick="setFilter('status', 'inStock')">In Stock</div>
                                    <div onclick="setFilter('status', 'outOfStock')">Out of Stock</div>
                                    <div onclick="setFilter('status', 'lowStock')">Low Stock</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                <?php 
                                    switch($date_filter) {
                                        case 'today': echo 'Today'; break;
                                        case 'last7': echo 'Last 7 days'; break;
                                        case 'last28': echo 'Last 28 days'; break;
                                        default: echo 'Date';
                                    }
                                ?>
                            </span>
                            <i class="fa-solid fa-chevron-down arrow-down"></i>
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="setFilter('date', '')">All Time</div>
                                    <div onclick="setFilter('date', 'today')">Today</div>
                                    <div onclick="setFilter('date', 'last7')">Last 7 days</div>
                                    <div onclick="setFilter('date', 'last28')">Last 28 days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <i class="fa-solid fa-xmark text-[14px] text-[#262626]"></i>
                <a href="products.php" class="text-[#262626] text-[14px] font-Onest font-regular">Clear filter</a>
            </div>

            <a href="products.php?export=excel&category=<?php echo urlencode($category_filter); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>&search=<?php echo urlencode($search_query); ?>" class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                <i class="fa-solid fa-download text-[16px]"></i>
                Export
            </a>
        </div>
    </form>

    <!-- Rest of your table code remains the same -->
</div>
            <div class=" overflow-x-auto mt-3">
                <table cols="" class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                            <th class="text-nowrap p-2 flex items-center gap-2">
                                <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Product</span>
                            </th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">SKU</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Quantity</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Category</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                                <i class="fa-solid fa-table-columns text-[20px]"></i>
                            </th>
                        </thead>

            <tbody>
    <?php
    // Check if we have products from the filtered query
    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            // Determine stock status
            $stock_status = "available";
            $status_class = "bg-[#D51E5E]";
            $status_text = "In Stock";
            
            if ($product['total_quantity'] <= 0) {
                $stock_status = "outOfStock";
                $status_class = "bg-[#262626]";
                $status_text = "Out of Stock";
            } elseif ($product['total_quantity'] < 10) {
                $stock_status = "lowStock";
                $status_class = "bg-[#E8B006]";
                $status_text = "Low Stock";
            }
            
            // Format the date
            $date_added = new DateTime($product['date_added']);
            $formatted_date = $date_added->format('d/m/Y h:i a');
    ?>
    <tr>
        <td class="flex items-center gap-[10px] p-3">
            <div class="flex items-center gap-2">
                <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                    <?php if (!empty($product['main_image'])): ?>
                        <?php if (is_external_image_url($product['main_image']) || file_exists("../../assets/products/" . $product['main_image'])): ?>
                            <img src="<?php echo htmlspecialchars(product_image_url($product['main_image'], '../../assets/products/')); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                        <?php else: ?>
                            <img src="../assets/dash/product.svg" class="w-full h-full" alt="Default Product" />
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="../assets/dash/product.svg" class="w-full h-full" alt="Default Product" />
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-[4px]">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']"><?php echo htmlspecialchars($product['product_name']); ?></span>
                </div>
            </div>
        </td>
        <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($product['sku']); ?></td>
        <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo number_format((float)$product['total_quantity']); ?></td>
        <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2"><?php echo htmlspecialchars($product['category_title']); ?></td>
        <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦<?php echo number_format((float)$product['min_price']); ?></td>

        <td>
            <button type="button" class="py-1 px-4 text-nowrap <?php echo $status_class; ?> text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px] <?php if ($stock_status !== 'available') echo 'text-nowrap'; ?>">
                <?php echo $status_text; ?>
            </button>
        </td>

        <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-nowrap"><?php echo $formatted_date; ?></td>

        <td class="relative">
            <i class="fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer" onclick="openOrdermenu(this)"></i>

            <!-- Order Menu (specific to this row) -->
            <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                <div class="flex flex-col gap-3">
                    <a href="<?php echo product_url($product); ?>" class="text-[16px] font-medium text-[#262626]">View Details</a>
                    <a href="./reviews.php?product_id=<?php echo $product['product_id']; ?>" class="text-[16px] font-medium text-[#262626]">View Review</a>
                    <a href="./edit-product.php?id=<?php echo $product['product_id']; ?>" class="text-[16px] font-medium text-[#262626]">Edit</a>
                    <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>')" class="text-[16px] font-medium text-[#D93939]">Delete</a>
                </div>
            </div>
        </td>
    </tr>
    <?php
        }
    } else {
        // No products found - show message based on whether filters are active
        $filter_active = !empty($category_filter) || !empty($status_filter) || !empty($date_filter) || !empty($search_query);
        
        if ($filter_active) {
            echo '<tr><td colspan="8" class="text-center py-4 text-gray-500">No products found matching your filters</td></tr>';
        } else {
            echo '<tr><td colspan="8" class="text-center py-4 text-gray-500">No products found</td></tr>';
        }
    }
    ?>
</tbody>
<script>
    function confirmDelete(productId, productName) {
        if (confirm('Are you sure you want to delete "' + productName + '"? This action cannot be undone.')) {
            window.location.href = 'products.php?delete_product=' + productId;
        }
    }
</script>
                </table>
            </div>

        </div>

       <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">
        Showing <?php echo min($per_page, $total_products - $start); ?> results from <?php echo $total_products; ?>
    </span>
    <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
        <?php if ($page > 1): ?>
            <div class="flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-chevron-left text-[12px]"></i>
                <a href="products.php?page=<?php echo $page-1; ?>&category=<?php echo urlencode($category_filter); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>&search=<?php echo urlencode($search_query); ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</a>
            </div>
        <?php endif; ?>

        <div class="w-full flex items-center justify-between md:gap-6">
            <?php
            // Show first page
            if ($page > 3) {
                echo '<a href="products.php?page=1&category='.urlencode($category_filter).'&status='.urlencode($status_filter).'&date='.urlencode($date_filter).'&search='.urlencode($search_query).'" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">1</a>';
                if ($page > 4) echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
            }
            
            // Show pages around current page
            for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
                if ($i == $page) {
                    echo '<span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#C2185B]">'.$i.'</span>';
                } else {
                    echo '<a href="products.php?page='.$i.'&category='.urlencode($category_filter).'&status='.urlencode($status_filter).'&date='.urlencode($date_filter).'&search='.urlencode($search_query).'" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">'.$i.'</a>';
                }
            }
            
            // Show last page
            if ($page < $total_pages - 2) {
                if ($page < $total_pages - 3) echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                echo '<a href="products.php?page='.$total_pages.'&category='.urlencode($category_filter).'&status='.urlencode($status_filter).'&date='.urlencode($date_filter).'&search='.urlencode($search_query).'" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">'.$total_pages.'</a>';
            }
            ?>
        </div>

        <?php if ($page < $total_pages): ?>
            <div class="flex items-center gap-2 cursor-pointer">
                <a href="products.php?page=<?php echo $page+1; ?>&category=<?php echo urlencode($category_filter); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>&search=<?php echo urlencode($search_query); ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</a>
                <i class="fa-solid fa-chevron-right text-[12px]"></i>
            </div>
        <?php endif; ?>
    </div>
</div>

    </div>
    </div>



    <!-- The modals starts -->
    <div id="myModal" class="modal reg">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
        <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Product Overview</h1>
        <i class="fa-solid fa-xmark text-[24px] cursor-pointer absolute top-4 right-4" id="closmyModalhere" alt="close"></i>

        <div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-4 mt-2">
            <?php
            // Get product statistics from database
            $stats_query = "SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN (SELECT SUM(quantity) FROM product_variants WHERE product_id = products.product_id) <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN (SELECT SUM(quantity) FROM product_variants WHERE product_id = products.product_id) > 0 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN (SELECT SUM(quantity) FROM product_variants WHERE product_id = products.product_id) BETWEEN 1 AND 9 THEN 1 ELSE 0 END) as low_stock,
                (SELECT COUNT(*) FROM products WHERE date_added >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)) as new_last_28_days,
                (SELECT COUNT(*) FROM products WHERE date_added >= DATE_SUB(CURDATE(), INTERVAL 56 DAY) AND date_added < DATE_SUB(CURDATE(), INTERVAL 28 DAY)) as previous_28_days
            FROM products";
            
            $stats_result = mysqli_query($con, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            
            // Calculate percentage changes
            $total_change = $stats['new_last_28_days'] - $stats['previous_28_days'];
            $total_percentage = $stats['previous_28_days'] != 0 
                ? round(($total_change / $stats['previous_28_days']) * 100) 
                : 100;
            
            $out_of_stock_change = 0; // You would need to track this over time
            $out_of_stock_percentage = 12; // Example value - implement tracking
            
            $in_stock_change = 0; // You would need to track this over time
            $in_stock_percentage = 12; // Example value - implement tracking
            
            $low_stock_change = 0; // You would need to track this over time
            $low_stock_percentage = 12; // Example value - implement tracking
            ?>
            
            <!-- Total Products Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Products</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((float)$stats['total_products']); ?></h2>
                        <p class="text-[#C2185B] text-[15px] text-[17px] font-regular font-['Open Sans']">Listed items</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid <?php echo $total_change >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?> text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $total_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo abs($total_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Out-of-Stock Products Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Out-of-Stock Products</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((int)$stats['out_of_stock']); ?></h2>
                        <p class="text-[#C2185B] text-[15px] text-[17px] font-regular font-['Open Sans']">items need restocking</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid <?php echo $out_of_stock_change >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?> text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $out_of_stock_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo abs($out_of_stock_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- In-Stock Products Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">In-Stock Products</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((int)$stats['in_stock']); ?></h2>
                        <p class="text-[#C2185B] text-[15px] text-[17px] font-regular font-['Open Sans']">available for purchase</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid <?php echo $in_stock_change >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?> text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $in_stock_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo abs($in_stock_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Warnings Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                </div>
                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Low Stock Warnings</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#C2185B] text-[18px] text-[22px] font-medium font-['Open Sans']"><?php echo number_format((int)$stats['low_stock']); ?></h2>
                        <p class="text-[#C2185B] text-[14px] text-[15px] font-regular font-['Open Sans']">products have less than 10 items left</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid <?php echo $low_stock_change >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?> text-[20px]"></i>
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $low_stock_change >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo abs($low_stock_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php
    include('../../includes/admin/create_category.php');
    include('../../includes/admin/categories.php');
    include('../../includes/admin/brands.php');
    include('../../includes/admin/create_brand.php');
    ?>




    <!-- Edit Category Modal -->
    <div id="editCategory" class="modal editCategory">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Edit Category</h1>
            <i class="fa-solid fa-xmark text-[24px] cursor-pointer absolute top-4 right-4" id="closeEC" alt="close"></i>

            <form method="post" enctype="multipart/form-data">
                <div class="flex flex-col gap-4">
                    <!-- Category Name -->
                    <div class="flex flex-col gap-2">
                        <label class="font-[#2c2c2c] font-['Open Sans] text-[16px] font-medium">Category Name</label>
                        <input type="text" id="edit_cat_title" name="cat_title" placeholder="Enter category name" required class="p-2 placeholder:text-[#D9D9D9] border-[#E1E1E1] border-[1px] outline-none font-[#2c2c2c] font-['Open Sans] text-[16px]" />
                        <input type="hidden" id="edit_cat_id" name="edit_id" value="">
                    </div>

                    <!-- Image Upload -->
                    <div class="flex flex-col gap-2">
                        <label class="font-[#2c2c2c] font-['Open Sans] text-[16px] font-medium">Category Image</label>
                        <div class="flex flex-col gap-2">
                            <div class="w-full h-[120px] border-[1px] border-dashed border-[#E1E1E1] rounded-lg flex items-center justify-center relative">
                                <input type="file" name="edit_cat_image" id="edit_cat_image" accept="image/*" class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10" onchange="previewEditImage(this)" />
                                <div id="edit-upload-placeholder" class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-regular fa-folder-open text-[30px] mx-auto" alt="upload"></i>
                                    <span class="text-[14px] text-[#9A9A9A] font-['Open Sans']">Click to upload or drag and drop</span>
                                    <span class="text-[12px] text-[#9A9A9A] font-['Open Sans']">SVG, PNG, JPG or GIF (max. 2MB)</span>
                                </div>
                                <div id="edit-image-preview" class="hidden w-full h-full">
                                    <img id="edit-preview-img" src="#" alt="Preview" class="w-full h-full object-contain" />
                                </div>
                            </div>
                            <p class="text-[12px] text-[#9A9A9A] font-['Open Sans']">Leave empty to keep the current image</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full text-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit Brand Modal -->
    <div id="editBrand" class="modal editBrand">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Edit Brand</h1>
            <i class="fa-solid fa-xmark text-[24px] cursor-pointer absolute top-4 right-4" id="closeEB" alt="close"></i>

            <form method="post" enctype="multipart/form-data">
                <div class="flex flex-col gap-4">
                    <!-- Brand Name -->
                    <div class="flex flex-col gap-2">
                        <label class="font-[#2c2c2c] font-['Open Sans] text-[16px] font-medium">Brand Name</label>
                        <input type="text" id="edit_brand_title" name="brand_title" placeholder="Enter brand name" required class="p-2 placeholder:text-[#D9D9D9] border-[#E1E1E1] border-[1px] outline-none font-[#2c2c2c] font-['Open Sans] text-[16px]" />
                        <input type="hidden" id="edit_brand_id" name="edit_id" value="">
                    </div>

                    <!-- Image Upload -->
                    <div class="flex flex-col gap-2">
                        <label class="font-[#2c2c2c] font-['Open Sans] text-[16px] font-medium">Brand Image</label>
                        <div class="flex flex-col gap-2">
                            <div class="w-full h-[120px] border-[1px] border-dashed border-[#E1E1E1] rounded-lg flex items-center justify-center relative">
                                <input type="file" name="edit_brand_image" id="edit_brand_image" accept="image/*" class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10" onchange="previewEditBrandImage(this)" />
                                <div id="edit-brand-upload-placeholder" class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-regular fa-folder-open text-[30px] mx-auto" alt="upload"></i>
                                    <span class="text-[14px] text-[#9A9A9A] font-['Open Sans']">Click to upload or drag and drop</span>
                                    <span class="text-[12px] text-[#9A9A9A] font-['Open Sans']">SVG, PNG, JPG or GIF (max. 2MB)</span>
                                </div>
                                <div id="edit-brand-image-preview" class="hidden w-full h-full">
                                    <img id="edit-brand-preview-img" src="#" alt="Preview" class="w-full h-full object-contain" />
                                </div>
                            </div>
                            <p class="text-[12px] text-[#9A9A9A] font-['Open Sans']">Leave empty to keep the current image</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full text-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- The modals ends -->






    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>
    <script>
        var categoryLists = document.getElementById("categoryLists");
        var openCategoriesLists = document.getElementById("openCategoriesLists");
        var closeCategoriesLists = document.getElementById("closeCategoriesLists");


        openCategoriesLists.onclick = function() {
            categoryLists.style.display = "block";
            createCategory.style.display = "none";
        }


        closeCategoriesLists.onclick = function() {
            categoryLists.style.display = "none";

        }


        var editModal = document.getElementById("editCategory");

        closeEC.onclick = function() {
            editModal.style.display = "none";

        }


        // Function to open edit category modal
        function openEditCategory(categoryId, categoryTitle) {
            var editModal = document.getElementById("editCategory");
            var titleInput = document.getElementById("edit_cat_title");
            var idInput = document.getElementById("edit_cat_id");

            // Set the values
            titleInput.value = categoryTitle;
            idInput.value = categoryId;

            // Show the modal
            editModal.style.display = "block";
            categoryLists.style.display = "none";

            // Close any open dropdown menus
            var dropdowns = document.getElementsByClassName("ordermenu-content");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].style.display = "none";
            }
        }



        // Image preview functionality for edit form
        function previewEditImage(input) {
            const uploadPlaceholder = document.getElementById('edit-upload-placeholder');
            const imagePreview = document.getElementById('edit-image-preview');
            const previewImg = document.getElementById('edit-preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    uploadPlaceholder.classList.add('hidden');
                    imagePreview.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function to reset edit form
        function resetEditForm() {
            const form = document.querySelector('#editCategory form');
            const uploadPlaceholder = document.getElementById('edit-upload-placeholder');
            const imagePreview = document.getElementById('edit-image-preview');

            form.reset();
            uploadPlaceholder.classList.remove('hidden');
            imagePreview.classList.add('hidden');
        }




        //brand 

        var createBrand = document.getElementById("createBrand");
        var openBrand = document.getElementById("openBrand");
        var closeCB = document.getElementById("closeCB");




        openBrand.onclick = function() {
            createBrand.style.display = "block";
        }


        closeCB.onclick = function() {
            createBrand.style.display = "none";
        }




        // for brands



        //brands      
        var openBrandsLists = document.getElementById("openBrandsLists");
        var brandLists = document.getElementById("brandLists");
        var closeBrandLists = document.getElementById("closeBrandLists");


        openBrandsLists.onclick = function() {
            brandLists.style.display = "block";
            createBrand.style.display = "none";
        }


        closeBrandLists.onclick = function() {
            brandLists.style.display = "none";

        }



        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set up Open Brand Modal button
            var openBrandBtn = document.getElementById("openBrand");
            var createBrandModal = document.getElementById("createBrand");

            if (openBrandBtn && createBrandModal) {
                openBrandBtn.onclick = function() {
                    createBrandModal.style.display = "block";

                }
            }

            // Edit Brand Modal
            var editBrandModal = document.getElementById("editBrand");
            var closeEditBtn = document.getElementById("closeEB");

            // Close Edit Modal
            if (closeEditBtn) {
                closeEditBtn.onclick = function() {
                    editBrandModal.style.display = "none";

                }
            }

            // Close when clicking outside
            window.onclick = function(event) {
                if (event.target == editBrandModal) {
                    editBrandModal.style.display = "none";
                    resetEditBrandForm();
                }
                if (event.target == createBrandModal) {
                    createBrandModal.style.display = "none";
                }
            }
        });

        // Image preview functionality for edit form
        function previewEditBrandImage(input) {
            const uploadPlaceholder = document.getElementById('edit-brand-upload-placeholder');
            const imagePreview = document.getElementById('edit-brand-image-preview');
            const previewImg = document.getElementById('edit-brand-preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    uploadPlaceholder.classList.add('hidden');
                    imagePreview.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function to reset edit form
        function resetEditBrandForm() {
            const form = document.querySelector('#editBrand form');
            const uploadPlaceholder = document.getElementById('edit-brand-upload-placeholder');
            const imagePreview = document.getElementById('edit-brand-image-preview');

            form.reset();
            uploadPlaceholder.classList.remove('hidden');
            imagePreview.classList.add('hidden');
        }

        // Function to open edit brand modal with image
        function openEditBrand(brandId, brandTitle, brandImage) {
            var editModal = document.getElementById("editBrand");
            var titleInput = document.getElementById("edit_brand_title");
            var idInput = document.getElementById("edit_brand_id");
            var uploadPlaceholder = document.getElementById('edit-brand-upload-placeholder');
            var imagePreview = document.getElementById('edit-brand-image-preview');
            var previewImg = document.getElementById('edit-brand-preview-img');
            var brandLists = document.getElementById("brandLists");

            // Set the values
            titleInput.value = brandTitle;
            idInput.value = brandId;

            // Show existing image if available
            if (brandImage && brandImage !== '') {
                previewImg.src = '../../assets/brands/' + brandImage;
                uploadPlaceholder.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            } else {
                uploadPlaceholder.classList.remove('hidden');
                imagePreview.classList.add('hidden');
            }

            // Show the modal
            editModal.style.display = "block";
            brandLists.style.display = "none";

            // Close any open dropdown menus
            var dropdowns = document.getElementsByClassName("ordermenu-content");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].style.display = "none";
            }
        }



    function confirmDelete(productId, productName) {
        if (confirm('Are you sure you want to delete "' + productName + '"? This action cannot be undone.')) {
            window.location.href = 'products.php?delete_product=' + productId;
        }
    }



    function setFilter(type, value) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    // Update the specific filter
    if (value) {
        params.set(type, value);
    } else {
        params.delete(type);
    }
    
    // Reset to page 1 when changing filters
    params.set('page', '1');
    
    // Submit the form
    window.location.href = 'products.php?' + params.toString();
}


const closmyModalhere = document.getElementById("closmyModalhere");
const mymodalhere = document.getElementById("myModal");

closmyModalhere.onclick = function () {
    mymodalhere.style.display = "none";
}


// Add this script to your page
document.addEventListener('DOMContentLoaded', function() {
    // Get the search input
    const searchInput = document.querySelector('input[type="text"]');
    const searchContainer = searchInput.closest('div');
    
    // Create suggestions container
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'bg-white border border-gray-200 rounded shadow-lg absolute left-0 right-0 z-50 hidden';
    suggestionsContainer.style.top = '60px'; // Position below header
    suggestionsContainer.style.maxWidth = '550px';
    suggestionsContainer.style.width = '100%';
    searchContainer.style.position = 'relative';
    searchContainer.appendChild(suggestionsContainer);
    
    // Listen for input in the search field
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (query.length === 0) {
            hideSuggestions();
            return;
        }
        
        // Display search suggestions based directly on what the user typed
        displaySuggestions(query);
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            hideSuggestions();
        }
    });
    
    // Function to display suggestions
    function displaySuggestions(query) {
        suggestionsContainer.innerHTML = '';
        
        // Define categories - now including Users
        const categories = [
            { 
                key: 'order', 
                title: `Order with ID: ${query}`,
                subtitle: 'Click to view order details',
                labelText: 'Order',
                labelClass: 'bg-green-100 text-green-800',
                url: './order-details.php?id='
            },
            { 
                key: 'reviews', 
                title: `Reviews with "${query}"`,
                subtitle: '',
                labelText: '',
                labelClass: '',
                url: './reviews.php?q='
            },
            { 
                key: 'review', 
                title: `Review with Product ID: ${query}`,
                subtitle: 'Click to view product reviews',
                labelText: 'Review',
                labelClass: 'bg-yellow-100 text-yellow-800',
                url: './reviews.php?product_id='
            },
            { 
                key: 'user', 
                title: `User with ID: ${query}`,
                subtitle: 'Click to view user details',
                labelText: 'User',
                labelClass: 'bg-blue-100 text-blue-800',
                url: './user-details.php?id='
            }
        ];
        
        // For each category, create a suggestion item with styling from the screenshot
        categories.forEach((category, index) => {
            const itemContainer = document.createElement('div');
            
            // Apply different styles based on category type
            if (category.key === 'reviews') {
                itemContainer.className = 'p-4 border-t border-b border-gray-200 bg-gray-50';
                itemContainer.innerHTML = `<div class="font-medium text-gray-700">${category.title}</div>`;
            } else {
                itemContainer.className = 'p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
                
                let labelHtml = '';
                if (category.labelText) {
                    labelHtml = `<span class="text-sm rounded-full px-3 py-1 ${category.labelClass}">${category.labelText}</span>`;
                }
                
                itemContainer.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">${category.title}</div>
                            <div class="text-sm text-gray-500">${category.subtitle}</div>
                        </div>
                        ${labelHtml}
                    </div>
                `;
                
                // Add click handler to navigate to the appropriate page
                itemContainer.addEventListener('click', function() {
                    window.location.href = `${category.url}${query}`;
                });
            }
            
            suggestionsContainer.appendChild(itemContainer);
        });
        
        // Show the suggestions container
        suggestionsContainer.classList.remove('hidden');
    }
    
    // Function to hide suggestions
    function hideSuggestions() {
        suggestionsContainer.classList.add('hidden');
    }
});
    </script>


</body>

</html>