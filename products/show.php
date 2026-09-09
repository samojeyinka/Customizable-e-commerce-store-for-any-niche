<?php
require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';
$user = isAuthenticated() ? getCurrentUser() : null;
include(__DIR__ . '/../config/products.php');

// Function to get cart count for current user
function getCartCount($user_id) {
    global $con;
    
    $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    return $data['total'] ?? 0;
}

// Check if user is logged in and get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_count = getCartCount($_SESSION['user_id']);
}


// Get product slug or ID from URL
$product_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if product identifier is valid
if ($product_slug === '' && $product_id <= 0) {
    echo "<script>alert('Invalid product ID'); window.location.href='" . DOMAIN . "/index.php';</script>";
    exit;
}

// Fetch product details (by slug when available, otherwise keep ID as fallback)
$by_slug = ($product_slug !== '');
$product_query = "SELECT p.*, c.category_title, b.brand_title 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 LEFT JOIN brands b ON p.brand_id = b.brand_id 
                 WHERE " . ($by_slug ? "p.product_slug = ?" : "p.product_id = ?");
$stmt = mysqli_prepare($con, $product_query);
if ($by_slug) {
    mysqli_stmt_bind_param($stmt, "s", $product_slug);
} else {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Product not found'); window.location.href='" . DOMAIN . "/index.php';</script>";
    exit;
}

$product = mysqli_fetch_assoc($result);
$product_id = (int) $product['product_id'];
$product_slug = isset($product['product_slug']) ? $product['product_slug'] : $product_slug;

// Fetch product variants
$variants_query = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY original_price";
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
while ($row = mysqli_fetch_assoc($images_result)) {
    $images[] = $row;
}

// Get main image
$main_image = DOMAIN . "/assets/products/default.jpg"; // Default image if none found
foreach ($images as $image) {
    if ($image['is_main'] == 1) {
        $main_image = product_image_url($image['image_path']);
        break;
    }
}

// Get available colors
$colors = explode(',', $product['colors']);
// Trim whitespace from each color
$colors = array_map('trim', $colors);

// Get lowest price from variants
$lowest_price = 0;
if (!empty($variants)) {
    $lowest_price = $variants[0]['original_price'];
    $has_discount = false;
    foreach ($variants as $variant) {
        if ($variant['discount_price'] && $variant['discount_price'] > 0) {
            $has_discount = true;
            if ($variant['discount_price'] < $lowest_price) {
                $lowest_price = $variant['discount_price'];
            }
        } elseif ($variant['original_price'] < $lowest_price) {
            $lowest_price = $variant['original_price'];
        }
    }
}


// The reviews

$avg_rating_sql = "SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total_reviews
                  FROM reviews
                  WHERE product_id = ? AND status = 'approved'";

