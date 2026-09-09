<nav class="w-[92%] max-w-[1440px] flex items-center justify-between gap-6">
    <a href="<?php echo DOMAIN; ?>/index.php" class="flex items-center gap-2.5 shrink-0">
        <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[34px] md:w-[40px]" />
        <span class="hidden lg:block text-[<?php echo store_color('color_heading'); ?>] text-[17px] md:text-[19px] tracking-[0.32em] uppercase font-['Montserrat'] font-semibold leading-none pt-[2px]"><?php echo store_escape(store('store_name', 'GLOREFY')); ?></span>
    </a>
    <?php
// Hardcoded categories and brands data for direct implementation
// You can place this at the top of your header.php or navigation file

// Categories data
$categories = [
    ['category_id' => 17, 'category_title' => 'Skincare'],
    ['category_id' => 18, 'category_title' => 'Face Makeup'],
    ['category_id' => 19, 'category_title' => 'Lips & Eyes'],
    ['category_id' => 20, 'category_title' => 'Hair Care'],
    ['category_id' => 21, 'category_title' => 'Beauty Tools'],
    ['category_id' => 22, 'category_title' => 'Fragrances'],
    ['category_id' => 23, 'category_title' => 'Body Care'],
    ['category_id' => 24, 'category_title' => 'Sets & Kits']
];

$brands = [
    ['brand_id' => 23, 'brand_title' => 'The Ordinary'],
    ['brand_id' => 24, 'brand_title' => 'Fenty Beauty'],
    ['brand_id' => 25, 'brand_title' => 'Maybelline'],
    ['brand_id' => 26, 'brand_title' => 'Neutrogena'],
    ['brand_id' => 27, 'brand_title' => 'MAC'],
    ['brand_id' => 28, 'brand_title' => 'CeraVe'],
    ['brand_id' => 29, 'brand_title' => 'Shea Moisture'],
    ['brand_id' => 30, 'brand_title' => 'Charlotte Tilbury'],
    ['brand_id' => 31, 'brand_title' => 'Olay']
];
?>

<!-- Desktop live search (driven by functions/search.js) -->
<div class="hidden md:flex flex-1 justify-center relative" data-glor-search data-glor-domain="<?php echo DOMAIN; ?>">
    <div class="flex items-center gap-2 w-full max-w-[440px] bg-white rounded-full pl-5 pr-1.5 py-1.5 border border-[#262626]/10 focus-within:border-[<?php echo store_color('color_primary'); ?>] transition-colors duration-300 shadow-[0_2px_12px_-6px_rgba(38,38,38,0.18)]">
        <i class="fa-solid fa-magnifying-glass text-[15px] text-[#262626]/35 leading-none" alt="Search"></i>
        <input 
            type="text" 
            id="searchInput" 
            data-glor-q
            placeholder="<?php echo store_escape(store('search_placeholder')); ?>" 
            class="flex-1 min-w-0 text-[13px] border-none outline-none placeholder:text-[#262626]/30 bg-transparent py-0.5" 
            autocomplete="off"
        />
        <button type="submit" id="searchButton" data-glor-btn class="shrink-0 px-4 py-1.5 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[11px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-colors">Search</button>
    </div>
    
    <!-- Search Results Dropdown (content rendered by search.js) -->
    <div id="searchResults" data-glor-panel class="absolute top-full left-1/2 -translate-x-1/2 w-full max-w-[440px] bg-white rounded-[14px] shadow-[0_20px_50px_-20px_rgba(0,0,0,0.25)] z-50 mt-2 hidden overflow-hidden"></div>
</div>


    <div class="flex items-center gap-5 md:gap-6 shrink-0">
        <a href="#" data-open-cart class="relative p-1.5 -m-1.5 text-[#262626] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors" aria-label="Shopping bag">
            <i class="fa-solid fa-bag-shopping text-[19px] md:text-[20px] leading-none" alt="bag"></i>
            
            <div id="cart-badge" class="hidden absolute top-[-1px] right-[-1px] items-center justify-center">
                <span id="cart-count" class="inline-flex items-center justify-center bg-[<?php echo store_color('color_primary'); ?>] text-white text-[9px] font-['Montserrat'] font-semibold leading-none rounded-full min-w-[15px] h-[15px] px-[4px] border-[2px] border-white">0</span>
            </div>
        </a>
        <a href="<?php echo DOMAIN; ?>/products/favourites.php" class="p-1.5 -m-1.5 text-[#262626] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors" aria-label="Favourites">
            <i class="fa-solid fa-heart text-[19px] md:text-[20px] leading-none" alt="favourites"></i>
        </a>

        <a onclick="openSidemenu()" class="flex items-center gap-1.5 cursor-pointer p-1.5 -m-1.5 text-[#262626] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors" aria-label="Account">
            <i class="fa-solid fa-user text-[19px] md:text-[20px] leading-none" alt="account"></i>
            <i class="fa-solid fa-chevron-down text-[9px] text-[#262626]/50 leading-none" alt="chevron"></i>
        </a>

    </div>
    
</nav>


<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/search.js" defer></script>