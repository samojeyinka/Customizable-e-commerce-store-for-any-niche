<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Transactions</title>



</head>


<body class="relative">

    <!-- ========================  The header  starts ======================== -->
    <header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
        <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

            <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
                <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                    <img src="../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                    <h1 class="hidden md:block text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </a>

                <img onclick="toggleNav()" src="../assets/home/menu.svg" alt="Search" class="w-[28px] cursor-pointer" />
                <h1 class="hidden md:block  text-[16px] md:text-[20px] font-Onest font-semibold">Transactions</h1>
            </div>


            <div class="flex items-center gap-0">
                <div class="flex items-center gap-2 border-[1px] border-[#F3F3F3] rounded-[25px] p-2">
                    <img src="../assets/global/search-normal.svg" alt="Search" class="w-[18px]" />
                    <input type="text" placeholder="Search name, Order ID..." class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                </div>

            </div>
            <div class="flex items-center gap-6 md:bg-[#F3F3F3] rounded-[4px] py-1 px-4">

                <span class="cursor-pointer relative" onclick="openNotification()">
                    <img src="../assets/global/bell.svg" class="w-[18px] md:w-[20px]" alt="bag" />
                    <div class="w-[8px] h-[8px] bg-[#1A237E] rounded-full absolute top-[-.1rem] left-3"></div>
                </span>


                <a href="./settings/profile.php" class="flex items-center gap-2 cursor-pointer">
                    <div class="w-[40px] h-[40px] md:w-[44px] md:h-[44px] rounded-[50%]">
                        <img src="../assets/home/user.svg" alt="Profile Picture" class="w-full h-full" />
                    </div>

                    <div class="hidden md:block  flex flex-col gap-0">
                        <p class="text-[15px] md:text-[16px] font-Onest font-medium text-[#262626]">John Paul</p>
                        <p class="text-[14px] md:text-[16px] font-Onest font-regular text-[#5B5B5B]">johnpaul111@gmail.com</p>

                    </div>
                </a>


            </div>
        </nav>


        ​
        <!-- The dropdowns -->


        <div id="notification" class="p-3 notification-content shadow-md bg-white rounded-[4px]">

            <div class="flex flex-col gap-2">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-1">
                        <h1 class="text-[18px] md:text-[20px] font-['Open Sans'] font-medium">Notificatons</h1>
                        <div class="flex items-center justify-center bg-[#1A237E] w-[20px] h-[20px] rounded-[50%]">
                            <h1 class="text-white text-[11px] md:text-[12px] font-['Open Sans'] font-medium">9</h1>
                        </div>
                    </div>

                    <a href="./notifications.php" class="text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-[#1A237E]">See all</a>


                </div>

                <div class="flex flex-col gap-2 h-[78vh] md:h-[75vh] overflow-y-auto">
                    <div class="flex flex-col gap-2">


                        <div class="w-full flex flex-col gap-2 rounded-[4px] bg-[#EEEEEE] p-2">
                            <div class="flex items-center justify-between">
                                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">New Order Places today</h1>
                                <div class="relative flex items-center gap-2">
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">Jan 25. 2025 09:38am</span>
                                    <img src="../assets/user/action.svg" class="cursor-pointer" onclick="openNotimenu(this)" />
                                    <!-- The small menu  starts -->
                                    <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                        </div>
                                    </div>
                                    <!-- The small menu  ends -->

                                </div>
                            </div>
                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><span class="underline">Order #4567</span> has been placed by Juliet August.</span>
                        </div>

                        <div class="w-full flex flex-col gap-2 rounded-[4px] bg-[#EEEEEE] p-2">
                            <div class="flex items-center justify-between">
                                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">New Order Places today</h1>
                                <div class="relative flex items-center gap-2">
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">Jan 25. 2025 09:38am</span>
                                    <img src="../assets/user/action.svg" class="cursor-pointer" onclick="openNotimenu(this)" />
                                    <!-- The small menu  starts -->
                                    <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                        </div>
                                    </div>
                                    <!-- The small menu  ends -->

                                </div>
                            </div>
                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><span class="underline">Order #4567</span> has been placed by Juliet August.</span>
                        </div>

                        <div class="w-full flex flex-col gap-2 rounded-[4px] bg-white p-2">
                            <div class="flex items-center justify-between">
                                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">New Order Places today</h1>
                                <div class="relative flex items-center gap-2">
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">Jan 25. 2025 09:38am</span>
                                    <img src="../assets/user/action.svg" class="cursor-pointer" onclick="openNotimenu(this)" />
                                    <!-- The small menu  starts -->
                                    <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                        </div>
                                    </div>
                                    <!-- The small menu  ends -->

                                </div>
                            </div>
                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><span class="underline">Order #4567</span> has been placed by Juliet August.</span>
                        </div>

                        <div class="w-full flex flex-col gap-2 rounded-[4px] bg-[#EEEEEE] p-2">
                            <div class="flex items-center justify-between">
                                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">New Order Places today</h1>
                                <div class="relative flex items-center gap-2">
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">Jan 25. 2025 09:38am</span>
                                    <img src="../assets/user/action.svg" class="cursor-pointer" onclick="openNotimenu(this)" />
                                    <!-- The small menu  starts -->
                                    <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                        </div>
                                    </div>
                                    <!-- The small menu  ends -->

                                </div>
                            </div>
                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><span class="underline">Order #4567</span> has been placed by Juliet August.</span>
                        </div>

                        <div class="w-full flex flex-col gap-2 rounded-[4px] bg-[#EEEEEE] p-2">
                            <div class="flex items-center justify-between">
                                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">New Order Places today</h1>
                                <div class="relative flex items-center gap-2">
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">Jan 25. 2025 09:38am</span>
                                    <img src="../assets/user/action.svg" class="cursor-pointer" onclick="openNotimenu(this)" />
                                    <!-- The small menu  starts -->
                                    <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                        </div>
                                    </div>
                                    <!-- The small menu  ends -->

                                </div>
                            </div>
                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><span class="underline">Order #4567</span> has been placed by Juliet August.</span>
                        </div>

                        <div class="w-full flex flex-col gap-2 rounded-[4px] bg-[#EEEEEE] p-2">
                            <div class="flex items-center justify-between">
                                <h1 class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">New Order Places today</h1>
                                <div class="relative flex items-center gap-2">
                                    <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]">Jan 25. 2025 09:38am</span>
                                    <img src="../assets/user/action.svg" class="cursor-pointer" onclick="openNotimenu(this)" />
                                    <!-- The small menu  starts -->
                                    <div class="not-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                            <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Mark as unread</a>
                                        </div>
                                    </div>
                                    <!-- The small menu  ends -->

                                </div>
                            </div>
                            <span class="text-[14px] md:text-[15px] font-['Open Sans'] font-regular text-[#9A9A9A]"><span class="underline">Order #4567</span> has been placed by Juliet August.</span>
                        </div>








                    </div>
                </div>
            </div>
        </div>



    </header>
    <!-- ========================  The header  ends ======================== -->

    <div id="mySidenav" class="sidenav p-2 hidden md:flex flex-col justify-between gap-2">

        <div class="flex flex-col gap-2">
            <a href="./overview.php" class="nav-link active flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/category.svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/category2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Overview</span></a>
            <a href="./products.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/book (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/book.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Products</span></a>
            <a href="./orders.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/bag-happy (2).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/bag-happy (1).svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Orders</span></a>
            <a href="./users.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/profile (2).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/profile (1).svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Users</span></a>
            <a href="./transactions.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/receipt-minus (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/receipt-minus.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Transactions</span></a>
        </div>

        <div class="flex flex-col gap-2 mb-7">
            <a href="./settings/options.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/setting-2 (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/setting-2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Settings</span></a>
            <span class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/logout.svg" class="nonactiveicon w-[20px] h-[20px]" /><span class="text-[#D93939]">Logout</span></span>
        </div>
    </div>


    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Transactions</h1>

            <div id="myBtn" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-Onest font-regular">Transactions Overview</h1>
                <img src="../assets/dash/Vector 6905.svg" />
            </div>
        </div>



        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <div id="myBtn" class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                <div class="flex items-center gap-0">
                    <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                        <img src="../assets/dash/search-normal (1).svg" alt="Search" class="w-[18px]" />
                        <input type="text" placeholder="Search" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                    </div>

                </div>

                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Filer by:</span>
                        <img src="../assets/dash/filter-horizontal.svg" class="md:hidden" />

                        <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">

                            <div class="custom-dropdown">
                                <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Date</span>
                                    <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                                </div>
                                <div class="dropdown-content">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                            <div onclick="selectOption(this)">Today</div>
                                            <div onclick="selectOption(this)">Last 7 days</div>
                                            <div onclick="selectOption(this)">Last 28 days</div>
                                            <div onclick="selectOption(this)">Custom date</div>


                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="custom-dropdown">
                                <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Status</span>
                                    <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                                </div>
                                <div class="dropdown-content">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                            <div onclick="selectOption(this)">In Stock</div>
                                            <div onclick="selectOption(this)">Out of Stock</div>
                                            <div onclick="selectOption(this)">Low Stock</div>



                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/Path.svg" />
                        <span class="text-[#262626] text-[14px] font-Onest font-regular">Clear filter</span>
                    </div>

                    <button class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                        <img src="../assets/dash/send-square.svg" />
                        Export as
                    </button>

                </div>
            </div>

            <div class=" overflow-x-auto mt-3 min-h-[20rem]">
                <table cols="" class="w-full shrink-0">

                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th class="text-nowrap p-2 flex items-center gap-2">
                            <input type="checkbox" />
                            <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Customer Name</span>
                        </th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Email</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Transaction ID</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>

                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                            <img src="../assets/dash/column.svg" class="min-w-[24px] min-h-[24px]" />
                        </th>
                    </thead>

                    <tbody>

                        <tr>
                            <td class="flex items-center gap-[10px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">4567KJ3456787</td>
                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Successful</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D51E5E]">Refund</a>
                                    </div>
                                </div>
                            </td>


                        </tr>

                        <tr>
                            <td class="flex items-center gap-[10px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">4567KJ3456787</td>
                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Successful</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D51E5E]">Refund</a>
                                    </div>
                                </div>
                            </td>


                        </tr>

                        <tr>
                            <td class="flex items-center gap-[10px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">4567KJ3456787</td>
                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#D93939] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Failded</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D51E5E]">Refund</a>
                                    </div>
                                </div>
                            </td>


                        </tr>

                        <tr>
                            <td class="flex items-center gap-[10px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">4567KJ3456787</td>
                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#D51E5E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Refunded</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D51E5E]">Refund</a>
                                    </div>
                                </div>
                            </td>


                        </tr>


                        <tr>
                            <td class="flex items-center gap-[10px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">4567KJ3456787</td>
                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Pending</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D51E5E]">Refund</a>
                                    </div>
                                </div>
                            </td>


                        </tr>


                    </tbody>
                </table>
            </div>

        </div>

        <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">Showing 10 results from 10,000</span>
            <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                <div class="flex items-center gap-2 cursor-pointer">
                    <img src="../assets/products/prev.svg" class="w-[6px] h-[11px]" />
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Prev</span>
                </div>

                <div class="w-full flex items-center justify-between md:gap-6">

                    <span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer bg-[#1A237E]">1</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">2</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">3</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">4</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">5</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">...</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular cursor-pointer">10</span>


                </div>

                <div class="flex items-center gap-2 cursor-pointer">
                    <img src="../assets/products/next.svg" class="w-[6px] h-[11px]" />
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Next</span>
                </div>

            </div>
        </div>

    </div>
    </div>



    <!-- The modals starts -->
    <div id="myModal" class="modal reg thisone">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Transaction Overview</h1>
            <img src="../assets/global/close-circle.svg" alt="close" id="closeauth" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />


            <div class="flex items-center gap-3 my-4">
                <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Sort by:</span>

                <div class="flex items-center gap-2 md:gap-3 lg:gap-4">
                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">2025</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="selectOption(this)">2025</div>
                                    <div onclick="selectOption(this)">2024</div>
                                    <div onclick="selectOption(this)">2023</div>
                                    <div onclick="selectOption(this)">2022</div>


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Last 28 days</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="selectOption(this)">Today</div>
                                    <div onclick="selectOption(this)">Last 7 days</div>
                                    <div onclick="selectOption(this)">Last 28 days</div>
                                    <div onclick="selectOption(this)">Custom date</div>


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">Amount</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626 cursor-pointer">
                                    <div onclick="selectOption(this)">Amount</div>
                                    <div onclick="selectOption(this)">Numbers</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/Frame 1171276632 (3).svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Revenue</span>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">₦900,780</h2>
                        <!-- <p class="text-[#1A237E] text-[15px] text-[17px] font-regular font-['Open Sans']">items need restocking</p> -->
                    </div>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                    </div>

                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2  p-2 gap-4 mt-2">
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/Frame 1171276632 (3).svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Refunded</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">₦8,000</h2>
                            <!-- <p class="text-[#1A237E] text-[15px] text-[17px] font-regular font-['Open Sans']">Listed items</p> -->
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/decrease.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#D93939]">+12%</span> from last 28 days</p>
                        </div>

                    </div>
                </div>

                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Failed Transactions </span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">₦90,000</h2>
                            <!-- <p class="text-[#1A237E] text-[15px] text-[17px] font-regular font-['Open Sans']">items need restocking</p> -->
                        </div>

                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                        </div>

                    </div>
                </div>

                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Completed Transactions</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">₦197,000</h2>
                            <!-- <p class="text-[#1A237E] text-[15px] text-[17px] font-regular font-['Open Sans']">available for purchase</p> -->
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/decrease.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#D93939]">+12%</span> from last 28 days</p>
                        </div>

                    </div>
                </div>

                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/Frame 1171276632 (2).svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Pending Transactionss</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">₦700,000</h2>
                            <!-- <p class="text-[#1A237E] text-[14px] text-[15px] font-regular font-['Open Sans']">products have less than 5 items left</p> -->
                        </div>

                        <div class="flex items-center gap-1">
                            <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                        </div>

                    </div>
                </div>


            </div>

            ​
        </div>

    </div>

    <!-- The modals ends -->






    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>



</body>

</html>