<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH ADMIN | Sign In</title>
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

        <section id="dangeralert" class="flex flex-col items-center w-full bg-[#FDECEC] shadow-lg mt-2 py-3 px-4 rounded relative overflow-hidden">
            <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <div class="flex items-center gap-2 mr-auto">
                <img src="./assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[24px] cursor-pointer" />
                <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                    Incorrect details
                </p>
            </div>
            <p class="text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[3rem] pr-3 ">
                Your email or password is incorrect. Try again
            </p>
        </section>

        <form class="flex flex-col gap-4 pt-4">
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

            <a href="/victosah-admin/forgotten-password.php" class='text-[14px] font-["Open Sans] text-[#777777] font-regular  cursor-pointer'>Forgot Password?</>

                <a href="/victosah-admin/verify-signin.php" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Sign In</a>

        </form>


    </div>

    <script src="./functions/modals.js"></script>
</body>

</html>