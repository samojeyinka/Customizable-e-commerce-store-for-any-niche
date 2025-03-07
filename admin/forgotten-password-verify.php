<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH ADMIN | Verify</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="./styles/modal.css">
    <link rel="stylesheet" href="./styles/tabs.css">


    <style>
        body {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url("./assets/global/bg.svg");
            background-position: center;
            background-size: cover;
        }
    </style>

</head>

<body>
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">

        <div class="w-[95%] mx-auto flex items-center justify-between">
            <a href="/victosah-admin/forgotten-password.php" class="flex items-center gap-2">
                <img src="./assets/global/arrow-left.svg" />
                <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
            </a>
            <div class="flex items-center gap-1 md:gap-2">
                <img src="./assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
            </div>
        </div>

        <p class="font-['Open Sans']  text-[18px] text-[22px] font-medium text-center">
            ADMIN PANEL
        </p>



        <p class="pl-[2.5%] font-['Open Sans']  text-[18px] text-[22px] font-medium text-left pt-5 text-[#1A237E]">
            Verification
        </p>
        <p class="pl-[2.5%] font-['Open Sans']  text-[16px] text-[20px] font-medium text-left">
            Let us verify it’s you
        </p>
        <p class="pl-[2.5%]  mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Enter the 4 digit code sent to golibe.f@gmail.com to create your account
        </p>

        <form class="w-full mt-[1rem] flex items-center flex-col">

            <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                Enter Code
            </p>

            <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
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


            <a href="/victosah-admin/change-password.php"


                class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                Verify
            </a>
        </form>

        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left">
            Resend code in <span class="text-[#1A237E]">23sec</span>
        </p>




    </div>



    </div>

    <script src="./functions/modals.js"></script>
</body>

</html>