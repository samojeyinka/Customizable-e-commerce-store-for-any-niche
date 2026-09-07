<?php
$hero = store('hero');
$slideUrls = isset($hero['slides']) && is_array($hero['slides']) ? array_values($hero['slides']) : [];
$overlayFrom = store_escape(!empty($hero['overlay_from']) ? $hero['overlay_from'] : store_color('color_heading'));
$overlayTo = store_escape(!empty($hero['overlay_to']) ? $hero['overlay_to'] : store_color('color_primary'));
?>
<!-- ======================== The hero starts ======================== -->
<div class="carousel relative">
    <div class="carousel-inner">
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="carousel-item" style="background-image:url('<?php echo store_escape($slideUrls[$i] ?? ''); ?>')"></div>
        <?php endfor; ?>
    </div>

    <!-- Overlay gradient for readability -->
    <div class="absolute inset-0 bg-linear-to-r from-[<?php echo $overlayFrom; ?>]/80 via-[<?php echo $overlayFrom; ?>]/40 to-[<?php echo $overlayTo; ?>]/30"></div>

    <!-- Static text overlay -->
    <div class="absolute inset-0 flex items-center justify-center flex-col text-center text-white">
        
        <h1 class="w-[90%] md:w-[70%] text-[36px] md:text-[58px] leading-tight font-Onest font-bold drop-shadow-sm">
            <?php echo store_escape($hero['title'] ?? 'Glow Up Your Beauty Routine'); ?>
        </h1>
        <p class="w-[90%] md:w-[70%] lg:w-[55%] text-[15px] md:text-[18px] font-['Open Sans'] font-light mt-3">
            <?php echo store_escape($hero['subtitle'] ?? ''); ?>
        </p>
        <div class="flex items-center gap-4 mt-8 flex-col sm:flex-row w-[80%] md:w-auto justify-center">
            <a href="<?php echo store_escape($hero['btn1_url'] ?? DOMAIN . '/products/index.php'); ?>"
                class="text-center w-full sm:w-auto py-3 px-8 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] md:text-[18px] font-medium font-['Open Sans'] cursor-pointer rounded-full shadow-lg hover:bg-[<?php echo store_color('color_primary_dark'); ?>] hover:scale-[1.02] transition-all">
                <?php echo store_escape($hero['btn1_text'] ?? 'Shop Now'); ?>
            </a>
            <a href="<?php echo store_escape($hero['btn2_url'] ?? DOMAIN . '/details/about-us.php'); ?>"
                class="text-center w-full sm:w-auto py-3 px-8 bg-white/15 backdrop-blur-sm border border-white/40 text-white text-[16px] md:text-[18px] font-medium font-['Open Sans'] cursor-pointer rounded-full hover:bg-white/25 transition-all">
                <?php echo store_escape($hero['btn2_text'] ?? 'Our Story'); ?>
            </a>
        </div>
    </div>

    <!-- Navigation Controls -->
    <div class="controls">
        <button id="prev">&#10094;</button>
        <button id="next">&#10095;</button>
    </div>
    <div class="indicators">
        <div data-index="0" class="active"></div>
        <div data-index="1"></div>
        <div data-index="2"></div>
        <div data-index="3"></div>
    </div>
</div>
<!-- ======================== The hero ends ======================== -->