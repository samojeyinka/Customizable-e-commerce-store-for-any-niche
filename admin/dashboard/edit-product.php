<?php
// Add this to the top of your edit-product.php file, replacing the existing PHP processing section

// Set upload limits
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '20M');
ini_set('max_file_uploads', '20');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');

// Include database connection
include('../../config/connect.php');

// Get product ID from URL parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No product ID provided!'); window.location.href='products.php';</script>";
    exit;
}

$product_id = $_GET['id'];

// Fetch existing product data
$product_query = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($con, $product_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$product_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($product_result) == 0) {
    echo "<script>alert('Product not found!'); window.location.href='products.php';</script>";
    exit;
}

$product = mysqli_fetch_assoc($product_result);

// Fetch product variants
$variants_query = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY variant_id";
$stmt = mysqli_prepare($con, $variants_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$variants_result = mysqli_stmt_get_result($stmt);
$variants = [];
while ($row = mysqli_fetch_assoc($variants_result)) {
    $variants[] = $row;
}

// Fetch product images
$images_query = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC, display_order ASC";
$stmt = mysqli_prepare($con, $images_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$images_result = mysqli_stmt_get_result($stmt);
$images = [];
$main_image = null;
while ($row = mysqli_fetch_assoc($images_result)) {
    if ($row['is_main'] == 1) {
        $main_image = $row;
    } else {
        $images[] = $row;
    }
}

// Process form submission for product update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_product') {
    // Get basic product information
    $product_name = $_POST['productName'];
    $category_id = $_POST['productCategory'];
    $brand_id = !empty($_POST['productBrand']) ? $_POST['productBrand'] : NULL;
    $sku = $_POST['sku'];
    $colors = $_POST['colors'];
    $is_featured = isset($_POST['isFeatured']) && $_POST['isFeatured'] == 'true' ? 1 : 0;

    // Get rich text content
    $details = $_POST['details'];
    $warranty = $_POST['warranty'];
    $care = $_POST['care'];

// Regenerate a unique slug from the product name
    $product_slug = generate_product_slug($product_name, $product_id);

    // Update product in database
    $update_product = "UPDATE products SET 
                        product_name = ?, 
                        product_slug = ?, 
                        category_id = ?, 
                        brand_id = ?, 
                        sku = ?, 
                        colors = ?, 
                        details = ?, 
                        warranty = ?, 
                        care = ?, 
                        is_featured = ? 
                       WHERE product_id = ?";

    $stmt = mysqli_prepare($con, $update_product);
    mysqli_stmt_bind_param($stmt, "ssissssssii", $product_name, $product_slug, $category_id, $brand_id, $sku, $colors, $details, $warranty, $care, $is_featured, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        // Process variants
        if (isset($_POST['variant_id']) && is_array($_POST['variant_id'])) {
            // Get existing variant IDs to track which ones to delete
            $existing_variants = [];
            $variant_query = "SELECT variant_id FROM product_variants WHERE product_id = ?";
            $stmt_variant_fetch = mysqli_prepare($con, $variant_query);
            mysqli_stmt_bind_param($stmt_variant_fetch, "i", $product_id);
            mysqli_stmt_execute($stmt_variant_fetch);
            $variant_result = mysqli_stmt_get_result($stmt_variant_fetch);
            while ($row = mysqli_fetch_assoc($variant_result)) {
                $existing_variants[] = $row['variant_id'];
            }

            // Process submitted variants
            $processed_variants = [];
            for ($i = 0; $i < count($_POST['variant_id']); $i++) {
                $variant_id = $_POST['variant_id'][$i];
                $size = trim($_POST['size'][$i]);
                $texture = trim($_POST['texture'][$i]);
                $original_amount = trim($_POST['originalAmount'][$i]);
                $discount_amount = !empty($_POST['discountAmount'][$i]) ? $_POST['discountAmount'][$i] : NULL;
                $quantity = trim($_POST['quantity'][$i]);
                $status = $_POST['status'][$i];

                // Skip empty variants - prevent creating empty variants
                if (empty($size) && empty($original_amount) && empty($quantity)) {
                    continue;
                }

                if ($variant_id > 0) {
                    // Update existing variant
                    $update_variant = "UPDATE product_variants SET 
                                        size = ?, 
                                        texture = ?, 
                                        original_price = ?, 
                                        discount_price = ?, 
                                        quantity = ?, 
                                        status = ? 
                                      WHERE variant_id = ? AND product_id = ?";
                    $stmt_variant = mysqli_prepare($con, $update_variant);
                    mysqli_stmt_bind_param($stmt_variant, "ssddisii", $size, $texture, $original_amount, $discount_amount, $quantity, $status, $variant_id, $product_id);
                    mysqli_stmt_execute($stmt_variant);
                    $processed_variants[] = $variant_id;
                } else {
                    // Add new variant (only if it's not empty)
                    $insert_variant = "INSERT INTO product_variants (product_id, size, texture, original_price, discount_price, quantity, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt_variant = mysqli_prepare($con, $insert_variant);
                    mysqli_stmt_bind_param($stmt_variant, "issddis", $product_id, $size, $texture, $original_amount, $discount_amount, $quantity, $status);
                    mysqli_stmt_execute($stmt_variant);
                    $new_variant_id = mysqli_insert_id($con);
                    if ($new_variant_id) {
                        $processed_variants[] = $new_variant_id;
                    }
                }
            }

            // Delete variants that weren't in the form submission
            foreach ($existing_variants as $existing_id) {
                if (!in_array($existing_id, $processed_variants)) {
                    $delete_variant = "DELETE FROM product_variants WHERE variant_id = ? AND product_id = ?";
                    $stmt_delete = mysqli_prepare($con, $delete_variant);
                    mysqli_stmt_bind_param($stmt_delete, "ii", $existing_id, $product_id);
                    mysqli_stmt_execute($stmt_delete);
                }
            }
        }

        // Process main image deletion if requested
        if (isset($_POST['delete_main_image']) && $_POST['delete_main_image'] == '1') {
            $get_main_image = "SELECT image_id, image_path FROM product_images WHERE product_id = ? AND is_main = 1";
            $stmt_get_main = mysqli_prepare($con, $get_main_image);
            mysqli_stmt_bind_param($stmt_get_main, "i", $product_id);
            mysqli_stmt_execute($stmt_get_main);
            $main_image_result = mysqli_stmt_get_result($stmt_get_main);
            
            if ($row = mysqli_fetch_assoc($main_image_result)) {
                $upload_dir = "../../assets/products/";

                // Delete physical file (only for locally stored images)
                if (!is_external_image_url($row['image_path'])) {
                    $file_path = $upload_dir . $row['image_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                
                // Delete database record
                $delete_main = "DELETE FROM product_images WHERE image_id = ?";
                $stmt_del_main = mysqli_prepare($con, $delete_main);
                mysqli_stmt_bind_param($stmt_del_main, "i", $row['image_id']);
                mysqli_stmt_execute($stmt_del_main);
            }
        }

        // Process main image - URL takes priority over file upload
        $main_image_url = isset($_POST['mainImageUrl']) ? trim($_POST['mainImageUrl']) : '';
        if (!empty($main_image_url) && is_external_image_url($main_image_url)) {
            // Check if there's an existing main image
            $check_main = "SELECT image_id, image_path FROM product_images WHERE product_id = ? AND is_main = 1";
            $stmt_check = mysqli_prepare($con, $check_main);
            mysqli_stmt_bind_param($stmt_check, "i", $product_id);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);

            if (mysqli_num_rows($result_check) > 0) {
                // Update existing main image
                $old_image = mysqli_fetch_assoc($result_check);
                if (!is_external_image_url($old_image['image_path'])) {
                    $old_path = "../../assets/products/" . $old_image['image_path'];
                    if (file_exists($old_path)) {
                        unlink($old_path); // Delete old image file
                    }
                }

                $update_image = "UPDATE product_images SET image_path = ? WHERE image_id = ?";
                $stmt_update = mysqli_prepare($con, $update_image);
                mysqli_stmt_bind_param($stmt_update, "si", $main_image_url, $old_image['image_id']);
                mysqli_stmt_execute($stmt_update);
            } else {
                // Insert new main image
                $insert_image = "INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 1, 1)";
                $stmt_image = mysqli_prepare($con, $insert_image);
                mysqli_stmt_bind_param($stmt_image, "is", $product_id, $main_image_url);
                mysqli_stmt_execute($stmt_image);
            }
        } elseif (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] == 0) {
            $upload_dir = "../../assets/products/";

            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = basename($_FILES["mainImage"]["name"]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = "product_" . $product_id . "_main_" . uniqid() . "." . $file_ext;
            $target_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES["mainImage"]["tmp_name"], $target_file)) {
                // Check if there's an existing main image
                $check_main = "SELECT image_id, image_path FROM product_images WHERE product_id = ? AND is_main = 1";
                $stmt_check = mysqli_prepare($con, $check_main);
                mysqli_stmt_bind_param($stmt_check, "i", $product_id);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);

                if (mysqli_num_rows($result_check) > 0) {
                    // Update existing main image
                    $old_image = mysqli_fetch_assoc($result_check);
                    if (!is_external_image_url($old_image['image_path'])) {
                        $old_path = $upload_dir . $old_image['image_path'];
                        if (file_exists($old_path)) {
                            unlink($old_path); // Delete old image file
                        }
                    }

                    $update_image = "UPDATE product_images SET image_path = ? WHERE image_id = ?";
                    $stmt_update = mysqli_prepare($con, $update_image);
                    mysqli_stmt_bind_param($stmt_update, "si", $new_file_name, $old_image['image_id']);
                    mysqli_stmt_execute($stmt_update);
                } else {
                    // Insert new main image
                    $insert_image = "INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 1, 1)";
                    $stmt_image = mysqli_prepare($con, $insert_image);
                    mysqli_stmt_bind_param($stmt_image, "is", $product_id, $new_file_name);
                    mysqli_stmt_execute($stmt_image);
                }
            }
        }

        // Process other images
        $upload_dir = "../../assets/products/";

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Process image deletions first (independent of file upload)
        if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                // Delete selected images
                foreach ($_POST['delete_images'] as $img_id) {
                    $get_image = "SELECT image_path FROM product_images WHERE image_id = ? AND product_id = ? AND is_main = 0";
                    $stmt_get = mysqli_prepare($con, $get_image);
                    mysqli_stmt_bind_param($stmt_get, "ii", $img_id, $product_id);
                    mysqli_stmt_execute($stmt_get);
                    $get_result = mysqli_stmt_get_result($stmt_get);

                    if ($row = mysqli_fetch_assoc($get_result)) {
                        // Delete physical file (only for locally stored images)
                        if (!is_external_image_url($row['image_path'])) {
                            $file_path = $upload_dir . $row['image_path'];
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }

                        // Delete database record
                        $delete_img = "DELETE FROM product_images WHERE image_id = ? AND product_id = ?";
                        $stmt_del = mysqli_prepare($con, $delete_img);
                        mysqli_stmt_bind_param($stmt_del, "ii", $img_id, $product_id);
                        mysqli_stmt_execute($stmt_del);
                    }
                }
            }

        if (isset($_FILES['otherImages']) && is_array($_FILES['otherImages']['name'])) {
            // Count files
            $fileCount = count($_FILES['otherImages']['name']);

            // Loop through each file to add new ones
            for ($i = 0; $i < $fileCount; $i++) {
                // Skip empty entries
                if (empty($_FILES['otherImages']['name'][$i])) {
                    continue;
                }

                // Check file error
                if ($_FILES['otherImages']['error'][$i] !== 0) {
                    continue;
                }

                // Process the file
                $file_name = basename($_FILES['otherImages']['name'][$i]);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $new_file_name = "product_" . $product_id . "_" . uniqid() . "." . $file_ext;
                $target_file = $upload_dir . $new_file_name;

                // Try to move the file
                if (move_uploaded_file($_FILES['otherImages']['tmp_name'][$i], $target_file)) {
                    // Get the max display order for existing images
                    $order_query = "SELECT MAX(display_order) as max_order FROM product_images WHERE product_id = ?";
                    $stmt_order = mysqli_prepare($con, $order_query);
                    mysqli_stmt_bind_param($stmt_order, "i", $product_id);
                    mysqli_stmt_execute($stmt_order);
                    $order_result = mysqli_stmt_get_result($stmt_order);
                    $order_row = mysqli_fetch_assoc($order_result);
                    $max_order = $order_row['max_order'] ? $order_row['max_order'] + 1 : 2;

                    $insert_image = "INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 0, ?)";
                    $stmt_image = mysqli_prepare($con, $insert_image);
                    mysqli_stmt_bind_param($stmt_image, "isi", $product_id, $new_file_name, $max_order);
                    mysqli_stmt_execute($stmt_image);
                }
            }
        }

        // Process other image URLs
        if (!empty($_POST['otherImagesUrl'])) {
            product_image_urls_to_rows($con, $product_id, $_POST['otherImagesUrl']);
        }

        echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error updating product: " . mysqli_error($con) . "');</script>";
    }
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<title>Update Product</title>
    <?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<title>Update Product</title>
    <?php include '../tailwind-components.php'; ?>
