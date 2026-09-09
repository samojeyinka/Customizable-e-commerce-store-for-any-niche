<?php
require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';
$user = isAuthenticated() ? getCurrentUser() : null;
include(__DIR__ . '/../config/products.php');

// Check if user is logged in
if (!isAuthenticated()) {
    // Redirect to login page
    header('Location: ' . DOMAIN . '/includes/auth/login/signin.php');
    exit;
}

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Get user's favorites with product details including first variant info
$query = "
    SELECT 
        f.favorite_id,
p.product_id,
        p.product_name,
        p.product_slug,
        p.is_featured,
        p.colors,
        c.category_title,
        b.brand_title,
        i.image_path AS main_image,
        MIN(v.original_price) AS min_price,
        MIN(CASE WHEN v.discount_price > 0 THEN v.discount_price ELSE NULL END) AS min_discount_price,
        (SELECT size FROM product_variants WHERE product_id = p.product_id ORDER BY original_price LIMIT 1) AS first_size,
        (SELECT texture FROM product_variants WHERE product_id = p.product_id ORDER BY original_price LIMIT 1) AS first_texture,
        (SELECT quantity FROM product_variants WHERE product_id = p.product_id ORDER BY original_price LIMIT 1) AS first_quantity,
        (SELECT status FROM product_variants WHERE product_id = p.product_id ORDER BY original_price LIMIT 1) AS first_status
    FROM 
        favorites f
    INNER JOIN 
        products p ON f.product_id = p.product_id
    LEFT JOIN 
        categories c ON p.category_id = c.category_id
    LEFT JOIN 
        brands b ON p.brand_id = b.brand_id
    LEFT JOIN 
        product_images i ON p.product_id = i.product_id AND i.is_main = 1
    LEFT JOIN 
        product_variants v ON p.product_id = v.product_id
    WHERE 
        f.user_id = ?
    GROUP BY 
        f.favorite_id, p.product_id, p.product_name, p.product_slug, p.is_featured, p.colors, c.category_title, b.brand_title, i.image_path
    ORDER BY 
        f.created_at DESC
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$favorite_products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get the user's cart count
$cart_count = 0;
$cart_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
$cart_stmt = mysqli_prepare($con, $cart_query);
mysqli_stmt_bind_param($cart_stmt, "i", $user_id);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);
$cart_data = mysqli_fetch_assoc($cart_result);
$cart_count = $cart_data['total'] ?? 0;

// In your PHP section, fetch the cart items for the current user
$cart_items = [];
$cart_items_query = "SELECT product_id, cart_id FROM cart WHERE user_id = ?";
$cart_items_stmt = mysqli_prepare($con, $cart_items_query);
mysqli_stmt_bind_param($cart_items_stmt, "i", $user_id);
mysqli_stmt_execute($cart_items_stmt);
$cart_items_result = mysqli_stmt_get_result($cart_items_stmt);

while ($item = mysqli_fetch_assoc($cart_items_result)) {
    $cart_items[$item['product_id']] = $item['cart_id'];
}


require_once "../includes/auth/google.php";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | Favourites</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[<?php echo store_color('color_bg'); ?>]">
      
    <?php
     include(__DIR__ . '/../includes/header.php');
     include(__DIR__ . '/../includes/options.php');
        ?>

