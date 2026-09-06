<?php
// products/filter.php - Filter markup only. Logic lives in products/filters.php
// which is included (once) by products/index.php BEFORE config/products.php.
if (!function_exists('buildFilterUrl')) {
    require_once __DIR__ . '/filters.php';
}

function filter_facet_links($type, $options, $all_label = 'All')
{
    $html = '<a href="' . htmlspecialchars(buildFilterUrl($type, '')) . '" class="' . (isSelected($type, '') ? 'font-bold text-[#C2185B]' : '') . '">' . htmlspecialchars($all_label) . '</a>';

    foreach ($options as $opt) {
        $sel = isSelected($type, $opt['value']);
        $html .= '<a href="' . htmlspecialchars(buildFilterUrl($type, $opt['value'])) . '" class="' . ($sel ? 'font-bold text-[#C2185B]' : '') . '">'
            . htmlspecialchars($opt['label'])
            . ' <span class="' . ($opt['count'] > 0 ? 'text-[#B8BBD7]' : 'text-[#D8D8D8]') . '">(' . (int)$opt['count'] . ')</span></a>';
    }

    return $html;
}

function filter_dropdown_active($type)
{
    if ($type === 'sort') {
        return !isSelected('sort', '') && !isSelected('sort', 'latest');
    }
    return !isSelected($type, '');
}
?>

<!-- Desktop Filter Bar -->
<div class="w-[90%] mx-auto max-w-[1440px] py-2 hidden md:flex items-center gap-5">
    <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Filter</span>

    <div class="flex items-center md:gap-2 lg:gap-4">
        <!-- Sort -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('sort') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo sort_label(); ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php foreach ($sort_options as $opt): ?>
                        <a href="<?php echo htmlspecialchars(buildFilterUrl('sort', $opt['value'])); ?>" class="<?php echo isSelected('sort', $opt['value']) ? 'font-bold text-[#C2185B]' : ''; ?>"><?php echo $opt['label']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('category') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $category_label !== '' ? htmlspecialchars($category_label) : 'Category'; ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php echo filter_facet_links('category', $available_categories, 'All Categories'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('brand') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $brand_label !== '' ? htmlspecialchars($brand_label) : 'Brand'; ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php echo filter_facet_links('brand', $available_brands, 'All Brands'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('color') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $color_filter !== '' ? ucfirst($color_filter) : 'Color'; ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php echo filter_facet_links('color', $available_colors, 'All Colors'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Price -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('price') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo ($price_range !== '' && isset($price_ranges[$price_range])) ? $price_ranges[$price_range] : 'Amount'; ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php echo filter_facet_links('price', $available_price_ranges, 'All Prices'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Size -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('size') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $size_filter !== '' ? htmlspecialchars($size_filter) : 'Size'; ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php echo filter_facet_links('size', $available_sizes, 'All Sizes'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Texture -->
        <div class="filter-dropdown<?php echo filter_dropdown_active('texture') ? ' has-filter' : ''; ?>">
            <div class="md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 filter-toggle">
                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $texture_filter !== '' ? ucfirst($texture_filter) : 'Texture'; ?></span>
                <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
            </div>
            <div class="filter-menu">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                        <?php echo filter_facet_links('texture', $available_textures, 'All Textures'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="<?php echo $base_filter_url; ?>" class="flex items-center gap-1 cursor-pointer">
        <span class="text-[#EE3F3F] text-[13px] md:text-[14px] font-Onest font-regular underline">Reset filter</span>
    </a>
</div>

<!-- Mobile Filter Panel -->
<div id="filteroptions" class="filteroptions-content fixed-filter border-[1px] border-[#E1E1E1] bg-white md:hidden">
    <div class="relative p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filters</h3>
            <a href="<?php echo $base_filter_url; ?>" class="text-[#EE3F3F] text-sm">
                Reset All
            </a>
        </div>

        <div class="flex flex-col gap-4">
            <!-- Sort By -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('sort') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo sort_label(); ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php foreach ($sort_options as $opt): ?>
                        <a href="<?php echo htmlspecialchars(buildFilterUrl('sort', $opt['value'])); ?>" class="<?php echo isSelected('sort', $opt['value']) ? 'text-[#C2185B] font-medium' : ''; ?>"><?php echo $opt['label']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('category') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo $category_label !== '' ? htmlspecialchars($category_label) : 'Category'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php echo filter_facet_links('category', $available_categories, 'All Categories'); ?>
                    </div>
                </div>
            </div>

            <!-- Brand -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('brand') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo $brand_label !== '' ? htmlspecialchars($brand_label) : 'Brand'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php echo filter_facet_links('brand', $available_brands, 'All Brands'); ?>
                    </div>
                </div>
            </div>

            <!-- Color -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('color') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo $color_filter !== '' ? ucfirst($color_filter) : 'Color'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php echo filter_facet_links('color', $available_colors, 'All Colors'); ?>
                    </div>
                </div>
            </div>

            <!-- Price Range -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('price') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo ($price_range !== '' && isset($price_ranges[$price_range])) ? $price_ranges[$price_range] : 'Amount'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php echo filter_facet_links('price', $available_price_ranges, 'All Prices'); ?>
                    </div>
                </div>
            </div>

            <!-- Size -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('size') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo $size_filter !== '' ? htmlspecialchars($size_filter) : 'Size'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php echo filter_facet_links('size', $available_sizes, 'All Sizes'); ?>
                    </div>
                </div>
            </div>

            <!-- Texture -->
            <div class="custom-dropdown w-full">
                <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-2 px-3 dropdown-toggle<?php echo filter_dropdown_active('texture') ? ' border-[#C2185B]' : ''; ?>">
                    <span class="text-[#262626] text-[14px] font-Onest font-regular"><?php echo $texture_filter !== '' ? ucfirst($texture_filter) : 'Texture'; ?></span>
                    <i class="fa-solid fa-chevron-down arrow-down text-[12px] text-[#262626] leading-none"></i>
                </div>
                <div class="dropdown-content">
                    <div class="flex flex-col gap-2 p-2">
                        <?php echo filter_facet_links('texture', $available_textures, 'All Textures'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>