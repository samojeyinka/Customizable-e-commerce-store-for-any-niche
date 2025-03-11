<?php
// Get filter parameters from URL
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$color_filter = isset($_GET['color']) ? $_GET['color'] : '';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';
$size_filter = isset($_GET['size']) ? $_GET['size'] : '';
$texture_filter = isset($_GET['texture']) ? $_GET['texture'] : '';

// Get all available colors from database
$colors_query = "SELECT DISTINCT colors FROM products WHERE colors IS NOT NULL AND colors != ''";
$colors_result = mysqli_query($con, $colors_query);
$available_colors = [];
while ($color_row = mysqli_fetch_assoc($colors_result)) {
    // Split multiple colors (assuming they're comma-separated)
    $color_values = explode(',', $color_row['colors']);
    foreach ($color_values as $color) {
        $color = trim($color);
        if (!empty($color) && !in_array($color, $available_colors)) {
            $available_colors[] = $color;
        }
    }
}

// Get all available sizes from database
$sizes_query = "SELECT DISTINCT size FROM product_variants WHERE size IS NOT NULL AND size != ''";
$sizes_result = mysqli_query($con, $sizes_query);
$available_sizes = [];
while ($size_row = mysqli_fetch_assoc($sizes_result)) {
    $size = trim($size_row['size']);
    if (!empty($size) && !in_array($size, $available_sizes)) {
        $available_sizes[] = $size;
    }
}

// Get all available textures from database
$textures_query = "SELECT DISTINCT texture FROM product_variants WHERE texture IS NOT NULL AND texture != ''";
$textures_result = mysqli_query($con, $textures_query);
$available_textures = [];
while ($texture_row = mysqli_fetch_assoc($textures_result)) {
    $texture = trim($texture_row['texture']);
    if (!empty($texture) && !in_array($texture, $available_textures)) {
        $available_textures[] = $texture;
    }
}

// Get all available categories
$categories_query = "SELECT category_id, category_title FROM categories ORDER BY category_title";
$categories_result = mysqli_query($con, $categories_query);
$available_categories = [];
while ($category_row = mysqli_fetch_assoc($categories_result)) {
    $available_categories[] = $category_row;
}

// Define price ranges
$price_ranges = [
    '0-50000' => '₦0 - ₦50,000',
    '50000-150000' => '₦50,000 - ₦150,000',
    '150000-250000' => '₦150,000 - ₦250,000',
    '250000-500000' => '₦250,000 - ₦500,000',
    '500000-99999999' => '₦500,000 and above'
];

// Function to check if a filter option is selected
function isSelected($filter_type, $value) {
    global $sort_by, $color_filter, $price_range, $size_filter, $texture_filter, $category_id;
    
    switch ($filter_type) {
        case 'sort':
            return $sort_by == $value;
        case 'color':
            return $color_filter == $value;
        case 'price':
            return $price_range == $value;
        case 'size':
            return $size_filter == $value;
        case 'texture':
            return $texture_filter == $value;
        case 'category':
            return $category_id == $value;
        default:
            return false;
    }
}

// Function to build a filter URL while preserving other parameters
function buildFilterUrl($param_name, $param_value) {
    $params = $_GET;
    
    // Reset to page 1 when changing filters
    $params['page'] = 1;
    
    if (empty($param_value)) {
        unset($params[$param_name]);
    } else {
        $params[$param_name] = $param_value;
    }
    
    return '?' . http_build_query($params);
}

// Update these functions in filter.php

