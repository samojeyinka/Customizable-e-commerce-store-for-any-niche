<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH ADMIN | Forgotten Password</title>
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
            <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">WELCOME BACK</h3>

            <div class="flex items-center gap-1 md:gap-2">
                <img src="./assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
            </div>
        </div>
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-['Open Sans'] font-medium pt-5">ADMIN PANEL</h3>

 <p class="font-['Open Sans']  text-[18px] text-[22px] text-[#1A237E] font-medium text-left">
            Forgot Password
        </p>
        <p class="text-left text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
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
                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
            </div>


            <a href="/victosah-admin/forgotten-password-verify.php"
              
                class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                Continue
            </a>
        </form>




    </div>

    <script src="./functions/modals.js"></script>
</body>

</html>