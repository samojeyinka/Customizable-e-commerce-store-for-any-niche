<?php
require_once __DIR__ . "/../config/config.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | About Us</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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






<div class="w-[90%] mx-auto max-w-[1440px] flex flex-col-reverse md:flex-row gap-5 py-5">
    <div class="text-[#262626]  text-[15px] md:text-[16px] font-['Open Sans'] font-medium-regular">
    At Glorefy, we believe beauty is a celebration of your true self where confidence meets radiance. Specializing in premium skincare, makeup, and beauty essentials, we thoughtfully curate products that help you glow with warmth, style, and self-care.<br/>

From dermatologically tested serums and nourishing moisturizers to long-wear makeup and luxurious body care, our collection is crafted from the finest, cleanest ingredients to deliver visible results and timeless appeal. Whether you're perfecting your daily glow-up or pampering yourself with a full beauty ritual, our products blend skin-loving science with everyday elegance.<br/>

With a commitment to quality, sustainability, and innovation, we offer cruelty-free, dermatologist-approved, and meticulously selected beauty essentials. Our mission is simple: to help you discover and embrace the glow within, mbecause when you feel beautiful, you shine.
    </div>

    <img src="https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80" class="w-[50%] rounded-[12px] object-cover" alt="Glorefy skincare"/>
</div>

    


        <section class="w-full py-[4rem]">
            <div class="w-[90%] m-auto max-w-[1440px]">

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-[40%]">
                        <img src="https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80" class="w-full h-full min-h-[320px] object-cover rounded-[16px]" alt="Glorefy beauty"/>
                    </div>
                    <div class="w-full md:w-[60%]">
                        <h3 class="text-[#C2185B] text-center text-[25px] md:text-[30px] font-['Montserrat'] font-medium">Why Choose Glorefy</h3>

                        <div class="w-full md:w-[90%] grid grid-cols-1 md:grid-cols-2 gap-3 mt-10">

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <i class="fa-solid fa-badge-check text-[30px] text-[#C2185B] leading-none"></i>

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Authentic Beauty Products</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>Every brand we stock is 100% authentic, sourced directly, and dermatologist-approved for real results.</p>
                                </div>
                            </div>


                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <i class="fa-solid fa-headset text-[30px] text-[#C2185B] leading-none"></i>

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Beauty That Cares</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>Cruelty-free, clean formulas crafted from skin-loving, sustainable ingredients you can trust.</p>
                                </div>
                            </div>

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <i class="fa-solid fa-truck-fast text-[30px] text-[#C2185B] leading-none"></i>

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Fast & Reliable Delivery</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>We ensure quick and reliable delivery, so your order arrives on time and in perfect condition.</p>
                                </div>
                            </div>

                           

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <i class="fa-solid fa-badge-check text-[30px] text-[#C2185B] leading-none"></i>

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Beauty For Every Skin Type</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>From sensitive to oily skin, find products tailored to your unique beauty needs and goals.</p>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>

            </div>
        </section>

        <section class="w-full pb-10">
            <div class="w-[90%] m-auto max-w-[1440px]">
                <h3 class="text-[#C2185B] text-center text-[22px] md:text-[30px] font-['Montserrat'] font-medium">Our Beauty Collection</h3>

                <div class="flex flex-col md:flex-row items-center gap-4 mt-8">
                    <div class="w-full md:w-[50%] h-[288.76px] md:h-[488.76px] rounded-[8px] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1200&q=80" alt="Skincare" class="w-full h-full object-cover" />
                        <p class='absolute left-20 md:left-10 bottom-10 md:bottom-7 text-[20px] md:text-[24px] text-white font-medium'>Skincare Essentials</p>
                        <p class='md:hidden absolute left-4 md:left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                        Serums, moisturizers, and cleansers formulated for your daily glow
                                </p>
                    </div>

                    <div class="flex flex-col gap-2 md:gap-4 w-full  md:w-[50%]">
                        <div class="w-full flex items-center gap-4">
                        <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80" alt="Face Makeup" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px]  md:text-[18px] lg:text-nowrap text-white font-medium'>Face Makeup</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Foundations, concealers, powders, and blush for a flawless finish.
                                </p>
                            </div>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=800&q=80" alt="Hair Care" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Hair Care</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Nourishing shampoos, conditioners, and treatments for healthy, shiny hair.
                                </p>
                            
                            </div>
                        </div>

                        <div class="w-full flex items-center gap-4">
                        <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=800&q=80" alt="Fragrances" class="w-full object-cover h-full" />
                                <p class='absolute left-[25%] bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Fragrances</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Long-lasting perfumes and body mists for a signature scent.
                                </p>
                            </div>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80" alt="Beauty Tools" class="w-full object-cover h-full" />
                                <p class='absolute  left-3 bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Beauty Tools</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Professional brushes, sponges, and tools to perfect every application.
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