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
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Document</title>

    <style>
        .modal.logout {
            padding-top: 10%;
        }

        .ordermenu-content {
            display: none;
            position: absolute;
            right: 0;
            z-index: 10;
            min-width: 160px;

        }
    </style>

</head>

<body>

    <!-- ========================  The header  starts ======================== -->
    <header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
        <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

            <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
                <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                    <img src="../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                    <h1 class="hidden md:block text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </a>

                <img onclick="toggleNav()" src="../assets/home/menu.svg" alt="Search" class="w-[28px] cursor-pointer" />
                <h1 class="hidden md:block  text-[16px] md:text-[20px] font-Onest font-semibold">Overview</h1>
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
            <a href="./products4.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"> <img src="../assets/dash/receipt-minus.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Transactions</span></a>
        </div>

        <div class="flex flex-col gap-2 mb-7">
            <a href="./settings/options.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/setting-2 (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/setting-2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Settings</span></a>
            <span id="logmeout" class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"> <img src="../assets/dash/logout.svg" class="nonactiveicon w-[20px] h-[20px]" /><span class="text-[#D93939]">Logout</span></span>
        </div>
    </div>

    <div id="main" class="h-full px-5 pb-10 flex flex-col gap-3">

        <h1 class="md:hidden  text-[18px] font-Onest font-semibold">Overview</h1>
        <!-- The sort by starts -->
        <div class="flex items-center gap-3">
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
        <!-- The sort by ends -->


        <!-- The stats starts -->
        <div class="flex flex-col md:flex-row items-center justify-between p-2 gap-3">
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Users Registered</span>
                    <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">7,000</h2>

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
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Sales</span>
                    <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">7,000</h2>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                    </div>

                </div>
            </div>
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/order.svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Orders</span>
                    <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">7,000</h2>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                    </div>

                </div>
            </div>

        </div>
        <!-- The stats ends -->


        <!-- The charts area starts -->
        <div class="flex items-start flex-col md:flex-row justify-between p-2 gap-3">
            <div class="w-full md:w-[66%] border-[1px] border-[#E7E7E7] rounded-[8px] p-2 flex flex-col gap-3">
                <div class="flex flex-col-reverse gap-2 md:flex-row md:items-center justify-between">

                    <div class="flex md:hidden items-center gap-1">
                        <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                    </div>
                    <div class="flex items-center gap-[3px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Revenue:</span>
                        <h2 class="text-[#1A237E] text-[16px] text-[20px] font-medium font-['Open Sans']">₦70,000</h2>

                        <div class="hidden md:flex items-center gap-1">
                            <img src="../assets/dash/increase.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']"><span class="text-[#39D959]">+12%</span> from last 28 days</p>
                        </div>

                    </div>


                    <div class="flex items-center gap-2 md:gap-2 lg:gap-4">
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

                <!-- The graph section starts -->
                <div class="w-full max-h-[300px] overflow-y-auto">

                    <div class="chart-container hidden">
                        <svg width="100%" height="400" viewBox="0 0 1000 400" preserveAspectRatio="none">
                            <!-- Define gradient -->
                            <defs>
                                <linearGradient id="greenGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#4ade80" stop-opacity="0.3" />
                                    <stop offset="100%" stop-color="#4ade80" stop-opacity="0.1" />
                                </linearGradient>
                            </defs>

                            <!-- Y-axis grid lines -->
                            <g class="grid-lines">
                                <line class="axis-line" x1="50" y1="50" x2="950" y2="50" />
                                <line class="axis-line" x1="50" y1="100" x2="950" y2="100" />
                                <line class="axis-line" x1="50" y1="150" x2="950" y2="150" />
                                <line class="axis-line" x1="50" y1="200" x2="950" y2="200" />
                                <line class="axis-line" x1="50" y1="250" x2="950" y2="250" />
                                <line class="axis-line" x1="50" y1="300" x2="950" y2="300" />
                            </g>

                            <!-- Y-axis labels -->
                            <g class="y-axis-labels">
                                <text class="axis-text" x="20" y="50">₦12k</text>
                                <text class="axis-text" x="20" y="100">₦10k</text>
                                <text class="axis-text" x="20" y="150">₦8k</text>
                                <text class="axis-text" x="20" y="200">₦6k</text>
                                <text class="axis-text" x="20" y="250">₦4k</text>
                                <text class="axis-text" x="20" y="300">₦2k</text>
                                <text class="axis-text" x="20" y="350">₦0</text>
                            </g>

                            <!-- X-axis labels -->
                            <g class="x-axis-labels">
                                <text class="axis-text" x="50" y="370">Jan</text>
                                <text class="axis-text" x="130" y="370">Feb</text>
                                <text class="axis-text" x="210" y="370">Mar</text>
                                <text class="axis-text" x="290" y="370">Apr</text>
                                <text class="axis-text" x="370" y="370">May</text>
                                <text class="axis-text" x="450" y="370">Jun</text>
                                <text class="axis-text" x="530" y="370">Jul</text>
                                <text class="axis-text" x="610" y="370">Aug</text>
                                <text class="axis-text" x="690" y="370">Sep</text>
                                <text class="axis-text" x="770" y="370">Oct</text>
                                <text class="axis-text" x="850" y="370">Nov</text>
                                <text class="axis-text" x="930" y="370">Dec</text>
                            </g>

                            <!-- Area chart -->
                            <path class="area-path" d="M50,150 
                L130,180 
                L210,150 
                L290,250 
                L370,120 
                L450,80 
                L530,200 
                L610,150 
                L690,160 
                L770,170 
                L850,140 
                L930,80 
                L930,350 
                L50,350 Z" />
                        </svg>
                    </div>

                    <div class="chart-container flex flex-col items-center">
                        <canvas id="lineChart"></canvas>
                    </div>



                </div>

            </div>

            <div class="w-full md:w-[34%] border-[1px] border-[#E7E7E7] rounded-[8px] p-2 flex flex-col gap-3">
                <div class="flex items-center gap-2 md:gap-2 lg:gap-4 mx-auto">
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

                <div class="flex flex-col gap-2">
                    <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium text-center">Mode of orders</span>
                </div>

                <div class="progress-container">
                    <svg class="progress-circle" width="170" height="170" viewBox="0 0 200 200">
                        <circle class="progress-background" cx="100" cy="100" r="85" />
                        <path class="progress-arc"
                            d="M 100,100 m -85,0 a 85,85 0 1,1 170,0"
                            stroke-dasharray="534 534"
                            stroke-dashoffset="133" />
                    </svg>
                    <div class="content">
                        <!-- <svg class="bucket-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 6h18l-1.5 15h-15L3 6z"/>
                <path d="M7 6V4a2 2 0 012-2h6a2 2 0 012 2v2"/>
            </svg> -->

                        <img src="../assets/dash/bag-happy.svg" class="bucket-icon" />

                        <span class="value">5,024</span>
                    </div>
                </div>

                <!-- <div class="mx-auto rounded-[50%] bg-[#1A237E] w-[170px] h-[170px] relative flex justify-center items-center">
                    <div class="mx-auto rounded-[50%] bg-white w-[140px] h-[140px] relative"></div>
                </div> -->

                <div class="flex items-center justify-between">
                    <div class="flex items-start gap-1">
                        <img src="../assets/dash/blue.svg" />
                        <div class="flex flex-col gap-1">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium text-left">Express Delivery</span>
                            <span class="text-[#9A9A9A] text-[13px] md:text-[14px] font-Onest font-regular text-left">1,024</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-1">
                        <img src="../assets/dash/blue.svg" />
                        <div class="flex flex-col gap-1">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium text-left">Pick Up</span>
                            <span class="text-[#9A9A9A] text-[13px] md:text-[14px] font-Onest font-regular text-left">4,000</span>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- The charts area ends -->


        <!-- selling location and best selling products setion starts -->
        <div class="flex flex-col md:flex-row items-start gap-3 md:h-[394px]">
            <!-- The sales location starts -->
            <div class="w-full md:w-[50%] h-full overflow-y-auto p-3 border-[1px] border-[#E7E7E7] rounded-[8px]">
                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-[3px]">
                        <span class="text-[#262626] text-[15px] md:text-[17px] font-medium font-['Open Sans']">Sales by Location</span>
                    </div>

                    <div class="flex items-center gap-2 md:gap-2 lg:gap-3">
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
                            <div class="md:min-w-[65px] lg:min-w-[60px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
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

                <div class="w-full h-[1px] bg-[#E7E7E7] my-3"></div>

                <div class="w-full flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-[3px]">
                            <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Lagos</p>
                            <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']">105,000 sales</p>
                        </div>

                        <div class="flex items-center gap-[5px]">
                            <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦10,000</p>
                            <button class="w-[fit-content] bg-[#39D959] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium">+10%</button>
                        </div>

                    </div>

                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-[3px]">
                            <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Lagos</p>
                            <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']">105,000 sales</p>
                        </div>

                        <div class="flex items-center gap-[5px]">
                            <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦10,000</p>
                            <button class="w-[fit-content] bg-[#39D959] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium">+10%</button>
                        </div>

                    </div>

                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-[3px]">
                            <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Lagos</p>
                            <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']">105,000 sales</p>
                        </div>

                        <div class="flex items-center gap-[5px]">
                            <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦10,000</p>
                            <button class="w-[fit-content] bg-[#D93939] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium">-5%</button>
                        </div>

                    </div>

                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-[3px]">
                            <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Lagos</p>
                            <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']">105,000 sales</p>
                        </div>

                        <div class="flex items-center gap-[5px]">
                            <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦10,000</p>
                            <button class="w-[fit-content] bg-[#D93939] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium">-5%</button>
                        </div>

                    </div>

                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col gap-[3px]">
                            <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Lagos</p>
                            <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']">105,000 sales</p>
                        </div>

                        <div class="flex items-center gap-[5px]">
                            <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦10,000</p>
                            <button class="w-[fit-content] bg-[#D93939] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium">-5%</button>
                        </div>

                    </div>

                </div>

            </div>
            <!-- The sales location ends -->

            <!-- The best selling products section starts -->
            <div class="w-full md:w-[50%]  h-full overflow-yauto p-3 border-[1px] border-[#E7E7E7] rounded-[8px]">
                <div class="flex items-center justify-between">


                    <span class="text-left text-[#262626] text-[15px] md:text-[17px] font-medium font-['Open Sans']">Best Sellers</span>


                    <div class="flex items-center gap-2 md:gap-2 lg:gap-3">
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
                            <div class="md:min-w-[65px] lg:min-w-[60px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
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

                <div class="w-full h-[1px] bg-[#E7E7E7] my-3"></div>

                <div class="w-full flex flex-col gap-2 overflow-auto">
                    <div class="flex items-center justify-between bg-[#E7E7E7] p-2">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" />
                            <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Product</span>
                        </div>


                        <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans'] invisible">Amount</span>
                        <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans'] md:mr-10">Amount</span>
                        <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</span>
                    </div>

                    <div class="flex items-start justify-between gap-2 p-2">

                        <div class="flex items-center gap-[10px]">
                            <input type="checkbox" class="border-[#E1E1E1]" />
                            <div class="flex items-start  gap-2">
                                <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                    <img src="../assets/dash/product.svg" class="w-full h-full" />
                                </div>
                                <div class="flex flex-col gap-[4px]">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Bounce Pillow</span>
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">SKU:2345678</span>
                                </div>
                            </div>
                        </div>

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-regular font-['Open Sans']">₦300,000</span>

                        <button class="shrink-0 w-[fit-content] bg-[#39D959] py-1 px-3 rounded-[28px] text-[14px] text-white font-medium">In stock</button>
                    </div>

                    <div class="flex items-start justify-between gap-2 p-2">

                        <div class="flex items-center gap-[10px]">
                            <input type="checkbox" class="border-[#E1E1E1]" />
                            <div class="flex items-start  gap-2">
                                <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                    <img src="../assets/dash/product.svg" class="w-full h-full" />
                                </div>
                                <div class="flex flex-col gap-[4px]">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Bounce Pillow</span>
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">SKU:2345678</span>
                                </div>
                            </div>
                        </div>

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-regular font-['Open Sans']">₦300,000</span>

                        <button class="shrink-0 w-[fit-content] bg-[#262626] py-1 px-3 rounded-[28px] text-[14px] text-white font-medium">Out of Stock</button>
                    </div>

                    <div class="flex items-start justify-between gap-2 p-2">

                        <div class="flex items-center gap-[10px]">
                            <input type="checkbox" class="border-[#E1E1E1]" />
                            <div class="flex items-start  gap-2">
                                <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                    <img src="../assets/dash/product.svg" class="w-full h-full" />
                                </div>
                                <div class="flex flex-col gap-[4px]">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">Bounce Pillow</span>
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']">SKU:2345678</span>
                                </div>
                            </div>
                        </div>

                        <span class="text-[#262626] text-[15px] md:text-[16px] font-regular font-['Open Sans']">₦300,000</span>

                        <button class="shrink-0 w-[fit-content] bg-[#39D959] py-1 px-3 rounded-[28px] text-[14px] text-white font-medium">In stock</button>
                    </div>

                </div>

            </div>
            <!-- The best selling products section ends -->

        </div>
        <!-- selling location and best selling products setion ends -->

        <!-- The recent order starts  -->
        <div class="w-full p-3 border-[1px] border-[#E7E7E7] rounded-[8px]">

            <div class="flex items-center justify-between py-3">
                <span class="text-[#262626] text-[15px] md:text-[17px] font-medium font-['Open Sans']">Recent Orders</span>
                <a href="./notifications.php" class="text-[#1A237E] text-[14px] md:text-[15px] font-medium font-['Open Sans'] cursor-pointer">See All</a>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th class="text-nowrap p-2 flex items-center gap-2">
                            <input type="checkbox" />
                            <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Order ID</span>
                        </th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Customer Name</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Amount</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Status</th>
                        <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date</th>
                        <th class="">
                            <img src="../assets/dash/column.svg" class="min-w-[24px] min-h-[24px]" />
                        </th>





                    </thead>

                    <tbody>

                        <tr>
                            <td class="text-nowrap flex items-center gap-2 px-2 py-5">
                                <input type="checkbox" />
                                <span class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</span>
                            </td>
                            <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">Enyesiobi Golibe</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular px-2">₦300,000</td>




                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Pending</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-nowrap">12/02/2045 09:00am</td>

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
                            <td class="flex items-center gap-2 px-2 py-5">
                                <input type="checkbox" />
                                <span class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</span>
                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Enyesiobi Golibe</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</td>




                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Pending</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">12/02/2045 09:00am</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                            <td class="flex items-center gap-2 px-2 py-5">
                                <input type="checkbox" />
                                <span class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</span>
                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Enyesiobi Golibe</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</td>




                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Pending</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">12/02/2045 09:00am</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                            <td class="flex items-center gap-2 px-2 py-5">
                                <input type="checkbox" />
                                <span class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</span>
                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Enyesiobi Golibe</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</td>




                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#E8B006] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Pending</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">12/02/2045 09:00am</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] cursor-pointer" onclick="openOrdermenu(this)" />

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
                            <td class="flex items-center gap-2 px-2 py-5">
                                <input type="checkbox" />
                                <span class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">#12345</span>
                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">Enyesiobi Golibe</td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</td>




                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Completed</button>
                            </td>

                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">12/02/2045 09:00am</td>

                            <td class="relative">
                                <img src="../assets/user/action.svg" class="w-[24px] cursor-pointer" onclick="openOrdermenu(this)" />

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
        <!-- The recent order ends  -->
    </div>

    <!-- The logout box -->

    <!-- The logout modal -->

    <div id="logout" class="modal logout">
        <div class="modal-content overflow-hidden px-5 py-10">
            <img src="../assets/global/close-circle.svg" alt="close" id="closelo" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4" />

            <p class="text-[#EE3F3F] font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                Log Out
            </p>
            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                Come back soon! We’ll be here when you’re ready to shop again.
            </p>




            <button class="w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#EE3F3F] text-white rounded-[8px] mt-10 cursor-pointer"
                id="closelo">
                Log Out
            </button>
            </form>


        </div>
    </div>



    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <script type="text/javascript" src="../functions/dash.js"></script>
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>

    <script>
        // Toggle menu open/close
        function openOrdermenu(element) {
            const menu = element.nextElementSibling;
            const isOpen = menu.style.display === 'block';

            // First close all menus
            document.querySelectorAll('.ordermenu-content').forEach(m => {
                m.style.display = 'none';
            });

            // If the clicked menu wasn't open, then open it
            if (!isOpen) {
                menu.style.display = 'block';

                // Position check (for bottom items)
                const rect = element.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;

                if (spaceBelow < 200) {
                    menu.style.bottom = '100%';
                    menu.style.top = 'auto';
                } else {
                    menu.style.top = '100%';
                    menu.style.bottom = 'auto';
                }
            }

            // Stop event propagation to prevent immediate closing
            event.stopPropagation();
        }

        // Add this to your script to close menus when clicking elsewhere
        document.addEventListener('click', function(event) {
            // Only close if click is not on an action button
            if (!event.target.closest('[onclick="openOrdermenu(this)"]')) {
                document.querySelectorAll('.ordermenu-content').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });


        var logout = document.getElementById("logout");
        var logmeout = document.getElementById("logmeout");
        var closelo = document.getElementById("closelo");



        logmeout.onclick = function() {
            logout.style.display = "block";
            console.log("log out now")
        }


        closelo.onclick = function() {
            logout.style.display = "none";
        }


        

    </script>
</body>

</html>