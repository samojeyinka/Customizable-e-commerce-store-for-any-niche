<?php

require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';

$user = isAuthenticated() ? getCurrentUser() : null;

// Get category and brand from URL parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : null;

// Set the number of products per page
$products_per_page = 12;

// Get the current page from URL parameter, default to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Ensure current_page is at least 1
if ($current_page < 1) {
    $current_page = 1;
}

// Calculate the starting position for the SQL LIMIT clause
$start = ($current_page - 1) * $products_per_page;

// Base query for products with their main image and first variant price
$query = "
    SELECT 
        p.product_id,
        p.product_name, 
        p.is_featured,
        c.category_title,
        b.brand_title,
        i.image_path AS main_image,
        MIN(v.original_price) AS min_price,
        MIN(v.discount_price) AS min_discount_price
    FROM 
        products p
    LEFT JOIN 
        categories c ON p.category_id = c.category_id
    LEFT JOIN 
        brands b ON p.brand_id = b.brand_id
    LEFT JOIN 
        product_images i ON p.product_id = i.product_id AND i.is_main = 1
    LEFT JOIN 
        product_variants v ON p.product_id = v.product_id
    WHERE 1=1
";

// Add filters for category and brand if they exist
if ($category_id) {
    $query .= " AND p.category_id = " . $category_id;
}

if ($brand_id) {
    $query .= " AND p.brand_id = " . $brand_id;
}

// Complete the query with GROUP BY and ORDER BY
$query .= "
    GROUP BY 
        p.product_id, p.product_name, p.is_featured, c.category_title, b.brand_title, i.image_path
    ORDER BY 
        p.date_added DESC
";

// Get total number of products with these filters (for calculating total pages)
$count_query = "SELECT COUNT(*) as total FROM (" . $query . ") as counted";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_products = $count_row['total'];

// Calculate total pages
$total_pages = ceil($total_products / $products_per_page);

// Add LIMIT clause for pagination
$query .= " LIMIT $start, $products_per_page";

// Execute the main query
$result = mysqli_query($con, $query);

// Check if query was successful
if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit;
}

// Get the category and brand names for display
$category_name = "All Products";
$brand_name = "";

if ($category_id) {
    $cat_query = "SELECT category_title FROM categories WHERE category_id = ?";
    $cat_stmt = mysqli_prepare($con, $cat_query);
    mysqli_stmt_bind_param($cat_stmt, "i", $category_id);
    mysqli_stmt_execute($cat_stmt);
    $cat_result = mysqli_stmt_get_result($cat_stmt);
    if ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $category_name = $cat_row['category_title'];
    }
}

if ($brand_id) {
    $brand_query = "SELECT brand_title FROM brands WHERE brand_id = ?";
    $brand_stmt = mysqli_prepare($con, $brand_query);
    mysqli_stmt_bind_param($brand_stmt, "i", $brand_id);
    mysqli_stmt_execute($brand_stmt);
    $brand_result = mysqli_stmt_get_result($brand_stmt);
    if ($brand_row = mysqli_fetch_assoc($brand_result)) {
        $brand_name = $brand_row['brand_title'];
    }
}

// Combined title for display
$page_title = $category_name;
if ($brand_name) {
    $page_title .= " - " . $brand_name;
}

// Get the user's cart count if they're logged in
$cart_count = 0;
if ($user) {
    $cart_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $cart_stmt = mysqli_prepare($con, $cart_query);
    mysqli_stmt_bind_param($cart_stmt, "i", $user['id']);
    mysqli_stmt_execute($cart_stmt);
    $cart_result = mysqli_stmt_get_result($cart_stmt);
    $cart_data = mysqli_fetch_assoc($cart_result);
    $cart_count = $cart_data['total'] ?? 0;
}


// In your PHP section, fetch the cart items for the current user
$cart_items = [];
if ($user) {
    $cart_items_query = "SELECT product_id, cart_id FROM cart WHERE user_id = ?";
    $cart_items_stmt = mysqli_prepare($con, $cart_items_query);
    mysqli_stmt_bind_param($cart_items_stmt, "i", $user['id']);
    mysqli_stmt_execute($cart_items_stmt);
    $cart_items_result = mysqli_stmt_get_result($cart_items_stmt);
    
    while ($item = mysqli_fetch_assoc($cart_items_result)) {
        $cart_items[$item['product_id']] = $item['cart_id'];
    }
}



