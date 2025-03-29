<?php

?>

<style>
    .actionstab{
        display: none;
    }

    .productbox:hover  .actionstab{
            display: block;
    }
</style>

<div class="w-full flex items-center  gap-4 my-4">
    <!-- The products cards -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <!-- Product Card -->
            <div class="w-[18rem] productbox rounded-[8px] overflow-hidden flex flex-col">
                <a href="<?php echo DOMAIN; ?>/products/show.php?id=<?php echo $product['product_id']; ?>" class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <?php if ($product['is_featured']): ?>
                        <button class="w-[fit-content] h-[fit-content] bg-[#D51E5E] absolute top-4 left-4 rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-1 px-2 absolute">Featured</button>
                    <?php endif; ?>
                    
                    <img src="<?php echo !empty($product['main_image']) ? DOMAIN . '/assets/products/' . $product['main_image'] : DOMAIN .'/assets/products/default.svg'; ?>" class="w-full h-[230px] object-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                    
           <!-- Only change the text of the button, keep styling consistent -->
        <button 
            class="hidden add-to-cart-btn bg-[#1A237E] text-white rounded-[8px] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[90%] absolute bottom-0 left-[5%]"
            data-product-id="<?php echo $product['product_id']; ?>"
            data-in-cart="<?php echo array_key_exists($product['product_id'], $cart_items) ? 'true' : 'false'; ?>"
            data-cart-id="<?php echo array_key_exists($product['product_id'], $cart_items) ? $cart_items[$product['product_id']] : ''; ?>"
        >
            <?php echo array_key_exists($product['product_id'], $cart_items) ? 'Added to Cart' : 'Add to Cart'; ?>
        </button>
                    
                    <div class="actionstab absolute w-[fit-content] right-2 top-4">
                        <div class="flex flex-col gap-2 md:gap-4">
                            <!-- <img src="../assets/products/h1.svg" class="w-[25px] md:w-[35px] cursor-pointer add-to-favourite" />-->
                              <img src="<?php echo DOMAIN; ?>/assets/products/<?php echo isset($favorites[$product['product_id']]) ? 'addtofav.svg' : 'addedtofav.svg'; ?>" 
             class="w-[30px] md:w-[35px] cursor-pointer add-to-favourite <?php echo isset($favorites[$product['product_id']]) ? 'favorite-active' : ''; ?>" 
             data-product-id="<?php echo $product['product_id']; ?>" />
       
                           
<img src="<?php echo array_key_exists($product['product_id'], $cart_items) ? DOMAIN . '/assets/products/addedtocart.svg' : DOMAIN . '/assets/products/addtocart.svg'; ?>" 
     alt="<?php echo array_key_exists($product['product_id'], $cart_items) ? 'Remove from Cart' : 'Add to Cart'; ?>" 
     class="cart-toggle-icon w-[30px] md:w-[35px] cursor-pointer <?php echo array_key_exists($product['product_id'], $cart_items) ? 'in-cart' : ''; ?>" 
     data-product-id="<?php echo $product['product_id']; ?>"
     data-cart-id="<?php echo array_key_exists($product['product_id'], $cart_items) ? $cart_items[$product['product_id']] : ''; ?>"
     data-in-cart-image="<?php echo DOMAIN; ?>/assets/products/addedtocart.svg"
     data-default-image="<?php echo DOMAIN; ?>/assets/products/addtocart.svg"
/>
                            <img src="<?php echo DOMAIN; ?>/assets/products/go.svg" class="w-[30px] md:w-[35px] cursor-pointer" />
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

<script>



// const route = 'https://victosah.com';

 const route = 'http://localhost/victosah';
    
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
            fetch(route + '/products/remove-from-cart.php', {
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
                //alert('An error occurred. Please try again.');
            });
            
        } else {
            // Item is not in cart, add it
            // Create form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            
            // Send AJAX request to add to cart
            fetch(route + '/products/add-to-cart.php', {
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
                //alert('An error occurred. Please try again.');
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
            fetch(route + '/products/toggle-favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added' || data.action === 'exists') {
                        // Update icon to show favorited state
                        this.classList.add('favorite-active');
                        this.src = '<?php echo DOMAIN; ?>/assets/products/addtofav.svg'; // Replace with your filled heart icon
                        
                        // Show toast notification
                        showToast('Product added to favorites!', 'success');
                    } else if (data.action === 'removed') {
                        // Update icon to show unfavorited state
                        this.classList.remove('favorite-active');
                        this.src = '<?php echo DOMAIN; ?>/assets/products/addedtofav.svg'; // Replace with your empty heart icon
                        
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
        fetch(route + '/products/check-favorites.php', {
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