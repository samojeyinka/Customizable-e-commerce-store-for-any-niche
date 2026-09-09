<?php
$why = store('why');
$features = isset($why['features']) && is_array($why['features']) ? array_values($why['features']) : [];
$whyImage = !empty($why['image']) ? $why['image'] : DOMAIN . '/assets/home/default.svg';
$whyBadgeTitle = $why['badge_title'] ?? 'Dermatologist-approved formulas';
$whyBadgeText = $why['badge_text'] ?? '';
$icons = ['validation.svg', '24-support.svg', 'truck-fast.svg', 'validation.svg'];
?>
<!-- ========================  The Why section starts ======================== -->
<section class="w-full py-20 md:py-28">
    <div class="w-[90%] mx-auto max-w-[1440px]">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 lg:items-center">

            <!-- Image panel -->
            <div class="w-full lg:w-[44%] relative">
                <img src="<?php echo store_escape($whyImage); ?>" alt="<?php echo store_escape($why['title'] ?? 'Why choose us'); ?>" class="w-full aspect-[4/5] object-cover rounded-[24px]" />
                <div class="absolute -bottom-5 md:-bottom-6 left-5 md:left-8 right-5 md:right-auto md:max-w-[300px] bg-white/95 backdrop-blur rounded-[16px] px-6 py-5 shadow-[0_18px_40px_-20px_rgba(0,0,0,0.25)]">
                    <p class="text-[<?php echo store_color('color_primary'); ?>] text-[12px] tracking-[0.22em] uppercase font-['Montserrat'] font-semibold"><?php echo store_escape($whyBadgeTitle); ?></p>
                    <p class="text-[#777777] text-[13px] md:text-[14px] font-['Open_Sans'] mt-1.5 leading-relaxed"><?php echo store_escape($whyBadgeText); ?></p>
                </div>
            </div>

            <!-- Copy + features -->
            <div class="w-full lg:w-[56%] mt-8 lg:mt-0">
                <span class="text-[11px] md:text-[12px] tracking-[0.4em] uppercase text-[<?php echo store_color('color_primary'); ?>] font-['Montserrat'] font-semibold"><?php echo store_escape($why['label'] ?? 'Why us'); ?></span>
                <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-[32px] md:text-[42px] leading-[1.12] font-['Cormorant_Garamond'] font-medium mt-3"><?php echo store_escape($why['title'] ?? ''); ?></h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-10 mt-12">
                    <?php foreach ($features as $i => $feature): ?>
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center" style="background-color: var(--glor-tint, #F5EEF2)">
                            <img src="<?php echo DOMAIN; ?>/assets/home/<?php echo store_escape($icons[$i] ?? 'validation.svg'); ?>" class="w-[22px] h-[22px]" alt="" />
                        </div>
                        <div>
                            <strong class="block text-[16px] md:text-[17px] font-['Montserrat'] font-semibold text-[<?php echo store_color('color_heading'); ?>]"><?php echo store_escape($feature['title'] ?? ''); ?></strong>
                            <p class="text-[14px] text-[#777777] font-['Open_Sans'] leading-relaxed mt-1.5"><?php echo store_escape($feature['text'] ?? ''); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- ========================  The Why section ends ======================== -->