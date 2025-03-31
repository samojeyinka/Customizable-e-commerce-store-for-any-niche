<?php

require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';

$user = isAuthenticated() ? getCurrentUser() : null;
include(__DIR__ . '/../config/products.php');
require_once "../includes/auth/google.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />

   
    <style>

        main{
            max-width: 1280px;
            margin:auto;
        }
/* Mobile filter options */
.filteroptions-content {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
}

/* Custom dropdown for mobile filter */
.custom-dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;

    background-color: white;
    border: 1px solid #E1E1E1;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 10;
    margin-top: 4px;
    max-height: 200px;
  
}

.custom-dropdown.active .dropdown-content {
    display: block;
}

.custom-dropdown.active .arrow-down {
    transform: rotate(180deg);
}
</style>


</head>

<body>
    <main class="bg-[#FEFEFE]">

    <?php
include(__DIR__ . "/../includes/header.php");
include(__DIR__ . '/../includes/options.php');
    ?>


<!-- Add to Cart Toast Notification -->
<div id="cart-toast" class="hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
    Item added to your cart!
</div>


            <div class="w-[90%] mx-auto">
        <div class="flex items-center gap-1">
            <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
            <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
            
            <?php if ($category_id): ?>
                <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Products</a>
                <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">
                    <?php echo htmlspecialchars($category_name); ?>
                    <?php if ($brand_id): ?>
                        <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px] inline-block" />
                        <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">
                            <?php echo htmlspecialchars(trim($brand_name, " -")); ?>
                        </span>
                    <?php endif; ?>
                </span>
            <?php else: ?>
                <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Products</span>
            <?php endif; ?>
        </div>
    </div>


    <div class="w-[90%] flex items-center justify-between mx-auto">
        <h2 class="text-[#262626] text-[20px] md:text-[22px] font-Onest font-medium">
            <?php echo htmlspecialchars($page_title); ?>
        </h2>
        <img onclick="filterMenu()" src="<?php echo DOMAIN; ?>/assets/products/mail.svg" class="w-[30px] cursor-pointer md:hidden" />
    </div>




      <!-- The filter here, ignore this -->
                <?php
                // include('./filter.php');
                include(__DIR__ . '/filter.php');
                include(__DIR__ . '/product-lists.php');

                ?>

            </div>


      


             <!-- Pagination -->
            <!-- Updated pagination section to preserve category and brand filters -->
<div class="w-[90%] py-2 mx-auto">
    <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
        <!-- Previous Page Link -->
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $brand_id ? '&brand='.$brand_id : ''; ?>" class="flex items-center gap-2 cursor-pointer">
                <img src="<?php echo DOMAIN; ?>/assets/products/prev.svg" class="w-[6px] h-[11px]" />
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                <img src="<?php echo DOMAIN; ?>/assets/products/prev.svg" class="w-[6px] h-[11px]" />
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
            </div>
        <?php endif; ?>

        <!-- Page Numbers -->
        <div class="w-full flex items-center justify-between md:gap-6">
            <?php
            // Determine range of pages to show
            $range = 2; // Show 2 pages before and after current page
            $start_page = max(1, $current_page - $range);
            $end_page = min($total_pages, $current_page + $range);
            
            // Always show first page
            if ($start_page > 1) {
                echo '<a href="?page=1' . ($category_id ? '&category='.$category_id : '') . ($brand_id ? '&brand='.$brand_id : '') . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">1</a>';
                if ($start_page > 2) {
                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                }
            }
            
            // Show page links within the range
            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $current_page) {
                    echo '<span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#1A237E]">' . $i . '</span>';
                } else {
                    echo '<a href="?page=' . $i . ($category_id ? '&category='.$category_id : '') . ($brand_id ? '&brand='.$brand_id : '') . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $i . '</a>';
                }
            }
            
            // Always show last page
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                }
                echo '<a href="?page=' . $total_pages . ($category_id ? '&category='.$category_id : '') . ($brand_id ? '&brand='.$brand_id : '') . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $total_pages . '</a>';
            }
            ?>
        </div>

        <!-- Next Page Link -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $brand_id ? '&brand='.$brand_id : ''; ?>" class="flex items-center gap-2 cursor-pointer">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                <img src="<?php echo DOMAIN; ?>/assets/products/next.svg" class="w-[6px] h-[11px]" />
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                <img src="<?php echo DOMAIN; ?>/assets/products/next.svg" class="w-[6px] h-[11px]" />
            </div>
        <?php endif; ?>
    </div>
</div>
        </div>

    <!-- Add this to your HTML -->