// Check if each product is in user's favorites
$favorites = [];
if ($user) {
    $favorites_query = "SELECT product_id FROM favorites WHERE user_id = ?";
    $favorites_stmt = mysqli_prepare($con, $favorites_query);
    mysqli_stmt_bind_param($favorites_stmt, "i", $user['id']);
    mysqli_stmt_execute($favorites_stmt);
    $favorites_result = mysqli_stmt_get_result($favorites_stmt);
    
    while ($fav = mysqli_fetch_assoc($favorites_result)) {
        $favorites[$fav['product_id']] = true;
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />

   


</head>

<body>
    <main class="bg-[#FEFEFE]">

    <?php
include(__DIR__ . "/../includes/header.php");
include(__DIR__ . '/../includes/options.php');
    ?>


<!-- Add to Cart Toast Notification -->
<div id="cart-toast" class="hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
    Item added to your cart!
</div>


            <div class="w-[90%] mx-auto">
        <div class="flex items-center gap-1">
            <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
            <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
            
            <?php if ($category_id): ?>
                <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Products</a>
                <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">
                    <?php echo htmlspecialchars($category_name); ?>
                    <?php if ($brand_id): ?>
                        <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px] inline-block" />
                        <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">
                            <?php echo htmlspecialchars(trim($brand_name, " -")); ?>
                        </span>
                    <?php endif; ?>
                </span>
            <?php else: ?>
                <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Products</span>
            <?php endif; ?>
        </div>
    </div>


    <div class="w-[90%] flex items-center justify-between mx-auto">
        <h2 class="text-[#262626] text-[20px] md:text-[22px] font-Onest font-medium">
            <?php echo htmlspecialchars($page_title); ?>
        </h2>
        <img onclick="filterMenu()" src="<?php echo DOMAIN; ?>/assets/products/mail.svg" class="w-[30px] cursor-pointer md:hidden" />
    </div>




      <!-- The filter here, ignore this -->
                <?php
                // include('./filter.php');
                include(__DIR__ . '/filter.php');

                ?>



            <div class="w-[90%] mx-auto grid grid-cols-2 lg:grid-cols-4 gap-4 my-4">
    <!-- The products cards -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <!-- Product Card -->
            <div class="w-[fit-content] productbox rounded-[8px] overflow-hidden flex flex-col">
                <a href="<?php echo DOMAIN; ?>/products/show.php?id=<?php echo $product['product_id']; ?>" class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <?php if ($product['is_featured']): ?>
                        <button class="w-[fit-content] h-[fit-content] bg-[#D51E5E] absolute top-4 left-4 rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-1 px-2 absolute">Featured</button>
                    <?php endif; ?>
                    
                    <img src="<?php echo !empty($product['main_image']) ? DOMAIN . '/assets/products/' . $product['main_image'] : DOMAIN .'/assets/products/default.svg'; ?>" class="w-[250px] h-[230px] object-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                    
           <!-- Only change the text of the button, keep styling consistent -->
        <button 
            class="add-to-cart-btn bg-[#1A237E] text-white rounded-[8px] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]"
            data-product-id="<?php echo $product['product_id']; ?>"
            data-in-cart="<?php echo array_key_exists($product['product_id'], $cart_items) ? 'true' : 'false'; ?>"
            data-cart-id="<?php echo array_key_exists($product['product_id'], $cart_items) ? $cart_items[$product['product_id']] : ''; ?>"
        >
            <?php echo array_key_exists($product['product_id'], $cart_items) ? 'Added to Cart' : 'Add to Cart'; ?>
        </button>
                    
                    <div class="actionstab absolute w-[fit-content] right-2 top-4">
                        <div class="flex flex-col gap-2 md:gap-4">
                            <!-- <img src="../assets/products/h1.svg" class="w-[25px] md:w-[35px] cursor-pointer add-to-favourite" />-->
                              <img src="<?php echo DOMAIN; ?>/assets/products/<?php echo isset($favorites[$product['product_id']]) ? 'addedtofav.svg' : 'addtofav.svg'; ?>" 
             class="w-[25px] md:w-[35px] cursor-pointer add-to-favourite <?php echo isset($favorites[$product['product_id']]) ? 'favorite-active' : ''; ?>" 
             data-product-id="<?php echo $product['product_id']; ?>" />
       
                           
<img src="<?php echo array_key_exists($product['product_id'], $cart_items) ? DOMAIN . '/assets/products/addedtocart.svg' : DOMAIN . '/assets/products/addtocart.svg'; ?>" 
     alt="<?php echo array_key_exists($product['product_id'], $cart_items) ? 'Remove from Cart' : 'Add to Cart'; ?>" 
     class="cart-toggle-icon w-[25px] md:w-[35px] cursor-pointer <?php echo array_key_exists($product['product_id'], $cart_items) ? 'in-cart' : ''; ?>" 
     data-product-id="<?php echo $product['product_id']; ?>"
     data-cart-id="<?php echo array_key_exists($product['product_id'], $cart_items) ? $cart_items[$product['product_id']] : ''; ?>"
     data-in-cart-image="<?php echo DOMAIN; ?>/assets/products/addedtocart.svg"
     data-default-image="<?php echo DOMAIN; ?>/assets/products/addtocart.svg"
/>
                            <img src="<?php echo DOMAIN; ?>/assets/products/go.svg" class="w-[25px] md:w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </a>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">
                        <?php      
                        $product_name = htmlspecialchars($product['product_name']);
                        echo (strlen($product_name) > 20) ? substr($product_name, 0, 23) . '...' : $product_name;
                        ?>
                    </span>
                    
                    <?php if (!empty($product['min_discount_price'])): ?>
                  
                        <div class="flex flex-col items-end">
    <?php if(empty($product['min_discount_price']) || $product['min_discount_price'] == 0): ?>
        <span class="text-[#262626] text-[14px] md:text-[15px] font-Onest font-medium">₦<?php echo number_format($product['min_price']); ?></span>
    <?php else: ?>
        <span class="text-[#262626] text-[14px] md:text-[15px] font-Onest font-medium">₦<?php echo number_format($product['min_discount_price']); ?></span>
    <?php endif; ?>
</div>
                    <?php else: ?>
                        <!-- Show regular price -->
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦<?php echo number_format($product['min_price']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-4 text-center py-8 text-gray-500">No products found</div>
    <?php endif; ?>
</div>

            


            </div>


      


             <!-- Pagination -->
            <!-- Updated pagination section to preserve category and brand filters -->
<div class="w-[90%] py-2 mx-auto">
    <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
        <!-- Previous Page Link -->
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $brand_id ? '&brand='.$brand_id : ''; ?>" class="flex items-center gap-2 cursor-pointer">
                <img src="<?php echo DOMAIN; ?>/assets/products/prev.svg" class="w-[6px] h-[11px]" />
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                <img src="<?php echo DOMAIN; ?>/assets/products/prev.svg" class="w-[6px] h-[11px]" />
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
            </div>
        <?php endif; ?>

        <!-- Page Numbers -->
        <div class="w-full flex items-center justify-between md:gap-6">
            <?php
            // Determine range of pages to show
            $range = 2; // Show 2 pages before and after current page
            $start_page = max(1, $current_page - $range);
            $end_page = min($total_pages, $current_page + $range);
            
            // Always show first page
            if ($start_page > 1) {
                echo '<a href="?page=1' . ($category_id ? '&category='.$category_id : '') . ($brand_id ? '&brand='.$brand_id : '') . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">1</a>';
                if ($start_page > 2) {
                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                }
            }
            
            // Show page links within the range
            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $current_page) {
                    echo '<span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#1A237E]">' . $i . '</span>';
                } else {
                    echo '<a href="?page=' . $i . ($category_id ? '&category='.$category_id : '') . ($brand_id ? '&brand='.$brand_id : '') . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $i . '</a>';
                }
            }
            
            // Always show last page
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                }
                echo '<a href="?page=' . $total_pages . ($category_id ? '&category='.$category_id : '') . ($brand_id ? '&brand='.$brand_id : '') . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $total_pages . '</a>';
            }
            ?>
        </div>

        <!-- Next Page Link -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $brand_id ? '&brand='.$brand_id : ''; ?>" class="flex items-center gap-2 cursor-pointer">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                <img src="<?php echo DOMAIN; ?>/assets/products/next.svg" class="w-[6px] h-[11px]" />
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                <img src="<?php echo DOMAIN; ?>/assets/products/next.svg" class="w-[6px] h-[11px]" />
            </div>
        <?php endif; ?>
    </div>
