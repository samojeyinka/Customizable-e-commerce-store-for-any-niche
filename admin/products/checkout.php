<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Checkout</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styles/checkout.css">
</head>

<body>
    <main class="bg-[#FEFEFE] relative">
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
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Cart</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Check Out</span>
                </div>
            </div>
        </section>

        <div class="w-[95%] md:w-[90%] mx-auto flex flex-col-reverse md:flex-row  gap-3 py-5">




            <div class="w-full md:w-[55%] flex flex-col gap-5 py-5">
                <div class="w-[95%] md:w-[90%] lg:w-[80%] border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">

                    <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                        How do you want to receive your product?
                    </p>

                    <div class="flex flex-col gap-2 pt-2">
                        <label for="pick" class="flex items-center gap-1 cursor-pointer">
                            <input type="radio" id="pick" name="delivery" class="" />
                            <p class="text-[15px] md:text-[16px]  text-[#262626] w-full font-['Open Sans'] font-regular">
                                Pick-Up
                            </p>
                        </label>
                        <label for="station" class="flex items-center gap-1 cursor-pointer">
                            <input type="radio" id="station" name="delivery" class="" />
                            <p class="text-[15px] md:text-[16px]  text-[#262626] w-full font-['Open Sans'] font-regular">
                                Express Delivery
                            </p>
                        </label>
                    </div>

                </div>

                <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">

                    <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                        Pickup Location
                    </p>

                    <div class="flex flex-col gap-2 pt-2">
                        <label for="loc" class="flex items-start  gap-2 cursor-pointer">
                            <input type="radio" id="loc" name="location" class="mt-2" />
                            <div class="flex flex-col gap-1">
                                <p class="text-[15px] md:text-[16px]  text-[#262626] w-full font-['Open Sans'] font-medium">
                                    Lagos Store
                                </p>

                                <p class="text-[13px] md:text-[14px]  text-[#262626] w-full font-['Open Sans'] font-regular">
                                    Location of the company<br />Pickup is available from 8am-6pm
                                </p>
                            </div>
                        </label>

                    </div>

                </div>



                <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
                    <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
                        <div class="flex flex-col items-center">


                            <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                                Contact Info
                            </p>

                            <p class="text-[14px] md:text-[16px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-1 md:pr-0">
                                We'll use this email to send you details and updates about your order
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Email Address
                                </label>
                                <input
                                    type="text"
                                    placeholder="Enter your email address"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>


                            <div class="w-full flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Add a note to your order
                                </label>
                                <textarea placeholder="Add note(Optional)" class="w-full min-h-[88px] max-h-[88px] font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" name="" id=""></textarea>

                            </div>


                        </div>


                    </div>
                </div>

                <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">


                    <div class="flex flex-col gap-4">



                        <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
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

                        <div class="w-full border-[1px] border-[#E1E1E1] rounded-[8px] p-4 flex flex-col gap-2 ">
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

                            </div>


                        </div>

                    </div>



                </div>

                <section class="flex flex-col items-center w-full bg-[#FFF9E6] border-[1px] border-[#FFECB2] py-3 px-4 rounded">
                    <div class="flex items-center gap-2 mr-auto">
                        <img src="../assets/global/infoinfo.svg" alt="Profile Picture" class="w-[24px]" />
                        <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                            Delivery Status
                        </p>
                    </div>
                    <p class="text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[3rem] pr-3 ">
                        We're sorry, but we currently do not deliver to your location. Please check back later or contact us for assistance
                    </p>
                </section>

                <section class="flex flex-col items-center w-full bg-[#ECFDEF] border-[1px] border-[#C3FACE] py-3 px-4 rounded">
                    <div class="flex items-center gap-2 mr-auto">
                        <img src="../assets/global/infosuccess.svg" alt="Profile Picture" class="w-[24px]" />
                        <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                            Delivery Status
                        </p>
                    </div>
                    <div class="ml-4 w-full flex items-center justify-between">
                        <p class="w-[90%] text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 pr-3 ">
                            We deliver to your location. This is the delivery fee
                        </p>

                        <p class="text-[15px] md:text-[16px] text-[#262626] font-['Open Sans'] font-medium">
                            ₦20,000
                        </p>

                    </div>
                </section>

                <label for="billing" class="font-['Open Sans'] text-[13px] md:text-[15px] font-regular text-[#5B5B5B] cursor-pointer">By proceeding with your purchase you agree to our Terms and Conditions and Privacy Policy</label>
                <button id="openpst" type="submit" class="w-full hidden md:max-w-[377px] md:flex items-center justify-center gap-2 mt-2 min-w-[377px] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                    Continue to Pay

                </button>

            </div>

            <div class="w-full md:w-[45%] flex flex-col gap-3">
                <div class="w-full flex flex-col gap-2 bg-[#E8E9F2] p-2 z-2  fixed top-[120px] right-0 md:hidden">
            <div class="w-[95%] mx-auto flex  flex-col gap-2 rounded-[4px] border-[1px] border-[#E1E1E1] p-2">