<div id="filteroptions" class="filteroptions-content border-[1px] border-[#E1E1E1] bg-white md:hidden">
    <div class="relative p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filters</h3>
            <a href="<?php echo parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>" class="text-[#EE3F3F] text-sm">
                Reset All
            </a>
        </div>

        <div class="flex flex-col gap-4">
            <!-- Sort By Filter -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular">
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
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <a href="<?php echo buildFilterUrl('sort', ''); ?>" class="<?php echo !$sort_by ? 'text-[#1A237E] font-medium' : ''; ?>">All</a>
                        <a href="<?php echo buildFilterUrl('sort', 'popular'); ?>" class="<?php echo $sort_by == 'popular' ? 'text-[#1A237E] font-medium' : ''; ?>">Popularity</a>
                        <a href="<?php echo buildFilterUrl('sort', 'latest'); ?>" class="<?php echo $sort_by == 'latest' ? 'text-[#1A237E] font-medium' : ''; ?>">Latest</a>
                        <a href="<?php echo buildFilterUrl('sort', 'price_high_low'); ?>" class="<?php echo $sort_by == 'price_high_low' ? 'text-[#1A237E] font-medium' : ''; ?>">Amount: High to Low</a>
                        <a href="<?php echo buildFilterUrl('sort', 'price_low_high'); ?>" class="<?php echo $sort_by == 'price_low_high' ? 'text-[#1A237E] font-medium' : ''; ?>">Amount: Low to High</a>
                    </div>
                </div>
            </div>

            <!-- Color Filter -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle <?php echo !empty($color_filter) ? 'border-[#1A237E]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular">
                        <?php echo empty($color_filter) ? 'Color' : ucfirst($color_filter); ?>
                    </span>
                    <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <a href="<?php echo buildFilterUrl('color', ''); ?>" class="<?php echo empty($color_filter) ? 'text-[#1A237E] font-medium' : ''; ?>">All Colors</a>
                        
                        <?php foreach ($available_colors as $color): ?>
                        <a href="<?php echo buildFilterUrl('color', $color); ?>" class="<?php echo $color_filter == $color ? 'text-[#1A237E] font-medium' : ''; ?>">
                            <?php echo ucfirst($color); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle <?php echo !empty($price_range) ? 'border-[#1A237E]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular">
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
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <a href="<?php echo buildFilterUrl('price', ''); ?>" class="<?php echo empty($price_range) ? 'text-[#1A237E] font-medium' : ''; ?>">All Prices</a>
                        
                        <?php foreach ($price_ranges as $range_key => $range_label): ?>
                        <a href="<?php echo buildFilterUrl('price', $range_key); ?>" class="<?php echo $price_range == $range_key ? 'text-[#1A237E] font-medium' : ''; ?>">
                            <?php echo $range_label; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Size Filter -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle <?php echo !empty($size_filter) ? 'border-[#1A237E]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular">
                        <?php echo empty($size_filter) ? 'Size' : $size_filter; ?>
                    </span>
                    <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <a href="<?php echo buildFilterUrl('size', ''); ?>" class="<?php echo empty($size_filter) ? 'text-[#1A237E] font-medium' : ''; ?>">All Sizes</a>
                        
                        <?php foreach ($available_sizes as $size): ?>
                        <a href="<?php echo buildFilterUrl('size', $size); ?>" class="<?php echo $size_filter == $size ? 'text-[#1A237E] font-medium' : ''; ?>">
                            <?php echo $size; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle <?php echo !empty($category_id) ? 'border-[#1A237E]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular">
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
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <a href="<?php echo buildFilterUrl('category', ''); ?>" class="<?php echo empty($category_id) ? 'text-[#1A237E] font-medium' : ''; ?>">All Categories</a>
                        
                        <?php foreach ($available_categories as $category): ?>
                        <a href="<?php echo buildFilterUrl('category', $category['category_id']); ?>" class="<?php echo $category_id == $category['category_id'] ? 'text-[#1A237E] font-medium' : ''; ?>">
                            <?php echo $category['category_title']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Texture Filter -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle <?php echo !empty($texture_filter) ? 'border-[#1A237E]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular">
                        <?php echo empty($texture_filter) ? 'Texture' : ucfirst($texture_filter); ?>
                    </span>
                    <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <a href="<?php echo buildFilterUrl('texture', ''); ?>" class="<?php echo empty($texture_filter) ? 'text-[#1A237E] font-medium' : ''; ?>">All Textures</a>
                        
                        <?php foreach ($available_textures as $texture): ?>
                        <a href="<?php echo buildFilterUrl('texture', $texture); ?>" class="<?php echo $texture_filter == $texture ? 'text-[#1A237E] font-medium' : ''; ?>">
                            <?php echo ucfirst($texture); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




        <?php