$stmt = mysqli_prepare($con, $avg_rating_sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$avg_result = mysqli_stmt_get_result($stmt);
$rating_data = mysqli_fetch_assoc($avg_result);

$avg_rating = $rating_data['avg_rating'] ?: 0;
$total_reviews = $rating_data['total_reviews'] ?: 0;

// Get reviews for this product (limited to 4 for display)
$reviews_sql = "SELECT r.review_id, r.rating, r.review_text, r.created_at,
               CONCAT(p.first_name, ' ', LEFT(p.last_name, 1), '.') as reviewer_name,
               p.profile_image
               FROM reviews r
               JOIN profiles p ON r.user_id = p.user_id
               WHERE r.product_id = ? AND r.status = 'approved'
               ORDER BY r.created_at DESC
               LIMIT 4";

$stmt = mysqli_prepare($con, $reviews_sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$reviews_result = mysqli_stmt_get_result($stmt);
$reviews = [];
while ($row = mysqli_fetch_assoc($reviews_result)) {
    $reviews[] = $row;
}

// Function to generate star rating HTML
function generateStarRating($rating) {
    $html = '<div class="flex items-center gap-1">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
$html .= '<i class="fa-solid fa-star text-[#FFC107] text-[16px] leading-none"></i>';
        } else {
            $html .= '<i class="fa-solid fa-star text-[#E0E0E0] text-[16px] leading-none"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}

require_once "../includes/auth/google.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | Product</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[<?php echo store_color('color_bg'); ?>]">
    <?php
     include('../includes/header.php');
    include('../includes/options.php');
    ?>
    
    <!-- Add to Cart Toast Notification -->
<div id="cart-toast" class="hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
    Item added to your cart!
</div>

        <section class="w-full bg-[<?php echo store_color('color_bg'); ?>] py-4">
            <div class="w-[90%] mx-auto max-w-[1440px]">

<div class="hidden md:flex items-center gap-1 cursor-pointer">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium"><?php echo $product['category_title']; ?></a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
              
                    <a href="./show.php?slug=<?php echo urlencode($product['product_slug']); ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">      <?php
                    $product_name = htmlspecialchars($product['product_name']);
    echo (strlen($product_name) > 20) ? substr($product_name, 0, 30) . '...' : $product_name; 
?></a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">View details</span>
                </div>

                <div class="flex items-center gap-1 cursor-pointer md:hidden">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">
<?php 
    // Truncate the category title to 10 characters maximum
    $truncated_title = (strlen($product['category_title']) > 10) 
        ? substr($product['category_title'], 0, 10) . '...' 
        : $product['category_title'];
    echo $truncated_title; 
?>
</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
              
                    <a href="./show.php?slug=<?php echo urlencode($product['product_slug']); ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">      <?php
                    $product_name = htmlspecialchars($product['product_name']);
    echo (strlen($product_name) > 9) ? substr($product_name, 0, 10) . '...' : $product_name; 
?></a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">View details</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto max-w-[1440px] flex flex-col md:flex-row gap-5">

            <div class="w-full flex flex-col gap-3">
            <div class="flex flex-col gap-2">
    <!-- Main Image -->
    <div class="w-full h-[397px] rounded-[4px] overflow-hidden">
        <img id="mainImage" src="<?php echo $main_image; ?>" class="w-full h-full object-cover" alt="<?php echo $product['product_name']; ?>" />
    </div>

    <!-- Thumbnail Images -->
    <div class="flex items-center gap-2">
        <?php foreach ($images as $index => $image): ?>
            <div class="w-[127.4px] h-[80px] rounded-[4px] overflow-hidden flex-shrink-0 cursor-pointer thumbnail-image"
                data-img="<?php echo htmlspecialchars(product_image_url($image['image_path'])); ?>" 
                onclick="changeMainImage('<?php echo htmlspecialchars(product_image_url($image['image_path'])); ?>')">
                <img src="<?php echo htmlspecialchars(product_image_url($image['image_path'])); ?>" class="w-full h-full object-cover" alt="Product image <?php echo $index + 1; ?>" />
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- The large screen reviews starts -->
<div class="w-full border-b-[1.5px] border-[#E1E1E1] hidden md:block">
    <div class="accordion w-full flex items-center justify-between cursor-pointer" id="accordionHeader">
        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">
            View Reviews (<?php echo $total_reviews; ?>)
        </span>
    </div>

    <div class="revs flex flex-col gap-3" id="accordionContent">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-4">
                <p class="text-[#5B5B5B] text-[14px] font-['Montserrat']">No reviews yet for this product.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../user/write-review.php?product_id=<?php echo $product_id; ?>" class="text-[<?php echo store_color('color_primary'); ?>] text-[14px] hover:underline mt-2 inline-block">Be the first to leave a review!</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="flex flex-col gap-2 border-b-[1px] pb-1 border-[#E1E1E1]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <?php 
                            $reviewer_avatar = !empty($review['profile_image']) 
                                ? DOMAIN . '/' . $review['profile_image'] 
                                : DOMAIN . '/assets/global/avatar.svg'; 
                            ?>
                            <img src="<?php echo htmlspecialchars($reviewer_avatar); ?>" 
                                 alt="<?php echo htmlspecialchars($review['reviewer_name']); ?>" 
                                 class="w-[24px] h-[24px] rounded-full object-cover" />
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-['Montserrat'] font-medium"><?php echo htmlspecialchars($review['reviewer_name']); ?></span>
                            <?php echo generateStarRating($review['rating']); ?>
                        </div>
                        <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular"><?php echo date('m/d/Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular"><?php echo htmlspecialchars($review['review_text']); ?></span>
                </div>
            <?php endforeach; ?>
            
            <?php if ($total_reviews > count($reviews)): ?>
                <div class="text-center mt-2">
                    <a href="../products/product-reviews.php?id=<?php echo $product_id; ?>" class="text-[<?php echo store_color('color_primary'); ?>] text-[14px] hover:underline">View all <?php echo $total_reviews; ?> reviews</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<!-- The large screen reviews ends -->


            </div>

            <div class="w-full flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-start gap-2"> <span class="text-[#262626] text-[24px] md:text-[28px] font-['Montserrat'] font-medium"><?php echo $product['product_name']; ?></span>
                    <?php if ($product['is_featured']): ?>
                            <button class="w-[fit-content] h-[fit-content] bg-[<?php echo store_color('color_primary'); ?>]  rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-[1.5px] px-2 text-nowrap">Featured</button>
                            <?php endif; ?>
 </div>
                    <span class="text-[#262626] text-[18px] md:text-[20px] font-['Montserrat'] font-medium">₦<?php echo number_format((float)$lowest_price, 2); ?></span>
                    <div class="text-[#5B5B5B] text-[14px] font-['Montserrat']">
                        <!-- Category: <?php echo $product['category_title']; ?> -->
                        <?php if (!empty($product['brand_title'])): ?>
                            Tag: <?php echo $product['brand_title']; ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
    <div class="flex items-center gap-1">
        <?php echo generateStarRating($avg_rating); ?>
    </div>
    <span class="text-[#9A9A9A] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">(<?php echo $total_reviews; ?> reviews)</span>
</div>


                </div>

                <div class="flex flex-col gap-3">
                    <div class="w-[90%] md:w-[70%] flex items-center gap-4">
                <!-- Color Selection -->
                         <div class="w-full flex flex-col gap-1">
                         <label class="text-[#262626] text-[13px]  md:text-[14px] font-Onest font-medium">Select Color</label>
    <select name="productColor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none">
        <?php 
        // Get the colors array and make sure it's valid
        $colors_array = !empty($product['colors']) ? explode(',', $product['colors']) : [];
        $colors_array = array_map('trim', $colors_array);
        
        // Set the first color as default
        $first_color = !empty($colors_array) ? $colors_array[0] : '';
        
        if (empty($colors_array)): 
        ?>
            <option value="">No colors available</option>
        <?php else: ?>
            <option value="">Select a color</option>
            <?php foreach ($colors_array as $color): ?>
                <option value="<?php echo htmlspecialchars($color); ?>" <?php echo $color === $first_color ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($color); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
                    
<div class="w-full flex flex-col gap-1">

    <label class="text-[#262626] text-[13px]  md:text-[14px] font-Onest font-medium">   Select Size</label>
    <select name="productSize" id="product-size" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="updatePrice(this)">
        <?php 
        // Fetch variants and check if they exist
        $variants_query = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY original_price";
        $stmt = mysqli_prepare($con, $variants_query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $variants_result = mysqli_stmt_get_result($stmt);
        
        $variants = [];
        while ($row = mysqli_fetch_assoc($variants_result)) {
            $variants[] = $row;
        }
        
        // Get first variant for default selection
        $first_variant = !empty($variants) ? $variants[0] : null;
        
        if (empty($variants)): 
        ?>
            <option value="">No sizes available</option>
        <?php else: ?>
            <option value="">Select a size</option>
            <?php foreach ($variants as $variant): 
                $variant_price = !empty($variant['discount_price']) ? $variant['discount_price'] : $variant['original_price'];
            ?>
                <option 
                    value="<?php echo htmlspecialchars($variant['variant_id']); ?>" 
                    data-price="<?php echo htmlspecialchars($variant_price); ?>"
                    data-texture="<?php echo htmlspecialchars($variant['texture']); ?>"
                    data-quantity="<?php echo htmlspecialchars($variant['quantity']); ?>"
                    <?php echo ($first_variant && $variant['variant_id'] == $first_variant['variant_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($variant['size']); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

                    </div>
                    

                    <div class="flex items-end gap-4">
                    <div class="w-[50%] flex flex-col gap-1">
   
    <label class="text-[#262626] text-[13px]  md:text-[14px] font-Onest font-medium">  Select Quantity</label>
    <select name="quantity" id="quantity-select" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="updateTotalPrice()">
        <?php 
        // Get max quantity from first variant or use default
        $first_variant_quantity = 5; // Default value
        
        if (!empty($variants)) {
            $first_variant_quantity = isset($variants[0]['quantity']) ? intval($variants[0]['quantity']) : 5;
        }
        
        $max_qty = min($first_variant_quantity, 10); // Limit to 10 max
        
        // Create options
        for($i = 1; $i <= $max_qty; $i++): 
        ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
</div>


                        <span class="text-[#262626] text-[16px] md:text-[17px] font-['Montserrat'] font-medium"> Total: ₦<span id="total-price"><?php echo number_format((float)$lowest_price, 2); ?></span></span>

                    </div>

                    <!-- <div class="flex items-center gap-4">
                        <a href="./products/cart.php" class="w-[180px] bg-[#E8E9F2] border-[1px] border-[#969AC4] rounded-[8px] text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2 text-center">Add to cart</a>

                        <a href="./checkout.php" class="text-center w-[180px] bg-[<?php echo store_color('color_primary'); ?>] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2">Buy now</a>
                        <img src="../assets/products/fav.svg" class="w-[24px] cursor-pointer" />
                    </div> -->

                    <div class="flex items-center gap-4">
    <button 
        id="add-to-cart-btn"
        class="w-[180px] bg-[#E8E9F2] border-[1px] border-[#969AC4] rounded-[8px] text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2 text-center"
    >
        Add to cart
    </button>

    <button 
        id="buy-now-btn"
        class="w-[180px] bg-[<?php echo store_color('color_primary'); ?>] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2 text-center"
    >
        Buy now
    </button>
    <?php $show_fav = isset($favorites[$product_id]) ? 'fa-solid text-[' . store_color('color_primary') . '] favorite-active' : 'fa-regular text-[#262626]'; ?>
<i id="product-fav-heart" data-product-id="<?php echo (int)$product_id; ?>" class="<?php echo $show_fav; ?> fa-heart text-[24px] cursor-pointer leading-none"></i>
</div>
                </div>

                <!--<div class="w-full flex flex-col gap-2 rounded-[8px] border-[#E1E1E1] border-[1px] p-3">
                    
             
                
                
                <div class="flex flex-col gap-0 border-b-[1.2px] py-0 border-[#E1E1E1]">
                <div class="accordion w-full flex items-center justify-between cursor-pointer">
                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Details</span>
                    </div> 

                    <div class="faqext text-[16px] font-regular text-[#777777]">
                        <p>
                        <?php echo $product['details']; ?>
                        </p>
                    </div>

                    </div>
                    

                      
                <div class="flex flex-col gap-0 border-b-[1.2px] py-0 border-[#E1E1E1]">
                <div class="accordion w-full flex items-center justify-between cursor-pointer">
                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Sizes</span>
                    </div> 

                    <div class="faqext text-[16px] font-regular text-[#777777]">
                    <p>
    <?php 
    if (!empty($product['sizes_details'])) {
        echo $product['sizes_details'];
    } else {
        echo "No sizes details available.";
    }
    ?>
</p>
                    </div>

                    </div>


                      
                <div class="flex flex-col gap-0 border-b-[1.2px] py-0 border-[#E1E1E1]">
                <div class="accordion w-full flex items-center justify-between cursor-pointer">
                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Warranty</span>
                    </div> 

                    <div class="faqext text-[16px] font-regular text-[#777777]">
                        <p>
                        <?php echo $product['warranty']; ?>
                        </p>
                    </div>

                    </div>


                      
                <div class="flex flex-col gap-0">
                <div class="accordion w-full flex items-center justify-between cursor-pointer">
                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Care</span>
                    </div> 

                    <div class="faqext text-[16px] font-regular text-[#777777]">
                        <p>
                        <?php echo $product['care']; ?>
                        </p>
                    </div>

                    </div>

                      
               

                   


                </div>-->

            </div>

<!-- The mobile reviews starts -->
<div class="w-full border-b-[1.5px] border-[#E1E1E1] md:hidden">
    <div class="accordion w-full flex items-center justify-between cursor-pointer" id="mobileAccordionHeader">
        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">
            View Reviews (<?php echo $total_reviews; ?>)
        </span>
    </div>

    <div class="faqext flex flex-col gap-3 transition-all duration-300 ease-in-out" id="mobileAccordionContent">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-4">
                <p class="text-[#5B5B5B] text-[14px] font-['Montserrat']">No reviews yet for this product.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../user/write-review.php?product_id=<?php echo $product_id; ?>" class="text-[<?php echo store_color('color_primary'); ?>] text-[14px] hover:underline mt-2 inline-block">Be the first to leave a review!</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="flex flex-col gap-2 border-b-[1px] pb-1 border-[#E1E1E1]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <?php 
                            $reviewer_avatar = !empty($review['profile_image']) 
                                ? DOMAIN . '/' . $review['profile_image'] 
                                : DOMAIN . '/assets/global/avatar.svg'; 
                            ?>
                            <img src="<?php echo htmlspecialchars($reviewer_avatar); ?>" 
                                 alt="<?php echo htmlspecialchars($review['reviewer_name']); ?>" 
                                 class="w-[24px] h-[24px] rounded-full object-cover" />
                            <?php echo generateStarRating($review['rating']); ?>
                        </div>
                        <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular"><?php echo date('m/d/Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular"><?php echo htmlspecialchars($review['review_text']); ?></span>
                </div>
            <?php endforeach; ?>
            
            <?php if ($total_reviews > count($reviews)): ?>
                <div class="text-center mt-2">
                    <a href="../products/product-reviews.php?id=<?php echo $product_id; ?>" class="text-[<?php echo store_color('color_primary'); ?>] text-[14px] hover:underline">View all <?php echo $total_reviews; ?> reviews</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<!-- The mobile reviews ends -->
        </div>


        <div class="w-[90%] mx-auto max-w-[1440px] mt-[3rem]"> <span class="text-[#262626] text-[22px]  md:text-[27px] font-Onest font-regular">You May Also Like</span></div>

        

        <!-- The similar produts starts -->
         <div class="reviews-container" id="reviews">
         <!-- <div class="reviews-wrapper w-[90%] mx-auto max-w-[1440px] flex items-center gap-5 bg-[#FFFFFF] py-3 overflow-scroll mb-5"> -->
        <div class="reviews-wrapper">
            <!-- The products cards -->

            <?php 
        // Optional: Include filter.php if you need it
        // include(__DIR__ . '/filter.php');
        
        // IMPORTANT: Include products.php first to get product data
        include(__DIR__ . '/../config/products.php');
        
        // Then include product-lists.php to display products
        include(__DIR__ . '/flex-product-lists.php');
        ?>
      
        </div>
        </div>
        <!-- The similar produts ends -->




        <div id="cart-message" class="hidden mt-2 p-2 text-green-700 bg-green-100 rounded-md"></div>

        <?php
        include(__DIR__ . '/../includes/footer.php');

?>
    </main>


   
    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>



<script>
 function changeMainImage(imgSrc) {
        // Target the main image element by its id and change its src
        document.getElementById('mainImage').src = imgSrc;
    }


    document.addEventListener("DOMContentLoaded", function () {
    const mobileAccordionHeader = document.getElementById("mobileAccordionHeader");
    const mobileAccordionContent = document.getElementById("mobileAccordionContent");
    
    // Track state with a variable instead of relying on style checking
    let isOpen = true;
    
    // Keep it open by default
    mobileAccordionContent.style.maxHeight = mobileAccordionContent.scrollHeight + "px";
    
    mobileAccordionHeader.addEventListener("click", function () {
        isOpen = !isOpen; // Toggle state
        
        if (isOpen) {
            mobileAccordionContent.style.maxHeight = mobileAccordionContent.scrollHeight + "px";
        } else {
            mobileAccordionContent.style.maxHeight = "0px";
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const accordionHeader = document.getElementById("accordionHeader");
    const accordionContent = document.getElementById("accordionContent");
    
    // Set initial state - open by default
    let isOpen = true;
    
    // Initial setup for animation
    accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
    accordionContent.style.overflow = "hidden";
    accordionContent.style.transition = "max-height 0.3s ease-in-out";
    
    accordionHeader.addEventListener("click", function () {
        isOpen = !isOpen;
        
        if (isOpen) {
            // Open the accordion with animation
            accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
        } else {
            // Close the accordion with animation
            accordionContent.style.maxHeight = "0px";
        }
    });
});


    


    // Variables to store selected values
let selectedVariantId = null;
let selectedColor = null;
let selectedQuantity = 1;
let currentPrice = 0;
let maxQuantity = 5;

// Function to update price when size is changed
function updatePrice(sizeSelect) {
    const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
    
    if (!selectedOption.value) {
        // No option selected
        return;
    }
    
    const variantId = selectedOption.value;
    const price = parseFloat(selectedOption.getAttribute('data-price'));
    const texture = selectedOption.getAttribute('data-texture');
    const maxQty = parseInt(selectedOption.getAttribute('data-quantity'));
    
    // Update selected variant and price
    selectedVariantId = variantId;
    currentPrice = price;
    maxQuantity = maxQty;
    
    // Update texture display if available
    if (texture) {
        const textureElement = document.getElementById('selected-texture');
        if (textureElement) {
            textureElement.innerText = texture;
            document.getElementById('texture-display').classList.remove('hidden');
        }
    } else {
        const textureDisplay = document.getElementById('texture-display');
        if (textureDisplay) {
            textureDisplay.classList.add('hidden');
        }
    }
    
    // Update quantity dropdown options
    updateQuantityOptions(maxQty);
    
    // Update total price
    updateTotalPrice();
}

// Function to update quantity options based on available stock
function updateQuantityOptions(maxQty) {
    const quantitySelect = document.getElementById('quantity-select');
    if (!quantitySelect) return;
    
    const currentValue = parseInt(quantitySelect.value) || 1;
    
    // Remember current selection
    const previousValue = currentValue;
    
    // Clear existing options
    quantitySelect.innerHTML = '';
    
    // Add new options
    const limit = Math.min(maxQty, 10);
    for (let i = 1; i <= limit; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        quantitySelect.appendChild(option);
    }
    
    // Restore previous selection if possible
    if (previousValue <= limit) {
        quantitySelect.value = previousValue;
    } else {
        quantitySelect.value = 1;
    }
    
    selectedQuantity = parseInt(quantitySelect.value);
}

// Update total price based on quantity and selected variant
function updateTotalPrice() {
    const quantitySelect = document.getElementById('quantity-select');
    if (quantitySelect) {
        selectedQuantity = parseInt(quantitySelect.value) || 1;
    }
    
    const total = currentPrice * selectedQuantity;
    const totalElement = document.getElementById('total-price');
    if (totalElement) {
        totalElement.innerText = total.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize size select first to get the price
    const sizeSelect = document.getElementById('product-size');
    if (sizeSelect && sizeSelect.options.length > 0) {
        // If there's a selected option, use that
        if (sizeSelect.selectedIndex > 0) {
            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            currentPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            selectedVariantId = selectedOption.value;
            
            // Handle texture display
            const texture = selectedOption.getAttribute('data-texture');
            if (texture) {
                const textureElement = document.getElementById('selected-texture');
                if (textureElement) {
                    textureElement.innerText = texture;
                    document.getElementById('texture-display').classList.remove('hidden');
                }
            }
            
            maxQuantity = parseInt(selectedOption.getAttribute('data-quantity')) || 5;
        } 
        // If nothing selected but options exist, select the first option
        else if (sizeSelect.options.length > 1) {
            sizeSelect.selectedIndex = 1; // Skip the "Select a size" option
            const firstOption = sizeSelect.options[1];
            currentPrice = parseFloat(firstOption.getAttribute('data-price')) || 0;
            selectedVariantId = firstOption.value;
            
            // Handle texture display
            const texture = firstOption.getAttribute('data-texture');
            if (texture) {
                const textureElement = document.getElementById('selected-texture');
                if (textureElement) {
                    textureElement.innerText = texture;
                    document.getElementById('texture-display').classList.remove('hidden');
                }
            }
            
            maxQuantity = parseInt(firstOption.getAttribute('data-quantity')) || 5;
        }
        
        // Add change event listener
        sizeSelect.addEventListener('change', function() {
            updatePrice(this);
        });
    }
    
    // Initialize quantity select
    const quantitySelect = document.getElementById('quantity-select');
    if (quantitySelect) {
        selectedQuantity = parseInt(quantitySelect.value) || 1;
        quantitySelect.addEventListener('change', updateTotalPrice);
    }
    
    // Calculate initial total price
    updateTotalPrice();
    
    // Thumbnail image clicks
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        thumb.addEventListener('click', function() {
            const mainImg = document.querySelector('.w-full.h-[397px].rounded-[4px].overflow-hidden img');
            if (mainImg) {
                mainImg.src = this.getAttribute('data-img');
            }
        });
    });

    // Buttons
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        thumb.addEventListener('click', function() {
            const mainImg = document.querySelector('.w-full.h-[397px].rounded-[4px].overflow-hidden img');
            if (mainImg) {
                mainImg.src = this.getAttribute('data-img');
            }
        });
    });
});

// Validate that necessary options are selected
function validateSelection() {
    const sizeSelect = document.getElementById('product-size');
    if (!sizeSelect || !sizeSelect.value) {
        alert('Please select a size');
        return false;
    }
    
    const colorSelect = document.querySelector('select[name="productColor"]');
    if (colorSelect && colorSelect.options.length > 1 && !colorSelect.value) {
        alert('Please select a color');
        return false;
    }
    
    return true;
}

(function(){
    function waitCart(){
        return new Promise(function(res){
            function chk(){if(window.GlorifyCart)res(window.GlorifyCart);else setTimeout(chk,60);}
            chk();
        });
    }
    document.addEventListener('DOMContentLoaded', function(){
        waitCart().then(function(C){
            var pid=<?php echo (int)$product_id; ?>;
            C.setDetailPid(pid);
            var addBtn=document.getElementById('add-to-cart-btn');
            var buyBtn=document.getElementById('buy-now-btn');
            if(addBtn){
                addBtn.addEventListener('click', function(){
                    if(typeof validateSelection==='function'&&!validateSelection())return;
                    var vid=document.getElementById('product-size')?document.getElementById('product-size').value:null;
                    var qty=document.getElementById('quantity-select')?document.getElementById('quantity-select').value:1;
                    if(C.has(pid,vid||null))C.remove(pid,vid||null);
                    else C.add(pid,vid||null,qty,{toast:'Added to cart'});
                });
            }
            if(buyBtn){
                buyBtn.addEventListener('click', function(){
                    if(typeof validateSelection==='function'&&!validateSelection())return;
                    var vid=document.getElementById('product-size')?document.getElementById('product-size').value:null;
                    var qty=document.getElementById('quantity-select')?document.getElementById('quantity-select').value:1;
                    C.add(pid,vid||null,qty,{onSuccess:function(){window.location.href='<?php echo DOMAIN; ?>/products/checkout.php';}});
                });
            }
            C.syncDetailBtn();
        });
    });
})();
</script>
</body>

</html>