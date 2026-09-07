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
<section class="w-full py-4 border-b-[1px] border-[#E1E1E1]">
    <div class="w-[90%] mx-auto max-w-[1440px] hidden md:flex items-center justify-between">
        <div class="w-[70%] flex items-center gap-10">
            <?php foreach ($topCategories as $category): ?>
            <div class="custom-dropdown shrink-0">
                <div class="flex items-center gap-2 dropdown-toggle">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium pointer-events-none hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                        <?php echo htmlspecialchars($category['category_title']); ?>
                    </span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none transition-transform duration-200 pointer-events-none"></i>
                </div>
                <div class="dropdown-content top-[30px] z-[50] min-w-[10rem] p-2">
                    <div class="flex flex-col gap-1">
                        <?php if (!empty($category['brands'])): ?>
                            <?php foreach ($category['brands'] as $brand): ?>
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>&brand=<?php echo $brand['brand_id']; ?>" class="text-nowrap text-[14px] font-['Open Sans'] text-[#262626]">
                                <?php echo htmlspecialchars($brand['brand_title']); ?>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>" class="text-center text-[14px] font-['Open Sans'] text-[#262626]">View All</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isAuthenticated()): ?>
            <div class="flex items-center gap-2 cursor-pointer pt-4">
                <i class="fa-solid fa-truck-fast text-[24px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                <a href="<?php echo DOMAIN; ?>/user/orders.php" class='text-[13px] md:text-[14px] font-["Open Sans"] text-[<?php echo store_color('color_primary'); ?>] font-medium underline'>Track your order</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="w-[90%] mx-auto max-w-[1440px] flex items-center gap-10 md:hidden">
        <i class="fa-solid fa-bars cursor-pointer text-[24px] text-[#262626]" onclick="openMobileMenu()" alt="menu"></i>

        <!-- The mobile nav starts -->
        <div id="menuNav" class="dropdown-menu border-t-[1px] border-[#E1E1E1] bg-white overflow-y-auto">
            <div class="w-[92%] mx-auto max-w-[1440px] py-4">
                <?php foreach ($topCategories as $category): ?>
                    <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626] font-['Open Sans'] font-medium"><?php echo htmlspecialchars($category['category_title']); ?></button>
                    <div class="menufaqext text-[16px] font-regular text-[#262626] flex flex-col gap-3">
                    <?php if (!empty($category['brands'])): ?>
                        <?php foreach ($category['brands'] as $brand): ?>    
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>&brand=<?php echo $brand['brand_id']; ?>" class="text-nowrap">
                            <?php echo htmlspecialchars($brand['brand_title']); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>" class="text-left">View All</a>
                    <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if (isAuthenticated()): ?>
                    <div class="flex items-center gap-2 cursor-pointer pt-4">
                        <i class="fa-solid fa-truck-fast text-[24px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                        <a href="<?php echo DOMAIN; ?>/user/orders.php" class='text-[13px] md:text-[14px] font-["Open Sans"] text-[<?php echo store_color('color_primary'); ?>] font-medium underline'>Track your order</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- The mobile nav ends -->

<!-- Mobile live search (driven by functions/search.js) -->
<div class="mobile-search-container flex items-center gap-0 relative w-full" data-glor-search data-glor-domain="<?php echo DOMAIN; ?>">
    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[<?php echo store_color('color_tint'); ?>] rounded-l-[4px] p-2 w-full bg-white">
        <i class="fa-solid fa-magnifying-glass text-[24px] text-[#777777] leading-none" alt="Search"></i>
        <input 
            type="text" 
            id="mobileOnlySearchInput" 
            data-glor-q
            placeholder="<?php echo store_escape(store('search_placeholder')); ?>" 
            class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7] bg-transparent" 
            autocomplete="off"
        />
    </div>
    <button type="submit" id="mobileOnlySearchButton" data-glor-btn class="py-2 px-4 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
    
    <!-- Search Results Dropdown - content rendered by search.js -->
    <div id="mobileOnlySearchResults" data-glor-panel class="absolute top-full left-0 w-full bg-white shadow-md rounded-b-md z-[100] mt-1 hidden"></div>
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