<div id="cart-toast" class="hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
            Item added to your cart!
        </div>

        <!-- Favorites Removed Notification -->
        <div id="favorites-toast" class="hidden fixed bottom-4 right-4 bg-red-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
            Item removed from favorites!
        </div>


        <section class="w-full pt-7 pb-3 border-b border-[#262626]/[0.05]">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-2 text-[12px] font-['Montserrat'] font-medium tracking-[0.03em]">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#5F5F5F] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors duration-200">Home</a>
                    <i class="fa-solid fa-chevron-right text-[9px] text-[#262626]/20 leading-none"></i>
                    <span class="text-[<?php echo store_color('color_heading'); ?>] font-semibold">My Favourites</span>
                </div>
            </div>
        </section>

        <?php if (count($favorite_products) > 0): ?>
        <div class="w-full bg-[<?php echo store_color('color_bg'); ?>] py-6 md:py-10">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <h1 class="text-[<?php echo store_color('color_heading'); ?>] text-[30px] md:text-[38px] leading-[1.12] font-['Cormorant_Garamond'] font-medium mb-6 md:mb-8">My Favourites</h1>
            </div>
            <div class="w-[90%] mx-auto max-w-[1440px] hidden md:block bg-white rounded-[20px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)] overflow-x-auto">


                <table class="w-full [&_th]:px-5 [&_th]:py-4 [&_td]:px-5 [&_td]:py-4 [&_td]:border-b [&_td]:border-[#262626]/[0.05] [&_td]:align-middle">
                    <thead class="text-[#6B6B6B] text-[11px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-left bg-[#FBF9FA] border-b border-[#262626]/10">
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Add</th>
                        <th>Action</th>

                    </thead>

                    <tbody class="">
                    <?php foreach ($favorite_products as $product): ?>
                        <tr class="favorite-item" data-favorite-id="<?php echo $product['favorite_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                            <td class="flex items-center gap-4 align-middle">

                                <div class="w-[96px] h-[96px] shrink-0 rounded-[12px] overflow-hidden">
                                <a href="<?php echo product_url($product); ?>">
                                    <img src="<?php echo !empty($product['main_image']) ? product_image_url($product['main_image']) : DOMAIN . '/assets/products/default.svg'; ?>" 
                                        class="w-full h-full object-cover" 
                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                                </a>
                                </div>
                                <div class="flex flex-col gap-[2px]">
                                    <p class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">       <?php echo htmlspecialchars($product['product_name']); ?></p>
                                    <?php if (!empty($product['first_size'])): ?>
                                        <p  class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
                                            Size: <?php echo htmlspecialchars($product['first_size']); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php 
                                    // Get first color if available
                                    $colors = explode(',', $product['colors']);
                                    $firstColor = trim($colors[0]);
                                    if (!empty($firstColor)): 
                                    ?>
                                        <p  class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
                                            Color: <?php echo htmlspecialchars($firstColor); ?>
                                    </p>
                                    <?php endif; ?>

                                </div>

                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                            <div>
                                            <?php if (!empty($product['min_discount_price'])): ?>
                                                <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">
                                                    ₦<?php echo number_format((float)$product['min_discount_price']); ?>
                                                </span>
                                                <span class="text-gray-500 text-xs line-through ml-1">
                                                    ₦<?php echo number_format((float)$product['min_price']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">
                                                    ₦<?php echo number_format((float)$product['min_price']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                            </td>




                            <td>

                                <button type="submit" class="inline-block px-3.5 py-1.5 text-white text-[10px] tracking-[0.1em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full whitespace-nowrap <?php echo $product['first_quantity'] > 0 ? 'bg-[#39D959]' : 'bg-[#262626]'; ?>">
                                            <?php echo $product['first_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                            </button>
                            </td>

                            <td>

                                <?php if (array_key_exists($product['product_id'], $cart_items)): ?>
                                        <button 
                                            class="cart-toggle-button py-1 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-full tracking-[0.1em] uppercase"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-cart-id="<?php echo $cart_items[$product['product_id']]; ?>"
                                            data-in-cart="true">
                                            Added to Cart
                                        </button>
                                    <?php else: ?>
                                        <button 
                                                                                       class="cart-toggle-button py-1 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-full tracking-[0.1em] uppercase"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-in-cart="false">
                                            Add to Cart
                                        </button>
                                    <?php endif; ?>
                            </td>


                            <td>
                            <button 
                                    class="remove-favorite-btn text-[15px] md:text-[16px] font-['Open Sans] text-[#EE3F3F] font-['Montserrat'] font-semibold underline underline-offset-4 cursor-pointer hover:opacity-70 transition-opacity"
                                    data-favorite-id="<?php echo $product['favorite_id']; ?>"
                                    data-product-id="<?php echo $product['product_id']; ?>">
                                 Remove from favorite
                                </button>

                            </td>

                        </tr>
                        <?php endforeach; ?>
                     

                    </tbody>
                </table>



            </div>



            <div class="w-[90%] mx-auto max-w-[1440px] mt-6 md:hidden">
                <div class="w-full flex flex-col gap-4">
                    <?php foreach ($favorite_products as $product): ?>
                    <div class="favorite-item border border-[#262626]/10 rounded-[16px] p-4 flex flex-col gap-3 bg-white shadow-[0_2px_14px_-8px_rgba(0,0,0,0.12)]" data-favorite-id="<?php echo $product['favorite_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">

                        <div class="flex items-center justify-between">
                            <button type="submit" class="px-3 py-1.5 <?php echo $product['first_quantity'] > 0 ? 'bg-[#39D959]' : 'bg-[#262626]'; ?> text-white text-[10px] tracking-[0.1em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full whitespace-nowrap"><?php echo $product['first_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?></button>
                            <button class="remove-favorite-btn text-[14px] font-['Open Sans'] text-[#EE3F3F] font-['Montserrat'] font-semibold underline underline-offset-4 cursor-pointer hover:opacity-70 transition-opacity" data-favorite-id="<?php echo $product['favorite_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">Remove from favorite</button>
                        </div>

                        <div class="w-full h-px bg-[#262626]/[0.06]"></div>

                        <div class="flex justify-between">
                            <div class="flex flex-col gap-2">

                                <div class="flex gap-2">

                                    <div class="w-[88px] h-[88px] rounded-[12px] overflow-hidden shrink-0">
                                        <img src="<?php echo !empty($product['main_image']) ? product_image_url($product['main_image']) : DOMAIN . '/assets/products/default.svg'; ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                                    </div>
                                    <div class="flex flex-col gap-[2px]">
                                        <p class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> <?php echo htmlspecialchars($product['product_name']); ?></p>
                                        <?php $firstColor = trim(explode(',', $product['colors'])[0]); ?>
                                        <?php if (!empty($firstColor)): ?>
                                        <p class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> <?php echo htmlspecialchars($firstColor); ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($product['first_size'])): ?>
                                        <p class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> <?php echo htmlspecialchars($product['first_size']); ?></p>
                                        <?php endif; ?>
                                        <p class="text-[#5F5F5F] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Quantity:</b> 1</p>
                                    </div>

                                </div>

                                <?php if (array_key_exists($product['product_id'], $cart_items)): ?>
                                <button class="cart-toggle-button w-[fit-content] rounded-full py-1.5 px-4 tracking-[0.1em] uppercase bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] font-['Open Sans'] cursor-pointer" data-product-id="<?php echo $product['product_id']; ?>" data-cart-id="<?php echo $cart_items[$product['product_id']]; ?>" data-in-cart="true">Added to Cart</button>
                                <?php else: ?>
                                <button class="cart-toggle-button w-[fit-content] rounded-full py-1.5 px-4 tracking-[0.1em] uppercase bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] font-['Open Sans'] cursor-pointer" data-product-id="<?php echo $product['product_id']; ?>" data-in-cart="false">Add to Cart</button>
                                <?php endif; ?>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                                <?php if (!empty($product['min_discount_price'])): ?>
                                    ₦<?php echo number_format((float)$product['min_discount_price']); ?>
                                <?php else: ?>
                                    ₦<?php echo number_format((float)$product['min_price']); ?>
                                <?php endif; ?>
                            </p>
                        </div>

                    </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
        <?php else: ?> 
                <div class="w-[90%] mx-auto max-w-[1440px] py-16">
                    <div class="flex flex-col items-center gap-5 text-center bg-white rounded-[20px] border border-[#262626]/10 shadow-[0_4px_24px_-12px_rgba(0,0,0,0.08)] px-6 py-16">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background-color:var(--glor-tint)">
                            <i class="fa-regular fa-heart text-[30px] leading-none" style="color:var(--glor-primary)" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-[20px] md:text-[22px] font-['Montserrat'] font-semibold text-[#262626]">You have not add any item to favorite</h3>
                    </div>
                </div>
            <?php endif; ?>

        <?php

include(__DIR__ . '/../includes/footer.php');
        ?>
    </main>


   
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove from favorites functionality
        const removeFavoriteButtons = document.querySelectorAll('.remove-favorite-btn');
        const favoritesToast = document.getElementById('favorites-toast');
        
        removeFavoriteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const favoriteId = this.getAttribute('data-favorite-id');
                const productId = this.getAttribute('data-product-id');
                const favoriteItems = document.querySelectorAll(`.favorite-item[data-favorite-id="${favoriteId}"]`);
                
                // Send AJAX request to remove from favorites
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('action', 'remove');
                
                fetch('./toggle-favorite.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item(s) from DOM with animation
                        favoriteItems.forEach(item => {
                            item.style.transition = 'all 0.3s ease';
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.9)';
                        });
                        
                        setTimeout(() => {
                            favoriteItems.forEach(item => item.remove());
                            
                            // Check if there are no more favorites
                            if (document.querySelectorAll('.favorite-item').length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 300);
                        
                        // Show toast
                        favoritesToast.classList.remove('hidden');
                        setTimeout(() => {
                            favoritesToast.classList.add('hidden');
                        }, 3000);
                    } else {
                        alert(data.message || 'Error removing from favorites');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
        });
        
    </script>

    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>
</body>

</html