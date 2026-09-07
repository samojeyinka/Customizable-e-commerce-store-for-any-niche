<?php

// Set upload limits
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '20M');
ini_set('max_file_uploads', '20');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');


// Include database connection
include('../../config/connect.php');

// Process form submission for product creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'create_product') {
    // Get basic product information
    $product_name = $_POST['productName'];
    $category_id = $_POST['productCategory'];
    $brand_id = !empty($_POST['productBrand']) ? $_POST['productBrand'] : NULL;
    $sku = $_POST['sku'];
    $colors = $_POST['colors'];
    $is_featured = isset($_POST['isFeatured']) && $_POST['isFeatured'] == 'true' ? 1 : 0;

    // Get rich text content
    $details = $_POST['details'];
    $sizes_details = $_POST['sizes_details'];
    $warranty = $_POST['warranty'];
    $care = $_POST['care'];


    // Auto-generate SKU but allow manual override
    if (empty($_POST['sku'])) {
        // Get first 3 letters of category
        $category_query = "SELECT category_title FROM categories WHERE category_id = ?";
        $stmt = mysqli_prepare($con, $category_query);
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $category_title);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $category_prefix = substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $category_title)), 0, 3);

        // Get random alphanumeric string
        $random_part = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

        // Create SKU with timestamp for uniqueness
        $sku = $category_prefix . '-' . $random_part . '-' . substr(time(), -4);
    } else {
        $sku = $_POST['sku'];
    }

// Generate a unique slug from the product name
    $product_slug = generate_product_slug($product_name);

    // Insert product into database
    $insert_product = "INSERT INTO products (product_name, product_slug, category_id, brand_id, sku, colors, details, sizes_details, warranty, care, is_featured) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $insert_product);
    mysqli_stmt_bind_param($stmt, "ssisssssssi", $product_name, $product_slug, $category_id, $brand_id, $sku, $colors, $details, $sizes_details, $warranty, $care, $is_featured);

    if (mysqli_stmt_execute($stmt)) {
        $product_id = mysqli_insert_id($con);

        // Process variants
        if (isset($_POST['size']) && is_array($_POST['size'])) {
            for ($i = 0; $i < count($_POST['size']); $i++) {
                $size = $_POST['size'][$i];
                $texture = $_POST['texture'][$i];
                $original_amount = $_POST['originalAmount'][$i];
                $discount_amount = !empty($_POST['discountAmount'][$i]) ? $_POST['discountAmount'][$i] : NULL;
                $quantity = $_POST['quantity'][$i];
                $status = $_POST['status'][$i];

                $insert_variant = "INSERT INTO product_variants (product_id, size, texture, original_price, discount_price, quantity, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_variant = mysqli_prepare($con, $insert_variant);
                mysqli_stmt_bind_param($stmt_variant, "issddis", $product_id, $size, $texture, $original_amount, $discount_amount, $quantity, $status);
                mysqli_stmt_execute($stmt_variant);
            }
        }

        // Process main image
        if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] == 0) {
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
                $insert_image = "INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 1, 1)";
                $stmt_image = mysqli_prepare($con, $insert_image);
                mysqli_stmt_bind_param($stmt_image, "is", $product_id, $new_file_name);
                mysqli_stmt_execute($stmt_image);
            }
        }

     

        // Process other images
        if (isset($_FILES['otherImages']) && is_array($_FILES['otherImages']['name'])) {
            $upload_dir = "../../assets/products/";

            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Count files
            $fileCount = count($_FILES['otherImages']['name']);

            // Debug information
            error_log("Processing $fileCount additional images");

            // Loop through each file
            for ($i = 0; $i < $fileCount; $i++) {
                // Skip empty entries
                if (empty($_FILES['otherImages']['name'][$i])) {
                    continue;
                }

                // Check file error
                if ($_FILES['otherImages']['error'][$i] !== 0) {
                    error_log("Error with file #$i: " . $_FILES['otherImages']['error'][$i]);
                    continue;
                }

                // Process the file
                $file_name = basename($_FILES['otherImages']['name'][$i]);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $new_file_name = "product_" . $product_id . "_" . uniqid() . "." . $file_ext;
                $target_file = $upload_dir . $new_file_name;

                // Try to move the file
                if (move_uploaded_file($_FILES['otherImages']['tmp_name'][$i], $target_file)) {
                    // Add to database
                    $insert_image = "INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 0, ?)";
                    $display_order = $i + 2; // Start from 2 since main image is 1
                    $stmt_image = mysqli_prepare($con, $insert_image);
                    mysqli_stmt_bind_param($stmt_image, "isi", $product_id, $new_file_name, $display_order);

                    if (!mysqli_stmt_execute($stmt_image)) {
                        error_log("Database error for file #$i: " . mysqli_error($con));
                    } else {
                        error_log("Successfully saved file #$i: $new_file_name");
                    }
                } else {
                    error_log("Failed to move uploaded file #$i, error code: " . $_FILES['otherImages']['error'][$i]);
                }
            }
        }




        echo "<script>alert('Product created successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error creating product: " . mysqli_error($con) . "');</script>";
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
    <title>Create New Product</title>
    <?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
            <h1 class="text-[20px] font-Onest font-semibold mb-3">Create New Product</h1>

            <!-- Navigation Tabs -->
            <div class="w-[fit-content] justify-between overflow-x-auto flex border-b border-[#DDDDDD] mb-6" id="tabs">
                <button data-tab="all" class="text-nowrap px-4 py-2 text-[#007F7F] border-b-2 border-[#007F7F] tab-button">About Product</button>
                <button data-tab="orders" class="text-nowrap px-4 py-2 text-gray-600 hover:text-gray-900 tab-button">Pricing, discount & Availability</button>
                <button data-tab="transactions" class="text-nowrap px-4 py-2 text-gray-600 hover:text-gray-900 tab-button">Media</button>
            </div>

            <!-- Complete Form -->
            <form id="productForm" action="new-product.php" method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="create_product">

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
                                                    echo "<option value='" . $row['category_id'] . "'>" . $row['category_title'] . "</option>";
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
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Enter colors (comma separated)">
                                </div>

                                <div class="w-full flex flex-col md:flex-row items-center gap-3">
                                    <!-- Brand -->
                                    <div class="w-full md:w-[50%]">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                           Tag
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
                                                    echo "<option value='" . $row['brand_id'] . "'>" . $row['brand_title'] . "</option>";
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
                                        <input type="checkbox" id="featuredToggle" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
                                    </label>
                                    <input type="hidden" name="isFeatured" value="false">
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
                                                contenteditable="true"></div>
                                            <input type="hidden" name="details">
                                        </div>
                                    </div>

                                    <!-- Sizes details -->
                                    <div class="hidden">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Sizes
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
                                                contenteditable="true"></div>
                                            <input type="hidden" name="sizes_details">
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
                                                contenteditable="true"></div>
                                            <input type="hidden" name="warranty">
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
                                                contenteditable="true"></div>
                                            <input type="hidden" name="care">
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
                            <!-- Template for item section -->
                            <div class="item-section border-b pb-6">
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
                                            <option value="smooth">Foam</option>
                                            <option value="smooth">Leather</option>
                                            <option value="smooth">Patterns</option>
                                            <option value="smooth">Ripples</option>
                                            <option value="smooth">Fabric</option>
                                            <option value="smooth">Waves</option>
                                            <option value="rough">Rough</option>
                                            <option value="matte">Matte</option>
                                            <option value="glossy">Glossy</option>
                                            <!-- Outer Shell Texture Options -->
