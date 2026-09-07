<?php
$shop = store('shop_section');
$items = isset($shop['items']) && is_array($shop['items']) ? array_values($shop['items']) : [];
$viewAllUrl = !empty($shop['view_all_url']) ? $shop['view_all_url'] : DOMAIN . '/products/index.php';
$shopSearchUrl = function ($keyword) {
    return DOMAIN . '/products/index.php?search=' . urlencode((string) $keyword);
};
?>
<!-- ========================  The "What You Can Get Here" section starts ======================== -->
<section class="w-full py-12 md:py-16">
    <div class="w-[90%] max-w-[1440px] mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8 md:mb-10">
            <div>
                <span class="text-[12px] md:text-[13px] tracking-[0.3em] uppercase text-[<?php echo store_color('color_primary'); ?>] font-['Open Sans'] font-semibold"><?php echo store_escape($shop['eyebrow'] ?? 'Browse'); ?></span>
                <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[24px] md:text-[34px] font-['Montserrat'] font-semibold mt-1"><?php echo store_escape($shop['title'] ?? 'What You Can Get Here'); ?></h3>
                <p class="text-[#777777] text-[14px] md:text-[15px] font-['Open Sans'] mt-2 max-w-[50ch]"><?php echo store_escape($shop['subtitle'] ?? ''); ?></p>
            </div>
            <a href="<?php echo store_escape($viewAllUrl); ?>" class="w-fit border-[1px] border-[<?php echo store_color('color_primary'); ?>] text-[<?php echo store_color('color_primary'); ?>] rounded-full px-5 py-2 text-[14px] font-Onest font-medium hover:bg-[<?php echo store_color('color_primary'); ?>] hover:text-white transition-colors whitespace-nowrap">
                <?php echo store_escape($shop['view_all_text'] ?? 'View All Products'); ?>
            </a>
        </div>

        <div class="flex flex-col md:flex-row items-stretch gap-4">
            <?php if (isset($items[0])): ?>
            <a href="<?php echo $shopSearchUrl($items[0]['keyword'] ?? $items[0]['title']); ?>" class="group relative block w-full md:w-[50%] h-[288px] md:h-[488.76px] rounded-[16px] overflow-hidden">
                <img src="<?php echo store_escape($items[0]['image'] ?? ''); ?>" alt="<?php echo store_escape($items[0]['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/25 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5 md:p-7">
                    <h4 class="text-[22px] md:text-[28px] text-white font-Onest font-semibold drop-shadow-sm"><?php echo store_escape($items[0]['title'] ?? ''); ?></h4>
                    <p class="mt-2 text-[14px] md:text-[15px] text-white/90 font-['Open Sans'] leading-relaxed max-w-[40ch]"><?php echo store_escape($items[0]['subtitle'] ?? ''); ?></p>
                    <span class="mt-4 inline-flex items-center gap-2 text-white text-[14px] font-Onest font-medium">
                        Shop Now
                        <i class="fa-solid fa-arrow-right text-[12px] transition-transform duration-300 group-hover:translate-x-1"></i>
                    </span>
                </div>
            </a>
            <?php endif; ?>

            <div class="flex flex-col gap-4 w-full md:w-[50%]">
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach (array_slice($items, 1, 2) as $item): ?>
                    <a href="<?php echo $shopSearchUrl($item['keyword'] ?? $item['title']); ?>" class="group relative block w-full h-[180px] md:h-[235px] rounded-[16px] overflow-hidden">
                        <img src="<?php echo store_escape($item['image'] ?? ''); ?>" alt="<?php echo store_escape($item['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4">
                            <h4 class="text-[17px] md:text-[20px] text-white font-Onest font-semibold drop-shadow-sm"><?php echo store_escape($item['title'] ?? ''); ?></h4>
                            <p class="mt-1 text-[12px] md:text-[13px] text-white/90 font-['Open Sans'] leading-relaxed"><?php echo store_escape($item['subtitle'] ?? ''); ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <?php foreach (array_slice($items, 3, 2) as $item): ?>
                    <a href="<?php echo $shopSearchUrl($item['keyword'] ?? $item['title']); ?>" class="group relative block w-full h-[180px] md:h-[235px] rounded-[16px] overflow-hidden">
                        <img src="<?php echo store_escape($item['image'] ?? ''); ?>" alt="<?php echo store_escape($item['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4">
                            <h4 class="text-[17px] md:text-[20px] text-white font-Onest font-semibold drop-shadow-sm"><?php echo store_escape($item['title'] ?? ''); ?></h4>
                            <p class="mt-1 text-[12px] md:text-[13px] text-white/90 font-['Open Sans'] leading-relaxed"><?php echo store_escape($item['subtitle'] ?? ''); ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================  The "What You Can Get Here" section ends ======================== -->