</div>
        </div>

        <div id="filteroptions" class="filteroptions-content border-[1px] border-[#E1E1E1] bg-white">
            <div class="relative">

                <div class="flex flex-col gap-2">
                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Sort by</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="selectOption(this)">All</div>
                                    <div onclick="selectOption(this)">Popularity</div>
                                    <div onclick="selectOption(this)">Latest</div>
                                    <div onclick="selectOption(this)">Amount: High to Low</div>
                                    <div onclick="selectOption(this)">Amount: Low to High</div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Color</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Black</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Blue</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Red</span>
                                    </label>


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Amount</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦50,000 - ₦150,000</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦151,000 - 250,000</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦251,000 - ₦500,000</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦500,000 and above</span>
                                    </label>



                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Size</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                                    </label>





                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Category</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Blankets </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Throws</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Bed Sheets</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Pillowcases</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Duvet Covers</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Comforters </span>
                                    </label>



                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Mattress Toppers</span>
                                    </label>


                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Lightning</span>
                                    </label>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Texture</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Soft </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Medium </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Hard </span>
                                    </label>





                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Rating</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 star </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">4 star</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">3 star</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">2 star</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">1 star</span>
                                    </label>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>


        <?php
include(__DIR__ . "/../includes/footer.php");
?>

    </main>



    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="../functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>
    <script>
    // Add active class to current page in pagination
    document.addEventListener('DOMContentLoaded', function() {
        // Preserve filter parameters when paginating
        const paginationLinks = document.querySelectorAll('.w-[90%] .ml-auto a');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const page = this.getAttribute('href').split('=')[1];
                
                // Remove page parameter
                urlParams.delete('page');
                
                // Add new page parameter
                urlParams.append('page', page);
                
                // Update href
                this.setAttribute('href', '?' + urlParams.toString());
            });
        });
    });

    // Add this to a separate JS file or include at the bottom of your page

