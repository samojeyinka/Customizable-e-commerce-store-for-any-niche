<?php
$shop = store('shop_section');
$items = isset($shop['items']) && is_array($shop['items']) ? array_values($shop['items']) : [];
$viewAllUrl = !empty($shop['view_all_url']) ? $shop['view_all_url'] : DOMAIN . '/products/index.php';
$viewAllText = !empty($shop['view_all_text']) ? $shop['view_all_text'] : 'View All Products';
$shopSearchUrl = function ($keyword) {
    return DOMAIN . '/products/index.php?search=' . urlencode((string) $keyword);
};
$tileLabels = ['01', '02', '03', '04', '05'];
?>
<!-- ========================  The "What You Can Get Here" section starts ======================== -->
<section class="w-full pt-16 md:pt-20 pb-4">
    <div class="w-[90%] mx-auto max-w-[1440px]">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 md:gap-8 mb-10 md:mb-14">
            <div class="max-w-[560px]">
                <span class="text-[11px] md:text-[12px] tracking-[0.4em] uppercase text-[<?php echo store_color('color_primary'); ?>] font-['Montserrat'] font-semibold"><?php echo store_escape($shop['eyebrow'] ?? 'Browse'); ?></span>
                <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[32px] md:text-[44px] leading-[1.12] font-['Cormorant_Garamond'] font-medium mt-3"><?php echo store_escape($shop['title'] ?? 'What You Can Get Here'); ?></h3>
                <p class="text-[#777777] text-[14px] md:text-[15px] font-['Open_Sans'] mt-4 leading-relaxed"><?php echo store_escape($shop['subtitle'] ?? ''); ?></p>
            </div>
            <a href="<?php echo store_escape($viewAllUrl); ?>" class="group inline-flex items-center gap-3 w-fit text-[12px] tracking-[0.22em] uppercase font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>] border-b border-[<?php echo store_color('color_heading'); ?>] pb-1 hover:text-[<?php echo store_color('color_primary'); ?>] hover:border-[<?php echo store_color('color_primary'); ?>] transition-colors whitespace-nowrap">
                <?php echo store_escape($viewAllText); ?>
                <i class="fa-solid fa-arrow-right text-[11px] transition-transform duration-300 group-hover:translate-x-1"></i>
            </a>
        </div>

        <?php if (!empty($items)): ?>
        <div class="flex flex-col md:flex-row gap-4 md:gap-5">
            <?php if (isset($items[0])): ?>
            <a href="<?php echo $shopSearchUrl($items[0]['keyword'] ?? $items[0]['title']); ?>" class="group relative block w-full md:w-[54%] h-[380px] md:h-[560px] rounded-[20px] overflow-hidden">
                <img src="<?php echo store_escape($items[0]['image'] ?? ''); ?>" alt="<?php echo store_escape($items[0]['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1200ms] ease-out group-hover:scale-[1.06]" />
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/25 to-transparent"></div>
                <span class="absolute top-6 left-7 text-[11px] tracking-[0.35em] uppercase text-white/80 font-['Montserrat'] font-medium"><?php echo $tileLabels[0]; ?></span>
                <div class="absolute inset-x-0 bottom-0 p-7 md:p-9 flex flex-col items-start">
                    <h4 class="text-white text-[28px] md:text-[38px] leading-tight font-['Cormorant_Garamond'] font-medium"><?php echo store_escape($items[0]['title'] ?? ''); ?></h4>
                    <p class="mt-2 text-[13px] md:text-[14px] text-white/80 font-['Open_Sans'] leading-relaxed max-w-[44ch]"><?php echo store_escape($items[0]['subtitle'] ?? ''); ?></p>
                    <span class="mt-5 inline-flex items-center gap-3 text-[11px] tracking-[0.28em] uppercase font-['Montserrat'] font-semibold text-white border-b border-white/60 pb-1.5 group-hover:border-white group-hover:gap-4 transition-all">
                        Shop Now
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </a>
            <?php endif; ?>

            <div class="flex flex-col gap-4 md:gap-5 w-full md:w-[46%]">
                <div class="flex flex-col sm:flex-row gap-4 md:gap-5">
                    <?php foreach (array_slice($items, 1, 2) as $k => $item): $idx = $k + 1; ?>
                    <a href="<?php echo $shopSearchUrl($item['keyword'] ?? $item['title']); ?>" class="group relative block w-full h-[240px] md:h-[272px] rounded-[20px] overflow-hidden">
                        <img src="<?php echo store_escape($item['image'] ?? ''); ?>" alt="<?php echo store_escape($item['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1200ms] ease-out group-hover:scale-[1.08]" />
                        <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/20 to-transparent"></div>
                        <span class="absolute top-5 left-6 text-[10px] tracking-[0.35em] uppercase text-white/75 font-['Montserrat'] font-medium"><?php echo $tileLabels[$idx]; ?></span>
                        <div class="absolute inset-x-0 bottom-0 p-5 md:p-6">
                            <h4 class="text-white text-[20px] md:text-[24px] leading-snug font-['Cormorant_Garamond'] font-medium"><?php echo store_escape($item['title'] ?? ''); ?></h4>
                            <p class="mt-1 text-[12px] text-white/75 font-['Open_Sans'] leading-relaxed"><?php echo store_escape($item['subtitle'] ?? ''); ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 md:gap-5">
                    <?php foreach (array_slice($items, 3, 2) as $k => $item): $idx = $k + 3; ?>
                    <a href="<?php echo $shopSearchUrl($item['keyword'] ?? $item['title']); ?>" class="group relative block w-full h-[240px] md:h-[272px] rounded-[20px] overflow-hidden">
                        <img src="<?php echo store_escape($item['image'] ?? ''); ?>" alt="<?php echo store_escape($item['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1200ms] ease-out group-hover:scale-[1.08]" />
                        <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/20 to-transparent"></div>
                        <span class="absolute top-5 left-6 text-[10px] tracking-[0.35em] uppercase text-white/75 font-['Montserrat'] font-medium"><?php echo $tileLabels[$idx]; ?></span>
                        <div class="absolute inset-x-0 bottom-0 p-5 md:p-6">
                            <h4 class="text-white text-[20px] md:text-[24px] leading-snug font-['Cormorant_Garamond'] font-medium"><?php echo store_escape($item['title'] ?? ''); ?></h4>
                            <p class="mt-1 text-[12px] text-white/75 font-['Open_Sans'] leading-relaxed"><?php echo store_escape($item['subtitle'] ?? ''); ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<!-- ========================  The "What You Can Get Here" section ends ======================== -->