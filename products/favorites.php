<?php
// Include database connection and auth
include('../config/connect.php');
require_once '../includes/auth/auth.php';

// Check if user is logged in
if (!isAuthenticated()) {
    // Redirect to login page
    header('Location: ../login.php');
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
    <title>VICTOSAH | My Favorites</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/tabs.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/faq.css" />
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

        <!-- Favorites Removed Notification -->
        <div id="favorites-toast" class="hidden fixed bottom-4 right-4 bg-red-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
            Item removed from favorites!
        </div>

        <div class="w-[90%] mx-auto">
            <div class="flex items-center gap-1">
                <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                <img src="../assets/products/right.svg" class="w-[7px]" />
                <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Favorites</span>
            </div>
        </div>

        <div class="w-[90%] flex items-center justify-between mx-auto mt-4 mb-6">
            <h2 class="text-[#262626] text-[20px] md:text-[22px] font-Onest font-medium">My Favorites</h2>
        </div>

        <div class="w-[90%] mx-auto">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 my-4">
                    <!-- Favorite Products -->
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <!-- Product Card -->
                        <div class="w-full border border-gray-200 rounded-lg overflow-hidden shadow-sm favorite-item" data-favorite-id="<?php echo $product['favorite_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                            <div class="relative">
                                <a href="./show.php?id=<?php echo $product['product_id']; ?>">
                                    <img src="<?php echo !empty($product['main_image']) ? '../assets/products/' . $product['main_image'] : '../assets/products/default.svg'; ?>" 
                                        class="w-full h-[280px] object-cover" 
                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                                </a>
                                
                                <?php if ($product['is_featured']): ?>
                                    <span class="absolute top-2 left-2 bg-[#D51E5E] text-white text-xs px-2 py-1 rounded-full">Featured</span>
                                <?php endif; ?>
                                
                                <button 
                                    class="remove-favorite-btn absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition-colors"
                                    data-favorite-id="<?php echo $product['favorite_id']; ?>"
                                    data-product-id="<?php echo $product['product_id']; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium truncate">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h3>
                                
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <?php if (!empty($product['first_size'])): ?>
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                            Size: <?php echo htmlspecialchars($product['first_size']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($product['first_texture'])): ?>
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                            Texture: <?php echo htmlspecialchars($product['first_texture']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Get first color if available
                                    $colors = explode(',', $product['colors']);
                                    $firstColor = trim($colors[0]);
                                    if (!empty($firstColor)): 
                                    ?>
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                            Color: <?php echo htmlspecialchars($firstColor); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-2">
                                    <div class="flex justify-between items-center">
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
                                        
                                        <span class="text-xs <?php echo $product['first_quantity'] > 0 ? 'text-green-600' : 'text-red-600'; ?> px-2 py-1 rounded">
                                            <?php echo $product['first_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex space-x-2">
                                    <?php if (array_key_exists($product['product_id'], $cart_items)): ?>
                                        <button 
                                            class="cart-toggle-button w-full bg-[#E1F5E6] border border-[#4CAF50] text-[#262626] rounded-md py-2 px-4 text-sm font-medium"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-cart-id="<?php echo $cart_items[$product['product_id']]; ?>"
                                            data-in-cart="true">
                                            Added to Cart
                                        </button>
                                    <?php else: ?>
                                        <button 
                                            class="cart-toggle-button w-full bg-[#1A237E] text-white rounded-md py-2 px-4 text-sm font-medium"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-in-cart="false">
                                            Add to Cart
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
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
        </div>
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
                            this.classList.add('bg-[#E1F5E6]', 'border', 'border-[#4CAF50]', 'text-[#262626]');
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

    <script src="../functions/modals.js"></script>
    <script src="../functions/modals2.js"></script>
    <script src="../functions/functions.js"></script>
    <script src="../functions/tabs.js"></script>
    <script type="text/javascript" src="../functions/accordion.js"></script>
    <script type="text/javascript" src="../functions/faq.js"></script>
    <script type="text/javascript" src="../functions/dropdown.js"></script>
</body>
</html>