function applyFiltersToQuery($base_query) {
    global $sort_by, $color_filter, $price_range, $size_filter, $texture_filter, $con;
    
    // Start with the base query
    $query = $base_query;
    
    // Apply color filter
    if (!empty($color_filter)) {
        $query .= " AND p.colors LIKE '%" . mysqli_real_escape_string($con, $color_filter) . "%'";
    }
    
    // Apply size filter - Note that size has spaces in the database (e.g., "4.5 x 6 x 10")
    if (!empty($size_filter)) {
        $query .= " AND EXISTS (
            SELECT 1 FROM product_variants pv 
            WHERE pv.product_id = p.product_id 
            AND pv.size = '" . mysqli_real_escape_string($con, $size_filter) . "'
        )";
    }
    
    // Apply texture filter (microfiber, smooth, etc.)
    if (!empty($texture_filter)) {
        $query .= " AND EXISTS (
            SELECT 1 FROM product_variants pv 
            WHERE pv.product_id = p.product_id 
            AND pv.texture = '" . mysqli_real_escape_string($con, $texture_filter) . "'
        )";
    }
    
    // Apply price range filter
    if (!empty($price_range)) {
        $price_parts = explode('-', $price_range);
        if (count($price_parts) == 2) {
            $min_price = (int)$price_parts[0];
            $max_price = (int)$price_parts[1];
            
            // This query handles products with both discount price and original price
            $query .= " AND EXISTS (
                SELECT 1 FROM product_variants pv 
                WHERE pv.product_id = p.product_id 
                AND (
                    (pv.discount_price > 0 AND pv.discount_price BETWEEN $min_price AND $max_price)
                    OR 
                    (pv.discount_price IS NULL AND pv.original_price BETWEEN $min_price AND $max_price)
                )
            )";
        }
    }
    
    return $query;
}

// Update the function to get available sizes from your database
function getAvailableSizes($con) {
    $sizes_query = "SELECT DISTINCT size FROM product_variants WHERE size IS NOT NULL AND size != '' ORDER BY original_price";
    $sizes_result = mysqli_query($con, $sizes_query);
    $available_sizes = [];
    
    if ($sizes_result) {
        while ($size_row = mysqli_fetch_assoc($sizes_result)) {
            $available_sizes[] = $size_row['size'];
        }
    }
    
    return $available_sizes;
}

// Update the function to get available textures from your database
function getAvailableTextures($con) {
    $textures_query = "SELECT DISTINCT texture FROM product_variants WHERE texture IS NOT NULL AND texture != ''";
    $textures_result = mysqli_query($con, $textures_query);
    $available_textures = [];
    
    if ($textures_result) {
        while ($texture_row = mysqli_fetch_assoc($textures_result)) {
            $available_textures[] = $texture_row['texture'];
        }
    }
    
    return $available_textures;
}

// Modify this function to ensure proper sorting based on your price structure
function applySortingToQuery($query) {
    global $sort_by;
    
    // Remove any existing ORDER BY clause
    $query = preg_replace('/ORDER BY.*$/i', '', $query);
    
    // Add the new ORDER BY clause based on sort_by
    switch ($sort_by) {
        case 'popular':
            $query .= " ORDER BY p.is_featured DESC, p.product_id DESC";
            break;
        case 'price_high_low':
            // Handle products with and without discount prices
            $query .= " ORDER BY MIN(CASE 
                WHEN v.discount_price > 0 THEN v.discount_price 
                ELSE v.original_price 
            END) DESC";
            break;
        case 'price_low_high':
            // Handle products with and without discount prices
            $query .= " ORDER BY MIN(CASE 
                WHEN v.discount_price > 0 THEN v.discount_price 
                ELSE v.original_price 
            END) ASC";
            break;
        case 'latest':
        default:
            $query .= " ORDER BY p.date_added DESC";
            break;
    }
    
    return $query;
}
?>

