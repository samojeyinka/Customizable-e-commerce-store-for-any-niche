<?php
$faq = store('faq');
$items = isset($faq['items']) && is_array($faq['items']) ? array_values($faq['items']) : [];
$faqTitle = !empty($faq['title']) ? $faq['title'] : 'Frequently Asked Questions';
$columns = array_chunk($items, (int) ceil(count($items) / 2));
?>
   <!-- ========================  The FAQ section starts ======================== -->
   <section class="w-[90%] mx-auto max-w-[1440px] pt-20 md:pt-28 pb-8">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="mx-auto text-center text-[34px] md:text-[48px] text-[<?php echo store_color('color_heading'); ?>] font-['Cormorant_Garamond'] font-medium">
                    <?php echo store_escape($faqTitle); ?>
                </h2>
            </div>

            <div class="flex flex-col md:flex-row items-start md:gap-5">
                <?php foreach ($columns as $col): ?>
                <div class="w-full">
                    <?php foreach ($col as $item): ?>
                    <button class="accordion cursor-pointer w-full flex items-center justify-between gap-4 border-b-[1px] border-[#E1E1E1] py-5 text-[15px] md:text-[16px] text-[<?php echo store_color('color_heading'); ?>] font-['Montserrat'] font-medium hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors"><?php echo store_escape($item['q'] ?? ''); ?></button>
                    <div class="faqext text-[15px] font-regular text-[#777777]">
                        <p class="text-left"><?php echo store_escape($item['a'] ?? ''); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </section>
        <!-- ========================  The FAQ section ends ======================== -->