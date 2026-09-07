<?php
$why = store('why');
$features = isset($why['features']) && is_array($why['features']) ? array_values($why['features']) : [];
$whyImage = !empty($why['image']) ? $why['image'] : DOMAIN . '/assets/home/default.svg';
?>
   <!-- ========================  The Why section starts ======================== -->
   <section class="w-full py-[4rem]">
            <div class="w-[90%] m-auto max-w-[1440px]">

                <div class="flex flex-col md:flex-row gap-8">
                    <div class="w-full md:w-[40%] relative">
                        <img src="<?php echo store_escape($whyImage); ?>" alt="<?php echo store_escape($why['title'] ?? 'Why choose us'); ?>" class="w-full h-full min-h-[320px] object-cover rounded-[16px]" />
                        <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur rounded-[12px] px-5 py-4">
                            <p class="text-[<?php echo store_color('color_heading'); ?>] text-[15px] md:text-[16px] font-['Open Sans'] font-semibold"><?php echo store_escape($why['badge_title'] ?? ''); ?></p>
                            <p class="text-[#777777] text-[13px] md:text-[14px] font-['Open Sans']"><?php echo store_escape($why['badge_text'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="w-full md:w-[60%]">
                        <div class="md:text-center">
                            <span class="text-[12px] md:text-[13px] tracking-[0.3em] uppercase text-[<?php echo store_color('color_primary'); ?>] font-['Open Sans'] font-semibold"><?php echo store_escape($why['label'] ?? 'Why us'); ?></span>
                            <h3 class="text-[<?php echo store_color('color_heading'); ?>] text-center text-[25px] md:text-[30px] font-['Montserrat'] font-semibold"><?php echo store_escape($why['title'] ?? ''); ?></h3>
                        </div>

                        <div class="w-full md:w-[90%] grid grid-cols-1 md:grid-cols-2 gap-3 mt-10">

                            <?php foreach ($features as $i => $feature): ?>
                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="<?php echo DOMAIN; ?>/assets/home/<?php
                                    $icons = ['validation.svg', '24-support.svg', 'truck-fast.svg', 'validation.svg'];
                                    echo store_escape($icons[$i] ?? 'validation.svg');
                                ?>" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'><?php echo store_escape($feature['title'] ?? ''); ?></strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'><?php echo store_escape($feature['text'] ?? ''); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        </div>

                    </div>
                </div>

            </div>
        </section>
        <!-- ========================  The Why section ends ======================== -->