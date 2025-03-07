<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Cart</title>
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
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Cart</span>
                </div>
            </div>
        </section>


        <div class="w-full bg-[#FFFFFF] py-5">
            <div class="w-[90%] mx-auto hidden md:block">


                <table cols="" class="w-full">
                    <thead class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Availability</th>
                        <th>Action</th>
                    </thead>

                    <tbody class="">
                        <tr>
                            <td class="py-3 flex gap-2">

                                <div class="w-[131.64px] h-[88.73px] rounded-[4px] overflow-hidden">
                                    <img src="../assets/products/img1.svg" class="w-full h-full" />
                                </div>
                                <div class="flex flex-col gap-[2px]">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: Bounce Pillow</p>
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: Blue</p>
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: King size (6 a 4 in)</p>
                                </div>

                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</td>
                            <td>
                                <div class="flex items-center gap-5">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">1</p>
                                    <img src="../assets/products/down.svg" class="w-[12px]" />
                                </div>
                            </td>

                            <td>
                                <button type="submit" class="py-2 px-4 bg-[#D51E5E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                            </td>

                            <td>
                                <p class='text-[15px] md:text-[16px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from cart</p>
                            </td>


                        </tr>

                        <tr>
                            <td class="py-3 flex gap-2">

                                <div class="w-[131.64px] h-[88.73px] rounded-[4px] overflow-hidden">
                                    <img src="../assets/products/img1.svg" class="w-full h-full" />
                                </div>
                                <div class="flex flex-col gap-[2px]">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: Bounce Pillow</p>
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: Blue</p>
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: King size (6 a 4 in)</p>
                                </div>

                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</td>
                            <td>
                                <div class="flex items-center gap-5">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">10</p>
                                    <img src="../assets/products/down.svg" class="w-[12px]" />
                                </div>
                            </td>

                            <td>
                                <button type="submit" class="py-2 px-4 bg-[#000000] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out Stock</button>
                            </td>

                            <td>
                                <p class='text-[15px] md:text-[16px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from cart</p>
                            </td>


                        </tr>
                    </tbody>
                </table>

                <a href="/victosah/products/checkout.php" class="flex items-center justify-center gap-2 mt-2 max-w-[377px] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                    <span>Continue to Checkout</span>
                    <img src="../assets/products/to.svg" class="w-[16px] mt-1" />
</a>

            </div>

            <div class="w-[90%] mx-auto  md:hidden">
                <div class="w-full flex flex-col gap-4">
                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex justify-between">
                    <div class="flex flex-col gap-2">
                    <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#D51E5E] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                        <div class="flex gap-2">
                        
                            <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: Bounce Pillow</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: Blue</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: King size (6 a 4 in)</p>
                            </div>

                        </div>

                        <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from cart</p>
                        </div>


                        <div class="flex flex-col gap-2">
                       
                            <div>
                                <div class="flex items-center gap-3">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Gty: 10</p>
                                    <img src="../assets/products/down.svg" class="w-[12px]" />
                                </div>
                        </div>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>
                    
                    </div>

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex justify-between">
                    <div class="flex flex-col gap-2">
                    <button type="submit" class="max-w-[107px] py-[6px] px-3 bg-[#000000] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out of Stock</button>
                        <div class="flex gap-2">
                        
                            <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Name: Bounce Pillow</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Color: Blue</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Size: King size (6 a 4 in)</p>
                            </div>

                        </div>

                        <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from cart</p>
                        </div>


                        <div class="flex flex-col gap-2">
                       
                            <div>
                                <div class="flex items-center gap-3">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Gty: 10</p>
                                    <img src="../assets/products/down.svg" class="w-[12px]" />
                                </div>
                        </div>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>
                    
                    </div>

                    <a href="/victosah/products/checkout.php" class="flex items-center justify-center gap-2 mt-2 w-full py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                    <span>Continue to Checkout</span>
                    <img src="../assets/products/to.svg" class="w-[16px] mt-1" />
                </a>

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