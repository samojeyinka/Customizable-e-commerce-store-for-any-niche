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
    <title>Users</title>
    <style>
        .tab-active {
            background-color: #1A237E;
            color: white;
        }

        .tab-inactive {
            background-color: #F3F3F3;
            color: #262626;
        }

        .section-active {
            display: block;
        }

        .section-inactive {
            display: none;
        }
    </style>

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
                <h1 class="hidden md:block  text-[16px] md:text-[20px] font-Onest font-semibold">Users</h1>
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
            <a href="./page3.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/receipt-minus (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/receipt-minus.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Transactions</span></a>
        </div>

        <div class="flex flex-col gap-2 mb-7">
            <a href="./settings/options.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/setting-2 (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/setting-2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Settings</span></a>
            <span class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/logout.svg" class="nonactiveicon w-[20px] h-[20px]" /><span class="text-[#D93939]">Logout</span></span>
        </div>
    </div>


    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Users</h1>

            <div id="myBtn" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-Onest font-regular">Users Overview</h1>
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
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Phone Number</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Total orders</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Last Order</th>


                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                            <img src="../assets/dash/column.svg" class="min-w-[24px] min-h-[24px]" />
                        </th>
                    </thead>

                    <tbody>

                        <tr>
                            <td class="flex items-center gap-[0px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">09090909090</td>

                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">5</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <span id="opencustomeroverview" class="text-[16px] font-medium text-[#262626] cursor-pointer">View Details</span>
                                        <span id="disableAcc" class="text-[16px] font-medium text-[#E8B006] cursor-pointer">Disable account</span>
                                        <span id="cancelOrder" class="text-[16px] font-medium text-[#D93939] cursor-pointer">Delete account</span>
                                    </div>
                                </div>
                            </td>


                        </tr>

                        <tr>
                            <td class="flex items-center gap-[0px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">09090909090</td>

                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">5</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Disable account</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D93939]">Delete account</a>
                                    </div>
                                </div>
                            </td>


                        </tr>


                        <tr>
                            <td class="flex items-center gap-[0px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">09090909090</td>

                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">5</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Disable account</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D93939]">Delete account</a>
                                    </div>
                                </div>
                            </td>


                        </tr>

                        <tr>
                            <td class="flex items-center gap-[0px] p-3">
                                <input type="checkbox" class="border-[#E1E1E1]" />
                                <span class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Enyesiobi Golibe</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">golibe.f@gmail.com</td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">09090909090</td>

                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">5</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

                                <!-- Order Menu (specific to this row) -->
                                <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                    <div class="flex flex-col gap-3">
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#262626]">View Details</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#E8B006]">Disable account</a>
                                        <a href="../products/show.php" class="text-[16px] font-medium text-[#D93939]">Delete account</a>
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
    <div id="myModal" class="modal reg">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Users Overview</h1>
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
                </div>

            </div>
            <div class="grid grid-cols-1 md:grid-cols-2  px-2 py-2 md:pb-10 gap-4 mt-2">
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Users</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">53,000</h2>
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
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">New Signups</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">52,370</h2>
                            <!-- <p class="text-[#1A237E] text-[15px] text-[17px] font-regular font-['Open Sans']">items need restocking</p> -->
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


    <!-- The delete account modal -->
    <div id="ticket" class="modal ticket">
        <div class="modal-content overflow-hidden px-5 py-10 flex flex-col">
            <img src="../../assets/global/close-circle.svg" alt="close" id="closeticket" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4" />

            <p class="text-[#EE3F3F] font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                Delete Account
            </p>

            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-medium text-[#262626] mt-2">
                This action is irreversible! Are you sure you want to proceed?
            </p>
            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-medium text-[#262626] mt-2">
                What happens when you delete a user account
            </p>


            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">

                • The account loses access permanently.<br />
                • All orders, saved items, and account data are erased.<br />
                • Any pending orders or transactions are automatically canceled.<br />
                • Reviews and ratings left by the user may also be removed.<br />
                • The user will be notified via email about their account status.
            </p>

            <div class="w-[95%] md:w-[67%] item  mx-auto flex items-center gap-2 mt-2">
                <input type="checkbox" />
                <p class="text-[15px] text-left md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">
                    Don’t see this again
                </p>
            </div>



            <button class="w-full md:w-[67%] mx-auto text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#EE3F3F] text-white rounded-[8px] mt-10 cursor-pointer"
                id="">
                Yes, Delete Account
            </button>



        </div>
    </div>


    <!-- The disable account modal -->
    <div id="disable" class="modal ticket">
        <div class="modal-content overflow-hidden px-5 py-10 flex flex-col">
            <img src="../../assets/global/close-circle.svg" alt="close" id="closedisable" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4" />

            <p class="text-[#E8B006] font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                Disable Account
            </p>

            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-medium text-[#262626] mt-2">
                Are you sure you want to disable this account? You can reactivate it anytime.
            </p>
            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-medium text-[#262626] mt-2">
                What happens when you disable a user account?
            </p>


            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">


                • The user cannot log in or access their account.<br />
                • Their orders, reviews, and history remain stored.<br />
                • Admins can reactivate the account at any time.<br />
                • The user will be notified via email about their account status.

            </p>

            <div class="w-[95%] md:w-[67%] item  mx-auto flex items-center gap-2 mt-2">
                <input type="checkbox" />
                <p class="text-[15px] text-left md:text-[16px] font-['Open Sans'] font-medium text-[#262626]">
                    Don’t see this again
                </p>
            </div>



            <button class="w-full md:w-[67%] mx-auto text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#E8B006] text-white rounded-[8px] mt-10 cursor-pointer"
                id="">
                Yes, Disable Account
            </button>



        </div>
    </div>

    <!-- The customer overview modal -->
    <div id="customeroverview" class="modal customeroverview">
        <div class="modal-content overflow-hidden px-5 py-10">
            <img src="../../assets/global/close-circle.svg" alt="close" id="closecustomeroverview" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4" />

            <p class="text-[#2C2C2C] font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                Customer Details
            </p>


            <div class="w-full mt-4">
                <!-- Tabs -->
                <div class="flex gap-3">
                    <button id="tab-customer-details" class="tab-active px-5 py-2 font-['Open Sans'] text-[16px] font-medium rounded-[8px]">Customers Details</button>
                    <button id="tab-order-history" class="tab-inactive  px-5 py-2 font-['Open Sans'] text-[16px] font-medium rounded-[8px]">Order History</button>
                    <button id="tab-tickets" class="tab-inactive  px-5 py-2 font-['Open Sans'] text-[16px] font-medium rounded-[8px]">Review</button>

                </div>

                <!-- Content Sections -->
                <div id="section-customer-details" class="section-active p-4">

                    <div class="accordion-container w-full">
                        <!-- Customer Details Section -->
                        <div class="accordion-item border-b">
                            <div class="accordion-header flex justify-between items-center p-4 cursor-pointer">
                                <h2 class="accordion-title text-lg font-medium">Customer Details</h2>
                                <span class="accordion-icon">
                                    <svg class="w-4 h-4 transform transition-transform duration-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                            <div class="accordion-content p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-gray-600">Status</div>
                                    <div class="text-right text-green-500 font-medium">Active</div>

                                    <div class="text-gray-600">Name</div>
                                    <div class="text-right">Enyesiobi Golibe</div>

                                    <div class="text-gray-600">Email</div>
                                    <div class="text-right">golibe.f@gmail.com</div>

                                    <div class="text-gray-600">Phone Number</div>
                                    <div class="text-right">07089898989</div>

                                    <div class="text-gray-600">Order Notes</div>
                                    <div class="text-right text-sm">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolorst laborum.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address Section -->
                        <div class="accordion-item">
                            <div class="accordion-header flex justify-between items-center p-4 cursor-pointer">
                                <h2 class="accordion-title text-lg font-medium">Shipping Address</h2>
                                <span class="accordion-icon">
                                    <svg class="w-4 h-4 transform transition-transform duration-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                            <div class="accordion-content p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-gray-600">Address</div>
                                    <div class="text-right">70, Shola Martins street, New Oko-Oba, Agege, Lagos</div>

                                    <div class="text-gray-600">Phone Number</div>
                                    <div class="text-right">07089898989</div>

                                    <div class="text-gray-600">State</div>
                                    <div class="text-right">Lagos</div>

                                    <div class="text-gray-600">City</div>
                                    <div class="text-right">Ikeja</div>

                                    <div class="text-gray-600">Zip Code</div>
                                    <div class="text-right">442210</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div id="section-order-history" class="section-inactive p-4">
                    <div class=" overflow-x-auto mt-3 min-h-[20rem]">
                        <table cols="" class="w-full shrink-0">

                            <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                                <th class="text-nowrap p-2 flex items-center gap-2">
                                    <input type="checkbox" />
                                    <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Order ID</span>
                                </th>
                                <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>
                                <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                                <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Mode of Order</th>
                                <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>

                                <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                                    <img src="../assets/dash/column.svg" class="min-w-[24px] min-h-[24px]" />
                                </th>
                            </thead>

                            <tbody>

                                <tr>
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">#12345</span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                                    <td>
                                        <button type="submit" class="py-1 px-4 bg-[#1A7E79] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Confirmed</button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Express Delivery</td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">#12345</span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                                    <td>
                                        <button type="submit" class="py-1 px-4 bg-[#D93939] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Refunded</button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Express Delivery</td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">#12345</span>
                                    </td>

                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                                    <td>
                                        <button type="submit" class="py-1 px-4 bg-[#D51E5E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Dispatched</button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Express Delivery</td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">#12345</span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                                    <td>
                                        <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Processed</button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Express Delivery</td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                                    <td class="flex items-center gap-[10px] p-3">
                                        <input type="checkbox" class="border-[#E1E1E1]" />
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">#12345</span>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>
                                    <td>
                                        <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Delivered</button>
                                    </td>
                                    <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Express Delivery</td>
                                    <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">12/02/2045 09:00am</td>
                                    <td class="relative">
                                        <img src="../assets/user/action.svg" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                </div>

                <div id="section-tickets" class="section-inactive p-4">
                    <div class="w-full rounded-[16px] bg-white mx-auto p-3">
                        <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Reviews (15)</h1>

                        <div class="w-full">


                            <div class="faqext flex flex-col gap-3 mt-3">

                                <div class="flex flex-col gap-2 border-b-[1px] pb-1  border-[#E1E1E1]">
                                    <div class="flex items-center  gap-2">
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                            <img src="../assets/dash/product.svg" class="w-full h-full" />
                                        </div>
                                        <div class="flex flex-col gap-[4px]">
                                            <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Bounce Pillow</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">

                                            <div class="flex items-center gap-1">
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                            </div>

                                            <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>
                                        </div>


                                    </div>
                                    <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                                </div>


                                <div class="flex flex-col gap-2 border-b-[1px] pb-1  border-[#E1E1E1]">
                                    <div class="flex items-center  gap-2">
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                            <img src="../assets/dash/product.svg" class="w-full h-full" />
                                        </div>
                                        <div class="flex flex-col gap-[4px]">
                                            <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Bounce Pillow</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">

                                            <div class="flex items-center gap-1">
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                            </div>

                                            <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>
                                        </div>


                                    </div>
                                    <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                                </div>


                                <div class="flex flex-col gap-2 border-b-[1px] pb-1  border-[#E1E1E1]">
                                    <div class="flex items-center  gap-2">
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                            <img src="../assets/dash/product.svg" class="w-full h-full" />
                                        </div>
                                        <div class="flex flex-col gap-[4px]">
                                            <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Bounce Pillow</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">

                                            <div class="flex items-center gap-1">
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/star.svg" class="w-[16px]" />
                                                <img src="../assets/products/lstar.svg" class="w-[16px]" />
                                            </div>

                                            <span class="text-[#777777] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">11/05/2024</span>
                                        </div>


                                    </div>
                                    <span class="text-[#5B5B5B] text-[13px] md:text-[14px] font-['Montserrat'] font-regular">The softesr pillow ever, I also love how comfortable it is</span>
                                </div>

                            </div>


                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>





    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/tab.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all accordion headers using class selector
            const accordionHeaders = document.querySelectorAll('.accordion-header');

            // Add click event listener to each accordion header
            accordionHeaders.forEach(header => {
                // Get the content element that follows this header by class
                const content = header.parentElement.querySelector('.accordion-content');

                // Get the arrow icon using class
                const arrow = header.querySelector('.accordion-icon svg');

                // Click handler for toggling accordion
                header.addEventListener('click', function() {
                    // Toggle content visibility
                    const isVisible = content.style.display !== 'none' && content.style.display !== '';

                    // Toggle content
                    if (isVisible) {
                        content.style.display = 'none';
                        arrow.classList.remove('rotate-180');
                    } else {
                        content.style.display = 'block';
                        arrow.classList.add('rotate-180');
                    }
                });

                // Initialize all accordions as open (comment out if you want them closed by default)
                content.style.display = 'block';
                arrow.classList.add('rotate-180');
            });
        });





        var ticket = document.getElementById("ticket");
        var cancelOrder = document.getElementById("cancelOrder");
        var closeticket = document.getElementById("closeticket");


        cancelOrder.onclick = function() {
            ticket.style.display = "block";

        }


        closeticket.onclick = function() {
            ticket.style.display = "none";
        }


        var disable = document.getElementById("disable");
        var disableAcc = document.getElementById("disableAcc");
        var closedisable = document.getElementById("closedisable");


        disableAcc.onclick = function() {
            disable.style.display = "block";

        }


        closedisable.onclick = function() {
            disable.style.display = "none";
        }


        var customeroverview = document.getElementById("customeroverview");
        var opencustomeroverview = document.getElementById("opencustomeroverview");
        var closecustomeroverview = document.getElementById("closecustomeroverview");


        opencustomeroverview.onclick = function() {
            customeroverview.style.display = "block";

        }


        closecustomeroverview.onclick = function() {
            customeroverview.style.display = "none";
        }



        // The customer details starts

        // Tab Switching Functionality
        const tabs = {
            'tab-customer-details': 'section-customer-details',
            'tab-order-history': 'section-order-history',
            'tab-tickets': 'section-tickets'
        };

        function activateTab(tabId) {
            // Deactivate all tabs
            Object.keys(tabs).forEach(tab => {
                document.getElementById(tab).classList.remove('tab-active');
                document.getElementById(tab).classList.add('tab-inactive');
                document.getElementById(tabs[tab]).classList.remove('section-active');
                document.getElementById(tabs[tab]).classList.add('section-inactive');
            });

            // Activate selected tab
            document.getElementById(tabId).classList.remove('tab-inactive');
            document.getElementById(tabId).classList.add('tab-active');
            document.getElementById(tabs[tabId]).classList.remove('section-inactive');
            document.getElementById(tabs[tabId]).classList.add('section-active');
        }

        // Add event listeners to tabs
        Object.keys(tabs).forEach(tabId => {
            document.getElementById(tabId).addEventListener('click', () => activateTab(tabId));
        });

        // Close button functionality
        document.getElementById('close-button').addEventListener('click', () => {
            alert('Close button clicked');
            // In a real application, this would likely close the modal or navigate away
        });
    </script>

</body>

</html>