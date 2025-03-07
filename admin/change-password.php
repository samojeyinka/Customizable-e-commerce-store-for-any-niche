<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH ADMIN | Change Password</title>
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
        <a href="/victosah-admin/forgotten-password-verify.php" class="flex items-center gap-2">
            <img src="./assets/global/arrow-left.svg"/>
    <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
    </a>

    <div class="flex items-center gap-1 md:gap-2">
               
                    <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </div>
    </div>
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-['Open Sans'] font-medium pt-5">ADMIN PANEL</h3>

                
                    <p class="text-[#1A237E] font-['Open Sans']  text-[18px] text-[22px] font-medium text-left">
                        Reset Password
                    </p>
                    <p class="text-left text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Enter your new password
                    </p>



                    <form class="w-full mt-[1rem] flex flex-col gap-4">


                        <div class="flex flex-col gap-1">
                            <label
                                htmlFor="firstname"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Password
                            </label>

                            <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                <input
                                    type="password"
                                    placeholder="Enter your password"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                <img src="./assets/global/eye.svg" class="w-[24px] cursor-pointer" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label
                                htmlFor="firstname"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Confirm Password
                            </label>

                            <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                <input
                                    type="password"
                                    placeholder="Confirm your password"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                <img src="./assets/global/eye-slash.svg" class="w-[24px] cursor-pointer" />
                            </div>

                            <p class="text-[#D93939] font-['Open Sans']  text-[15px] text-[16px] font-regular text-left">
                            Password doesn’t match
                    </p>

                        </div>


                        <a href="/victosah-admin/change-password-success.php"
                           
                            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                           Verify
                        </a>
                    </form>


             
     
    </div>

    <script src="./functions/modals.js"></script>
</body>

</html>