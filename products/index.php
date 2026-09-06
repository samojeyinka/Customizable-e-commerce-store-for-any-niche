<?php

require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';

$user = isAuthenticated() ? getCurrentUser() : null;

// Filter logic MUST load before config/products.php so the filter/sort
// functions are available while the main product query is being built.
include(__DIR__ . '/filters.php');
include(__DIR__ . '/../config/products.php');
require_once "../includes/auth/google.php";

if ($search !== '') {
    $page_title = 'Search results for "' . $search . '"';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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


            <div class="w-[90%] mx-auto max-w-[1440px]">
        <div class="flex items-center gap-1">
            <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
            
            <?php if ($category_id): ?>
                <a href="<?php echo DOMAIN; ?>/products/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Products</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-medium">
                    <?php echo htmlspecialchars($category_name); ?>
                    <?php if ($brand_id): ?>
                        <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                        <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-medium">
                            <?php echo htmlspecialchars(trim($brand_name, " -")); ?>
                        </span>
                    <?php endif; ?>
                </span>
            <?php else: ?>
                <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-medium">Products</span>
            <?php endif; ?>
        </div>
    </div>


    <div class="w-[90%] mx-auto max-w-[1440px] flex items-start md:items-center justify-between gap-3">
        <div>
            <h2 class="text-[#262626] text-[20px] md:text-[22px] font-Onest font-medium">
                <?php echo htmlspecialchars($page_title); ?>
            </h2>
            <p class="text-[#8A8A8A] text-[13px] md:text-[14px] font-Onest font-regular">
                <?php echo $total_products; ?> product<?php echo $total_products == 1 ? '' : 's'; ?> found
            </p>
        </div>
        <button onclick="filterMenu()" class="md:hidden flex items-center gap-2 border-[1px] border-[#C5C5C5] rounded-[4px] px-3 py-1.5 text-[#262626] text-[14px] font-Onest font-medium cursor-pointer">
            Filters
        </button>
    </div>

    <?php if (!empty($filter_notices)): ?>
    <div class="w-[90%] mx-auto max-w-[1440px] mt-3">
        <?php foreach ($filter_notices as $notice): ?>
        <div class="flex items-center justify-between gap-3 border-[1px] border-[#EDC7D3] bg-[#FDF2F6] rounded-[6px] px-3 py-2 mb-2">
            <span class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo htmlspecialchars($notice['message']); ?></span>
            <a href="<?php echo htmlspecialchars($notice['url']); ?>" class="text-[#C2185B] text-[13px] md:text-[14px] font-Onest font-regular underline shrink-0">Remove</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php $chips = active_filter_chips(); if (!empty($chips)): ?>
    <div class="w-[90%] mx-auto max-w-[1440px] mt-3 flex flex-wrap items-center gap-2">
        <?php foreach ($chips as $chip): ?>
        <a href="<?php echo htmlspecialchars($chip['url']); ?>" class="flex items-center gap-2 bg-[#F5F5F5] border-[1px] border-[#E1E1E1] rounded-[28px] px-3 py-1 text-[#262626] text-[12px] md:text-[13px] font-Onest font-regular cursor-pointer">
            <?php echo htmlspecialchars($chip['label']); ?>
            <i class="fa-solid fa-xmark text-[11px] text-[#8A8A8A] leading-none"></i>
        </a>
        <?php endforeach; ?>
        <a href="<?php echo $base_filter_url; ?>" class="text-[#EE3F3F] text-[12px] md:text-[13px] font-Onest font-regular underline cursor-pointer">Clear all</a>
    </div>
    <?php endif; ?>

        <?php
                include(__DIR__ . '/filter.php');

                if ($total_products > 0) {
                    include(__DIR__ . '/product-lists.php');
                } else {
                    ?>
                    <div class="w-[90%] mx-auto max-w-[1440px] py-16 text-center">
                        <h3 class="text-[20px] md:text-[24px] font-Onest font-medium text-[#262626]">No products found</h3>
                        <p class="mt-3 mx-auto max-w-[36rem] text-[#8A8A8A] text-[14px] md:text-[15px] font-Onest font-regular">
                            <?php
                            if (!empty($filter_notices)) {
                                $messages = array_map(function ($n) {
                                    return $n['message'];
                                }, $filter_notices);
                                echo htmlspecialchars(implode(' ', $messages));
                            } else {
                                echo 'We couldn’t find any products matching your filters. Try adjusting or clearing them.';
                            }
                            ?>
                        </p>
                        <a href="<?php echo $base_filter_url; ?>" class="inline-block mt-5 bg-[#C2185B] text-white rounded-[28px] px-6 py-2 text-[14px] font-Onest font-medium cursor-pointer">
                            Clear all filters
                        </a>
                    </div>
                    <?php
                }
                ?>

            </div>

             <!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div class="w-[90%] py-2 mx-auto">
    <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
        <!-- Previous Page Link -->
        <?php if ($current_page > 1): ?>
            <a href="<?php echo htmlspecialchars(buildFilterUrl('page', $current_page - 1)); ?>" class="flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-chevron-left text-[11px] text-[#262626] leading-none"></i>
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                <i class="fa-solid fa-chevron-left text-[11px] text-[#262626] leading-none"></i>
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
            </div>
        <?php endif; ?>

        <!-- Page Numbers -->
        <div class="w-full flex items-center justify-between md:gap-6">
            <?php
            $range = 2;
            $start_page = max(1, $current_page - $range);
            $end_page = min($total_pages, $current_page + $range);

            if ($start_page > 1) {
                echo '<a href="' . htmlspecialchars(buildFilterUrl('page', 1)) . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">1</a>';
                if ($start_page > 2) {
                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $current_page) {
                    echo '<span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#C2185B]">' . $i . '</span>';
                } else {
                    echo '<a href="' . htmlspecialchars(buildFilterUrl('page', $i)) . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $i . '</a>';
                }
            }

            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>';
                }
                echo '<a href="' . htmlspecialchars(buildFilterUrl('page', $total_pages)) . '" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">' . $total_pages . '</a>';
            }
            ?>
        </div>

        <!-- Next Page Link -->
        <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo htmlspecialchars(buildFilterUrl('page', $current_page + 1)); ?>" class="flex items-center gap-2 cursor-pointer">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                <i class="fa-solid fa-chevron-right text-[11px] text-[#262626] leading-none"></i>
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                <i class="fa-solid fa-chevron-right text-[11px] text-[#262626] leading-none"></i>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

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
    function filterMenu() {
        document.getElementById('filteroptions').style.display = 'block';
    }

    function closeFilterPanel() {
        document.getElementById('filteroptions').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Desktop filter dropdowns
        document.querySelectorAll('.filter-dropdown').forEach(function(dropdown) {
            const toggle = dropdown.querySelector('.filter-toggle');
            if (!toggle) return;

            toggle.addEventListener('click', function(e) {
                e.stopPropagation();

                document.querySelectorAll('.filter-dropdown').forEach(function(dd) {
                    if (dd !== dropdown) dd.classList.remove('active');
                });

                dropdown.classList.toggle('active');
            });
        });

        // Mobile filter panel dropdowns
        document.querySelectorAll('#filteroptions .custom-dropdown .dropdown-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();

                const dropdown = this.closest('.custom-dropdown');

                document.querySelectorAll('#filteroptions .custom-dropdown').forEach(function(dd) {
                    if (dd !== dropdown) dd.classList.remove('active');
                });

                dropdown.classList.toggle('active');
            });
        });

        // Close dropdowns / panel when clicking elsewhere
        document.querySelectorAll('#filteroptions .dropdown-content').forEach(function(content) {
            content.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.filter-dropdown').forEach(function(dd) {
                dd.classList.remove('active');
            });

            if (e.target && e.target.id === 'filteroptions') {
                closeFilterPanel();
            }
        });
    });
    </script>
</body>

</html>