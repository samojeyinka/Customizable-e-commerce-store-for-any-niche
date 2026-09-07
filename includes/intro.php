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
    <div class="absolute inset-0 bg-linear-to-r from-[<?php echo $gradFrom; ?>]/90 via-[<?php echo $gradFrom; ?>]/70 to-[<?php echo $gradTo; ?>]/60"></div>

    <div class="relative w-[90%] mx-auto max-w-[1440px] flex flex-col items-center text-center">
        <span class="text-[12px] md:text-[13px] tracking-[0.35em] uppercase text-white/80 font-['Open Sans'] font-semibold mb-5"><?php echo store_escape($intro['label'] ?? ''); ?></span>
        <h2 class="md:w-[70%] text-[26px] md:text-[38px] leading-snug text-white font-['Montserrat'] font-semibold">
            <?php echo store_escape($intro['title'] ?? ''); ?>
        </h2>
        <?php if (!empty($stats)): ?>
        <div class="flex items-center gap-8 md:gap-12 mt-10">
            <?php foreach ($stats as $i => $stat): if ($i > 0): ?>
            <div class="w-[1px] h-10 bg-white/30"></div>
            <?php endif; ?>
            <div class="text-center">
                <p class="text-white text-[24px] md:text-[32px] font-Onest font-bold"><?php echo store_escape($stat['value'] ?? ''); ?></p>
                <p class="text-white/80 text-[13px] md:text-[14px] font-['Open Sans']"><?php echo store_escape($stat['label'] ?? ''); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<!-- ========================  The Intro section ends ======================== -->