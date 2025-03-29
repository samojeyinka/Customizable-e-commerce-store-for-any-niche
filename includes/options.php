<?php
// Complete solution for category-brand navigation menu with click-based dropdowns

// ===== DATABASE CONNECTION =====

require_once __DIR__ . '/../config/servername.php';


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ===== FETCH CATEGORIES WITH THEIR ASSOCIATED BRANDS =====
if (!function_exists('getCategoriesWithBrands')) {
function getCategoriesWithBrands($pdo) {
    try {
        // Get all categories
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
            
            // Add to results
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Store</title>

 <style>
    /* Custom dropdown styles */
    .custom-dropdown {
        position: relative;
        cursor: pointer;
    }
    
    .dropdown-content {
        position: absolute;
        top: 30px !important;
        left: 0;
        background-color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 0.375rem;
        z-index: 50;
        display: none;
    }
    
    .custom-dropdown.open .dropdown-content {
        display: block;
    }
    
    .custom-dropdown.open .arrow-down {
        transform: rotate(180deg);
    }
    

    .mobileOnlySearchResults{
        z-index: 100 !important;
    }
    /* Add any additional styles you need */
 </style>
</head>
<body>


<!-- The big screen -->
<section class="w-full py-4 border-b-[1px] border-[#E1E1E1]">
    <div class="w-[90%] mx-auto hidden md:flex items-center justify-between">
        <div class="w-[70%] flex items-center gap-10">
            <?php foreach ($topCategories as $category): ?>
            <div class="custom-dropdown shrink-0">
                <div class="flex items-center gap-2 dropdown-toggle">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium pointer-events-none">
                        <?php echo htmlspecialchars($category['category_title']); ?>
                    </span>
                    <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px] transition-transform duration-200 pointer-events-none" />
                </div>
                <div class="dropdown-content min-w-[10rem] p-2">
                    <div class="flex flex-col gap-3">
                        <?php if (!empty($category['brands'])): ?>
                            <?php foreach ($category['brands'] as $brand): ?>
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>&brand=<?php echo $brand['brand_id']; ?>" class="text-nowrap">
                                <?php echo htmlspecialchars($brand['brand_title']); ?>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?php echo DOMAIN; ?>/products/index.php?category=<?php echo $category['category_id']; ?>" class="text-center">View All</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isAuthenticated()): ?>
    <div class="flex items-center gap-2 cursor-pointer pt-4">
        <img src="<?php echo DOMAIN; ?>/assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
        <!-- <strong class='text-[13px] md:text-[14px] font-["Open Sans"] text-[#1A237E] font-medium underline'>Track your order</strong> -->
        <a href="<?php echo DOMAIN; ?>/user/orders.php" class='text-[13px] md:text-[14px] font-["Open Sans"] text-[#1A237E] font-medium underline'>Track your order</a>
    </div>
<?php else: ?>
<?php endif; ?>
    </div>

    <div class="w-[90%] mx-auto flex items-center gap-10  md:hidden">
                <img src="<?php echo DOMAIN; ?>/assets/global/menu.svg" alt="menu" class="cursor-pointer w-[24px]" onclick="openMobileMenu()" />


                <!-- The mobile nav starts -->
                <div id="menuNav" class="dropdown-menu border-t-[1px] border-[#E1E1E1] bg-white">

                    <div class="w-[92%] mx-auto">
                    <?php foreach ($topCategories as $category): ?>
                        <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626]  font-['Open Sans'] font-medium"><?php echo htmlspecialchars($category['category_title']); ?></button>
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
        <img src="<?php echo DOMAIN; ?>/assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
        <a href="<?php echo DOMAIN; ?>/user/orders.php" class='text-[13px] md:text-[14px] font-["Open Sans"] text-[#1A237E] font-medium underline'>Track your order</a>
    </div>
<?php else: ?>
<?php endif; ?>

                    </div>

                </div>
                <!-- The mobile nav ends -->


                <?php
// Hardcoded categories and brands data specifically for mobile search
// Different variable names to avoid conflicts
$mobileSearchCategories = [
    ['category_id' => 17, 'category_title' => 'Bedsheets'],
    ['category_id' => 18, 'category_title' => 'Foams'],
    ['category_id' => 19, 'category_title' => 'Pillows'],
    ['category_id' => 20, 'category_title' => 'Lightings'],
    ['category_id' => 21, 'category_title' => 'Duvets'],
    ['category_id' => 22, 'category_title' => 'Mattress'],
    ['category_id' => 23, 'category_title' => 'Duvet Bedsheet & Pillowcases'],
    ['category_id' => 24, 'category_title' => 'Toppers']
];

// Brands data with unique variable name
$mobileSearchBrands = [
    ['brand_id' => 23, 'brand_title' => 'Mattress'],
    ['brand_id' => 24, 'brand_title' => 'Duvet & Bedsheet & Pillowcases'],
    ['brand_id' => 25, 'brand_title' => 'Duvet & Bedsheet'],
    ['brand_id' => 26, 'brand_title' => 'Bedsheet & Pillowcases'],
    ['brand_id' => 27, 'brand_title' => 'Duvet & Pillowcases'],
    ['brand_id' => 28, 'brand_title' => 'Mattress Topper'],
    ['brand_id' => 29, 'brand_title' => 'Throw Pillow'],
    ['brand_id' => 30, 'brand_title' => 'Pillows'],
    ['brand_id' => 31, 'brand_title' => 'Duvets']
];
?>

<!-- Mobile Search Input - Uses different IDs to avoid conflicts -->
<div class="mobile-search-container flex items-center gap-0 relative w-full">
    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2 w-full">
        <img src="<?php echo DOMAIN; ?>/assets/global/search.svg" alt="Search" class="w-[24px]" />
        <input 
            type="text" 
            id="mobileOnlySearchInput" 
            placeholder="What are you shopping for?" 
            class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" 
            autocomplete="off"
        />
    </div>
    <button type="submit" id="mobileOnlySearchButton" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
    
    <!-- Search Results Dropdown - Unique ID -->
    <div id="mobileOnlySearchResults" class="absolute top-full left-0 w-full bg-white shadow-md rounded-b-md z-50 mt-1 hidden">
        <div class="p-3">
            <!-- Categories Section -->
            <div class="mb-3">
                <h4 class="text-[#1A237E] font-medium text-[14px] mb-2 font-Onest">Categories</h4>
                <div id="mobileOnlyCategoryList" class="flex flex-col gap-2"></div>
            </div>
            
            <!-- Tags Section -->
            <div>
                <h4 class="text-[#1A237E] font-medium text-[14px] mb-2 font-Onest">Tags</h4>
                <div id="mobileOnlyBrandList" class="flex flex-col gap-2"></div>
            </div>
            
            <!-- No Results Message -->
            <div id="mobileOnlyNoResults" class="hidden text-center py-2">
                <p class="text-[14px] text-[#777]">No matching results found</p>
            </div>
        </div>
    </div>
</div>

<script>
// Use an IIFE to isolate variables and avoid global scope conflicts
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Using unique variable names to avoid conflicts
        const mobileCategories = <?php echo json_encode($mobileSearchCategories); ?>;
        const mobileBrands = <?php echo json_encode($mobileSearchBrands); ?>;
        const mobileDomain = '<?php echo DOMAIN; ?>';
        
        // DOM Elements with unique IDs
        const mobileSearchInput = document.getElementById('mobileOnlySearchInput');
        const mobileSearchResults = document.getElementById('mobileOnlySearchResults');
        const mobileCategoryList = document.getElementById('mobileOnlyCategoryList');
        const mobileBrandList = document.getElementById('mobileOnlyBrandList');
        const mobileNoResults = document.getElementById('mobileOnlyNoResults');
        const mobileSearchButton = document.getElementById('mobileOnlySearchButton');
        
        // Skip initialization if elements don't exist (prevents errors)
        if (!mobileSearchInput || !mobileSearchResults) return;
        
        // Filter function with unique name
        function filterMobileResults(query) {
            query = query.toLowerCase().trim();
            
            if (query.length < 2) {
                mobileSearchResults.classList.add('hidden');
                return;
            }
            
            // Show results container
            mobileSearchResults.classList.remove('hidden');
            
            // Filter categories
            const filteredCategories = mobileCategories.filter(category => 
                category.category_title.toLowerCase().includes(query)
            );
            
            // Filter brands
            const filteredBrands = mobileBrands.filter(brand => 
                brand.brand_title.toLowerCase().includes(query)
            );
            
            displayMobileResults(filteredCategories, filteredBrands);
        }
        
        // Display results function with unique name
        function displayMobileResults(filteredCategories, filteredBrands) {
            mobileCategoryList.innerHTML = '';
            mobileBrandList.innerHTML = '';
            
            const hasCategories = filteredCategories.length > 0;
            const hasBrands = filteredBrands.length > 0;
            
            // Display categories
            if (hasCategories) {
                filteredCategories.forEach(category => {
                    const item = document.createElement('a');
                    item.href = `${mobileDomain}/products/index.php?category=${category.category_id}`;
                    item.className = 'text-[13px] hover:text-[#1A237E] transition-colors';
                    item.textContent = category.category_title;
                    mobileCategoryList.appendChild(item);
                });
            }
            
            // Display brands
            if (hasBrands) {
                filteredBrands.forEach(brand => {
                    const item = document.createElement('a');
                    item.href = `${mobileDomain}/products/index.php?brand=${brand.brand_id}`;
                    item.className = 'text-[13px] hover:text-[#1A237E] transition-colors';
                    item.textContent = brand.brand_title;
                    mobileBrandList.appendChild(item);
                });
            }
            
            // Show/hide no results message
            if (!hasCategories && !hasBrands) {
                mobileNoResults.classList.remove('hidden');
            } else {
                mobileNoResults.classList.add('hidden');
            }
        }
        
        // Debounce function with unique name
        function debounceMobile(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }
        
        // Create debounced version with unique name
        const debouncedMobileFilter = debounceMobile(function(query) {
            filterMobileResults(query);
        }, 300);
        
        // Event listeners
        mobileSearchInput.addEventListener('input', function() {
            debouncedMobileFilter(this.value);
        });
        
        mobileSearchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                filterMobileResults(this.value);
            }
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileSearchInput.contains(event.target) && !mobileSearchResults.contains(event.target)) {
                mobileSearchResults.classList.add('hidden');
            }
        });
        
        // Handle Enter key press
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = `${mobileDomain}/products/index.php?search=${encodeURIComponent(this.value.trim())}`;
            }
        });
        
        // Handle search button click
        mobileSearchButton.addEventListener('click', function() {
            window.location.href = `${mobileDomain}/products/index.php?search=${encodeURIComponent(mobileSearchInput.value.trim())}`;
        });
    });
})(); // Immediately invoked function to isolate scope
</script>
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
        console.log("clickin")
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
</body>
</html>