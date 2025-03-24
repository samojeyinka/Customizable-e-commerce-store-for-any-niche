<?php
require_once __DIR__ . "/../config/config.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | About Us</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/inputs.css">
    <link rel="stylesheet" href="./details.css">
</head>

<body>
    <main class="bg-[#FEFEFE]">
    <?php
      include('../includes/header.php');
      include('../includes/options.php');
      ?>
    

        <div class="details-sec aboutus  flex items-center justify-center">
        <h3 class="text-white text-center text-[30px] md:text-[60px] font-['Open Sans'] font-medium text-center">About Us</h3>
        </div>






<div class="w-[90%] mx-auto flex flex-col-reverse md:flex-row gap-5 py-5">
    <div class="text-[#262626]  text-[15px] md:text-[16px] font-['Open Sans'] font-medium-regular">
    At Victosah Solutions, we believe your home should be a sanctuary—where comfort meets elegance. Specializing in luxury bedding and home interiors, we craft thoughtfully designed products that enhance your living spaces with warmth, style, and relaxation.<br/>

From premium-quality bed linens and plush duvets to elegant home décor pieces, our collection is made from the finest materials to ensure durability, comfort, and timeless appeal. Whether you’re transforming your bedroom into a cozy retreat or elevating your home’s ambiance, our designs seamlessly blend beauty with functionality.<br/>

With a commitment to quality, sustainability, and innovation, we take pride in offering eco-friendly, hypoallergenic, and meticulously crafted home essentials. Our mission is simple: to bring you the best in comfort and design so you can create a home you truly love.
    </div>

    <img src="../assets/global/avs.svg" class="w-[50%]"/>
</div>

    


        <section class="w-full py-[4rem]">
            <div class="w-[90%] m-auto">

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-[40%]">
                        <img src="../assets/home/Union.svg" />
                    </div>
                    <div class="w-full md:w-[60%]">
                        <h3 class="text-[#0E1345] text-center text-[25px] md:text-[30px] font-['Montserrat'] font-medium">Why Choose Victosah Solution</h3>

                        <div class="w-full md:w-[90%] grid grid-cols-1 md:grid-cols-2 gap-3 mt-10">

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/validation.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Premium Quality Materials</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>We use high-thread-count cotton, moisture-wicking bamboo, and other luxurious fabrics for superior comfort.</p>
                                </div>
                            </div>


                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/24-support.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Durability & Easy Care</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>Our bedding is designed to last, with easy-to-maintain, fade-resistant fabrics.  We prioritize eco-friendly materials and responsible production</p>
                                </div>
                            </div>

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/truck-fast.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Fast & Reliable Delivery</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>We ensure quick and reliable delivery, so your order arrives on time and in perfect condition.</p>
                                </div>
                            </div>

                           

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/validation.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Stylish & Versatile Designs</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>From modern minimalism to classic elegance, our bedding suits every home style.</p>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>

            </div>
        </section>

        <section class="w-full pb-10">
            <div class="w-[90%] m-auto">
                <h3 class="text-[#0E1345] text-center text-[22px] md:text-[30px] font-['Montserrat'] font-medium">Our Bedding Collection</h3>

                <div class="flex flex-col md:flex-row items-center gap-4 mt-8">
                    <div class="w-full md:w-[50%] h-[288.76px] md:h-[488.76px] rounded-[8px] overflow-hidden relative">
                        <img src="../assets/global/c1.svg" alt="Bed" class="w-full h-full object-cover" />
                        <p class='absolute left-20 md:left-10 bottom-10 md:bottom-7 text-[20px] md:text-[24px] text-white font-medium'>Blankets & Throws</p>
                        <p class='md:hidden absolute left-4 md:left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                        Perfect for layering, adding both warmth and style to your bedroom
                                </p>
                    </div>

                    <div class="flex flex-col gap-2 md:gap-4 w-full  md:w-[50%]">
                        <div class="w-full flex items-center gap-4">
                        <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/cl2.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px]  md:text-[18px] lg:text-nowrap text-white font-medium'>Bed Sheets & Pillowcases</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Made from soft, breathable fabrics like cotton, linen, and bamboo for a smooth, comfortable feel.
                                </p>
                            </div>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/c3.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Pillows & Mattress Toppers</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Ergonomically designed for support and relaxation, catering to different sleep preferences.
                                </p>
                            
                            </div>
                        </div>

                        <div class="w-full flex items-center gap-4">
                        <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/c4.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute left-[25%] bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Lightnings</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Made from soft, breathable fabrics like cotton, linen, and bamboo for a smooth, comfortable feel.
                                </p>
                            </div>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/c5.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute  left-3 bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Duvet Covers & Comforters </p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Designed for warmth and elegance, available in various textures and patterns to complement any décor.
                                </p>
                            </div>
                        </div>

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