include(__DIR__ . "/../includes/footer.php");
?>

    </main>



    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="../functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>

    <script>
    // Add active class to current page in pagination
    document.addEventListener('DOMContentLoaded', function() {
        // Preserve filter parameters when paginating
        const paginationLinks = document.querySelectorAll('.w-[90%] .ml-auto a');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const page = this.getAttribute('href').split('=')[1];
                
                // Remove page parameter
                urlParams.delete('page');
                
                // Add new page parameter
                urlParams.append('page', page);
                
                // Update href
                this.setAttribute('href', '?' + urlParams.toString());
            });
        });
    });

    // Add this to a separate JS file or include at the bottom of your page

document.addEventListener('DOMContentLoaded', function() {
    // Get all pagination links
    const paginationLinks = document.querySelectorAll('.w-[90%] .ml-auto a');
    
    // Function to preserve filters when navigating pagination
    function preserveFiltersOnPagination() {
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the page number from the href
                const hrefParts = this.getAttribute('href').split('=');
                if (hrefParts.length < 2) return;
                
                const page = hrefParts[1];
                
                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                
                // Remove page parameter and add the new one
                urlParams.delete('page');
                urlParams.append('page', page);
                
                // Navigate to the new URL with all filters preserved
                window.location.href = '?' + urlParams.toString();
            });
        });
    }
    
    // Handle filter form submissions
    const filterForms = document.querySelectorAll('.filter-form');
    if (filterForms.length > 0) {
        filterForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Start with a fresh URLSearchParams
                const urlParams = new URLSearchParams();
                
                // Add all form data to URL parameters
                const formData = new FormData(form);
                for (const [key, value] of formData.entries()) {
                    if (value) {
                        urlParams.append(key, value);
                    }
                }
                
                // Reset to page 1 when filtering
                urlParams.set('page', '1');
                
                // Navigate to filtered results
                window.location.href = '?' + urlParams.toString();
            });
        });
    }
    
    // Initialize the event listeners
    preserveFiltersOnPagination();
    
    // Highlight active filters based on URL parameters
    function highlightActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Loop through all URL parameters
        for (const [key, value] of urlParams.entries()) {
            // Skip page parameter
            if (key === 'page') continue;
            
            // Find filter inputs matching this parameter
            const filterInputs = document.querySelectorAll(`[name="${key}"]`);
            
            filterInputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    // For checkboxes/radios, check if value matches
                    if (input.value === value) {
                        input.checked = true;
                    }
                } else {
                    // For other inputs, set the value
                    input.value = value;
                }
            });
            
            // Highlight active filter labels
            const filterLabels = document.querySelectorAll(`.filter-label[data-filter="${key}"][data-value="${value}"]`);
            filterLabels.forEach(label => {
                label.classList.add('active-filter');
            });
        }
    }
    
    // Call function to highlight active filters
    highlightActiveFilters();
    
    // Reset filters button
    const resetFilterBtn = document.querySelector('.reset-filters');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            window.location.href = window.location.pathname;
        });
    }
});




function filterMenu() {
    document.getElementById("filteroptions").style.display = "block";
}

document.addEventListener('DOMContentLoaded', function() {
    // Close mobile filter when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target == document.getElementById('filteroptions')) {
            document.getElementById('filteroptions').style.display = 'none';
        }
    });
    
    // Mobile dropdown toggles
    document.querySelectorAll(".custom-dropdown .dropdown-toggle").forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.closest('.custom-dropdown');
            
            // Close all other dropdowns
            document.querySelectorAll('.custom-dropdown').forEach(function(dd) {
                if (dd !== dropdown) {
                    dd.classList.remove('active');
                }
            });
            
            // Toggle this dropdown
            dropdown.classList.toggle('active');
        });
    });
    
    // Close dropdowns when clicking elsewhere
    document.addEventListener('click', function() {
        document.querySelectorAll('.custom-dropdown').forEach(function(dropdown) {
            dropdown.classList.remove('active');
        });
    });
    
    // Prevent closing dropdowns when clicking inside them
    document.querySelectorAll('.dropdown-content').forEach(function(content) {
        content.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Function to select an option
    window.selectOption = function(element) {
        const dropdown = element.closest('.custom-dropdown');
        const toggleText = dropdown.querySelector('.dropdown-toggle span');
        toggleText.textContent = element.textContent;
        dropdown.classList.remove('active');
    };
});

    </script> 
</body>

</html>