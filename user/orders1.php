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
    <title>VICTOSAH | My Orders</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/tabs.css">
    <link rel="stylesheet" href="../styles/styles.css">

    <style>
        .ordermenu-content {
            display: none;
            position: absolute;
            top: 70%;
            /* Positions it directly below the opener */
            right:-50%;
            /* Aligns it with the left edge of the opener */
            min-width: 160px;
            min-height: 10rem;
            z-index: 5;
            background-color: white !important;
            border: 1px solid #E1E1E1;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 8px;
            border-radius: 4px;
        }

        .showom {
            display: block;
        }

        @media screen and (max-width:768px){

            .ordermenu-content {

            top: 100%;
            right:0%;
        }

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
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Orders</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] gap-3 mx-auto bg-[#FFFFFF] py-5 flex items-center flex-col-reverse md:flex-row justify-between">

            <div class="w-full flex items-center gap-4">
                <button type="submit" class="py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">All</button>
                <button type="submit" class="py-1 px-4 bg-[#F3F3F3] text-[#262626] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Ongoing</button>
                <button type="submit" class="py-1 px-4 bg-[#F3F3F3] text-[#262626] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Delivered</button>

            </div>

            <div class="w-full md:w-[80%] lg:w-[70%] flex items-center justify-between">
                <div class="w-full flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">

                    <input type="text" placeholder="Search order ID" class="w-full lg:w-[18rem] text-[14px] border-[#B8BBD7] outline-none placeholder:text-[#B8BBD7]" />
                </div>
                <button type="submit" class="py-2 px-4 bg-[#E8E9F2] text-black text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px] border-[1px] border-[]">Search</button>
            </div>

        </div>

        <div class="w-full bg-[#FFFFFF] py-5">
            <div class="w-[90%] mx-auto hidden md:block">


                <table cols="" class="w-full">
                    <thead class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th>Product</th>
                        <th>Amount/Quantity</th>
                        <th>Order ID</th>
                        <th>Order Type</th>
                        <th>Status</th>
                        <th>Date</th>
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
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000/1</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">PickUp</td>



                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Ongoing</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Jan 12, 2025</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                        <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                    </div>
                                </div>
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
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000/1</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">PickUp</td>



                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Jan 12, 2025</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                        <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                    </div>
                                </div>
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
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000/1</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">PickUp</td>



                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Jan 12, 2025</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                        <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                    </div>
                                </div>
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
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000/1</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">PickUp</td>



                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Jan 12, 2025</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                        <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                    </div>
                                </div>
                            </td>


                        </tr>

                    </tbody>
                </table>




            </div>



            <div class="w-[90%] mx-auto  md:hidden">
                <div class="w-full flex flex-col gap-4">

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">

                        <div class="flex items-center justify-between relative">
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">January 12, 2025</p>
                            <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)"/>


                            <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                        <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                    </div>
                        </div>
                        </div>

                        <div class="w-full h-[1px] bg-[#E1E1E1]"></div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#E8B006] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Ongoing</button>
                            <div class="flex flex-col gap-[2px] text-right">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Pickup</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Order ID:</b> #12345</p>

                            </div>
                        </div>

                        <div class="w-full h-[1px] bg-[#E1E1E1]"></div>

                        <div class="flex justify-between">
                            <div class="flex flex-col gap-2">

                                <div class="flex gap-2">

                                    <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                        <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="flex flex-col gap-[2px]">
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> Bounce Pillow</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> Blue</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> King size (6 a 4 in)</p>
                                    </div>

                                </div>

                                <p class='text-[14px] font-["Open Sans] text-[#1A237E] font-regular underline cursor-pointer'>Track your order</p>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>



                    </div>

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">

                        <div class="flex items-center justify-between relative">
                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">January 12, 2025</p>
                            <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)"/>
                        
                            <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                                        <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
                                    </div>
                        </div> 
                        </div>

                        <div class="w-full h-[1px] bg-[#E1E1E1]"></div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#39D959] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
                            <div class="flex flex-col gap-[2px] text-right">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Express Delivery</p>
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Order ID:</b> #12345</p>

                            </div>
                        </div>

                        <div class="w-full h-[1px] bg-[#E1E1E1]"></div>

                        <div class="flex justify-between">
                            <div class="flex flex-col gap-2">

                                <div class="flex gap-2">

                                    <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                        <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="flex flex-col gap-[2px]">
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> Bounce Pillow</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> Blue</p>
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> King size (6 a 4 in)</p>
                                    </div>

                                </div>

                                <p class='text-[14px] font-["Open Sans] text-[#1A237E] font-regular underline cursor-pointer'>Track your order</p>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>



                    </div>

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">

<div class="flex items-center justify-between relative">
    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">January 12, 2025</p>
    <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)"/>

    <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
            <div class="flex flex-col gap-3">
                <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
            </div>
</div> 
</div>

<div class="w-full h-[1px] bg-[#E1E1E1]"></div>

<div class="flex items-center justify-between">
    <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#39D959] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
    <div class="flex flex-col gap-[2px] text-right">
        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Express Delivery</p>
        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Order ID:</b> #12345</p>

    </div>
</div>

<div class="w-full h-[1px] bg-[#E1E1E1]"></div>

<div class="flex justify-between">
    <div class="flex flex-col gap-2">

        <div class="flex gap-2">

            <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
            </div>
            <div class="flex flex-col gap-[2px]">
                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> Bounce Pillow</p>
                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> Blue</p>
                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> King size (6 a 4 in)</p>
            </div>

        </div>

        <p class='text-[14px] font-["Open Sans] text-[#1A237E] font-regular underline cursor-pointer'>Track your order</p>
    </div>

    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
</div>



</div>


<div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">

<div class="flex items-center justify-between relative">
    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">January 12, 2025</p>
    <img src="../assets/user/action.svg" class="w-[24px] ml-auto cursor-pointer" onclick="openOrdermenu(this)"/>

    <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
            <div class="flex flex-col gap-3">
                <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Re-Order</a>
                <a href="./orders.php" class="text-[16px] font-medium text-[#262626]">Track Order</a>
                <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">Leave a review</a>
                <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Report an issue</a>
            </div>
</div> 
</div>

<div class="w-full h-[1px] bg-[#E1E1E1]"></div>

<div class="flex items-center justify-between">
    <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#39D959] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
    <div class="flex flex-col gap-[2px] text-right">
        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">Express Delivery</p>
        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Order ID:</b> #12345</p>

    </div>
</div>

<div class="w-full h-[1px] bg-[#E1E1E1]"></div>

<div class="flex justify-between">
    <div class="flex flex-col gap-2">

        <div class="flex gap-2">

            <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                <img src="../assets/products/img1.svg" class="w-full h-full object-cover" />
            </div>
            <div class="flex flex-col gap-[2px]">
                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Name:</b> Bounce Pillow</p>
                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Color:</b> Blue</p>
                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Size:</b> King size (6 a 4 in)</p>
            </div>

        </div>

        <p class='text-[14px] font-["Open Sans] text-[#1A237E] font-regular underline cursor-pointer'>Track your order</p>
    </div>

    <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
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
    <script type="text/javascript" src="../functions/order.js"></script>

    <script>
      
    </script>

</body>

</html