<div class="flex items-center justify-between">
    <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Subtotal</p>
    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦1,500,000</p>
</div>

<div class="flex items-center justify-between">
    <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Shipping fee</p>
    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦600</p>
</div>

<div class="flex items-center justify-between">
    <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Total</p>
    <p class="text-[#484F98] text-[18px] md:text-[22px] font-['Open Sans'] font-bold">₦1,520,000</p>
</div>

            </div>

            <button id="openpst" type="submit" class="w-[95%] mx-auto md:max-w-[377px] flex items-center justify-center gap-2 mt-2 min-w-[377px] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                    Continue to Pay

                </button>
            </div>

                <div class="w-full flex flex-col gap-3 rounded-[4px] bg-[#E8E9F2] md:bg-[#EEEEEE] mt-[9rem] md:mt-0  p-2">
                    
                <div class="flex items-center justify-between">
                    <p class="text-[#262626] text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Your Order</p>
                    <img src="../assets/products/down2.svg" class="rotate-[180deg] cursor-pointer md:hidden"/>
                </div>
                    <div class="flex items-center justify-between">
                        <div class="py-3 flex gap-2">

                            <div class="w-[80.64px] h-[48.73px] rounded-[4px] overflow-hidden">
                                <img src="../assets/products/img1.svg" class="w-full h-full" />
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: Bounce Pillow</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: Blue</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: King size (6 a 4 in)</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Quantity: 1</p>
                            </div>

                        </div>
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">₦300,000</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="py-3 flex gap-2">

                            <div class="w-[80.64px] h-[48.73px] rounded-[4px] overflow-hidden">
                                <img src="../assets/products/img1.svg" class="w-full h-full" />
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: Bounce Pillow</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: Blue</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: King size (6 a 4 in)</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Quantity: 1</p>
                            </div>

                        </div>
                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">₦300,000</p>
                    </div>
                </div>

                <div class="w-full hidden md:flex flex-col gap-2 rounded-[4px] border-[1px] border-[#E1E1E1] p-2">

                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Subtotal</p>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦1,500,000</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Shipping fee</p>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-medium">₦600</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-[#5B5B5B] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Total</p>
                        <p class="text-[#484F98] text-[18px] md:text-[22px] font-['Open Sans'] font-bold">₦1,520,000</p>
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


    <!-- The Modal -->
    <div id="paystack" class="payment">
        <!-- Modal content -->
        <div class="payment-content ps overflow-hidden py-[7rem] flex flex-col gap-4 items-center">
        <h1 class="text-[20px] text-[24px] text-[#262626] text-center font-Onest font-semibold">Payment gateway</h1>
        <button id="openps" class="bg-blue-800 p-1 text-white text-[18px] cursor-pointer  w-[150px] rounded">Pay</button>
        </div>

            ​
        </div>

        <div id="paysuccess" class="payment ps">
     
        <div class="payment-content pss overflow-hidden py-[4rem] flex flex-col gap-4 items-center">
       
          <div class="modal-content overflow-hidden flex flex-col items-center p-4">

          <img src="../assets/global/success.svg" class="mx-auto w-[120px] md:w-[150px]"/>
         
              <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
              Order Confirmed
              </p>
              <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
              Your order has been placed successfully. A confirmation email has been sent to you. Thank you for shopping with us
              </p>

    
                 
                 
                <a href="/victosah/products/view-order.php" class="w-[80%] text-center text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer"
                id="closepssucces"
                >
                View Order
                </a>
              </form>

        
            </div>
        
           
        </div>

            ​
        </div>

   



    <script>
        var paystack = document.getElementById("paystack");
        var paysuccess = document.getElementById("paysuccess");
        var openps = document.getElementById("openps");
        var openpsts = document.getElementById("openpst");
        var closepssucces = document.getElementById("closepssucces");
        

        openpsts.onclick = function() {
            paystack.style.display = "block";
        }


        openps.onclick = function() {
            paystack.style.display = "none";
            paysuccess.style.display = "block";
        }

        closepssucces.onclick = function() {
            paysuccess.style.display = "none";
        }
  
    </script>

</body>

</html