<!-- Desktop Filter Bar -->
<div class="w-[90%] mx-auto py-2 hidden md:flex items-center gap-5">
    <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Filter</span>

    <div class="flex items-center md:gap-2 lg:gap-4">
        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                    <?php 
                    $sort_text = 'Sort by';
                    switch ($sort_by) {
                        case 'popular': $sort_text = 'Popularity'; break;
                        case 'price_high_low': $sort_text = 'Price: High to Low'; break;
                        case 'price_low_high': $sort_text = 'Price: Low to High'; break;
                        case 'latest': $sort_text = 'Latest'; break;
                    }
                    echo $sort_text;
                    ?>
                </span>
                <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <a href="<?php echo buildFilterUrl('sort', ''); ?>" class="<?php echo isSelected('sort', '') ? 'font-bold text-[#1A237E]' : ''; ?>">All</a>
                        <a href="<?php echo buildFilterUrl('sort', 'popular'); ?>" class="<?php echo isSelected('sort', 'popular') ? 'font-bold text-[#1A237E]' : ''; ?>">Popularity</a>
                        <a href="<?php echo buildFilterUrl('sort', 'latest'); ?>" class="<?php echo isSelected('sort', 'latest') ? 'font-bold text-[#1A237E]' : ''; ?>">Latest</a>
                        <a href="<?php echo buildFilterUrl('sort', 'price_high_low'); ?>" class="<?php echo isSelected('sort', 'price_high_low') ? 'font-bold text-[#1A237E]' : ''; ?>">Amount: High to Low</a>
                        <a href="<?php echo buildFilterUrl('sort', 'price_low_high'); ?>" class="<?php echo isSelected('sort', 'price_low_high') ? 'font-bold text-[#1A237E]' : ''; ?>">Amount: Low to High</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                    <?php echo empty($color_filter) ? 'Color' : ucfirst($color_filter); ?>
                </span>
                <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <a href="<?php echo buildFilterUrl('color', ''); ?>" class="<?php echo empty($color_filter) ? 'font-bold text-[#1A237E]' : ''; ?>">All</a>
                        
                        <?php foreach ($available_colors as $color): ?>
                        <a href="<?php echo buildFilterUrl('color', $color); ?>" class="<?php echo isSelected('color', $color) ? 'font-bold text-[#1A237E]' : ''; ?>">
                            <?php echo ucfirst($color); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                    <?php 
                    $price_text = 'Amount';
                    if (!empty($price_range) && isset($price_ranges[$price_range])) {
                        $price_text = $price_ranges[$price_range];
                    }
                    echo $price_text;
                    ?>
                </span>
                <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <a href="<?php echo buildFilterUrl('price', ''); ?>" class="<?php echo empty($price_range) ? 'font-bold text-[#1A237E]' : ''; ?>">All</a>
                        
                        <?php foreach ($price_ranges as $range_key => $range_label): ?>
                        <a href="<?php echo buildFilterUrl('price', $range_key); ?>" class="<?php echo isSelected('price', $range_key) ? 'font-bold text-[#1A237E]' : ''; ?>">
                            <?php echo $range_label; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
    <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
            <?php echo empty($size_filter) ? 'Size' : $size_filter; ?>
        </span>
        <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
    </div>
    <div class="filter-menu">
        <div class="flex items-center gap-3">
            <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                <a href="<?php echo buildFilterUrl('size', ''); ?>" class="<?php echo empty($size_filter) ? 'font-bold text-[#1A237E]' : ''; ?>">All Sizes</a>
                
                <?php 
                // Get sizes directly from database
                $sizes = getAvailableSizes($con);
                foreach ($sizes as $size): 
                ?>
                <a href="<?php echo buildFilterUrl('size', $size); ?>" class="<?php echo $size_filter == $size ? 'font-bold text-[#1A237E]' : ''; ?>">
                    <?php echo $size; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                    <?php 
                    $category_text = 'Category';
                    if ($category_id) {
                        foreach ($available_categories as $cat) {
                            if ($cat['category_id'] == $category_id) {
                                $category_text = $cat['category_title'];
                                break;
                            }
                        }
                    }
                    echo $category_text;
                    ?>
                </span>
                <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <a href="<?php echo buildFilterUrl('category', ''); ?>" class="<?php echo empty($category_id) ? 'font-bold text-[#1A237E]' : ''; ?>">All</a>
                        
                        <?php foreach ($available_categories as $category): ?>
                        <a href="<?php echo buildFilterUrl('category', $category['category_id']); ?>" class="<?php echo isSelected('category', $category['category_id']) ? 'font-bold text-[#1A237E]' : ''; ?>">
                            <?php echo $category['category_title']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-dropdown">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                    <?php echo empty($texture_filter) ? 'Texture' : ucfirst($texture_filter); ?>
                </span>
                <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <a href="<?php echo buildFilterUrl('texture', ''); ?>" class="<?php echo empty($texture_filter) ? 'font-bold text-[#1A237E]' : ''; ?>">All</a>
                        
                        <?php foreach ($available_textures as $texture): ?>
                        <a href="<?php echo buildFilterUrl('texture', $texture); ?>" class="<?php echo isSelected('texture', $texture) ? 'font-bold text-[#1A237E]' : ''; ?>">
                            <?php echo ucfirst($texture); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="<?php echo parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>" class="flex items-center gap-1 cursor-pointer">
        <img src="<?php echo DOMAIN; ?>/assets/products/round.svg" class="w-[15.63px]" />
        <span class="text-[#EE3F3F] text-[13px] md:text-[14px] font-Onest font-regular">Reset filter</span>
    </a>
