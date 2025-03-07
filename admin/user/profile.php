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
<style>
    body{
    overflow-x: hidden;
} 
</style>

</head>

<body>
    <main class="bg-[#FEFEFE]">
        <header class="w-full bg-[#E8E9F2] flex items-center justify-center p-3">
            <nav class="w-[90%] flex items-center justify-between">
                <div class="flex items-center gap-1 md:gap-2">
                    <img src="../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                    <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </div>
                <div class="hidden md:flex items-center gap-0">
                    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="../assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
                </div>
                <div class="flex items-center gap-6">
                    <a href="#">
                        <img src="../assets/global/bag.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>
                    <a href="#">
                        <img src="../assets/global/lovely.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>

                    <a href="#">
                        <img src="../assets/global/profile.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>

                </div>
            </nav>
        </header>

        <section class="w-full bg-[#FFFFFFF] py-4 border-b-[1px] border-[#E1E1E1]">
            <div class="w-[90%] mx-auto hidden  md:flex items-center justify-between">
                <div class="flex items-center gap-10">

                    <div class="flex items-center gap-2 cursor-pointer">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Duvets</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />
                    </div>

                    <div class="flex items-center gap-2 cursor-pointer">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Bedsheets</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />
                    </div>

                    <div class="flex items-center gap-2 cursor-pointer">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Foams</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />
                    </div>

                    <div class="flex items-center gap-2 cursor-pointer">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Pillows</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />
                    </div>

                    <div class="flex items-center gap-2 cursor-pointer">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Lightings</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />
                    </div>


                </div>


                <div class="flex items-center gap-2 cursor-pointer">
                    <img src="../assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
                    <strong class='text-[13px] md:text-[14px] font-["Open Sans] text-[#1A237E] font-medium underline'>Track your order</strong>
                </div>

            </div>

            <div class="w-[90%] mx-auto flex items-center gap-10  md:hidden">
                <img src="../assets/global/menu.svg" alt="menu" class="cursor-pointer w-[24px]" />
                <div class="w-[100%]  flex items-center  items-center gap-0">
                    <div class="w-full flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="../assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
                </div>
            </div>
        </section>

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

                        <button type="submit" class="w-[fit-content] py-2 px-4 bg-transparent border-[1px] border-[#1A237E] text-[#1A237E] text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                            <img src="../assets/user/edit.svg" alt="Profile Picture" class="w-[24px]" />
                            <span>Edit details</span></button>


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
                <button type="submit" class="w-[fit-content] py-2 px-4 bg-transparent border-[1px] border-[#1A237E] text-[#1A237E] text-[16px] font-['Open Sans'] flex items-center gap-2 cursor-pointer rounded-[4px]">
                    <img src="../assets/user/edit.svg" alt="Profile Picture" class="w-[24px]" />
                    <span>Edit details</span></button>

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


                            <p class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#1A237E] cursor-pointer">Use my current location</p>
                            <div class="w-full flex  flex-col md:flex-row items-center gap-2">

                                <div class="w-full md:w-[60%] flex items-center gap-2">
                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            htmlFor="firstname"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            State
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2">
                                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Oyo</span>
                                                <img src="../assets/products/down2.svg" class="w-[12px]" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            htmlFor="firstname"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            State
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2">
                                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Oyo</span>
                                                <img src="../assets/products/down2.svg" class="w-[12px]" />
                                            </div>
                                        </div>
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


                            <p class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#1A237E] cursor-pointer">Use my current location</p>
                            <div class="w-full flex  flex-col md:flex-row items-center gap-2">

                                <div class="w-full md:w-[60%] flex items-center gap-2">
                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            htmlFor="firstname"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            State
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2">
                                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Oyo</span>
                                                <img src="../assets/products/down2.svg" class="w-[12px]" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full flex flex-col gap-1">
                                        <label
                                            htmlFor="firstname"
                                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                            State
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-full rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2">
                                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Oyo</span>
                                                <img src="../assets/products/down2.svg" class="w-[12px]" />
                                            </div>
                                        </div>
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



        <footer class="w-full bg-[#E8E9F2] py-7">
            <div class="w-[90%] flex gap-4 flex-col md:flex-row justify-between mx-auto">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-1">
                        <img src="../assets/global/logo.svg" class="w-[50px] h-[48.15px]" />
                        <h1 class="text-[20px] text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            <img src="../assets/global/location.svg" class="w-[24px] h-[24px]" />
                            <p class="text-[15px] text-[16px] font-['Open Sans'] font-regular">Location</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/global/call.svg" class="w-[24px] h-[24px]" />
                            <p class="text-[15px] text-[16px] font-['Open Sans'] font-regular">09090909090</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/global/mail.svg" class="w-[24px] h-[24px]" />
                            <p class="text-[15px] text-[16px] font-['Open Sans'] font-regular">supportvictosah@gmail.com</p>
                        </div>

                    </div>

                </div>

                <div class="flex flex-col gap-2">
                    <h1 class="text-[#262626] text-[20px] md:text-[24px] font-['Montserrat'] font-medium">Quick Links</h1>
                    <ul class="flex flex-col gap-2">
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="#" class="text-[#777777]">About Us</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="#" class="text-[#777777]">Track Your Order</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="#" class="text-[#777777]">Return Policy</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="#" class="text-[#777777]">Contact Us</a></li>

                    </ul>
                </div>

                <div class="flex flex-col gap-2">
                    <h1 class="text-[#262626] text-[20px] md:text-[24px] font-['Montserrat'] font-medium">Get on the List</h1>
                    <p class="text-[#777777] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Sign up to know when we have new products</p>

                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex items-center gap-2 border-[1px]  border-[#B8BBD7] rounded-[4px] p-1">

                            <input type="text" placeholder="Enter your email address" class="lg:w-[12rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                        </div>
                        <button type="submit" class="py-1 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]">Subscribe</button>
                    </div>
                </div>

            </div>
            <div class="w-[90%] flex flex-col py-4 mx-auto">
                <div class="flex flex-col gap-2">
                    <h1 class="text-[#262626] text-[20px] md:text-[24px] font-['Montserrat'] font-medium">Connect with us on:</h1>
                    <div class="flex items-center gap-7">
                        <a href="#"><img src="../assets/global/e1.svg" alt="Search" class="w-[13.83px]" /></a>
                        <a href="#"><img src="../assets/global/e2.svg" alt="Search" class="w-[21.83px]" /></a>
                        <a href="#"><img src="../assets/global/e3.svg" alt="Search" class="w-[21.83px]" /></a>
                        <a href="#"><img src="../assets/global/e4.svg" alt="Search" class="w-[21.83px]" /></a>
                        <a href="#"><img src="../assets/global/e5.svg" alt="Search" class="w-[17.83px]" /></a>
                        <a href="#"><img src="../assets/global/e6.svg" alt="Search" class="w-[30.22px]" /></a>
                    </div>
                </div>
                <div class="flex md:items-center flex-col gap-3 md:gap-0 md:flex-row justify-between mt-10">
                    <div class="flex items-center gap-[4rem]">
                        <p class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#777777]">Terms & Conditions</p>
                        <p class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#777777]">Privacy Policy</p>
                    </div>
                    <p class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#777777]">© 2025 Victosah Solutions | All Rights Reserved</p>

                </div>
            </div>
        </footer>
    </main>

</body>

</html