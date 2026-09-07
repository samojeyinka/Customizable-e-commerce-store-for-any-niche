<?php if (!isset($auth_flag)) { $auth_flag = (isset($_SESSION['user_id']) && $_SESSION['user_id']) ? 1 : 0; } ?>
<div id="cart-overlay" class="fixed inset-0 bg-black/40 z-[800] hidden"></div>
<div id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-[400px] bg-white z-[810] flex flex-col translate-x-full transition-transform duration-300">
    <div class="flex items-center justify-between px-4 py-3 border-b border-[#E1E1E1]">
        <h3 class="text-[16px] font-Onest font-medium text-[#262626]">Your Cart <span id="cart-drawer-count" class="text-[#777777]"></span></h3>
        <button type="button" data-close-cart class="text-[22px] text-[#262626] leading-none"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div id="cart-drawer-items" class="flex-1 overflow-y-auto flex flex-col"><div id="cart-empty" class="flex-1 flex flex-col items-center justify-center p-6 text-center"><i class="fa-solid fa-cart-shopping text-[48px] glor-text opacity-30 mb-3"></i><p class="text-[15px] text-[#262626] font-medium mb-1">Your cart is empty</p><p class="text-[13px] text-[#777777]">Add items to get started</p></div></div>
    <div id="cart-drawer-footer" class="px-4 py-3 border-t border-[#E1E1E1] hidden">
        <div class="flex items-center justify-between mb-3"><span class="text-[14px] font-Onest font-medium text-[#262626]">Subtotal</span><span id="cart-drawer-subtotal" class="text-[15px] font-Onest font-medium text-[#262626]">&#8358;0</span></div>
        <a href="<?php echo DOMAIN; ?>/products/cart.php" class="block w-full text-center py-2 px-4 border border-[<?php echo store_color('color_primary'); ?>] text-[<?php echo store_color('color_primary'); ?>] text-[14px] font-Onest font-medium rounded-[8px] mb-2 hover:bg-[<?php echo store_color('color_tint'); ?>]">View Cart</a>
        <a href="<?php echo DOMAIN; ?>/products/checkout.php" class="block w-full text-center py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[14px] font-Onest font-medium rounded-[8px] hover:bg-[<?php echo store_color('color_primary_dark'); ?>]">Checkout</a>
    </div>
</div>

<script>window.GLORY_CART_CONFIG={domain:<?php echo json_encode(DOMAIN); ?>,auth:<?php echo (int)$auth_flag; ?>};</script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/cart.js" defer></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/favourites.js" defer></script>