</div>

<!-- Mobile Filter Button -->
<div class="md:hidden">
    <div class="w-[100%] mx-auto bg-white">
        <div class="flex items-center justify-between">
            <button onclick="filterMenu()" class="flex items-center gap-2 cursor-pointer">
                <img src="<?php echo DOMAIN; ?>/assets/products/filter.svg" class="w-[20px]" />
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Filter</span>
            </button>
            
            <?php if (!empty($color_filter) || !empty($price_range) || !empty($size_filter) || !empty($texture_filter) || $sort_by != 'latest'): ?>
            <a href="<?php echo parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>" class="flex items-center gap-1 cursor-pointer">
                <span class="text-[#EE3F3F] text-[13px] md:text-[14px] font-Onest font-regular">Reset</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Styles for the filter dropdowns */
.filter-dropdown {
    position: relative;
    cursor: pointer;
}

.filter-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background-color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 0.375rem;
    z-index: 50;
    display: none;
    padding: 8px 12px;
    min-width: 12rem;
    max-height: 300px;
    overflow-y: auto;
}

.filter-dropdown.active .filter-menu {
    display: block;
}

.filter-dropdown.active .arrow-down {
    transform: rotate(180deg);
}

.filter-menu a {
    display: block;
    padding: 4px 0;
    transition: all 0.2s ease;
}

.filter-menu a:hover {
    color: #1A237E;
}

/* Active filters indicator */
.has-filter .filter-toggle {
    border-color: #1A237E;
}
</style>

<script>
// Dropdown functionality
document.addEventListener("DOMContentLoaded", function() {
    // Handle desktop filter dropdowns
    document.querySelectorAll(".filter-dropdown").forEach((dropdown) => {
        const toggle = dropdown.querySelector(".filter-toggle");

        toggle.addEventListener("click", function(event) {
            event.stopPropagation();
            
            // Close all other dropdowns
            document.querySelectorAll(".filter-dropdown").forEach((dd) => {
                if (dd !== dropdown) dd.classList.remove("active");
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle("active");
        });
        
        // Add visual indicator if filter is active
        const filterText = dropdown.querySelector(".filter-toggle span").innerText.toLowerCase();
        if (filterText !== 'sort by' && filterText !== 'color' && 
            filterText !== 'amount' && filterText !== 'size' && 
            filterText !== 'category' && filterText !== 'texture') {
            dropdown.classList.add('has-filter');
            dropdown.querySelector(".filter-toggle").style.borderColor = '#1A237E';
        }
    });

    // Close dropdowns when clicking elsewhere
    document.addEventListener("click", function() {
        document.querySelectorAll(".filter-dropdown").forEach((dropdown) => {
            dropdown.classList.remove("active");
        });
    });
});
</script>