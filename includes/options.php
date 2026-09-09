<?php
$pdo = pdo_db();

if (!function_exists('getCategoriesWithBrands')) {
function getCategoriesWithBrands($pdo) {
    try {
        $categoriesQuery = "SELECT category_id, category_title FROM categories ORDER BY category_id";
        $categoriesStmt = $pdo->query($categoriesQuery);
        $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get brands for each category (that have associated products)
        $result = [];
        foreach ($categories as $category) {
            $brandsQuery = "
                SELECT DISTINCT b.brand_id, b.brand_title 
                FROM brands b
                INNER JOIN products p ON b.brand_id = p.brand_id
                WHERE p.category_id = :category_id
                ORDER BY b.brand_title
            ";
            
            $brandsStmt = $pdo->prepare($brandsQuery);
            $brandsStmt->bindParam(':category_id', $category['category_id'], PDO::PARAM_INT);
            $brandsStmt->execute();
            $brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result[] = [
                'category_id' => $category['category_id'],
                'category_title' => $category['category_title'],
                'brands' => $brands
            ];
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error fetching categories and brands: " . $e->getMessage());
        return [];
    }
}

}

// Get all categories with their brands
$categoriesWithBrands = getCategoriesWithBrands($pdo);

// Sort categories by number of brands (descending order)
usort($categoriesWithBrands, function($a, $b) {
    $brandsCountA = isset($a['brands']) ? count($a['brands']) : 0;
    $brandsCountB = isset($b['brands']) ? count($b['brands']) : 0;
    return $brandsCountB - $brandsCountA; // Descending order
});

// Take only the first 5 categories with the most brands
$topCategories = array_slice($categoriesWithBrands, 0, 5);

// Optional: Convert to JSON for client-side use
$topCategoriesJson = json_encode($topCategories);
?>

<!-- The main category bar -->
<section class="w-full py-3.5 bg-white border-b border-[#262626]/[0.06]">
    <div class="w-[92%] mx-auto max-w-[1440px] hidden md:flex items-center justify-between">
        <div class="flex items-center gap-8 lg:gap-11">
            <?php foreach ($topCategories as $category): ?>
            <div class="custom-dropdown shrink-0 group">
                <div class="flex items-center gap-2 dropdown-toggle cursor-pointer">
                    <span class="text-[#262626]/80 group-hover:text-[<?php echo store_color('color_primary'); ?>] text-[11px] lg:text-[12px] font-['Montserrat'] font-semibold tracking-[0.14em] uppercase pointer-events-none transition-colors duration-200">
                        <?php echo htmlspecialchars($category['category_title']); ?>
                    </span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[9px] text-[#262626]/40 group-hover:text-[<?php echo store_color('color_primary'); ?>] leading-none transition-all duration-200 pointer-events-none"></i>
                </div>
                <div class="dropdown-content top-[34px] z-[50] min-w-[13rem] p-1.5 rounded-[14px] border-[#F0E8EC] shadow-[0_24px_60px_-28px_rgba(0,0,0,0.28)]">
                    <div class="flex flex-col gap-0.5">
                        <?php if (!empty($category['brands'])): ?>
                            <?php foreach ($category['brands'] as $brand): ?>
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>&brand=<?php echo $brand['brand_id']; ?>" class="text-nowrap rounded-[8px] px-3 py-2 text-[13px] font-['Open Sans'] text-[#262626] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                                <?php echo htmlspecialchars($brand['brand_title']); ?>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>" class="text-center rounded-[8px] px-3 py-2 text-[13px] font-['Open Sans'] text-[#262626] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">View All</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isAuthenticated()): ?>
            <a href="<?php echo DOMAIN; ?>/user/orders.php" class="flex items-center gap-2.5 shrink-0 group">
                <i class="fa-solid fa-truck-fast text-[16px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                <span class="text-[11px] tracking-[0.16em] uppercase font-['Montserrat'] font-semibold text-[#262626]/70 group-hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">Track your order</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="w-[92%] mx-auto max-w-[1440px] flex items-center gap-3 md:hidden">
        <button onclick="openMobileMenu()" aria-label="Open menu" class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full border border-[#262626]/10 text-[#262626] hover:text-[<?php echo store_color('color_primary'); ?>] hover:border-[<?php echo store_color('color_primary'); ?>] transition-colors cursor-pointer">
            <i class="fa-solid fa-bars text-[16px] leading-none" alt="menu"></i>
        </button>

        <!-- The mobile nav starts -->
        <div id="menuNav" class="dropdown-menu border-t-[1px] border-[#F0E8EC] bg-white overflow-y-auto">
            <div class="w-[92%] mx-auto max-w-[1440px] py-5">
                <?php foreach ($topCategories as $category): ?>
                    <button class="menu-accordion cursor-pointer w-full flex items-center justify-between gap-4 border-b-[1px] border-[#262626]/[0.07] py-4 text-[15px] md:text-[16px] text-[#262626] font-['Montserrat'] font-medium"><?php echo htmlspecialchars($category['category_title']); ?></button>
                    <div class="menufaqext text-[15px] font-regular text-[#262626] flex flex-col gap-3">
                    <?php if (!empty($category['brands'])): ?>
                        <?php foreach ($category['brands'] as $brand): ?>    
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>&brand=<?php echo $brand['brand_id']; ?>" class="text-nowrap text-[#262626]/80 hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                            <?php echo htmlspecialchars($brand['brand_title']); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>" class="text-left text-[#262626]/80">View All</a>
                    <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if (isAuthenticated()): ?>
                    <div class="flex items-center gap-2.5 cursor-pointer pt-6">
                        <i class="fa-solid fa-truck-fast text-[18px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                        <a href="<?php echo DOMAIN; ?>/user/orders.php" class='text-[13px] font-["Open Sans"] text-[<?php echo store_color('color_primary'); ?>] font-medium'>Track your order</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- The mobile nav ends -->

<!-- Mobile live search (driven by functions/search.js) -->
<div class="mobile-search-container flex items-center gap-0 relative flex-1 min-w-0" data-glor-search data-glor-domain="<?php echo DOMAIN; ?>">
    <div class="flex items-center gap-2 bg-white rounded-full pl-4 pr-1 py-1 border border-[#262626]/10 focus-within:border-[<?php echo store_color('color_primary'); ?>] transition-colors w-full">
        <i class="fa-solid fa-magnifying-glass text-[14px] text-[#262626]/35 leading-none" alt="Search"></i>
        <input 
            type="text" 
            id="mobileOnlySearchInput" 
            data-glor-q
            placeholder="<?php echo store_escape(store('search_placeholder')); ?>" 
            class="flex-1 min-w-0 text-[13px] border-none outline-none placeholder:text-[#B8BBD7] bg-transparent" 
            autocomplete="off"
        />
        <button type="submit" id="mobileOnlySearchButton" data-glor-btn class="shrink-0 px-4 py-1.5 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[11px] tracking-[0.1em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo store_color('color_primary_dark'); ?>] transition-colors">Search</button>
    </div>
    
    <!-- Search Results Dropdown - content rendered by search.js -->
    <div id="mobileOnlySearchResults" data-glor-panel class="absolute top-full left-0 w-full bg-white rounded-[14px] shadow-[0_20px_50px_-20px_rgba(0,0,0,0.25)] z-[100] mt-2 hidden overflow-hidden"></div>
</div>
    </div>
</section>

<script>
// Store the categories and brands data in JavaScript for potential client-side use
const categoriesData = <?php echo $topCategoriesJson; ?>;

// Dropdown functionality
document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    toggle.addEventListener("click", function(event) {
        event.stopPropagation();
        document.querySelectorAll(".custom-dropdown").forEach((dd) => {
            if (dd !== dropdown) dd.classList.remove("open");
        });
        dropdown.classList.toggle("open");
    });
});

document.addEventListener("click", function() {
    document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
        dropdown.classList.remove("open");
    });
});

function selectOption(element) {
    let dropdown = element.closest(".custom-dropdown");
    let toggle = dropdown.querySelector(".dropdown-toggle");
    toggle.innerText = element.innerText;
    dropdown.classList.remove("open");
}
</script>