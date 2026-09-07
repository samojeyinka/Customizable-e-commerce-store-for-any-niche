<?php
require_once __DIR__ . "/../config/config.php";

$contact = store('contact');
$cHeroImage = !empty($contact['hero_image']) ? $contact['hero_image'] : '';
$cHeroHeading = !empty($contact['hero_heading']) ? $contact['hero_heading'] : 'Contact Us';
$cPhone = !empty($contact['phone']) ? $contact['phone'] : '';
$cEmail = !empty($contact['email']) ? $contact['email'] : '';
$cAddress = !empty($contact['address']) ? $contact['address'] : '';
$cMapUrl = !empty($contact['map_url']) ? $contact['map_url'] : '';
$cFormSubject = !empty($contact['form_subject']) ? $contact['form_subject'] : 'Message from ' . store('store_name');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo store_escape(store('favicon_url')); ?>">
    <title><?php echo store_escape(store('store_name')) . ' | ' . store_escape($cHeroHeading); ?></title>
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
        <div class="details-sec contactus flex items-center justify-center" style="background-image:url('<?php echo store_escape($cHeroImage); ?>')">
            <h3 class="text-white text-center text-[30px] md:text-[60px] font-['Open Sans'] font-medium text-center"><?php echo store_escape($cHeroHeading); ?></h3>
        </div>

        <div class="w-[90%] flex flex-col md:flex-row justify-between gap-6 mx-auto pt-[4rem] pb-5">
            <div class="w-full md:w-[50%]">
                <h3 class="text-[<?php echo store_color('color_primary'); ?>] text-[25px] md:text-[32px] font-['Open Sans'] font-medium">Get in touch</h3>

                <div class="flex flex-col gap-4 pt-[1.5rem]">
                    <div class="w-full md:w-[70%] p-2 flex items-start gap-3 rounded-[4px] border-[1px] border-[#E6E6E6]">
                        <i class="fa-solid fa-phone text-[35px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#262626] text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Contact number</p>
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular"><?php echo store_escape($cPhone); ?></p>
                        </div>
                    </div>

                    <div class="w-full md:w-[70%] p-2 flex items-start gap-3 rounded-[4px] border-[1px] border-[#E6E6E6]">
                        <i class="fa-solid fa-envelope text-[35px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#262626] text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Email</p>
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular"><?php echo store_escape($cEmail); ?></p>
                        </div>
                    </div>

                    <div class="w-full md:w-[70%] p-2 flex items-start gap-3 rounded-[4px] border-[1px] border-[#E6E6E6]">
                        <i class="fa-solid fa-location-dot text-[35px] text-[<?php echo store_color('color_primary'); ?>] leading-none"></i>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#262626] text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Location</p>
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular"><?php echo store_escape($cAddress); ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="w-full md:w-[50%]">
                <h3 class="text-[#4730D0] text-[25px] md:text-[32px] font-['Open Sans'] font-medium">Send us a message</h3>

                <div class="flex flex-col gap-4 pt-[1.5rem]">
                    <form action="send_contact.php" method="POST" id="contactForm">
                        <div class="w-full flex flex-col gap-1">
                            <label
                                for="email"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Email
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="Enter your email address"
                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" 
                                required />
                        </div>

                        <div class="w-full flex flex-col gap-1 mt-4">
                            <label
                                for="name"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Name
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                placeholder="Enter your name"
                                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" 
                                required />
                        </div>

                        <div class="flex items-center gap-0 md:gap-1 w-full font-Satoshi bg-transparent outline-none border-[1px] border-[#E1E1E1] rounded-[8px] mt-4">
                            <div class="w-[210p ml-[1px] md:ml-1 pr-2 border-r-[1.5px] border-[#262626]">
                                +1
                            </div>
                            <input
                                id="phoneNumber"
                                name="phoneNumber"
                                type="text"
                                placeholder="Enter phone number"
                                class="w-full font-regular outline-none text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] text-[14px] md:text-[16px] rounded-[8px]" />
                        </div>

                        <div class="w-full flex flex-col gap-1 mt-4">
                            <label
                                for="message"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Message
                            </label>
                            <textarea 
                                id="message" 
                                name="message"
                                class="w-full min-h-[168px] max-h-[168px] font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"
                                required></textarea>
                        </div>

                        <!-- Hidden field for subject -->
                        <input type="hidden" name="subject" value="<?php echo store_escape($cFormSubject); ?>">

                        <button type="submit" name="send" class="w-[30%] py-[8px] px-3 bg-[<?php echo store_color('color_primary'); ?>] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] mt-4" id="submitBtn">Submit</button>
                        
                        <?php if(isset($_SESSION['contact_status'])): ?>
                            <div class="mt-4 py-2 px-4 rounded <?php echo $_SESSION['contact_status'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <?php echo $_SESSION['contact_message']; ?>
                                <?php unset($_SESSION['contact_status']); unset($_SESSION['contact_message']); ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-[90%] mx-auto max-w-[1440px] my-10">
            <!-- Dynamic Google Maps embed -->
            <iframe 
                src="<?php echo store_escape($cMapUrl); ?>" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>


        <?php
      include('../includes/footer.php');
      ?>
   
    </main>
    
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
</body>

</html>