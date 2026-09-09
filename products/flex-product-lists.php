<?php

?>

<div class="w-full flex items-center  gap-4 my-4">
    <!-- The products cards -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <!-- Product Card -->
            <div class="w-[18rem] productbox rounded-[8px] overflow-hidden flex flex-col">
                <a href="<?php echo product_url($product); ?>" class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <?php if ($product['is_featured']): ?>
                        <button class="w-[fit-content] h-[fit-content] bg-[<?php echo store_color('color_primary'); ?>] absolute top-4 left-4 rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-1 px-2 absolute">Featured</button>
                    <?php endif; ?>
                    
                    <img src="<?php echo !empty($product['main_image']) ? product_image_url($product['main_image']) : DOMAIN .'/assets/products/default.svg'; ?>" class="w-full h-[230px] object-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                    
           <!-- Only change the text of the button, keep styling consistent -->
        <button 
            class="hidden add-to-cart-btn bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[8px] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[90%] absolute bottom-0 left-[5%]"
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
        <span class="text-[#262626] text-[14px] md:text-[15px] font-Onest font-medium">₦<?php echo number_format((float)$product['min_price']); ?></span>
    <?php else: ?>
        <span class="text-[#262626] text-[14px] md:text-[15px] font-Onest font-medium">₦<?php echo number_format((float)$product['min_discount_price']); ?></span>
    <?php endif; ?>
</div>
                    <?php else: ?>
                        <!-- Show regular price -->
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦<?php echo number_format((float)$product['min_price']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-4 text-center py-8 text-gray-500">No products found</div>
    <?php endif; ?>
</div>
    
    
</script>