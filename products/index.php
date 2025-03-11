<?php

require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';

$user = isAuthenticated() ? getCurrentUser() : null;
include(__DIR__ . '/../config/products.php');

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

        <div id="filteroptions" class="filteroptions-content border-[1px] border-[#E1E1E1] bg-white">
            <div class="relative">

                <div class="flex flex-col gap-2">
                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Sort by</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="selectOption(this)">All</div>
                                    <div onclick="selectOption(this)">Popularity</div>
                                    <div onclick="selectOption(this)">Latest</div>
                                    <div onclick="selectOption(this)">Amount: High to Low</div>
                                    <div onclick="selectOption(this)">Amount: Low to High</div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Color</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Black</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Blue</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Red</span>
                                    </label>


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Amount</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦50,000 - ₦150,000</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦151,000 - 250,000</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦251,000 - ₦500,000</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">₦500,000 and above</span>
                                    </label>



                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Size</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 x 6 x 10 </span>
                                    </label>





                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Category</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Blankets </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Throws</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Bed Sheets</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Pillowcases</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Duvet Covers</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Comforters </span>
                                    </label>



                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Mattress Toppers</span>
                                    </label>


                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Lightning</span>
                                    </label>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Texture</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">All</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Soft </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Medium </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Hard </span>
                                    </label>





                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="w-full md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 md:py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Rating</span>
                            <img src="<?php echo DOMAIN; ?>/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5 star </span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">4 star</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">3 star</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">2 star</span>
                                    </label>

                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="color" class="w-[14px] h-[14px]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">1 star</span>
                                    </label>


                                </div>

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


    </script> 
</body>

</html>