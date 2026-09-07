<?php
$allReviews = store_reviews();
$rows = [array_values(array_filter($allReviews, fn($r) => (int)($r['row'] ?? 0) === 0)),
         array_values(array_filter($allReviews, fn($r) => (int)($r['row'] ?? 0) === 1))];

$renderStars = function ($rating) {
    $rating = max(1, min(5, (int) $rating));
    for ($i = 1; $i <= 5; $i++) {
        echo '<img src="' . DOMAIN . '/assets/products/' . ($i <= $rating ? 'star.svg' : 'lstar.svg') . '" class="w-[16px]" />';
    }
};

$renderSlide = function ($review) use ($renderStars) {
    ?>
    <div class="review flex flex-col gap-2 rounded-[12px] p-4 bg-[#FFFFFF] min-w-[280px] shadow-sm">
        <p class="text-[#777777] text-[14px] md:text-[15px] font-['Open Sans'] font-regular"><?php echo store_escape($review['text'] ?? ''); ?></p>
        <div class="flex items-center gap-1">
            <?php $renderStars($review['rating'] ?? 5); ?>
        </div>
        <p class="text-[<?php echo store_color('color_heading'); ?>] text-[15px] md:text-[16px] font-['Open Sans'] font-semibold">— <?php echo store_escape($review['name'] ?? ''); ?></p>
    </div>
    <?php
};
?>
<!-- ========================  The Review section starts ======================== -->
<div class="w-full py-12" style="background-color: var(--glor-tint2, #F8F0F4)">
    <div class="w-[90%] mx-auto max-w-[1440px] text-center mb-8">
        <span class="text-[12px] md:text-[13px] tracking-[0.3em] uppercase text-[<?php echo store_color('color_primary'); ?>] font-['Open Sans'] font-semibold">Testimonials</span>
        <h3 class="mx-auto text-[<?php echo store_color('color_heading'); ?>] text-center text-[22px] md:text-[30px] font-['Montserrat'] font-semibold"><?php echo store_escape(store('reviews_title', "Don't Just Hear From Us, Hear From Our Glorefy Family")); ?></h3>
    </div>

    <div class="pt-6 flex flex-col gap-6">
        <?php if (!empty($rows[0])): ?>
        <div class="reviews-slider-container overflow-hidden">
            <div class="reviews-slider reviews-slider-top flex animate-scroll-right">
                <div class="reviews-slide flex gap-4 mx-2">
                    <?php foreach ($rows[0] as $review) { $renderSlide($review); } ?>
                </div>
                <div class="reviews-slide flex gap-4 mx-2">
                    <?php foreach ($rows[0] as $review) { $renderSlide($review); } ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($rows[1])): ?>
        <div class="reviews-slider-container overflow-hidden">
            <div class="reviews-slider reviews-slider-bottom flex animate-scroll-left">
                <div class="reviews-slide flex gap-4 mx-2">
                    <?php foreach ($rows[1] as $review) { $renderSlide($review); } ?>
                </div>
                <div class="reviews-slide flex gap-4 mx-2">
                    <?php foreach ($rows[1] as $review) { $renderSlide($review); } ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript to ensure continuous smooth scrolling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topSlider = document.querySelector('.reviews-slider-top');
        const bottomSlider = document.querySelector('.reviews-slider-bottom');

        if (!topSlider && !bottomSlider) return;

        [topSlider, bottomSlider].forEach(slider => {
            if (!slider) return;
            const slides = slider.querySelectorAll('.reviews-slide');
            slides.forEach(slide => {
                slider.appendChild(slide.cloneNode(true));
            });
            const count = slider.querySelectorAll('.review').length;
            slider.style.animationDuration = (count * (slider === topSlider ? 2 : 1.5)) + 's';
        });
    });
</script>
<!-- ========================  The Review section ends ======================== -->