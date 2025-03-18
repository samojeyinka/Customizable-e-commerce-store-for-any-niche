
<?php

require_once "../../../config/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH - REQUEST OTP</title>
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

<a href="<?php echo DOMAIN; ?>">
        <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />
        </a>
        <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
            Forgot Password?
        </p>
        <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Enter the email you used in creating an account
        </p>



        <form class="w-full mt-[1rem] flex flex-col">


            <div class="flex flex-col gap-1">
                <label
                    htmlFor="firstname"
                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                    Email
                </label>
                <input
                    type="email"
                    placeholder="Enter your email address"
                    required
                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
            </div>


            <button
                type="submit"
                
                class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                Continue
            </button>
        </form>


    </div>
</div>

<?php
        include(__DIR__ . '/../../footer.php');
        ?>


<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>

</body>
</html>