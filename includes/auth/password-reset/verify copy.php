
<?php

require_once "../../../config/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH - VERIFY REQUEST</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../style.css" />
    <link rel="stylesheet" href="../../../styles/faq.css" />
    <link rel="stylesheet" href="../../../styles/modal.css">
    <link rel="stylesheet" href="../../../styles/tabs.css">
    <link rel="stylesheet" href="../../../styles/inputs.css">
</head>
<body>
    
<?php
        include(__DIR__ . '/../../header.php');
        include(__DIR__ . '/../../options.php');
        ?>

<div class="w-full">
<div class="w-[95%] md:w-[50%] mx-auto p-4 bg-white border border-[1px] border-[#EFEFEF] my-5 rounded-md relative">
<a href="<?php echo DOMAIN; ?>/includes/auth/password-reset/password.php">
        <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />
        </a>
   
        <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
            Let us verify it’s you
        </p>
        <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Enter the 4 digit code sent to golibe.f@gmail.com to create your account
        </p>



        <form class="w-full mt-[1rem] flex flex-col items-center">


            <div class="w-[fit-content] flex items-center gap-3 mx-auto">
                <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
            </div>


            <span
                id="openPasswordRequestNP"
                class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                Verify me
            </span>
        </form>

        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-center">
            Didn't get code? <span class="text-[#1A237E] font-medium cursor-pointer">Resend </span>
        </p>
    </div>
</div>

<?php
        include(__DIR__ . '/../../footer.php');
        ?>


<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

</body>
</html>