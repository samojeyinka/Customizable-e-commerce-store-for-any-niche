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


// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if product ID is valid
if ($product_id <= 0) {
    // echo "<script>alert('Invalid product ID'); window.location.href='./index.php';</script>";
    echo "<script>alert('Invalid product ID'); window.location.href='" . DOMAIN . "/index.php';</script>";
    exit;
}

// Fetch product details
$product_query = "SELECT p.*, c.category_title, b.brand_title 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 LEFT JOIN brands b ON p.brand_id = b.brand_id 
                 WHERE p.product_id = ?";
$stmt = mysqli_prepare($con, $product_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    // echo "<script>alert('Product not found'); window.location.href='../index.php';</script>";
    echo "<script>alert('Product not found'); window.location.href='" . DOMAIN . "/index.php';</script>";
    exit;
}

$product = mysqli_fetch_assoc($result);

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
        $main_image = DOMAIN . "/assets/products/" . $image['image_path'];
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
            $html .= '<img src="' . DOMAIN . '/assets/products/star.svg" class="w-[16px]" />';
        } else {
            $html .= '<img src="' . DOMAIN . '/assets/products/lstar.svg" class="w-[16px]" />';
        }
    }
    $html .= '</div>';
    return $html;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Product</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />

    <style>
                .custom-dropdown {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .arrow-down {
            margin-left: 5px;
            font-size: 14px;
            transition: transform 0.3s ease;
            color: gray;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 40px;
            left: 0;
            background: white;

            border-radius: 4px;
            min-width: 120px;
            width: fit-content;
            z-index: 10;
            text-wrap: nowrap;
            border: 1px solid #E1E1E1;
            padding: 10px;
        }


        .open .arrow-down {
            transform: rotate(180deg);
        }

        .open .dropdown-content {
            display: block;
        }


        .faqext,
        .menufaqext {
            max-height: 0;
            overflow: hidden;
            transition: max-height .5s ease;
        }

        .faqext p {
            text-align: left;
            padding-left: 2%;
            padding-right: 2%;
            padding: 0px;
        }



        .accordion:after,
        .menu-accordion:after {
            content: '\2039';
            font-size: 2.4rem;
            color: #9A9A9A;
            font-weight: 100;
            transform: rotate(-90deg);
        }


        .reviews-container {
margin: auto;
width: 90%;
max-width: 90%;
overflow-x: auto;
white-space: nowrap;
scroll-behavior: smooth;
padding: 10px 0;
cursor: grab;
}

.reviews-wrapper {
display: inline-flex;
gap: 15px;
padding: 10px;
}

.review {
min-width: 282px;
max-width: 282px;
text-wrap: wrap;
user-select: none;
}


.reviews-container::-webkit-scrollbar {
display: none;
}


.active-thumbnail {
    border: 2px solid #3498db; /* or any color that matches your design */
    opacity: 1;
}

.thumbnail-image {
    opacity: 0.7;
    transition: all 0.3s ease;
}

.thumbnail-image:hover {
    opacity: 0.9;
}


    </style>
</head>

<body>
    <main class="bg-[#FEFEFE]">
    <?php
     include('../includes/header.php');
    include('../includes/options.php');
    ?>
    
    <!-- Add to Cart Toast Notification -->
<div id="cart-toast" class="hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
    Item added to your cart!
</div>

        <section class="w-full bg-[#FFFFFFF] py-4">
            <div class="w-[90%] mx-auto">

<div class="hidden md:flex items-center gap-1 cursor-pointer">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium"><?php echo $product['category_title']; ?></a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
              
                    <a href="./show.php?id=<?php echo $product['product_id']; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">      <?php
                    $product_name = htmlspecialchars($product['product_name']);
    echo (strlen($product_name) > 20) ? substr($product_name, 0, 30) . '...' : $product_name; 
?></a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">View details</span>
                </div>

                <div class="flex items-center gap-1 cursor-pointer md:hidden">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">
<?php 
    // Truncate the category title to 10 characters maximum
    $truncated_title = (strlen($product['category_title']) > 10) 
        ? substr($product['category_title'], 0, 10) . '...' 
        : $product['category_title'];
    echo $truncated_title; 
?>
</a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
              
                    <a href="./show.php?id=<?php echo $product['product_id']; ?>" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">      <?php
                    $product_name = htmlspecialchars($product['product_name']);
    echo (strlen($product_name) > 9) ? substr($product_name, 0, 10) . '...' : $product_name; 
?></a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">View details</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto flex flex-col md:flex-row gap-5">

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
                data-img="<?php echo DOMAIN; ?>/assets/products/<?php echo $image['image_path']; ?>" 
                onclick="changeMainImage('<?php echo DOMAIN; ?>/assets/products/<?php echo $image['image_path']; ?>')">
                <img src="<?php echo DOMAIN; ?>/assets/products/<?php echo $image['image_path']; ?>" class="w-full h-full object-cover" alt="Product image <?php echo $index + 1; ?>" />
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
                <a href="../user/write-review.php?product_id=<?php echo $product_id; ?>" class="text-[#1A237E] text-[14px] hover:underline mt-2 inline-block">Be the first to leave a review!</a>
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
                    <a href="../products/product-reviews.php?id=<?php echo $product_id; ?>" class="text-[#1A237E] text-[14px] hover:underline">View all <?php echo $total_reviews; ?> reviews</a>
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
                            <button class="w-[fit-content] h-[fit-content] bg-[#D51E5E]  rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-[1.5px] px-2 text-nowrap">Featured</button>
                            <?php endif; ?>
 </div>
                    <span class="text-[#262626] text-[18px] md:text-[20px] font-['Montserrat'] font-medium">₦<?php echo number_format($lowest_price, 2); ?></span>
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


                        <span class="text-[#262626] text-[16px] md:text-[17px] font-['Montserrat'] font-medium"> Total: ₦<span id="total-price"><?php echo number_format($lowest_price, 2); ?></span></span>

                    </div>

                    <!-- <div class="flex items-center gap-4">
                        <a href="./products/cart.php" class="w-[180px] bg-[#E8E9F2] border-[1px] border-[#969AC4] rounded-[8px] text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2 text-center">Add to cart</a>

                        <a href="./checkout.php" class="text-center w-[180px] bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2">Buy now</a>
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
        class="w-[180px] bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2 text-center"
    >
        Buy now
    </button>
    <img src="<?php echo DOMAIN; ?>/assets/products/fav.svg" class="w-[24px] cursor-pointer" />
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
                <a href="../user/write-review.php?product_id=<?php echo $product_id; ?>" class="text-[#1A237E] text-[14px] hover:underline mt-2 inline-block">Be the first to leave a review!</a>
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
                    <a href="../products/product-reviews.php?id=<?php echo $product_id; ?>" class="text-[#1A237E] text-[14px] hover:underline">View all <?php echo $total_reviews; ?> reviews</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<!-- The mobile reviews ends -->
        </div>


        <div class="w-[90%] mx-auto mt-[3rem]"> <span class="text-[#262626] text-[22px]  md:text-[27px] font-Onest font-regular">You May Also Like</span></div>

        

        <!-- The similar produts starts -->
         <div class="reviews-container" id="reviews">
         <!-- <div class="reviews-wrapper w-[90%] mx-auto flex items-center gap-5 bg-[#FFFFFF] py-3 overflow-scroll mb-5"> -->
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


document.addEventListener('DOMContentLoaded', function() {
    // Cart functionality
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            if (!validateSelection()) return;
            
            // Get selected values
            const productId = <?php echo $product_id; ?>;
            const variantId = document.getElementById('product-size').value;
            const quantity = document.getElementById('quantity-select').value;
            
            // Create form data for AJAX request
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('variant_id', variantId);
            formData.append('quantity', quantity);
            
            // Change button text to indicate loading
            const originalText = addToCartBtn.textContent;
            addToCartBtn.textContent = 'Adding...';
            addToCartBtn.disabled = true;
            
            // Send AJAX request to add to cart
            fetch('./add-to-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const cartMessage = document.getElementById('cart-message');
                    cartMessage.textContent = 'Product added to cart successfully!';
                    cartMessage.classList.remove('hidden');
                    
                    // Change button text to "Added to cart"
                    addToCartBtn.textContent = 'Added to cart';
                    
                    // Add a visual indicator class to the button
                    addToCartBtn.classList.remove('bg-[#E8E9F2]');
                    addToCartBtn.classList.add('bg-[#E1F5E6]');
                    addToCartBtn.classList.add('border-[#4CAF50]');
                    
                    // Hide message after 3 seconds
                    setTimeout(() => {
                        cartMessage.classList.add('hidden');
                    }, 3000);
                    
                    // Reset button text after 3 seconds
                    setTimeout(() => {
                        addToCartBtn.textContent = originalText;
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.add('bg-[#E8E9F2]');
                        addToCartBtn.classList.remove('bg-[#E1F5E6]');
                        addToCartBtn.classList.remove('border-[#4CAF50]');
                    }, 3000);
                    
                    // Update cart count in header if it exists
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                        
                        // Make sure the cart badge is visible
                        const cartBadgeElement = document.getElementById('cart-badge');
                        if (cartBadgeElement) {
                            cartBadgeElement.classList.remove('hidden');
                        }
                    }
                } else {
                    // Reset button text
                    addToCartBtn.textContent = originalText;
                    addToCartBtn.disabled = false;
                    
                    // Handle error
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Error adding product to cart.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                
                // Reset button text
                addToCartBtn.textContent = originalText;
                addToCartBtn.disabled = false;
            });
        });
    }
    
    // Buy Now functionality remains the same
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function() {
            if (!validateSelection()) return;
            
            // First add to cart
            const productId = <?php echo $product_id; ?>;
            const variantId = document.getElementById('product-size').value;
            const quantity = document.getElementById('quantity-select').value;
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('variant_id', variantId);
            formData.append('quantity', quantity);
            
            // Send AJAX request to add to cart
            fetch('./add-to-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to checkout
                    window.location.href = '../products/checkout.php';
                } else {
                    // Handle error
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Error adding product to cart.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
});

// Update cart count in header if it exists
const cartCountElement = document.getElementById('cart-count');
if (cartCountElement) {
    cartCountElement.textContent = data.cart_count;
    
    // Make sure the cart badge is visible
    const cartBadgeElement = document.getElementById('cart-badge');
    if (cartBadgeElement) {
        cartBadgeElement.classList.remove('hidden');
    }
}
</script>
</body>

</html>