<option value="cotton">Cotton</option>
<option value="egyptian-cotton">Egyptian Cotton</option>
<option value="pima-cotton">Pima Cotton</option>
<option value="sateen">Sateen</option>
<option value="percale">Percale</option>
<option value="silk">Silk</option>
<option value="microfiber">Microfiber</option>
<option value="polyester">Polyester</option>
<option value="linen">Linen</option>

<!-- Overall Feel Options (based on fill) -->
<option value="down-filled">Down-filled</option>
<option value="feather-filled">Feather-filled</option>
<option value="wool-filled">Wool-filled</option>
<option value="cotton-filled">Cotton-filled</option>
<option value="synthetic-filled">Synthetic-filled</option>
<option value="microfiber-filled">Microfiber-filled</option>
<option value="polyester-filled">Polyester-filled</option>

<!-- Construction-based Texture -->
<option value="baffle-box">Baffle-box</option>
<option value="channel">Channel</option>
<option value="sewn-through">Sewn-through</option>

<!-- Additional Texture Descriptors -->
<option value="smooth">Smooth</option>
<option value="soft">Soft</option>
<option value="crisp">Crisp</option>
<option value="silky">Silky</option>
<option value="plush">Plush</option>
<option value="fluffy">Fluffy</option>
<option value="cloud-like">Cloud-like</option>
<option value="lightweight">Lightweight</option>
<option value="medium-weight">Medium-weight</option>
<option value="heavy-weight">Heavy-weight</option>
<option value="breathable">Breathable</option>
<option value="cool-touch">Cool-touch</option>
<option value="warm">Warm</option>
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
                                    <div class="discount-amount">
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
                            </div>
                        </div>

                        <!-- Add Another Button -->
                        <div class="flex justify-end space-x-4">
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
                                <div class="mainImagePreview hidden mb-4">
                                    <div class="relative inline-block">
                                        <img src="" alt="Preview" class="w-[199px] h-[150px]  rounded-lg">
                                        <button type="button" class="removeImage absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-lg hover:bg-gray-100">
                                            <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="md:w-[70%] mainImageUpload border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
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
                            </div>
                        </div>

                        <!-- Other Images Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Other Images (Optional)</label>
                            <div id="otherImagesContainer">
                                <!-- Preview Grid -->
                                <div class="otherImagesPreview grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Preview images will be inserted here -->
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
                            </div>
                        </div>
                        <div class="flex justify-center mt-8">
                    <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-blue-900 text-white font-medium rounded-lg cursor-pointer hover:bg-blue-800 transition-colors">
                        Create Product
                    </button>
                </div>
                    </div>
                </div>

                <!-- Submit Button -->
              
            </form>

        </div>
    </div>

    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>

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

            // Rich Text Editors
            document.querySelectorAll('.editor-toolbar').forEach(toolbar => {
                const targetId = toolbar.dataset.target;
                const editor = document.getElementById(targetId);
                const hiddenInput = editor.nextElementSibling;

                // Handle toolbar buttons
                toolbar.querySelectorAll('button').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        const command = button.dataset.command;

                        if (command === 'createLink') {
                            const url = prompt('Enter the URL:');
                            if (url) document.execCommand(command, false, url);
                        } else if (command === 'fontSize') {
                            const size = prompt('Enter font size (1-7):', '3');
                            if (size) document.execCommand(command, false, size);
                        } else {
                            document.execCommand(command, false, null);
                        }

                        // Update hidden input with HTML content
                        hiddenInput.value = editor.innerHTML;
                    });
                });

                // Update hidden input when content changes
                editor.addEventListener('input', () => {
                    hiddenInput.value = editor.innerHTML;
                });
            });

            // Featured Product Toggle
            const featuredToggle = document.getElementById('featuredToggle');
            const featuredInput = document.querySelector('input[name="isFeatured"]');

            featuredToggle.addEventListener('change', () => {
                featuredInput.value = featuredToggle.checked ? 'true' : 'false';
            });

            // Variants Section
            const itemsContainer = document.getElementById('itemsContainer');
            const addItemButton = document.getElementById('addItem');

            // Function to handle discount checkbox changes
            function handleDiscountCheckbox(section) {
                const checkbox = section.querySelector('input[type="checkbox"]');
                const discountField = section.querySelector('.discount-amount');

                checkbox.addEventListener('change', function() {
                    discountField.style.display = this.checked ? 'block' : 'none';
                });

                // Initial state
                discountField.style.display = checkbox.checked ? 'block' : 'none';
            }

            // Initialize first section
            handleDiscountCheckbox(itemsContainer.firstElementChild);

            // Add new item section
            addItemButton.addEventListener('click', function() {
                const newSection = itemsContainer.firstElementChild.cloneNode(true);

                // Reset values
                newSection.querySelectorAll('input').forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
                newSection.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });

                // Add remove button for sections after the first
                if (!newSection.querySelector('.remove-section')) {
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'remove-section text-red-600 hover:text-red-700 text-sm mt-2';
                    removeButton.innerHTML = 'Remove Item';
                    removeButton.onclick = function() {
                        newSection.remove();
                    };
                    newSection.appendChild(removeButton);
                }

                itemsContainer.appendChild(newSection);
                handleDiscountCheckbox(newSection);
            });

            // Updated Image Upload Handling
            function initializeUploader(containerId, isMultiple) {
                const container = document.getElementById(containerId);
                const uploadArea = container.querySelector(isMultiple ? '.otherImagesUpload' : '.mainImageUpload');
                const previewArea = container.querySelector(isMultiple ? '.otherImagesPreview' : '.mainImagePreview');
                const input = uploadArea.querySelector('input[type="file"]');

                // Handle click to upload
                uploadArea.addEventListener('click', () => input.click());

                // Handle drag and drop
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    uploadArea.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    uploadArea.addEventListener(eventName, () => {
                        uploadArea.classList.add('border-blue-500');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    uploadArea.addEventListener(eventName, () => {
                        uploadArea.classList.remove('border-blue-500');
                    });
                });

                // Handle file drop
                uploadArea.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    handleFiles(files);
                });

                // Handle file input change
                input.addEventListener('change', (e) => {
                    handleFiles(e.target.files);
                });

                function handleFiles(files) {
                    if (!isMultiple) {
                        // Single file handling
                        if (files[0]) {
                            const file = files[0];
                            if (isImageFile(file)) {
                                displayPreview(file);
                            }
                        }
                    } else {
                        // Multiple files handling - process all files
                        Array.from(files).forEach(file => {
                            if (isImageFile(file)) {
                                // No limit on preview count - was limiting to 5
                                displayPreview(file);
                            }
                        });
                    }
                }

                function isImageFile(file) {
                    const isImage = file['type'].split('/')[0] === 'image';
                    const isValidSize = file.size <= 10 * 1024 * 1024; // 5MB max

                    if (!isValidSize) {
                        alert('File too large. Maximum size is 5MB.');
                    }

                    return isImage && isValidSize;
                }

                function displayPreview(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (!isMultiple) {
                            // Single image preview
                            previewArea.innerHTML = createPreviewElement(e.target.result, 'main');
                            previewArea.classList.remove('hidden');
                            uploadArea.classList.add('hidden');
                        } else {
                            // Multiple images preview
                            const previewElement = document.createElement('div');
                            previewElement.className = 'preview-item';
                            previewElement.innerHTML = createPreviewElement(e.target.result, 'other');
                            previewArea.appendChild(previewElement);
                        }
                    }
                    reader.readAsDataURL(file);
                }

                function createPreviewElement(src, type) {
                    return `
        <div class="relative">
            <img src="${src}" alt="Preview" class="w-[199px] h-[150px] object-cover rounded-lg">
            <button type="button" class="removeImage absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-lg hover:bg-gray-100" data-container="${containerId}" data-type="${type}">
                <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        `;
                }
            }

            // Global event handler for removing images
            document.addEventListener('click', (e) => {
                const removeButton = e.target.closest('.removeImage');
                if (removeButton) {
                    const containerType = removeButton.getAttribute('data-type');
                    const containerId = removeButton.getAttribute('data-container');
                    const container = document.getElementById(containerId);
                    const previewElement = removeButton.closest('.relative');

                    if (containerType === 'main') {
                        // Main image handling
                        const previewArea = container.querySelector('.mainImagePreview');
                        const uploadArea = container.querySelector('.mainImageUpload');
                        const input = uploadArea.querySelector('input[type="file"]');

                        previewArea.classList.add('hidden');
                        uploadArea.classList.remove('hidden');
                        input.value = '';
                    } else {
                        // Other images handling - only remove the specific item
                        if (previewElement.closest('.preview-item')) {
                            previewElement.closest('.preview-item').remove();
                        } else {
                            previewElement.remove();
                        }
                    }
                }
            });

            // Initialize both uploaders
            initializeUploader('mainImageContainer', false);
            initializeUploader('otherImagesContainer', true);

            // Form Submission
            document.getElementById('productForm').addEventListener('submit', function(e) {
                // Make sure hidden inputs are updated
                document.querySelectorAll('[contenteditable="true"]').forEach(editor => {
                    const hiddenInput = editor.nextElementSibling;
                    hiddenInput.value = editor.innerHTML;
                });

                // Validate required fields
                const requiredFields = [{
                        field: 'productName',
                        message: 'Product name is required'
                    },
                    {
                        field: 'productCategory',
                        message: 'Product category is required'
                    }
                ];

                let isValid = true;
                requiredFields.forEach(item => {
                    const field = document.querySelector(`[name="${item.field}"]`);
                    if (!field.value.trim()) {
                        alert(item.message);
                        isValid = false;
                        e.preventDefault();
                        return;
                    }
                });

                // Validate variants
                const variantSections = document.querySelectorAll('.item-section');
                if (variantSections.length > 0) {
                    variantSections.forEach((section, index) => {
                        const size = section.querySelector('input[name="size[]"]').value;
                        const originalAmount = section.querySelector('input[name="originalAmount[]"]').value;
                        const quantity = section.querySelector('input[name="quantity[]"]').value;
                        const status = section.querySelector('select[name="status[]"]').value;

                        if (!size || !originalAmount || !quantity || !status) {
                            alert(`Please complete all required fields in variant #${index + 1}`);
                            isValid = false;
                            e.preventDefault();
                            return;
                        }
                    });
                }

                // Validate main image
                const mainImageInput = document.querySelector('input[name="mainImage"]');
                if (!mainImageInput.files || mainImageInput.files.length === 0) {
                    alert('Please upload a main product image');
                    isValid = false;
                    e.preventDefault();
                    return;
                }

                // If everything is valid, the form will submit
                if (isValid) {
                    // You could add a loading indicator here
                    console.log('Form is being submitted...');
                }
            });
        });
    </script>

</body>

</html>