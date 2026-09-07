<nav class="w-[90%] flex items-center justify-between">
    <a href="<?php echo DOMAIN; ?>/index.php" class="flex items-center gap-1 md:gap-2">
        <img src="<?php echo store_escape(store('logo_url')); ?>" alt="<?php echo store_escape(store('store_name')); ?>" class="w-[31.35px] md:w-[41.35px]" />
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
<div class="hidden md:flex items-center gap-0 relative" data-glor-search data-glor-domain="<?php echo DOMAIN; ?>">
    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
        <i class="fa-solid fa-magnifying-glass text-[24px] text-[#777777] leading-none" alt="Search"></i>
        <input 
            type="text" 
            id="searchInput" 
            data-glor-q
            placeholder="<?php echo store_escape(store('search_placeholder')); ?>" 
            class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7] bg-transparent" 
            autocomplete="off"
        />
    </div>
    <button type="submit" id="searchButton" data-glor-btn class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
    
    <!-- Search Results Dropdown (content rendered by search.js) -->
    <div id="searchResults" data-glor-panel class="absolute top-full left-0 w-full bg-white shadow-md rounded-b-md z-50 mt-1 hidden"></div>
</div>


    <div class="flex items-center gap-6">
        <a href="#" data-open-cart class="relative">
            <i class="fa-solid fa-bag-shopping text-[22px] md:text-[24px] text-[#262626] leading-none" alt="bag"></i>
            
            <div id="cart-badge" class="hidden absolute top-[-8px] right-[-8px] flex items-center justify-center">
    <span id="cart-count" class="inline-flex items-center justify-center bg-[<?php echo store_color('color_primary'); ?>] text-white text-[10px] font-['Open_Sans'] font-medium rounded-full w-[15px] h-[15px]">0</span>
</div>
        </a>
        <a href="<?php echo DOMAIN; ?>/products/favourites.php">
            <i class="fa-solid fa-heart text-[22px] md:text-[24px] text-[#262626] leading-none" alt="bag"></i>
        </a>

        <a onclick="openSidemenu()" class="flex items-center gap-1 cursor-pointer">
            <i class="fa-solid fa-user text-[22px] md:text-[24px] text-[#262626] leading-none" alt="bag"></i>
            <i class="fa-solid fa-chevron-down text-[12px] text-[#262626] leading-none" alt="bag"></i>
        </a>

    </div>
    
</nav>


<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/search.js" defer></script>