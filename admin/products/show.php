<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Product</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
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

        <section class="w-full bg-[#FFFFFFF] py-4">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Pillows</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Bounce Pillow</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">View details</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto flex flex-col md:flex-row gap-5">

            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-2">
                    <div class="w-full rounded-[4px] overflow-hidden">
                        <img src="../assets/products/img1.svg" class="w-full h-full" />
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-[127.4px] rounded-[4px] overflow-hidden">
                            <img src="../assets/products/img2.svg" class="w-full h-full" />
                        </div>
                        <div class="w-[127.4px] rounded-[4px] overflow-hidden">
                            <img src="../assets/products/img3.svg" class="w-full h-full" />
                        </div>
                        <div class="w-[127.4px] rounded-[4px] overflow-hidden">
                            <img src="../assets/products/img4.svg" class="w-full h-full" />
                        </div>
                        <div class="w-[127.4px] rounded-[4px] overflow-hidden">
                            <img src="../assets/products/img4.svg" class="w-full h-full" />
                        </div>
                        <div class="w-[127.4px] rounded-[4px] overflow-hidden">
                            <img src="../assets/products/img4.svg" class="w-full h-full" />
                        </div>
                    </div>
                </div>
                <div class="w-full border-b-[1.5px]  border-[#E1E1E1]">
                    <div class="w-full flex items-center justify-between py-1">

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">View Reviews (15)</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />


                    </div>

                    <div class="flex flex-col gap-3 my-3">

                        <div class="flex flex-col gap-2 border-b-[1px] pb-1  border-[#E1E1E1]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="../assets/global/avatar.svg" class="w-[24px]" />
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-['Montserrat'] font-medium">Favour</span>
                                    <div class="flex items-center gap-1">
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                    </div>
                                </div>
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>

                            </div>
                            <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                        </div>

                        <div class="flex flex-col gap-2 border-b-[1px] pb-1  border-[#E1E1E1]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="../assets/global/avatar.svg" class="w-[24px]" />
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-['Montserrat'] font-medium">Favour</span>
                                    <div class="flex items-center gap-1">
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                    </div>
                                </div>
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>

                            </div>
                            <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                        </div>

                        <div class="flex flex-col gap-2 border-b-[1px] pb-1  border-[#E1E1E1]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="../assets/global/avatar.svg" class="w-[24px]" />
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-['Montserrat'] font-medium">Favour</span>
                                    <div class="flex items-center gap-1">
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                    </div>
                                </div>
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>

                            </div>
                            <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                        </div>

                        <div class="flex flex-col gap-2  pb-1 ">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="../assets/global/avatar.svg" class="w-[24px]" />
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-['Montserrat'] font-medium">Favour</span>
                                    <div class="flex items-center gap-1">
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/star.svg" class="w-[16px]" />
                                        <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                    </div>
                                </div>
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>

                            </div>
                            <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                        </div>

                    </div>


                </div>

            </div>

            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2"> <span class="text-[#262626] text-[24px] md:text-[28px] font-['Montserrat'] font-medium">Bounce Pillow</span> <button class="w-[fit-content] h-[fit-content] bg-[#D51E5E]  rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-[1.5px] px-2 ">Best Seller</button> </div>
                    <span class="text-[#262626] text-[18px] md:text-[20px] font-['Montserrat'] font-medium">₦300,000</span>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1">
                            <img src="../assets/products/star.svg" class="w-[16px]" />
                            <img src="../assets/products/star.svg" class="w-[16px]" />
                            <img src="../assets/products/star.svg" class="w-[16px]" />
                            <img src="../assets/products/star.svg" class="w-[16px]" />
                            <img src="../assets/products/lstar.svg" class="w-[16px]" />
                        </div>
                        <span class="text-[#9A9A9A] text-[13px] md:text-[14px] font-['Open Sans'] font-medium">(15 reviews)</span>
                    </div>

                </div>

                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[#262626] text-[13px]  md:text-[14px] font-Onest font-medium">Select Color</label>
                            <div class="min-w-[9rem] rounded-[4px] border-[1px] border-[#E1E1E1] cursor-pointer flex items-center justify-between py-1 px-2">
                                <span class="text-[#D9D9D9] text-[13px] md:text-[14px] font-Onest font-regular">Select</span>
                                <img src="../assets/products/down.svg" class="w-[12px]" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-[#262626] text-[13px]  md:text-[14px] font-Onest font-medium">Select Size</label>
                            <div class="min-w-[9rem] rounded-[4px] border-[1px] border-[#E1E1E1] cursor-pointer flex items-center justify-between py-1 px-2">
                                <span class="text-[#D9D9D9] text-[13px] md:text-[14px] font-Onest font-regular">Select</span>
                                <img src="../assets/products/down.svg" class="w-[12px]" />
                            </div>
                        </div>


                    </div>

                    <div class="flex items-end gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[#262626] text-[13px]  md:text-[14px] font-Onest font-medium">Select Quantity</label>
                            <div class="min-w-[11rem] rounded-[4px] border-[1px] border-[#E1E1E1] cursor-pointer flex items-center justify-between py-1 px-2">
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">1</span>
                                <img src="../assets/products/down.svg" class="w-[12px]" />
                            </div>
                        </div>


                        <span class="text-[#262626] text-[16px] md:text-[17px] font-['Montserrat'] font-medium">Total: ₦300,000</span>

                    </div>

                    <div class="flex items-center gap-4">
                        <button class="w-[180px] bg-[#E8E9F2] border-[1px] border-[#969AC4] rounded-[8px] text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2">Add to cart</button>

                        <button class="w-[180px] bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2">Buy now</button>
                        <img src="../assets/products/fav.svg" class="w-[24px] cursor-pointer" />
                    </div>

                </div>

                <div class="w-full flex flex-col gap-2 rounded-[8px] border-[#E1E1E1] border-[1px] p-3">
                    <div class="w-full flex items-center justify-between border-b-[1.2px] py-1 border-[#E1E1E1] cursor-pointer">

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Details</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />


                    </div>

                    <div class="w-full flex items-center justify-between border-b-[1.2px] py-1 border-[#E1E1E1] cursor-pointer">

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Sizes</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />


                    </div>

                    <div class="w-full flex items-center justify-between border-b-[1.2px] py-1 border-[#E1E1E1] cursor-pointer">

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Warranty</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />


                    </div>

                    <div class="w-full flex items-center justify-between  py-1 border-[#E1E1E1] cursor-pointer">

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Care</span>
                        <img src="../assets/products/down.svg" class="w-[12px] h-[6px]" />


                    </div>


                </div>

            </div>

        </div>


        <div class="w-[90%] mx-auto mt-[3rem]"> <span class="text-[#262626] text-[22px]  md:text-[27px] font-Onest font-regular">You May Also Like</span></div>

        <div class="w-[90%] mx-auto flex items-center gap-5 bg-[#FFFFFF] py-3 overflow-scroll mb-5">


            <!-- The products cards -->

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <button class="w-[fit-content] h-[fit-content] bg-[#D51E5E] absolute top-4 left-4 rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-1 px-2 absolute">Best Seller</button>

                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-4">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px]productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <button class="w-[fit-content] h-[fit-content] bg-[#E8B006] absolute top-4 left-4 rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-1 px-2 absolute">New Product</button>

                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
                </div>
            </div>

            <div class="min-w-[270px] productbox rounded-[8px] overflow-hidden flex flex-col">
                <div class="w-full rounded-[8px] overflow-hidden relative cursor-pointer">
                    <img src="../assets/products/pillow.svg" class="w-full" />
                    <button class="addtocart bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">Add to Cart</button>
                    <button class="seeoptions bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer p-2 w-[80%] absolute bottom-0 left-[10%]">See options</button>
                    <div class="actionstab absolute w-[fit-content] right-2 top-6">
                        <div class="flex flex-col gap-4">
                            <img src="../assets/products/h1.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h2.svg" class="w-[35px] cursor-pointer" />
                            <img src="../assets/products/h3.svg" class="w-[35px] cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Love Pillow</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">₦50,000</span>
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

</html>