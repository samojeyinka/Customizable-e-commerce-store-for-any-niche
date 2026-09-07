<?php
require_once __DIR__ . "/../config/config.php";

$about = store('about');
$aboutWhy = isset($about['why']) && is_array($about['why']) ? $about['why'] : [];
$aboutCollection = isset($about['collection']) && is_array($about['collection']) ? $about['collection'] : [];
$aboutContent = isset($about['content']) && is_array($about['content']) ? $about['content'] : [];
$whyFeatures = isset($aboutWhy['features']) && is_array($aboutWhy['features']) ? array_values($aboutWhy['features']) : [];
$collectionCards = isset($aboutCollection['cards']) && is_array($aboutCollection['cards']) ? array_values($aboutCollection['cards']) : [];
$aboutHeroImage = !empty($about['hero_image']) ? $about['hero_image'] : '';
$aboutHeroHeading = !empty($about['hero_heading']) ? $about['hero_heading'] : 'About Us';
$aboutImage = !empty($about['image']) ? $about['image'] : DOMAIN . '/assets/home/default.svg';
$aboutWhyImage = !empty($aboutWhy['image']) ? $aboutWhy['image'] : $aboutImage;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo store_escape(store('favicon_url')); ?>">
    <title><?php echo store_escape(store('store_name')) . ' | ' . store_escape($aboutHeroHeading); ?></title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[<?php echo store_color('color_bg'); ?>]">
    <?php
      include('../includes/header.php');
      include('../includes/options.php');
      ?>
    

        <div class="details-sec aboutus flex items-center justify-center" style="background-image:url('<?php echo store_escape($aboutHeroImage); ?>')">
        <h3 class="text-white text-center text-[30px] md:text-[60px] font-['Open Sans'] font-medium text-center"><?php echo store_escape($aboutHeroHeading); ?></h3>
        </div>





<div class="w-[90%] mx-auto max-w-[1440px] flex flex-col-reverse md:flex-row gap-5 py-5">
    <div class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium-regular">
        <?php foreach ($aboutContent as $i => $paragraph): ?>
        <?php echo store_escape($paragraph); ?><?php if ($i < count($aboutContent) - 1): ?><br/><br/><?php endif; ?>
        <?php endforeach; ?>
    </div>

    <img src="<?php echo store_escape($aboutImage); ?>" class="w-[50%] rounded-[12px] object-cover" alt="<?php echo store_escape($aboutHeroHeading); ?>"/>
</div>

    


        <section class="w-full py-[4rem]">
            <div class="w-[90%] m-auto max-w-[1440px]">

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-[40%]">
                        <img src="<?php echo store_escape($aboutWhyImage); ?>" class="w-full h-full min-h-[320px] object-cover rounded-[16px]" alt="<?php echo store_escape($aboutWhy['title'] ?? ''); ?>"/>
                    </div>
                    <div class="w-full md:w-[60%]">
                        <h3 class="text-[<?php echo store_color('color_primary'); ?>] text-center text-[25px] md:text-[30px] font-['Montserrat'] font-medium"><?php echo store_escape($aboutWhy['title'] ?? 'Why Choose Us'); ?></h3>

                        <div class="w-full md:w-[90%] grid grid-cols-1 md:grid-cols-2 gap-3 mt-10">
                            <?php foreach ($whyFeatures as $feature): ?>
                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <i class="fa-solid <?php echo store_escape($feature['icon'] ?? 'fa-circle-check'); ?> text-[30px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>

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

        <section class="w-full pb-10">
            <div class="w-[90%] m-auto max-w-[1440px]">
                <h3 class="text-[<?php echo store_color('color_primary'); ?>] text-center text-[22px] md:text-[30px] font-['Montserrat'] font-medium"><?php echo store_escape($aboutCollection['title'] ?? 'Our Collection'); ?></h3>

                <div class="flex flex-col md:flex-row items-center gap-4 mt-8">
                    <?php if (isset($collectionCards[0])): $card = $collectionCards[0]; ?>
                    <div class="w-full md:w-[50%] h-[288.76px] md:h-[488.76px] rounded-[8px] overflow-hidden relative">
                        <img src="<?php echo store_escape($card['image'] ?? ''); ?>" alt="<?php echo store_escape($card['title'] ?? ''); ?>" class="w-full h-full object-cover" />
                        <p class='absolute left-20 md:left-10 bottom-10 md:bottom-7 text-[20px] md:text-[24px] text-white font-medium'><?php echo store_escape($card['title'] ?? ''); ?></p>
                        <p class='md:hidden absolute left-4 md:left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                        <?php echo store_escape($card['subtitle'] ?? ''); ?>
                                </p>
                    </div>
                    <?php endif; ?>

                    <div class="flex flex-col gap-2 md:gap-4 w-full  md:w-[50%]">
                        <?php for ($row = 0; $row < 2; $row++): ?>
                        <div class="w-full flex items-center gap-4">
                            <?php foreach (array_slice($collectionCards, 1 + $row * 2, 2) as $card): ?>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="<?php echo store_escape($card['image'] ?? ''); ?>" alt="<?php echo store_escape($card['title'] ?? ''); ?>" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px]  md:text-[18px] lg:text-nowrap text-white font-medium'><?php echo store_escape($card['title'] ?? ''); ?></p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                <?php echo store_escape($card['subtitle'] ?? ''); ?>
                                </p>
                            
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endfor; ?>

                    </div>
                </div>

            </div>
        </section>

     

        <?php
      include('../includes/footer.php');
      ?>
    </main>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
    <script src="./functions/tabs.js"></script>

</body>

</html>