document.addEventListener('DOMContentLoaded', function() {
    // Get all pagination links
    const paginationLinks = document.querySelectorAll('.w-[90%] .ml-auto a');
    
    // Function to preserve filters when navigating pagination
    function preserveFiltersOnPagination() {
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the page number from the href
                const hrefParts = this.getAttribute('href').split('=');
                if (hrefParts.length < 2) return;
                
                const page = hrefParts[1];
                
                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                
                // Remove page parameter and add the new one
                urlParams.delete('page');
                urlParams.append('page', page);
                
                // Navigate to the new URL with all filters preserved
                window.location.href = '?' + urlParams.toString();
            });
        });
    }
    
    // Handle filter form submissions
    const filterForms = document.querySelectorAll('.filter-form');
    if (filterForms.length > 0) {
        filterForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Start with a fresh URLSearchParams
                const urlParams = new URLSearchParams();
                
                // Add all form data to URL parameters
                const formData = new FormData(form);
                for (const [key, value] of formData.entries()) {
                    if (value) {
                        urlParams.append(key, value);
                    }
                }
                
                // Reset to page 1 when filtering
                urlParams.set('page', '1');
                
                // Navigate to filtered results
                window.location.href = '?' + urlParams.toString();
            });
        });
    }
    
    // Initialize the event listeners
    preserveFiltersOnPagination();
    
    // Highlight active filters based on URL parameters
    function highlightActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Loop through all URL parameters
        for (const [key, value] of urlParams.entries()) {
            // Skip page parameter
            if (key === 'page') continue;
            
            // Find filter inputs matching this parameter
            const filterInputs = document.querySelectorAll(`[name="${key}"]`);
            
            filterInputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    // For checkboxes/radios, check if value matches
                    if (input.value === value) {
                        input.checked = true;
                    }
                } else {
                    // For other inputs, set the value
                    input.value = value;
                }
            });
            
            // Highlight active filter labels
            const filterLabels = document.querySelectorAll(`.filter-label[data-filter="${key}"][data-value="${value}"]`);
            filterLabels.forEach(label => {
                label.classList.add('active-filter');
            });
        }
    }
    
    // Call function to highlight active filters
    highlightActiveFilters();
    
    // Reset filters button
    const resetFilterBtn = document.querySelector('.reset-filters');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            window.location.href = window.location.pathname;
        });
    }
});


