<?php

?>

<div class="w-[90%] mx-auto max-w-[1440px] grid grid-cols-2 lg:grid-cols-4 gap-4 my-4">
    <!-- The products cards -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <!-- Product Card -->
            <div class="w-[100%] productbox group rounded-[16px] overflow-hidden flex flex-col">
                <a href="<?php echo product_url($product); ?>" class="w-full rounded-[16px] overflow-hidden relative cursor-pointer block">
                    <?php if ($product['is_featured']): ?>
                        <button class="absolute top-4 left-4 z-10 rounded-full bg-white/95 backdrop-blur-sm text-[10px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold py-1.5 px-3 text-[<?php echo store_color('color_primary'); ?>] drop-shadow-sm">Featured</button>
                    <?php endif; ?>
                    
                    <img src="<?php echo !empty($product['main_image']) ? product_image_url($product['main_image']) : DOMAIN .'/assets/products/default.svg'; ?>" class="w-[100%] h-[230px] object-cover transition-transform duration-700 group-hover:scale-[1.06]" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                    
           <!-- Only change the text of the button, keep styling consistent -->
        <button 
            class="add-to-cart-btn absolute bottom-2.5 left-1/2 -translate-x-1/2 w-[86%] bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-full py-2.5 text-[11px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer shadow-md hover:bg-[<?php echo store_color('color_primary_dark'); ?>] hover:shadow-lg transition-all duration-300"
            data-product-id="<?php echo $product['product_id']; ?>"
            data-in-cart="<?php echo array_key_exists($product['product_id'], $cart_items) ? 'true' : 'false'; ?>"
            data-cart-id="<?php echo array_key_exists($product['product_id'], $cart_items) ? $cart_items[$product['product_id']] : ''; ?>"
        >
            <?php echo array_key_exists($product['product_id'], $cart_items) ? 'Added to Cart' : 'Add to Cart'; ?>
        </button>
                    
<div class="actionstab absolute w-[fit-content] right-2 top-4">
                        <div class="flex flex-col gap-2 md:gap-4">
<i class="cursor-pointer add-to-favourite text-[30px] md:text-[35px] leading-none drop-shadow-md <?php echo isset($favorites[$product['product_id']]) ? 'fa-solid fa-heart favorite-active text-[' . store_color('color_primary') . ']' : 'fa-regular fa-heart text-white'; ?>" 
             data-product-id="<?php echo $product['product_id']; ?>"></i>
        
                            
<i class="fa-solid <?php echo array_key_exists($product['product_id'], $cart_items) ? 'fa-circle-check text-[' . store_color('color_primary') . ']' : 'fa-cart-plus text-white'; ?> cursor-pointer cart-toggle-icon text-[30px] md:text-[35px] leading-none drop-shadow-md <?php echo array_key_exists($product['product_id'], $cart_items) ? 'in-cart' : ''; ?>"
     data-product-id="<?php echo $product['product_id']; ?>"
     data-cart-id="<?php echo array_key_exists($product['product_id'], $cart_items) ? $cart_items[$product['product_id']] : ''; ?>"
></i>
                    </div>
                    </div>
                </a>
                <div class="flex items-center justify-between gap-3 py-3 px-1">
                    <span class="text-[<?php echo store_color('color_heading'); ?>] text-[13px] md:text-[14px] font-['Montserrat'] font-medium leading-snug">
                        <?php      
                        $product_name = htmlspecialchars($product['product_name']);
                        echo (strlen($product_name) > 20) ? substr($product_name, 0, 23) . '...' : $product_name;
                        ?>
                    </span>
                    
                    <?php if (!empty($product['min_discount_price'])): ?>
                  
                        <div class="flex flex-col items-end shrink-0">
    <?php if(empty($product['min_discount_price']) || $product['min_discount_price'] == 0): ?>
        <span class="text-[<?php echo store_color('color_heading'); ?>] text-[14px] md:text-[15px] font-['Montserrat'] font-semibold">₦<?php echo number_format((float)$product['min_price']); ?></span>
    <?php else: ?>
        <span class="text-[<?php echo store_color('color_heading'); ?>] text-[14px] md:text-[15px] font-['Montserrat'] font-semibold">₦<?php echo number_format((float)$product['min_discount_price']); ?></span>
    <?php endif; ?>
</div>
                    <?php else: ?>
                        <!-- Show regular price -->
                        <span class="text-[<?php echo store_color('color_heading'); ?>] text-[13px] md:text-[14px] font-['Montserrat'] font-semibold shrink-0">₦<?php echo number_format((float)$product['min_price']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            
        <?php endwhile; ?>
    <?php else: ?>
<div class="col-span-4 text-center py-8 text-gray-500">No products found</div>
    <?php endif; ?>
</div>