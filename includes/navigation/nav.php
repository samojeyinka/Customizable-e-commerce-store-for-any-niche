<nav class="w-[90%] flex items-center justify-between">
    <a href="<?php echo DOMAIN; ?>/index.php" class="flex items-center gap-1 md:gap-2">
        <img src="<?php echo DOMAIN; ?>/assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
        <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
    </a>
    <?php
// Hardcoded categories and brands data for direct implementation
// You can place this at the top of your header.php or navigation file

// Categories data
$categories = [
    ['category_id' => 17, 'category_title' => 'Bedsheets'],
    ['category_id' => 18, 'category_title' => 'Foams'],
    ['category_id' => 19, 'category_title' => 'Pillows'],
    ['category_id' => 20, 'category_title' => 'Lightings'],
    ['category_id' => 21, 'category_title' => 'Duvets'],
    ['category_id' => 22, 'category_title' => 'Mattress'],
    ['category_id' => 23, 'category_title' => 'Duvet Bedsheet & Pillowcases'],
    ['category_id' => 24, 'category_title' => 'Toppers']
];

// Brands data
$brands = [
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

// The domain constant (you may already have this defined elsewhere)
if (!defined('DOMAIN')) {
    define('DOMAIN', 'http://localhost'); // Replace with your actual domain
}
?>

<!-- Replace your existing search container with this code -->
<div class="hidden md:flex items-center gap-0 relative">
    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
        <img src="<?php echo DOMAIN; ?>/assets/global/search.svg" alt="Search" class="w-[24px]" />
        <input 
            type="text" 
            id="searchInput" 
            placeholder="What are you shopping for?" 
            class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7] bg-transparent" 
            autocomplete="off"
        />
    </div>
    <button type="submit" id="searchButton" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
    
    <!-- Search Results Dropdown -->
    <div id="searchResults" class="absolute top-full left-0 w-full bg-white shadow-md rounded-b-md z-50 mt-1 hidden">
        <div class="p-3">
            <!-- Categories Section -->
            <div id="categoryResults" class="mb-3">
                <h4 class="text-[#1A237E] font-medium text-[14px] mb-2 font-Onest">Categories</h4>
                <div id="categoryList" class="flex flex-col gap-2"></div>
            </div>
            
            <!-- Brands Section -->
            <div id="brandResults">
                <h4 class="text-[#1A237E] font-medium text-[14px] mb-2 font-Onest">Tags</h4>
                <div id="brandList" class="flex flex-col gap-2"></div>
            </div>
            
            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-2">
                <p class="text-[14px] text-[#777]">No matching results found</p>
            </div>
        </div>
    </div>
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



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hardcoded data from PHP as JavaScript variables for client-side filtering
    const categories = <?php echo json_encode($categories); ?>;
    const brands = <?php echo json_encode($brands); ?>;
    const domain = '<?php echo DOMAIN; ?>';
    
    // DOM Elements - Desktop
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const categoryList = document.getElementById('categoryList');
    const brandList = document.getElementById('brandList');
    const noResults = document.getElementById('noResults');
    const searchButton = document.getElementById('searchButton');
    
    // DOM Elements - Mobile
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const mobileCategoryList = document.getElementById('mobileCategoryList');
    const mobileBrandList = document.getElementById('mobileBrandList');
    const mobileNoResults = document.getElementById('mobileNoResults');
    const mobileSearchButton = document.getElementById('mobileSearchButton');
    const mobileSearchModal = document.getElementById('mobileSearchModal');
    
    // Filter function that works with local hardcoded data
    function filterResults(query, isDesktop = true) {
        query = query.toLowerCase().trim();
        
        if (query.length < 2) {
            if (isDesktop) {
                searchResults.classList.add('hidden');
            }
            return;
        }
        
        // Filter categories
        const filteredCategories = categories.filter(category => 
            category.category_title.toLowerCase().includes(query)
        );
        
        // Filter brands
        const filteredBrands = brands.filter(brand => 
            brand.brand_title.toLowerCase().includes(query)
        );
        
        // Display results based on desktop or mobile
        if (isDesktop) {
            displayResults(
                filteredCategories, 
                filteredBrands, 
                categoryList, 
                brandList, 
                noResults, 
                searchResults
            );
        } else {
            displayResults(
                filteredCategories, 
                filteredBrands, 
                mobileCategoryList, 
                mobileBrandList, 
                mobileNoResults
            );
        }
    }
    
    // Function to display search results
    function displayResults(
        filteredCategories, 
        filteredBrands, 
        categoryListElement, 
        brandListElement, 
        noResultsElement, 
        resultsContainer = null
    ) {
        categoryListElement.innerHTML = '';
        brandListElement.innerHTML = '';
        
        const hasCategories = filteredCategories.length > 0;
        const hasBrands = filteredBrands.length > 0;
        
        // Display categories
        if (hasCategories) {
            filteredCategories.forEach(category => {
                const item = document.createElement('a');
                item.href = `${domain}/products/index.php?category=${category.category_id}`;
                item.className = 'text-[13px] hover:text-[#1A237E] transition-colors';
                item.textContent = category.category_title;
                categoryListElement.appendChild(item);
            });
        }
        
        // Display brands
        if (hasBrands) {
            filteredBrands.forEach(brand => {
                const item = document.createElement('a');
                item.href = `${domain}/products/index.php?brand=${brand.brand_id}`;
                item.className = 'text-[13px] hover:text-[#1A237E] transition-colors';
                item.textContent = brand.brand_title;
                brandListElement.appendChild(item);
            });
        }
        
        // Show no results message if necessary
        if (!hasCategories && !hasBrands) {
            noResultsElement.classList.remove('hidden');
        } else {
            noResultsElement.classList.add('hidden');
        }
        
        // Show results container (for desktop only)
        if (resultsContainer) {
            resultsContainer.classList.remove('hidden');
        }
    }
    
    // Debounce function to delay search execution while typing
    function debounce(func, wait) {
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
    
    // Create debounced versions of the search functions
    const debouncedFilterDesktop = debounce(function(query) {
        filterResults(query, true);
    }, 300);
    
    const debouncedFilterMobile = debounce(function(query) {
        filterResults(query, false);
    }, 300);
    
    // Desktop search input event
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            debouncedFilterDesktop(this.value);
        });
        
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                filterResults(this.value, true);
            }
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.classList.add('hidden');
            }
        });
        
        // Handle Enter key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = `${domain}/products/index.php?search=${encodeURIComponent(this.value.trim())}`;
            }
        });
        
        // Handle search button click
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                window.location.href = `${domain}/products/index.php?search=${encodeURIComponent(searchInput.value.trim())}`;
            });
        }
    }
    
    // Mobile search input event
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', function() {
            debouncedFilterMobile(this.value);
        });
        
        // Handle Enter key press for mobile
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = `${domain}/products/index.php?search=${encodeURIComponent(this.value.trim())}`;
            }
        });
        
        // Handle mobile search button click
        if (mobileSearchButton) {
            mobileSearchButton.addEventListener('click', function() {
                window.location.href = `${domain}/products/index.php?search=${encodeURIComponent(mobileSearchInput.value.trim())}`;
            });
        }
    }
});

// Functions to open/close mobile search modal
function openMobileSearch() {
    document.getElementById('mobileSearchModal').classList.remove('hidden');
    document.getElementById('mobileSearchModal').classList.add('flex');
    document.body.classList.add('overflow-hidden');
    document.getElementById('mobileSearchInput').focus();
}

function closeMobileSearch() {
    document.getElementById('mobileSearchModal').classList.add('hidden');
    document.getElementById('mobileSearchModal').classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}
</script>