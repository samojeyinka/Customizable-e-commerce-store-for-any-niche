<?php
$intro = store('intro');
$stats = isset($intro['stats']) && is_array($intro['stats']) ? array_values($intro['stats']) : [];
$introImage = !empty($intro['image']) ? $intro['image'] : DOMAIN . '/assets/home/default.svg';
$gradFrom = store_escape(!empty($intro['grad_from']) ? $intro['grad_from'] : store_color('color_heading'));
$gradTo = store_escape(!empty($intro['grad_to']) ? $intro['grad_to'] : store_color('color_primary'));
?>
<!-- ========================  The Intro section starts ======================== -->
<section class="w-full relative py-24 md:py-32 overflow-hidden my-10">
    <img src="<?php echo store_escape($introImage); ?>" alt="<?php echo store_escape($intro['label'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover" />
    <div class="absolute inset-0 bg-linear-to-r from-[<?php echo $gradFrom; ?>]/95 via-[<?php echo $gradFrom; ?>]/85 to-[<?php echo $gradTo; ?>]/75"></div>

    <div class="relative w-[90%] mx-auto max-w-[900px] flex flex-col items-center text-center">
        <div class="flex items-center gap-3 mb-6">
            <span class="hidden md:block w-10 h-px bg-white/40"></span>
            <span class="text-[11px] md:text-[12px] tracking-[0.45em] uppercase text-white/85 font-['Montserrat'] font-medium"><?php echo store_escape($intro['label'] ?? ''); ?></span>
            <span class="hidden md:block w-10 h-px bg-white/40"></span>
        </div>
        <h2 class="text-[28px] md:text-[42px] leading-[1.18] text-white font-['Cormorant_Garamond'] font-medium">
            <?php echo store_escape($intro['title'] ?? ''); ?>
        </h2>
        <?php if (!empty($stats)): ?>
        <div class="flex items-center gap-8 md:gap-14 mt-12">
            <?php foreach ($stats as $i => $stat): if ($i > 0): ?>
            <div class="w-[1px] h-12 bg-white/25"></div>
            <?php endif; ?>
            <div class="text-center">
                <p class="text-white text-[26px] md:text-[36px] font-['Montserrat'] font-semibold tracking-tight"><?php echo store_escape($stat['value'] ?? ''); ?></p>
                <p class="text-white/80 text-[12px] md:text-[13px] tracking-[0.12em] uppercase font-['Montserrat'] font-medium mt-2"><?php echo store_escape($stat['label'] ?? ''); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<!-- ========================  The Intro section ends ======================== -->