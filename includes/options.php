<?php
// Complete solution for category-brand navigation menu with click-based dropdowns

// ===== DATABASE CONNECTION =====

$host = 'localhost';
$dbname = 'victosah';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ===== FETCH CATEGORIES WITH THEIR ASSOCIATED BRANDS =====
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


                <div class="w-[100%]  flex items-center  items-center gap-0">
                    <div class="w-full flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="<?php echo DOMAIN; ?>/assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
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