document.addEventListener('DOMContentLoaded', function() {
    // Cart toggle functionality
    const cartToggleButtons = document.querySelectorAll('.cart-toggle-icon');
    const cartToggleBtns = document.querySelectorAll('.add-to-cart-btn');
    const cartCountElement = document.getElementById('cart-count');
    const cartBadge = document.getElementById('cart-badge');
    const cartToast = document.getElementById('cart-toast');
    
    // Function to handle cart toggling
    function handleCartToggle(productId, isInCart, clickedElement) {
        if (isInCart) {
            // Item is in cart, remove it
            const cartId = clickedElement.getAttribute('data-cart-id');
            
            if (!cartId) {
                console.error('No cart ID found for product:', productId);
                return;
            }
            
            // Send AJAX request to remove from cart
            fetch('./remove-from-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'cart_id=' + encodeURIComponent(cartId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                        
                        // Hide badge if cart is empty
                        if (data.cart_count <= 0) {
                            cartBadge.classList.add('hidden');
                        }
                    }
                    
                    // Update all elements for this product
                    updateCartElements(productId, false);
                    
                    // Show toast notification
                    cartToast.innerHTML = 'Item removed from your cart!';
                    cartToast.classList.remove('hidden');
                    cartToast.classList.add('flex', 'bg-orange-500'); // Different color for removal
                    
                    // Hide toast after 3 seconds
                    setTimeout(() => {
                        cartToast.classList.add('hidden');
                        cartToast.classList.remove('flex', 'bg-orange-500');
                        cartToast.innerHTML = 'Item added to your cart!';
                    }, 3000);
                } else {
                    alert(data.message || 'Error removing item from cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            
        } else {
            // Item is not in cart, add it
            // Create form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            
            // Send AJAX request to add to cart
            fetch('./add-to-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                        cartBadge.classList.remove('hidden');
                    }
                    
                    // Update all elements for this product
                    updateCartElements(productId, true, data.cart_id);
                    
                    // Show toast notification
                    cartToast.innerHTML = 'Item added to your cart!';
                    cartToast.classList.remove('hidden', 'bg-orange-500');
                    cartToast.classList.add('flex');
                    
                    // Hide toast after 3 seconds
                    setTimeout(() => {
                        cartToast.classList.add('hidden');
                        cartToast.classList.remove('flex');
                    }, 3000);
                } else {
                    // Handle error
                    if (data.redirect) {
                        // Redirect to login if user is not authenticated
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Error adding item to cart');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
    
    // Function to update all cart elements (button and icon) for a specific product
    function updateCartElements(productId, isInCart, cartId = '') {
        // Update icon button
        const cartIcons = document.querySelectorAll(`.cart-toggle-icon[data-product-id="${productId}"]`);
        cartIcons.forEach(icon => {
            if (isInCart) {
                icon.src = icon.getAttribute('data-in-cart-image');
                icon.classList.add('in-cart');
                icon.alt = 'Remove from Cart';
                icon.setAttribute('data-cart-id', cartId);
            } else {
                icon.src = icon.getAttribute('data-default-image');
                icon.classList.remove('in-cart');
                icon.alt = 'Add to Cart';
                icon.setAttribute('data-cart-id', '');
            }
        });
        
        // Update text button - ONLY change the text, not the styling
        const cartButtons = document.querySelectorAll(`.add-to-cart-btn[data-product-id="${productId}"]`);
        cartButtons.forEach(button => {
            if (isInCart) {
                button.textContent = 'Added to Cart';
                button.setAttribute('data-in-cart', 'true');
                button.setAttribute('data-cart-id', cartId);
            } else {
                button.textContent = 'Add to Cart';
                button.setAttribute('data-in-cart', 'false');
                button.setAttribute('data-cart-id', '');
            }
        });
    }
    
    // Add click event to icon buttons
    cartToggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Stop event from bubbling to parent link
            
            const productId = this.getAttribute('data-product-id');
            const isInCart = this.classList.contains('in-cart');
            
            handleCartToggle(productId, isInCart, this);
        });
    });
    
    // Add click event to text buttons
    cartToggleBtns.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Stop event from bubbling to parent link
            
            const productId = this.getAttribute('data-product-id');
            const isInCart = this.getAttribute('data-in-cart') === 'true';
            
            handleCartToggle(productId, isInCart, this);
        });
    });
    
    // Initialize button states on page load
    function initializeCartButtons() {
        // Collect all cart items into an object for easy lookup
        const cartItems = {};
        document.querySelectorAll('.cart-toggle-icon.in-cart').forEach(icon => {
            const productId = icon.getAttribute('data-product-id');
            const cartId = icon.getAttribute('data-cart-id');
            if (productId && cartId) {
                cartItems[productId] = cartId;
            }
        });
        
        // Update all add-to-cart buttons to match
        cartToggleBtns.forEach(button => {
            const productId = button.getAttribute('data-product-id');
            if (productId && cartItems[productId]) {
                button.textContent = 'Added to Cart';
                button.setAttribute('data-in-cart', 'true');
                button.setAttribute('data-cart-id', cartItems[productId]);
            }
        });
    }
    
    // Run initialization
    initializeCartButtons();
});





