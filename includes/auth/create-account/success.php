<?php
require_once __DIR__ . "/../../config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>


<div id="regSuccess" class="modal regsuccess">
    <div class="modal-content overflow-hidden p-4 flex flex-col items-center">

        <i class="fa-solid fa-circle-check text-[120px] text-[<?php echo store_color('color_primary'); ?>] mx-auto leading-none"></i>

        <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
            Account Creation Successful
        </p>
        <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Welcome aboard! Your account has been created successfully. Start exploring and enjoy shopping with us.
        </p>




        <span class="text-center w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[<?php echo store_color('color_primary'); ?>] text-white rounded-[8px] mt-10 cursor-pointer"
            id="closeregsucces">
            Continue Shopping
        </span>
        </form>


    </div>
</div>
    
</body>
</html>