<?php
$faq = store('faq');
$items = isset($faq['items']) && is_array($faq['items']) ? array_values($faq['items']) : [];
$faqTitle = !empty($faq['title']) ? $faq['title'] : 'Frequently Asked Questions';
$columns = array_chunk($items, (int) ceil(count($items) / 2));
?>
   <!-- ========================  The FAQ section starts ======================== -->
   <section class="w-[90%] mx-auto max-w-[1440px] pb-10">
            <h2 class="mx-auto text-center text-[30px] md:text-[40px] text-[<?php echo store_color('color_primary'); ?>] font-['League Gothic'] font-medium">
                <?php echo store_escape($faqTitle); ?>
            </h2>

            <div class="flex flex-col md:flex-row items-start md:gap-5">
                <?php foreach ($columns as $col): ?>
                <div class="w-full">
                    <?php foreach ($col as $item): ?>
                    <button class="accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626] font-['Open Sans'] font-medium"><?php echo store_escape($item['q'] ?? ''); ?></button>
                    <div class="faqext text-[16px] font-regular text-[#777777]">
                        <p><?php echo store_escape($item['a'] ?? ''); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </section>
        <!-- ========================  The FAQ section ends ======================== -->