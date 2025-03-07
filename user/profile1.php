<?php
// Include authentication utility
require_once '../includes/auth/auth.php';

// Authentication check
requireAuth();

// Get user data
$user = getCurrentUser();

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | My Profile</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/tabs.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>

</head>

<body>
    <main class="bg-[#FEFEFE]">


     
        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Profile</span>
                </div>
            </div>
        </section>

        <div class="w-[95%] md:w-[90%] mx-auto flex flex-col gap-3 py-5">

            <div class="flex items-center gap-2">
                <div class="w-[50px] h-[50px] md:w-[60px] md:h-[60px] rounded-[50%]">
                    <img src="../assets/user/avatar.svg" alt="Profile Picture" class="w-full h-full" />
                </div>

                <h1 class="text-[15px] md:text-[16px] font-Onest font-medium">golibe.f@gmail.com</h1>
            </div>

            <section class="flex flex-col items-center w-full bg-[#EEE7FF] py-3 px-4 rounded">
                <div class="flex items-center gap-2 mr-auto">
                    <img src="../assets/user/info.svg" alt="Profile Picture" class="w-[24px]" />
                    <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                        Need for your information
                    </p>
                </div>
                <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[4rem] pr-3 md:pr-0">
                    For a smoother checkout experience, your billing and contact address will be automatically filled based on your saved details. You can update or change them if needed during checkout
                </p>
            </section>


            <div class="w-full md:w-[95%] lg:w-[70%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
                <div class="flex flex-col gap-2">
                    <div class="flex md:items-center gap-2 md:gap-0 flex-col-reverse md:flex-row justify-between">
                        <div class="flex flex-col items-center">


                            <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                                Contact Info
                            </p>

                            <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                We'll use this email to send you details and updates about your order
                            </p>
                        </div>
<!-- 
                        <button type="submit" class="w-[fit-content] py-2 px-4 bg-transparent border-[1px] border-[#1A237E] text-[#1A237E] text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                            <img src="../assets/user/edit.svg" alt="Profile Picture" class="w-[24px]" />
                            <span>Edit details</span></button> -->
                            <button type="submit" class="w-[fit-content] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                            Save Changes
                        </button>


                    </div>

                    <div class="flex flex-col gap-">
                        <label
                            htmlFor="firstname"
                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                            Email address*
                        </label>
                        <input
                            type="text"
                            placeholder="Enter your email address"
                            class="w-full md:w-[65%] font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#DCDCDC] font-regular text-[#2C2C2C] placeholder:text-[#CCCCCC] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                    </div>

                </div>

            </div>

            <div class="w-full md:w-[90%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
                <!-- <button type="submit" class="w-[fit-content] py-2 px-4 bg-transparent border-[1px] border-[#1A237E] text-[#1A237E] text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                    <img src="../assets/user/edit.svg" alt="Profile Picture" class="w-[24px]" />
                    <span>Edit details</span></button> -->

                    <button type="submit" class="w-[fit-content] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                            Save Changes
                        </button>

                <div class="flex items-center flex-col md:flex-row gap-4">

                    <div class="w-full md:w-[50%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
                        <div class="flex flex-col items-center">


                            <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                                Delivery
                            </p>

                            <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                Enter the address where you want your order delivered
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Country
                                </label>
                                <input
                                    type="text"
                                    placeholder="Nigeria"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>

                            <div class="w-full flex items-center gap-3">
                                <div class="w-full flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        First Name
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="Nigeria"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Last Name
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="Enter first name"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>
                            </div>

                            <div class="flex items-center gap-0 md:gap-1 w-full font-Satoshi bg-transparent outline-none  border-[1px] border-[#E1E1E1] rounded-[8px]">
                                <div class="w-[210p ml-[1px] md:ml-1  pr-2 border-r-[2px] border-[#E1E1E1]">
                                    +234
                                </div>
                                <input
                                    type="text"
                                    name="phoneNumber"
                                    placeholder="Enter phone number"
                                    class=" w-full  font-regular outline-none text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Address
                                </label>
                                <input
                                    type="text"
                                    placeholder="Enter the address for us to deliver too"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>


                            <!-- <p class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#1A237E] cursor-pointer">Use my current location</p> -->
                            <div class="w-full flex  flex-col md:flex-row items-center gap-2">

<div class="w-full md:w-[60%] flex items-center gap-2">
    <div class="w-full flex flex-col gap-1">
    <label
        htmlFor="firstname"
        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
        State
    </label>
    <input
        type="text"
        placeholder=""
        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
    </div>

    <div class="w-full flex flex-col gap-1">
    <label
        htmlFor="firstname"
        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
        City
    </label>
    <input
        type="text"
        placeholder=""
        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
    </div>
</div>

<div class="w-full md:w-[40%] flex flex-col gap-1">
    <label
        htmlFor="firstname"
        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
        Zip Code
    </label>
    <input
        type="text"
        placeholder=""
        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
</div>

</div>

                            <div class="flex items-center gap-1">
                                <input type="checkbox" name="billing" id="billing" />
                                <label for="billing" class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#5B5B5B] cursor-pointer">Use same address for billing</label>
                            </div>
                        </div>


                    </div>

                    <div class="w-full md:w-[50%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
                        <div class="flex flex-col items-center">


                            <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                                Billing Address
                            </p>

                            <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                We use your billing address to verify your payment, and ensure a secure and seamless checkout experience
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Country
                                </label>
                                <input
                                    type="text"
                                    placeholder="Nigeria"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>

                            <div class="w-full flex items-center gap-3">
                                <div class="w-full flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        First Name
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="Nigeria"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>

                                <div class="w-full flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Last Name
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="Enter first name"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                                </div>
                            </div>



                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Address
                                </label>
                                <input
                                    type="text"
                                    placeholder="Enter the address for us to deliver too"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>


                            <!-- <p class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#1A237E] cursor-pointer">Use my current location</p> -->
                            <div class="w-full flex  flex-col md:flex-row items-center gap-2">

                                <div class="w-full md:w-[60%] flex items-center gap-2">
                                    <div class="w-full flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        State
                                    </label>
                                    <input
                                        type="text"
                                        placeholder=""
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                    </div>

                                    <div class="w-full flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        City
                                    </label>
                                    <input
                                        type="text"
                                        placeholder=""
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                    </div>
                                </div>

                                <div class="w-full md:w-[40%] flex flex-col gap-1">
                                    <label
                                        htmlFor="firstname"
                                        class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                        Zip Code
                                    </label>
                                    <input
                                        type="text"
                                        placeholder=""
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[4px] px-2 text-[14px] md:text-[16px] rounded-[4px]" />
                                </div>

                            </div>



                        </div>


                    </div>

                </div>



            </div>

        </div>




    </main>

    <script src="../functions/modals.js"></script>
    <script src="../functions/modals2.js"></script>
    <script src="../functions/functions.js"></script>
    <script src="../functions/tabs.js"></script>
    <script type="text/javascript" src="../functions/accordion.js"></script>
    <script type="text/javascript" src="../functions/faq.js"></script>
    <script type="text/javascript" src="../functions/dropdown.js"></script>

</body>

</html