document.addEventListener('DOMContentLoaded', function() {
    // Favorite toggle functionality for both list view and detail pages
    const favoriteButtons = document.querySelectorAll('.add-to-favourite');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get product ID from the closest parent with the data attribute
            let productId;
            
            // For product list view
            if (this.closest('.productbox')) {
                productId = this.closest('.productbox').querySelector('.cart-toggle-icon').getAttribute('data-product-id');
            } 
            // For detail page
            else if (document.getElementById('product-id')) {
                productId = document.getElementById('product-id').value;
            }
            
            if (!productId) {
                console.error('Product ID not found');
                return;
            }
            
            // Check if already favorited (icon has active class)
            const isFavorite = this.classList.contains('favorite-active');
            
            // Create form data
            const formData = new FormData();
            formData.append('product_id', productId);
            
            if (isFavorite) {
                formData.append('action', 'remove');
            }
            
            // Send AJAX request
            fetch('./toggle-favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added' || data.action === 'exists') {
                        // Update icon to show favorited state
                        this.classList.add('favorite-active');
                        this.src = '<?php echo DOMAIN; ?>/assets/products/addedtofav.svg'; // Replace with your filled heart icon
                        
                        // Show toast notification
                        showToast('Product added to favorites!', 'success');
                    } else if (data.action === 'removed') {
                        // Update icon to show unfavorited state
                        this.classList.remove('favorite-active');
                        this.src = '<?php echo DOMAIN; ?>/assets/products/addtofav.svg'; // Replace with your empty heart icon
                        
                        // Show toast notification
                        showToast('Product removed from favorites!', 'warning');
                    }
                } else {
                    // Handle error
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Error updating favorites');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
    
    // Function to show toast notification
    function showToast(message, type = 'success') {
        // Create toast if it doesn't exist
        let toast = document.getElementById('notification-toast');
        
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'notification-toast';
            toast.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300';
            document.body.appendChild(toast);
        }
        
        // Set appropriate styling based on type
        if (type === 'success') {
            toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300';
        } else if (type === 'warning') {
            toast.className = 'fixed bottom-4 right-4 bg-orange-500 text-white px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300';
        } else if (type === 'error') {
            toast.className = 'fixed bottom-4 right-4 bg-red-600 text-white px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300';
        }
        
        // Set message and show toast
        toast.textContent = message;
        toast.style.display = 'block';
        
        // Hide toast after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
                toast.style.opacity = '1';
            }, 300);
        }, 3000);
    }
    
    // Check favorites status on page load
    function checkFavoritesStatus() {
        // Get all product IDs on the page
        const productElements = document.querySelectorAll('[data-product-id]');
        const productIds = Array.from(productElements).map(el => el.getAttribute('data-product-id')).filter(Boolean);
        
        if (productIds.length === 0) return;
        
        // Send request to get favorites status
        fetch('./check-favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_ids: productIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update favorite icons based on status
                data.favorites.forEach(productId => {
                    const favoriteIcons = document.querySelectorAll(`.add-to-favourite[data-product-id="${productId}"]`);
                    favoriteIcons.forEach(icon => {
                        icon.classList.add('favorite-active');
                        icon.src = '<?php echo DOMAIN; ?>/assets/products/heart-filled.svg'; // Replace with your filled heart icon
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error checking favorites status:', error);
        });
    }
    
    // Run on page load if user is logged in
    if (document.querySelector('.user-logged-in')) {
        checkFavoritesStatus();
    }
});
    </script>
</body>

</html>