<nav class="w-[90%] flex items-center justify-between">
    <a href="<?php echo DOMAIN; ?>/index.php" class="flex items-center gap-1 md:gap-2">
        <img src="<?php echo DOMAIN; ?>/assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
        <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
    </a>
    <div class="hidden md:flex items-center gap-0">
        <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
            <img src="<?php echo DOMAIN; ?>/assets/global/search.svg" alt="Search" class="w-[24px]" />
            <input type="text" placeholder="What are you shopping for?" class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
        </div>
        <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
    </div>
    <div class="flex items-center gap-6">
        <a href="<?php echo DOMAIN; ?>/products/cart.php" class="relative">
            <img src="<?php echo DOMAIN; ?>/assets/global/bag.svg" class="w-[22px] md:w-[24px]" alt="bag" />
            
            <div id="cart-badge" class="<?php echo ($cart_count > 0) ? '' : 'hidden'; ?> absolute top-[-8px] right-[-8px] flex items-center justify-center">
    <span id="cart-count" class="inline-flex items-center justify-center bg-[#1A237E] text-white text-[10px] font-['Open_Sans'] font-medium rounded-full w-[15px] h-[15px]"><?php echo $cart_count; ?></span>
</div>
        </a>
        <a href="<?php echo DOMAIN; ?>/products/favourites.php">
            <img src="<?php echo DOMAIN; ?>/assets/global/lovely.svg" class="w-[22px] md:w-[24px]" alt="bag" />
        </a>

        <a onclick="openSidemenu()" class="flex items-center gap-1 cursor-pointer">
            <img src="<?php echo DOMAIN; ?>/assets/global/profile.svg" class="w-[22px] md:w-[24px]" alt="bag" />
            <img src="<?php echo DOMAIN; ?>/assets/products/down2.svg" class="w-[12px]" alt="bag" />
        </a>

    </div>
</nav>


