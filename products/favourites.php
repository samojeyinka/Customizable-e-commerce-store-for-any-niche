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
    header('Location: /includes/auth/login/signin.php');
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
        f.favorite_id, p.product_id, p.product_name, p.is_featured, p.colors, c.category_title, b.brand_title, i.image_path
    ORDER BY 
        f.date_added DESC
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Favourites</title>
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


        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Favourites</span>
                </div>
            </div>
        </section>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="w-full bg-[#FFFFFF] py-5">
            <div class="w-[90%] mx-auto hidden md:block">


                <table cols="" class="w-full">
                    <thead class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Add</th>
                        <th>Action</th>

                    </thead>

                    <tbody class="">
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <tr data-favorite-id="<?php echo $product['favorite_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                            <td class="py-3 flex gap-2">

                                <div class="w-[131.64px] h-[88.73px] rounded-[4px] overflow-hidden">
                                <a href="./show.php?id=<?php echo $product['product_id']; ?>">
                                    <img src="<?php echo !empty($product['main_image']) ? DOMAIN . '/assets/products/' . $product['main_image'] : DOMAIN . '/assets/products/default.svg'; ?>" 
                                        class="w-full h-[280px] object-cover" 
                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                                </a>
                                </div>
                                <div class="flex flex-col gap-[2px]">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">       <?php echo htmlspecialchars($product['product_name']); ?></p>
                                    <?php if (!empty($product['first_size'])): ?>
                                        <p  class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
                                            Size: <?php echo htmlspecialchars($product['first_size']); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php 
                                    // Get first color if available
                                    $colors = explode(',', $product['colors']);
                                    $firstColor = trim($colors[0]);
                                    if (!empty($firstColor)): 
                                    ?>
                                        <p  class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
                                            Color: <?php echo htmlspecialchars($firstColor); ?>
                                    </p>
                                    <?php endif; ?>

                                </div>

                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                            <div>
                                            <?php if (!empty($product['min_discount_price'])): ?>
                                                <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">
                                                    ₦<?php echo number_format($product['min_discount_price']); ?>
                                                </span>
                                                <span class="text-gray-500 text-xs line-through ml-1">
                                                    ₦<?php echo number_format($product['min_price']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">
                                                    ₦<?php echo number_format($product['min_price']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                            </td>




                            <td>

                                <button type="submit" class="py-1 px-4  text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px] <?php echo $product['first_quantity'] > 0 ? 'bg-[#39D959] text-white' : 'text-white bg-[#262626]'; ?> px-2 py-1 rounded">
                                            <?php echo $product['first_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                            </button>
                            </td>

                            <td>

                                <?php if (array_key_exists($product['product_id'], $cart_items)): ?>
                                        <button 
                                            class="cart-toggle-button py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-cart-id="<?php echo $cart_items[$product['product_id']]; ?>"
                                            data-in-cart="true">
                                            Added to Cart
                                        </button>
                                    <?php else: ?>
                                        <button 
                                                                                       class="cart-toggle-button py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-in-cart="false">
                                            Add to Cart
                                        </button>
                                    <?php endif; ?>
                            </td>


                            <td>
                            <button 
                                    class="remove-favorite-btn text-[15px] md:text-[16px] font-['Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer"
                                    data-favorite-id="<?php echo $product['favorite_id']; ?>"
                                    data-product-id="<?php echo $product['product_id']; ?>">
                                 Remove from cart
                                </button>

                            </td>

                        </tr>
                        <?php endwhile; ?>
                     

                    </tbody>
                </table>



            </div>



            <div class="w-[90%] mx-auto  md:hidden">
                <div class="w-full flex flex-col gap-4">

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">


                        <div class="flex items-center justify-between">
                            <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#39D959] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                            <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from favorite</p>
                        </div>

                        <div class="w-full h-[1px] bg-[#E1E1E1]"></div>

                        <div class="flex justify-between">
                            <div class="flex flex-col gap-2">

                                <div class="flex gap-2">

                                    <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                        <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="flex flex-col gap-[2px]">
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> Bounce Pillow</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> Blue</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> King size (6 a 4 in)</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Quantity:</b>1</p>
                                    </div>

                                </div>

                                <button type="submit" class="w-[fit-content] rounded-[4px] py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointerprounded-[8px]">Add to Cart</button>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>



                    </div>

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">


                        <div class="flex items-center justify-between">
                            <button type="submit" class="min-w-[87px] py-[6px] px-3 bg-[#262626] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out of Stock</button>
                            <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from favorite</p>
                        </div>

                        <div class="w-full h-[1px] bg-[#E1E1E1]"></div>

                        <div class="flex justify-between">
                            <div class="flex flex-col gap-2">

                                <div class="flex gap-2">

                                    <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                        <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="flex flex-col gap-[2px]">
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> Bounce Pillow</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> Blue</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> King size (6 a 4 in)</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Quantity:</b>1</p>
                                    </div>

                                </div>

                                <button type="submit" class="w-[fit-content] rounded-[4px] py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointerprounded-[8px]">Add to Cart</button>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>



                    </div>

                </div>
            </div>
        </div>
        <?php else: ?> 
                <div class="text-center py-16">
                    <div class="mx-auto h-16 w-16 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No favorites yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Start adding products to your favorites!</p>
                    <div class="mt-6">
                        <a href="./index.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#1A237E] hover:bg-[#0D1863] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Browse Products
                        </a>
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
                const favoriteItem = document.querySelector(`.favorite-item[data-favorite-id="${favoriteId}"]`);
                
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
                        // Remove item from DOM with animation
                        favoriteItem.style.transition = 'all 0.3s ease';
                        favoriteItem.style.opacity = '0';
                        favoriteItem.style.transform = 'scale(0.9)';
                        
                        setTimeout(() => {
                            favoriteItem.remove();
                            
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
        
        // Add to cart functionality
        const cartToggleButtons = document.querySelectorAll('.cart-toggle-button');
        const cartToast = document.getElementById('cart-toast');
        const cartCountElement = document.getElementById('cart-count');
        const cartBadge = document.getElementById('cart-badge');
        
        cartToggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const isInCart = this.getAttribute('data-in-cart') === 'true';
                
                if (isInCart) {
                    // Remove from cart
                    const cartId = this.getAttribute('data-cart-id');
                    
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
                            // Update button appearance
                            this.classList.remove('bg-[#E1F5E6]', 'border-[#4CAF50]');
                            this.classList.add('bg-[#1A237E]', 'text-white');
                            this.textContent = 'Add to Cart';
                            this.setAttribute('data-in-cart', 'false');
                            this.setAttribute('data-cart-id', '');
                            
                            // Update cart count
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                                
                                if (data.cart_count <= 0) {
                                    cartBadge.classList.add('hidden');
                                }
                            }
                            
                            // Show toast notification
                            cartToast.innerHTML = 'Item removed from your cart!';
                            cartToast.classList.remove('hidden', 'bg-green-600');
                            cartToast.classList.add('bg-orange-500');
                            
                            setTimeout(() => {
                                cartToast.classList.add('hidden');
                                cartToast.classList.remove('bg-orange-500');
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
                    // Add to cart
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('quantity', 1);
                    
                    fetch('./add-to-cart.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button appearance
                            this.classList.remove('bg-[#1A237E]', 'text-white');
                            this.classList.add('bg-[#1A237E]','text-white');
                            this.textContent = 'Added to Cart';
                            this.setAttribute('data-in-cart', 'true');
                            
                            if (data.cart_id) {
                                this.setAttribute('data-cart-id', data.cart_id);
                            }
                            
                            // Update cart count
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                                cartBadge.classList.remove('hidden');
                            }
                            
                            // Show toast notification
                            cartToast.classList.remove('hidden', 'bg-orange-500');
                            cartToast.classList.add('bg-green-600');
                            
                            setTimeout(() => {
                                cartToast.classList.add('hidden');
                            }, 3000);
                        } else {
                            if (data.redirect) {
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