<?php
$hero = store('hero');
$slideUrls = isset($hero['slides']) && is_array($hero['slides']) ? array_values($hero['slides']) : [];
$slideCount = count($slideUrls);
if ($slideCount === 0) {
    $slideUrls = [''];
    $slideCount = 1;
}
$overlayBase = store_escape(!empty($hero['overlay_from']) ? $hero['overlay_from'] : store_color('color_heading'));
$overlayAccent = store_escape(!empty($hero['overlay_to']) ? $hero['overlay_to'] : store_color('color_primary_dark', store_color('color_primary')));
$storeName = store_escape(store('store_name', 'GLOREFY'));
$heroTitle = store_escape($hero['title'] ?? 'Glow Up Your Beauty Routine');
$heroSubtitle = store_escape($hero['subtitle'] ?? '');
?>
<!-- ======================== The hero starts ======================== -->
<section class="carousel relative bg-[<?php echo $overlayBase; ?>]">
    <div class="carousel-inner">
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="carousel-item" style="background-image:url('<?php echo store_escape($slideUrls[$i % $slideCount] ?? ''); ?>')"></div>
        <?php endfor; ?>
    </div>

    <!-- Calm, dark gradient for readability (theme-aware, bottom-weighted) -->
    <div class="absolute inset-0 bg-linear-to-t from-[<?php echo $overlayBase; ?>]/95 via-[<?php echo $overlayBase; ?>]/55 to-[<?php echo $overlayBase; ?>]/15"></div>

    <!-- Static text overlay -->
    <div class="absolute inset-0 flex items-center justify-center text-center px-6 md:px-0">
        <div class="flex flex-col items-center text-white max-w-[820px]">
            <div class="flex items-center gap-3 mb-6">
                <span class="hidden md:block w-10 h-px bg-white/50"></span>
                <span class="text-[11px] md:text-[12px] tracking-[0.5em] uppercase text-white/90 font-['Montserrat'] font-medium"><?php echo $storeName; ?></span>
                <span class="hidden md:block w-10 h-px bg-white/50"></span>
            </div>

            <h1 class="text-[40px] md:text-[64px] lg:text-[72px] leading-[1.08] font-['Cormorant_Garamond'] font-medium text-white">
                <?php echo $heroTitle; ?>
            </h1>
            <p class="max-w-[560px] text-[15px] md:text-[17px] leading-relaxed text-white/85 mt-6 font-['Open_Sans'] font-light">
                <?php echo $heroSubtitle; ?>
            </p>

            <div class="flex items-center justify-center gap-5 mt-10 flex-col sm:flex-row">
                <a href="<?php echo store_escape($hero['btn1_url'] ?? DOMAIN . '/products/index.php'); ?>"
                    class="inline-flex items-center gap-3 w-full sm:w-auto justify-center py-3.5 px-10 bg-white text-[<?php echo $overlayBase; ?>] text-[13px] md:text-[14px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full hover:bg-[<?php echo $overlayAccent; ?>] hover:text-white transition-all duration-300">
                    <?php echo store_escape($hero['btn1_text'] ?? 'Shop Now'); ?>
                    <i class="fa-solid fa-arrow-right text-[12px]"></i>
                </a>
                <a href="<?php echo store_escape($hero['btn2_url'] ?? DOMAIN . '/details/about-us.php'); ?>"
                    class="inline-flex items-center gap-2 w-full sm:w-auto justify-center py-3.5 px-10 text-white text-[13px] md:text-[14px] tracking-[0.14em] uppercase font-['Montserrat'] font-semibold cursor-pointer rounded-full border border-white/50 hover:border-white hover:bg-white/10 transition-all duration-300">
                    <?php echo store_escape($hero['btn2_text'] ?? 'Our Story'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Controls -->
    <div class="controls">
        <button id="prev" aria-label="Previous slide">&#10094;</button>
        <button id="next" aria-label="Next slide">&#10095;</button>
    </div>
    <div class="indicators">
        <div data-index="0" class="active"></div>
        <div data-index="1"></div>
        <div data-index="2"></div>
        <div data-index="3"></div>
    </div>
</section>
<!-- ======================== The hero ends ======================== -->