</head>

<body>

    <!-- ========================  The header  starts ======================== -->
    <header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
        <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

            <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
                <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                    <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
                </a>

                <i class="fa-solid fa-bars text-[24px] cursor-pointer" onclick="toggleNav()" alt="Search"></i>
                <h1 class="hidden md:block  text-[16px] md:text-[20px] font-Onest font-semibold">Products</h1>
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

                <div class="flex items-center gap-2 cursor-pointer">
                    <div class="w-[40px] h-[40px] md:w-[44px] md:h-[44px] rounded-[50%]">
                        <i class="fa-solid fa-user text-[26px]" alt="Profile Picture"></i>
                    </div>

                    <div class="hidden md:block  flex flex-col gap-0">
                        <p class="text-[15px] md:text-[16px] font-Onest font-medium text-[#262626]">John Paul</p>
                        <p class="text-[14px] md:text-[16px] font-Onest font-regular text-[#5B5B5B]">johnpaul111@gmail.com</p>

                    </div>
                </div>

            </div>
        </nav>

        <!-- The dropdowns -->
        <div id="notification" class="p-3 notification-content shadow-md bg-white rounded-[4px]">
            <!-- Notification content here -->
        </div>
    </header>
    <!-- ========================  The header  ends ======================== -->

    <div id="mySidenav" class="sidenav p-2 hidden md:flex flex-col justify-between gap-2">
        <div class="flex flex-col gap-2">
            <a href="#" class="nav-link flex items-center gap-3" onclick="setActive(this)"><i class="fa-solid fa-layer-group activeicon text-[#FBFBFB]"></i> <i class="fa-solid fa-layer-group nonactiveicon text-[#ADAFCF]"></i><span>Overview</span></a>
            <a href="./products.php" class="nav-link active flex items-center gap-3" onclick="setActive(this)"><i class="fa-solid fa-box activeicon text-[#FBFBFB] text-[20px]"></i> <i class="fa-solid fa-box nonactiveicon text-[#ADAFCF] text-[20px]"></i><span>Products</span></a>
            <a href="./orders.php" class="nav-link flex items-center gap-3" onclick="setActive(this)"><i class="fa-solid fa-bag-shopping activeicon text-[#FBFBFB] text-[20px]"></i> <i class="fa-solid fa-bag-shopping nonactiveicon text-[#ADAFCF] text-[20px]"></i><span>Orders</span></a>
            <a href="./users.php" class="nav-link flex items-center gap-3" onclick="setActive(this)"><i class="fa-solid fa-user activeicon text-[#FBFBFB] text-[20px]"></i> <i class="fa-solid fa-user nonactiveicon text-[#ADAFCF] text-[20px]"></i><span>Users</span></a>
            <a href="./transactions.php" class="nav-link flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/receipt-minus (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/receipt-minus.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Transactions</span></a>
        </div>

        <div class="flex flex-col gap-2 mb-7">
            <a href="./settings.php" class="nav-link flex items-center gap-3" onclick="setActive(this)"><i class="fa-solid fa-gear activeicon text-[#FBFBFB] text-[20px]"></i> <i class="fa-solid fa-gear nonactiveicon text-[#ADAFCF] text-[20px]"></i><span>Settings</span></a>
            <span class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"><i class="fa-solid fa-right-from-bracket activeicon text-[20px] text-[#D93939]"></i> <i class="fa-solid fa-right-from-bracket nonactiveicon text-[20px] text-[#D93939]"></i><span class="text-[#D93939]">Logout</span></span>
        </div>
    </div>

    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <h1 class="text-[20px] font-Onest font-semibold mb-3">Update Product</h1>

            <!-- Navigation Tabs -->
            <div class="w-[fit-content] justify-between overflow-x-auto flex border-b border-[#DDDDDD] mb-6" id="tabs">
                <button data-tab="all" class="text-nowrap px-4 py-2 text-[#007F7F] border-b-2 border-[#007F7F] tab-button">About Product</button>
                <button data-tab="orders" class="text-nowrap px-4 py-2 text-gray-600 hover:text-gray-900 tab-button">Pricing, discount & Availability</button>
                <button data-tab="transactions" class="text-nowrap px-4 py-2 text-gray-600 hover:text-gray-900 tab-button">Media</button>
            </div>

    <form id="productForm" action="edit-product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="update_product">

        <!-- Tab Content 1: About Product -->
        <div id="all" class="tab-content active">
            <div class="w-full mx-auto bg-white rounded-lg shadow-sm">
                <div class="p-6 space-y-6 flex flex-col md:flex-row items-start gap-5">
                    <div class="w-full flex flex-col gap-3">
                        <div class="w-full flex flex-col md:flex-row items-center gap-3">
                            <!-- Product Name -->
                            <div class="w-full md:w-[50%]">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Product Name
                                </label>
                                <input
                                    type="text"
                                    name="productName"
                                    value="<?php echo htmlspecialchars($product['product_name']); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter product name" required>
                            </div>

                            <!-- Product Category -->
                            <div class="w-full md:w-[50%]">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Product Category
                                </label>
                                <select
                                    name="productCategory"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select</option>
                                    <?php
                                    // Fetch all categories from the database
                                    $select_categories = "SELECT * FROM categories ORDER BY category_title";
                                    $result_categories = mysqli_query($con, $select_categories);

                                    if ($result_categories && mysqli_num_rows($result_categories) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_categories)) {
                                            $selected = ($row['category_id'] == $product['category_id']) ? 'selected' : '';
                                            echo "<option value='" . $row['category_id'] . "' " . $selected . ">" . $row['category_title'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Colors Available -->
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Colors available
                            </label>
                            <input
                                type="text"
                                name="colors"
                                value="<?php echo htmlspecialchars($product['colors']); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter colors (comma separated)">
                        </div>

                        <div class="w-full flex flex-col md:flex-row items-center gap-3">
                            <!-- Brand -->
                            <div class="w-full md:w-[50%]">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Brand (Optional)
                                </label>
                                <select
                                    name="productBrand"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select</option>
                                    <?php
                                    // Fetch all brands from the database
                                    $select_brands = "SELECT * FROM brands ORDER BY brand_title";
                                    $result_brands = mysqli_query($con, $select_brands);

                                    if ($result_brands && mysqli_num_rows($result_brands) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_brands)) {
                                            $selected = ($row['brand_id'] == $product['brand_id']) ? 'selected' : '';
                                            echo "<option value='" . $row['brand_id'] . "' " . $selected . ">" . $row['brand_title'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- SKU -->
                            <div class="w-full md:w-[50%]">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    SKU
                                </label>
                                <input
                                    type="text"
                                    name="sku"
                                    value="<?php echo htmlspecialchars($product['sku']); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="#12345">
                            </div>
                        </div>

                        <!-- Featured Product Toggle -->
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-gray-700">
                                Set as Featured Product
                            </span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="featuredToggle" class="sr-only peer" <?php echo ($product['is_featured'] == 1) ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
                            </label>
                            <input type="hidden" name="isFeatured" value="<?php echo ($product['is_featured'] == 1) ? 'true' : 'false'; ?>">
                        </div>
                    </div>

                    <div class="w-full">
                        <!-- Rich Text Editor Fields -->
                        <div class="space-y-6">
                            <!-- Details -->
                            <div class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Details
                                </label>
                                <div class="border border-gray-300 rounded-md">
                                    <div class="bg-gray-50 p-2 border-b border-gray-300">
                                        <div class="flex gap-2 editor-toolbar" data-target="details-editor">
                                            <button type="button" data-command="bold" class="p-1 hover:bg-gray-200 rounded">
                                                <strong>B</strong>
                                            </button>
                                            <button type="button" data-command="italic" class="p-1 hover:bg-gray-200 rounded">
                                                <em>I</em>
                                            </button>
                                            <button type="button" data-command="underline" class="p-1 hover:bg-gray-200 rounded">
                                                <u>U</u>
                                            </button>
                                            <span class="border-l border-gray-300 mx-2"></span>
                                            <button type="button" data-command="fontSize" class="p-1 hover:bg-gray-200 rounded">
                                                A
                                            </button>
                                            <button type="button" data-command="createLink" class="p-1 hover:bg-gray-200 rounded">
                                                @
                                            </button>
                                            <span class="border-l border-gray-300 mx-2"></span>
                                            <button type="button" data-command="justifyLeft" class="p-1 hover:bg-gray-200 rounded"><i class="fa-solid fa-align-left text-[16px] leading-none"></i></button>
                                            <button type="button" data-command="justifyCenter" class="p-1 hover:bg-gray-200 rounded"><i class="fa-solid fa-align-center text-[16px] leading-none"></i></button>
                                        </div>
                                    </div>
                                    <div
                                        id="details-editor"
                                        class="w-full px-3 py-2 min-h-[100px] focus:outline-none"
                                        contenteditable="true"><?php echo $product['details']; ?></div>
                                    <input type="hidden" name="details" value="<?php echo htmlspecialchars($product['details']); ?>">
                                </div>
                            </div>

                            <!-- Warranty -->
                            <div class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Warranty
                                </label>
                                <div class="border border-gray-300 rounded-md">
                                    <div class="bg-gray-50 p-2 border-b border-gray-300">
                                        <div class="flex gap-2 editor-toolbar" data-target="warranty-editor">
                                            <button type="button" data-command="bold" class="p-1 hover:bg-gray-200 rounded">
                                                <strong>B</strong>
                                            </button>
                                            <button type="button" data-command="italic" class="p-1 hover:bg-gray-200 rounded">
                                                <em>I</em>
                                            </button>
                                            <button type="button" data-command="underline" class="p-1 hover:bg-gray-200 rounded">
                                                <u>U</u>
                                            </button>
                                            <span class="border-l border-gray-300 mx-2"></span>
                                            <button type="button" data-command="fontSize" class="p-1 hover:bg-gray-200 rounded">
                                                A
                                            </button>
                                            <button type="button" data-command="createLink" class="p-1 hover:bg-gray-200 rounded">
                                                @
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        id="warranty-editor"
                                        class="w-full px-3 py-2 min-h-[100px] focus:outline-none"
                                        contenteditable="true"><?php echo $product['warranty']; ?></div>
                                    <input type="hidden" name="warranty" value="<?php echo htmlspecialchars($product['warranty']); ?>">
                                </div>
                            </div>

                            <!-- Care -->
                            <div class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Care
                                </label>
                                <div class="border border-gray-300 rounded-md">
                                    <div class="bg-gray-50 p-2 border-b border-gray-300">
                                        <div class="flex gap-2 editor-toolbar" data-target="care-editor">
                                            <button type="button" data-command="bold" class="p-1 hover:bg-gray-200 rounded">
                                                <strong>B</strong>
                                            </button>
                                            <button type="button" data-command="italic" class="p-1 hover:bg-gray-200 rounded">
                                                <em>I</em>
                                            </button>
                                            <button type="button" data-command="underline" class="p-1 hover:bg-gray-200 rounded">
                                                <u>U</u>
                                            </button>
                                            <span class="border-l border-gray-300 mx-2"></span>
                                            <button type="button" data-command="fontSize" class="p-1 hover:bg-gray-200 rounded">
                                                A
                                            </button>
                                            <button type="button" data-command="createLink" class="p-1 hover:bg-gray-200 rounded">
                                                @
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        id="care-editor"
                                        class="w-full px-3 py-2 min-h-[100px] focus:outline-none"
                                        contenteditable="true"><?php echo $product['care']; ?></div>
                                    <input type="hidden" name="care" value="<?php echo htmlspecialchars($product['care']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content 2: Pricing and Availability -->
        <div id="orders" class="tab-content hidden">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
                <div id="itemsContainer" class="space-y-8">
                    <!-- Existing Variants -->
                    <?php foreach ($variants as $index => $variant): ?>
                        <div class="item-section border-b pb-6">
                            <input type="hidden" name="variant_id[]" value="<?php echo $variant['variant_id']; ?>">

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="hasDiscount[]" class="form-checkbox h-4 w-4 text-blue-600"
                                        <?php echo (!empty($variant['discount_price'])) ? 'checked' : ''; ?>>
                                    <span class="ml-2 text-sm text-gray-700">There will be a discount</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Size -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                                    <input type="text" name="size[]" value="<?php echo htmlspecialchars($variant['size']); ?>"
                                        placeholder="6X4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Texture -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Texture</label>
                                    <select name="texture[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select</option>
                                        <option value="smooth" <?php echo ($variant['texture'] == 'smooth') ? 'selected' : ''; ?>>Smooth</option>
                                        <option value="rough" <?php echo ($variant['texture'] == 'rough') ? 'selected' : ''; ?>>Rough</option>
                                        <option value="matte" <?php echo ($variant['texture'] == 'matte') ? 'selected' : ''; ?>>Matte</option>
                                        <option value="glossy" <?php echo ($variant['texture'] == 'glossy') ? 'selected' : ''; ?>>Glossy</option>
                                    </select>
                                </div>

                                <!-- Original Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Original Amount</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                        <input type="number" name="originalAmount[]" value="<?php echo $variant['original_price']; ?>"
                                            placeholder="Enter amount" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <!-- Discount Amount -->
                                <div class="discount-amount" <?php echo (empty($variant['discount_price'])) ? 'style="display:none;"' : ''; ?>>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                        <input type="number" name="discountAmount[]" value="<?php echo $variant['discount_price']; ?>"
                                            placeholder="Enter amount" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <!-- Item Quantity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Quantity</label>
                                    <input type="number" name="quantity[]" value="<?php echo $variant['quantity']; ?>"
                                        placeholder="Enter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Item Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Status</label>
                                    <select name="status[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select</option>
                                        <option value="available" <?php echo ($variant['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                        <option value="outOfStock" <?php echo ($variant['status'] == 'outOfStock') ? 'selected' : ''; ?>>Out of Stock</option>
                                        <option value="discontinued" <?php echo ($variant['status'] == 'discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                                    </select>
                                </div>
                            </div>

                            <?php if ($index > 0): ?>
                                <div class="mt-4 flex justify-end">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-700 focus:outline-none">
                                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Template for new variants (initially hidden) -->
                    <div id="new-item-template" class="item-section border-b pb-6" style="display: none;">
                        <input type="hidden" name="variant_id[]" value="0">

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="hasDiscount[]" class="form-checkbox h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">There will be a discount</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                                <input type="text" name="size[]" placeholder="6X4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Texture -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Texture</label>
                                <select name="texture[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select</option>
                                    <option value="smooth">Smooth</option>
                                    <option value="rough">Rough</option>
                                    <option value="matte">Matte</option>
                                    <option value="glossy">Glossy</option>
                                </select>
                            </div>

                            <!-- Original Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Original Amount</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                    <input type="number" name="originalAmount[]" placeholder="Enter amount" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Discount Amount -->
                            <div class="discount-amount" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                    <input type="number" name="discountAmount[]" placeholder="Enter amount" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Item Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Quantity</label>
                                <input type="number" name="quantity[]" placeholder="Enter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Item Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Status</label>
                                <select name="status[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select</option>
                                    <option value="available">Available</option>
                                    <option value="outOfStock">Out of Stock</option>
                                    <option value="discontinued">Discontinued</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="button" class="remove-item text-red-600 hover:text-red-700 focus:outline-none">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Another Button -->
                <div class="flex justify-end space-x-4 mt-4">
                    <button type="button" id="addItem" class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 focus:outline-none">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add another
                    </button>
                </div>
            </div>
        </div>


       <!-- Tab Content 3: Media -->
<div id="transactions" class="tab-content hidden">
    <div class="max-w-4xl mx-auto">
        <!-- Main Image Upload -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-2">Main Image</label>
            <div id="mainImageContainer" class="relative">
                <?php if ($main_image): ?>
                <div class="mainImagePreview mb-4">
                    <div class="relative inline-block">
                        <img src="<?php echo htmlspecialchars(product_image_url($main_image['image_path'], '../../assets/products/')); ?>" alt="Main Image" class="w-full h-48 object-cover rounded-lg">
                        <button type="button" class="removeImage absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-lg hover:bg-gray-100">
                            <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mainImageUpload border-2 border-dashed border-gray-300 rounded-lg p-8 text-center" style="display: none;">
                <?php else: ?>
                <div class="mainImagePreview hidden mb-4">
                    <div class="relative inline-block">
                        <img src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                        <button type="button" class="removeImage absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-lg hover:bg-gray-100">
                            <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mainImageUpload border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <?php endif; ?>
                    <input type="file" name="mainImage" class="hidden" accept=".png,.jpg,.jpeg,.gif">
                    <div class="space-y-2">
                        <i class="fa-regular fa-folder-open text-[30px] mx-auto"></i>
                        <div class="text-sm text-gray-600">
                            <label class="relative cursor-pointer text-[18px] font-medium text-[#262626]">
                                <span>Drop your files or click to upload</span>
                            </label>
                        </div>
                        <p class="text-[14px] text-[#9A9A9A] font-['Open Sans'] font-regular">Supported file types: PNG, JPG, GIF</p>

                        <button type="button" class="flex items-center gap-2 px-8 py-2 mx-auto bg-blue-900 text-white font-medium rounded-lg cursor-pointer hover:bg-blue-800 transition-colors">
                    Browse
                    </button>

                    </div>
                </div>

                <!-- Or use a URL for the main image -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">or Use Image URL</label>
                    <input type="url" name="mainImageUrl" placeholder="Paste image URL (https://...) to set the main image" value="<?php echo ($main_image && is_external_image_url($main_image['image_path'])) ? htmlspecialchars($main_image['image_path']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-[13px] text-[#9A9A9A] font-['Open Sans'] font-regular mt-1">Provide a URL instead of uploading a file. If both are provided, the URL takes priority.</p>
                </div>
            </div>
        </div>

        <!-- Other Images Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Other Images (Optional)</label>
            <div id="otherImagesContainer">
                <!-- Preview Grid -->
                <div class="otherImagesPreview grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                    <?php if(!empty($images)): ?>
                        <?php foreach ($images as $image): ?>
                        <div class="image-preview-item relative">
                            <img src="<?php echo htmlspecialchars(product_image_url($image['image_path'], '../../assets/products/')); ?>" alt="Product Image" class="w-full h-36 object-cover rounded-lg">
                            <button type="button" class="removeExistingImage absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-lg hover:bg-gray-100" data-image-id="<?php echo $image['image_id']; ?>">
                                <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <input type="hidden" name="existing_images[]" value="<?php echo $image['image_id']; ?>">
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Persistent Upload Area -->
                <div class="md:w-[70%] otherImagesUpload border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <input type="file" name="otherImages[]" class="hidden" accept=".png,.jpg,.jpeg,.gif" multiple>
                    <div class="space-y-2">
                        <i class="fa-regular fa-folder-open text-[30px] mx-auto"></i>
                        <div class="text-sm text-gray-600">
                            <label class="relative cursor-pointer text-[18px] font-medium text-[#262626]">
                                <span>Drop your files or click to upload</span>
                            </label>
                        </div>
                        <p class="text-[14px] text-[#9A9A9A] font-['Open Sans'] font-regular">Supported file types: PNG, JPG, GIF</p>

                        <button type="button" class="flex items-center gap-2 px-8 py-2 mx-auto bg-blue-900 text-white font-medium rounded-lg cursor-pointer hover:bg-blue-800 transition-colors">
                    Browse
                    </button>

                    </div>
                </div>

                <!-- Or use URLs for other images -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">or Paste Image URLs</label>
                    <input type="text" name="otherImagesUrl" placeholder="Paste image URLs, separated by commas or new lines" value="<?php
                $saved_other_urls = [];
                foreach ($images as $img_row) {
                    if (is_external_image_url($img_row['image_path'])) {
                        $saved_other_urls[] = $img_row['image_path'];
                    }
                }
                echo htmlspecialchars(implode(', ', $saved_other_urls));
                ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-[13px] text-[#9A9A9A] font-['Open Sans'] font-regular mt-1">Provide external image URLs. You can paste several separated by commas or new lines.</p>
                </div>
                
                <!-- Hidden inputs for deleted images -->
                <div id="deleteImagesContainer">
                    <!-- Will be populated by JavaScript when images are marked for deletion -->
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center mt-8">
                    <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-blue-900 text-white font-medium rounded-lg cursor-pointer hover:bg-blue-800 transition-colors">
                    Update Product
                    </button>
                </div>

</div>

</div>




    </form>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Navigation
    const tabs = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('text-[#007F7F]', 'border-b-2', 'border-[#007F7F]'));
            tabs.forEach(t => t.classList.add('text-gray-600'));
            tabContents.forEach(content => content.classList.remove('active'));
            tabContents.forEach(content => content.classList.add('hidden'));

            // Add active class to clicked tab
            tab.classList.remove('text-gray-600');
            tab.classList.add('text-[#007F7F]', 'border-b-2', 'border-[#007F7F]');

            // Show corresponding content
            const tabId = tab.getAttribute('data-tab');
            const tabContent = document.getElementById(tabId);
            tabContent.classList.remove('hidden');
            tabContent.classList.add('active');
        });
    });

    // Featured product toggle
    const featuredToggle = document.getElementById('featuredToggle');
    const isFeaturedInput = document.querySelector('input[name="isFeatured"]');

    featuredToggle.addEventListener('change', function() {
        isFeaturedInput.value = this.checked ? 'true' : 'false';
    });

    // Rich Text Editor functionality
    const editorToolbars = document.querySelectorAll('.editor-toolbar');

    editorToolbars.forEach(toolbar => {
        const buttons = toolbar.querySelectorAll('button');
        const targetId = toolbar.getAttribute('data-target');
        const editor = document.getElementById(targetId);
        const hiddenInput = editor.nextElementSibling;

        // Update hidden input with editor content when form is submitted
        editor.addEventListener('input', function() {
            hiddenInput.value = this.innerHTML;
        });

        // Initialize hidden inputs with editor content
        hiddenInput.value = editor.innerHTML;

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const command = this.getAttribute('data-command');
                
                if (command === 'createLink') {
                    const url = prompt('Enter the link URL:');
                    if (url) {
                        document.execCommand(command, false, url);
                    }
                } else if (command === 'fontSize') {
                    const size = prompt('Enter font size (1-7):', '3');
                    if (size) {
                        document.execCommand(command, false, size);
                    }
                } else {
                    document.execCommand(command, false, null);
                }
                
                editor.focus();
            });
        });
    });

    // Variants management
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemButton = document.getElementById('addItem');
    const newItemTemplate = document.getElementById('new-item-template');

    // Add new variant
    addItemButton.addEventListener('click', function() {
        const newItem = newItemTemplate.cloneNode(true);
        newItem.style.display = 'block';
        newItem.id = '';
        itemsContainer.appendChild(newItem);
        
        // Setup discount checkbox functionality for the new item
        setupDiscountCheckbox(newItem);
        
        // Setup remove button for the new item
        setupRemoveButton(newItem);
    });

    // Setup all existing discount checkboxes
    document.querySelectorAll('.item-section').forEach(setupDiscountCheckbox);
    
    // Setup all existing remove buttons
    document.querySelectorAll('.item-section').forEach(setupRemoveButton);

    function setupDiscountCheckbox(itemSection) {
        const checkbox = itemSection.querySelector('input[name="hasDiscount[]"]');
        const discountDiv = itemSection.querySelector('.discount-amount');
        
        if (checkbox && discountDiv) {
            checkbox.addEventListener('change', function() {
                discountDiv.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) {
                    discountDiv.querySelector('input').value = '';
                }
            });
        }
    }
    
    function setupRemoveButton(itemSection) {
        const removeButton = itemSection.querySelector('.remove-item');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                itemSection.remove();
            });
        }
    }

    // Image upload handling - FIXED VERSION
    setupImageUpload('mainImage', 'mainImageContainer');
    setupImageUpload('otherImages', 'otherImagesContainer', true);

    // Allow removing the current main image right on page load (no need to pick a new file first)
    const mainContainerBox = document.getElementById('mainImageContainer');
    const mainRemoveBtn = mainContainerBox.querySelector('.mainImagePreview .removeImage');
    if (mainRemoveBtn) {
        mainRemoveBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Mark the current main image for deletion
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_main_image';
            deleteInput.value = '1';
            document.getElementById('deleteImagesContainer').appendChild(deleteInput);

            // Hide preview and show upload area again
            mainContainerBox.querySelector('.mainImagePreview').style.display = 'none';
            mainContainerBox.querySelector('.mainImageUpload').style.display = 'block';

            // Clear any pending file or URL so it doesn't override the deletion
            const mainFile = mainContainerBox.querySelector('input[name="mainImage"]');
            if (mainFile) mainFile.value = '';
            const mainUrl = mainContainerBox.querySelector('input[name="mainImageUrl"]');
            if (mainUrl) mainUrl.value = '';
        });
    }

    function setupImageUpload(inputName, containerId, multiple = false) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const uploadArea = container.querySelector(`.${inputName}Upload`);
        const fileInput = uploadArea.querySelector(`input[name="${inputName}${multiple ? '[]' : ''}"]`);
        
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('border-blue-500');
        });
        
        uploadArea.addEventListener('dragleave', function() {
            uploadArea.classList.remove('border-blue-500');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-500');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect(fileInput);
            }
        });
        
        fileInput.addEventListener('change', function() {
            handleFileSelect(this);
        });
        
        function handleFileSelect(input) {
            if (!multiple) {
                // Single file (main image)
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewDiv = container.querySelector(`.${inputName}Preview`);
                        const imgElement = previewDiv.querySelector('img');
                        
                        imgElement.src = e.target.result;
                        previewDiv.classList.remove('hidden');
                        previewDiv.style.display = 'block';
                        uploadArea.style.display = 'none';
                    };
                    
                    reader.readAsDataURL(input.files[0]);
                }

                // Clear the file input so the load-time remove handler stays the single source of truth
                fileInput.value = '';
            } else {
                // Multiple files (other images)
                if (input.files && input.files.length > 0) {
                    const previewGrid = container.querySelector(`.${inputName}Preview`);
                    
                    for (let i = 0; i < input.files.length; i++) {
                        const file = input.files[i];
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'image-preview-item relative';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'w-full h-36 object-cover rounded-lg';
                            img.alt = 'Preview';
                            
                            const removeButton = document.createElement('button');
                            removeButton.type = 'button';
                            removeButton.className = 'removeNewImage absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-lg hover:bg-gray-100';
                            removeButton.innerHTML = `
                                <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            `;
                            
                            previewItem.appendChild(img);
                            previewItem.appendChild(removeButton);
                            previewGrid.appendChild(previewItem);
                            
                            // Setup remove button for new preview
                            removeButton.addEventListener('click', function() {
                                previewItem.remove();
                            });
                        };
                        
                        reader.readAsDataURL(file);
                    }
                }
            }
        }
    }
    
    // Handle existing image deletion
    document.querySelectorAll('.removeExistingImage').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-image-id');
            const previewItem = this.closest('.image-preview-item');
            
            // Add a hidden input to mark this image for deletion
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_images[]';
            deleteInput.value = imageId;
            document.getElementById('deleteImagesContainer').appendChild(deleteInput);
            
            // Hide the preview
            previewItem.style.display = 'none';
        });
    });

    // Make sure form submits all data correctly
    document.getElementById('productForm').addEventListener('submit', function(e) {
        // Update all rich text editor values before submission
        document.querySelectorAll('[contenteditable="true"]').forEach(editor => {
            const hiddenInput = editor.nextElementSibling;
            hiddenInput.value = editor.innerHTML;
        });
    });
});
</script>


</body>