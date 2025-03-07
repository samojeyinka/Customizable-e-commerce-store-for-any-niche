<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css" />
    <link rel="stylesheet" href="../../styles/styles.css" />
    <link rel="stylesheet" href="../../styles/overlay.css">
    <link rel="stylesheet" href="../../styles/dropdown.css" />
    <link rel="stylesheet" href=".././../styles/graph.css" />
    <link rel="stylesheet" href="../../styles/dash.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Document</title>
</head>

<body class="relative">
    <!-- ========================  The header  starts ======================== -->
    <header class="w-full bg-[#FFFFFF] z-100 flex items-center justify-center p-3 border-b-[1px] border-[#F8F8F8] fixed top-0 left-0">
        <nav class="w-full md:w-[98%] lg-w-[95%] flex items-center justify-between">

            <div class="flex items-center gap-5 md:gap-8 lg:gap-10">
                <a href="./index.php" class="flex items-center gap-1 md:gap-2">
                    <img src="../../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                    <h1 class="hidden md:block text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </a>

                <img onclick="toggleNav()" src="../../assets/home/menu.svg" alt="Search" class="w-[28px] cursor-pointer" />
                <h1 class="hidden md:block  text-[16px] md:text-[20px] font-Onest font-semibold">Settings</h1>
            </div>


            <div class="flex items-center gap-0">
                <div class="flex items-center gap-2 border-[1px] border-[#F3F3F3] rounded-[25px] p-2">
                    <img src="../../assets/global/search-normal.svg" alt="Search" class="w-[18px]" />
                    <input type="text" placeholder="Search name, Order ID..." class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                </div>

            </div>
            <div class="flex items-center gap-6 md:bg-[#F3F3F3] rounded-[4px] py-1 px-4">

                <span class="cursor-pointer relative" onclick="openNotification()">
                    <img src="../../assets/global/bell.svg" class="w-[18px] md:w-[20px]" alt="bag" />
                    <div class="w-[8px] h-[8px] bg-[#1A237E] rounded-full absolute top-[-.1rem] left-3"></div>
                </span>

                <a href="./profile.php" class="flex items-center gap-2 cursor-pointer">
                    <div class="w-[40px] h-[40px] md:w-[44px] md:h-[44px] rounded-[50%]">
                        <img src="../../assets/home/user.svg" alt="Profile Picture" class="w-full h-full" />
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
            <a href=".././overview.php" class="nav-link active flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/category.svg" class="activeicon w-[20px] h-[20px]" /> <img src="../../assets/dash/category2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Overview</span></a>
            <a href=".././products.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/book (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../../assets/dash/book.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Products</span></a>
            <a href=".././orders.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/bag-happy (2).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../../assets/dash/bag-happy (1).svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Orders</span></a>
            <a href="./page2.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/profile (2).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../../assets/dash/profile (1).svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Users</span></a>
            <a href="./page3.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/receipt-minus (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../../assets/dash/receipt-minus.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Transactions</span></a>
        </div>

        <div class="flex flex-col gap-2 mb-7">
            <a href="./options.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/setting-2 (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../../assets/dash/setting-2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Settings</span></a>
            <span class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"><img src="../../assets/dash/logout.svg" class="activeicon w-[20px] h-[20px]" /> <span class="text-[#D93939]">Logout</span></span>
        </div>
    </div>


    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">

        <div class="w-full md:w-[70%] bg-white p-6 space-y-4">
            <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Orders</h1>
            <!-- Password Change Button -->
            <button id="myvmBtn" class="cursor-pointer w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Change password</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Notification Settings -->
            <a href="./notifications.php" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Manage Notification Settings</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Two-factor Authentication Toggle -->
            <div class="flex items-center justify-between px-4 py-2">
                <span class="text-[#2C2C2C]">Enable Two-factor Authentication</span>
                <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" id="twoFAToggle">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
            </div>

            <!-- Login Status Check -->
            <button id="opencwyali" class="cursor-pointer w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Check where you are logged in</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

    </div>
    </div>



    <!-- The modals starts -->
    <div id="myModal" class="modal reg">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Product Overview</h1>
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
            <div class="grid grid-cols-1 md:grid-cols-2  p-2 gap-4 mt-2">
                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Orders</span>
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
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Completed Orders</span>
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

                <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                    <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                        <img src="../assets/dash/illu.svg" class="w-full h-full" />
                    </div>

                    <div class="flex flex-col gap-[1px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Pending Orders</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">430</h2>
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
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Returned Orders</span>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">200</h2>
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



    <!-- The verification starts -->
    <div id="verifyModal" class="modal verify-modal">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <div class="w-full">

                <div class="w-[95%] mx-auto flex items-center justify-between cursor-pointer" id="closevm">
                    <span class="flex items-center gap-2">
                        <img src="../../assets/global/arrow-left.svg" />
                        <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
                    </span>

                </div>




                <p class="pl-[2.5%] font-['Open Sans']  text-[18px] text-[22px] font-medium text-left pt-5 text-[#1A237E]">
                    Verification
                </p>
                <p class="pl-[2.5%] font-['Open Sans']  text-[16px] text-[20px] font-medium text-left">
                    Let us verify it’s you
                </p>
                <p class="pl-[2.5%]  mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                    Enter the 4 digit code sent to golibe.f@gmail.com to create your account
                </p>

                <form class="w-full mt-[1rem] flex items-center flex-col">

                    <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                        Enter Code
                    </p>

                    <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
                        <input
                            type="password"
                            inputMode="numeric"
                            class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                        <input
                            type="password"
                            inputMode="numeric"
                            class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                        <input
                            type="password"
                            inputMode="numeric"
                            class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                        <input
                            type="password"
                            inputMode="numeric"
                            class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none" />
                    </div>


                    <span

                        id="openChp"
                        class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                        Verify
                    </span>
                </form>

                <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left">
                    Resend code in <span class="text-[#1A237E]">23sec</span>
                </p>




            </div>

            ​
        </div>

    </div>
    <!-- The verification ends -->

    <!-- The change password starts -->
    <div id="changePassw" class="modal change-pass">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <div class="w-full">

                <span id="closechp" class="flex items-center gap-2 cursor-pointer">
                    <img src="../../assets/global/arrow-left.svg" />
                    <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
                </span>



                <p class="text-[#1A237E] font-['Open Sans']  text-[18px] text-[22px] font-medium text-left my-4">
                    Change Password
                </p>





                <form class="w-full mt-[1rem] flex flex-col gap-4">


                    <div class="flex flex-col gap-1">
                        <label
                            htmlFor="firstname"
                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                            Old Password
                        </label>

                        <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                            <input
                                type="password"
                                placeholder="Enter your old password"
                                class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                            <img src="../../assets/global/eye.svg" class="w-[24px] cursor-pointer" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            htmlFor="firstname"
                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                            New Password
                        </label>

                        <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                            <input
                                type="password"
                                placeholder="Enter your new password"
                                class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                            <img src="../../assets/global/eye.svg" class="w-[24px] cursor-pointer" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            htmlFor="firstname"
                            class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                            Confirm Password
                        </label>

                        <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                            <input
                                type="password"
                                placeholder="Re enter your new password"
                                class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                            <img src="../../assets/global/eye-slash.svg" class="w-[24px] cursor-pointer" />
                        </div>

                        <p class="text-[#D93939] font-['Open Sans']  text-[15px] text-[16px] font-regular text-left">
                            Password doesn’t match
                        </p>

                    </div>


                    <span
                        id="opencps"
                        class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                        Change
                    </span>
                </form>




            </div>
        </div>

    </div>
    <!-- The  change password ends -->


    <!-- The  change password success starts -->
    <div id="cps" class="modal cps">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <div class="w-full">

                <div class="flex flex-col gap-0">

                    <img src="../../assets/global/success.svg" class="mx-auto w-[120px]" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Congratulations!!!
                    </p>
                    <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        You have reset your password successfully
                    </p>




                    <span id="closecps" class="text-center w-[90%] mx-auto text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                        Done
                    </span>

                </div>



            </div>

        </div>

    </div>
    <!-- The  change password success ends -->


    <!-- The  check where u are logged in starts -->
    <div id="cwyali" class="modal cwyali">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <div class="w-full">
                <!-- Header with close button -->
                <div class="flex items-center justify-between p-4">
                    <h2 class="text-lg font-medium text-indigo-900">Active Sections</h2>
                    <img src="../../assets/global/close-circle.svg" alt="close" id="closecwyali" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />
                </div>

                <!-- Subtitle -->
                <div class="px-4 py-2 bg-gray-50 text-sm text-gray-500">
                    See where you are logged in
                </div>

                <!-- Device list -->
                <div class="p-4 space-y-4">
                    <!-- MacBook Pro -->
                    <div class="flex md:items-center gap-2 flex-col md:flex-row justify-between">
                        <div class="flex items-center">
                            <img src="../../assets/dash/Icon.svg" />
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">2022 MacBook Pro</p>
                                <div class="text-xs text-gray-500">
                                    <span>Lekki, Nigeria</span>
                                    <span class="mx-1">•</span>
                                    <span>June 12, 2022 at 10:32am</span>
                                </div>
                            </div>
                        </div>
                        <button class="w-[fit-content] cursor-pointer px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Remove Device
                        </button>
                    </div>

                    <!-- iPhone 13 -->
                    <div class="flex md:items-center gap-2 flex-col md:flex-row justify-between">
                        <div class="flex items-center">
                            <img src="../../assets/dash/Base Icon (3).svg" />
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">2022 iPhone 13</p>
                                <div class="text-xs text-gray-500">
                                    <span>Ikeja, Lagos, Nigeria</span>
                                    <span class="mx-1">•</span>
                                    <span>June 12, 2022 at 10:32am</span>
                                </div>
                            </div>
                        </div>
                        <button class="w-[fit-content] cursor-pointer px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Remove Device
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- The  check where u are logged in ends -->


  <!-- Enable 2FA Modal -->
  <div id="enableTwoFaModal" class="modal enabletwoFa">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
      <div class="w-full">
        <div class="w-[95%] mx-auto flex items-center justify-between cursor-pointer close-modal">
          <span class="flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-left">
              <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 class="text-[#262626] text-center text-[20px] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
          </span>
        </div>
        
        <p class="pl-[2.5%] font-['Open Sans'] text-[18px] md:text-[22px] font-medium text-left pt-5 text-[#1A237E]">
          Enable Two Factor Authentication (2FA)
        </p>
        
        <p class="pl-[2.5%] mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
          Enter the 4 digit code sent to miraclegift@gmail.com to enable your 2FA
        </p>
        
        <form class="w-full mt-[1rem] flex items-center flex-col">
          <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
            Enter Code
          </p>
          
          <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
          </div>
          
          <span
          id="enablesuccess"
            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer verify-btn"
          >
            Enable
          </span>
        </form>
        
        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left">
          Resend code in <span class="text-[#1A237E] countdown">23sec</span>
        </p>
      </div>
    </div>
  </div>

  <!-- Disable 2FA Modal -->
  <div id="disableTwoFaModal" class="modal enabletwoFa">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
      <div class="w-full">
        <div class="w-[95%] mx-auto flex items-center justify-between cursor-pointer close-modal">
          <span class="flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-left">
              <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 class="text-[#262626] text-center text-[20px] md:text-[24px] font-['Open Sans'] font-medium">Go back</h3>
          </span>
        </div>
        
        <p class="pl-[2.5%] font-['Open Sans'] text-[18px] md:text-[22px] font-medium text-left pt-5 text-[#1A237E]">
          Disable Two Factor Authentication (2FA)
        </p>
        
        <p class="pl-[2.5%] mr-auto text-[15px] text-left md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
          Enter the 4 digit code sent to miraclegift@gmail.com to disable your 2FA
        </p>
        
        <form class="w-full mt-[1rem] flex items-center flex-col">
          <p class="pl-[2.5%] mr-auto text-left font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
            Enter Code
          </p>
          
          <div class="pl-[2.5%] w-[fit-content] flex items-center gap-3 mr-auto pt-2">
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
            <input
              type="password"
              inputMode="numeric"
              class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none code-input"
              maxlength="1"
            />
          </div>
          
          <span
          id="unablesuccess"
            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer verify-btn"
          >
            Verify
          </span>
        </form>
        
        <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-left">
          Resend code in <span class="text-[#1A237E] countdown">23sec</span>
        </p>
      </div>
    </div>
  </div>



      <!-- The  enable 2fa success starts -->
      <div id="etfas" class="modal cps">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <div class="w-full">

                <div class="flex flex-col gap-0">

                    <img src="../../assets/global/success.svg" class="mx-auto w-[120px]" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Congratulations!!!
                    </p>
                    <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                    Two-Factor Authentication (2FA) has been successfully enabled for your account. Your security is now enhanced
                    </p>




                    <span id="closeenablesuccess" class="text-center w-[90%] mx-auto text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                        Done
                    </span>

                </div>



            </div>

        </div>

    </div>
    <!-- The  enable 2fa success ends -->

    
      <!-- The  enable 2fa success starts -->
      <div id="utfas" class="modal cps">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">
            <div class="w-full">

                <div class="flex flex-col gap-0">

                    <img src="../../assets/global/success.svg" class="mx-auto w-[120px]" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                    2FA Disabled
                    </p>
                    <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                    Two-Factor Authentication (2FA) has been disabled. Your account is now protected by your password only. It is advised to please enable 2FA again immediately
                    </p>




                    <span id="closeunablesuccess" class="text-center w-[90%] mx-auto text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                        Done
                    </span>

                </div>



            </div>

        </div>

    </div>
    <!-- The  unable 2fa success ends -->








    <script type="text/javascript" src="../../functions/drop-select.js"></script>
    <script type="text/javascript" src="../../functions/order.js"></script>
    <script type="text/javascript" src="../../functions/dash.js"></script>
    <script type="text/javascript" src="../../functions/tab.js"></script>
    <script type="text/javascript" src="../../functions/overlay.js"></script>
    <script type="text/javascript" src="../../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../../functions/nav.js"></script>

    <script>
        var verifymodal = document.getElementById("verifyModal");
        var vmbtn = document.getElementById("myvmBtn");
        var closevm = document.getElementById("closevm");


        vmbtn.onclick = function() {
            verifymodal.style.display = "block";

        }


        closevm.onclick = function() {
            verifymodal.style.display = "none";
        }


        var changePassw = document.getElementById("changePassw");
        var openChp = document.getElementById("openChp");
        var closechp = document.getElementById("closechp");


        openChp.onclick = function() {
            changePassw.style.display = "block";
            verifymodal.style.display = "none";

        }


        closechp.onclick = function() {
            changePassw.style.display = "none";
            verifymodal.style.display = "block";
        }



        var cps = document.getElementById("cps");
        var opencps = document.getElementById("opencps");
        var closecps = document.getElementById("closecps");


        opencps.onclick = function() {
            cps.style.display = "block";
            changePassw.style.display = "none";

        }


        closecps.onclick = function() {
            cps.style.display = "none";
        }


        var cwyali = document.getElementById("cwyali");
        var opencwyali = document.getElementById("opencwyali");
        var closecwyali = document.getElementById("closecwyali");


        opencwyali.onclick = function() {
            cwyali.style.display = "block";
            changePassw.style.display = "none";

        }


        closecwyali.onclick = function() {
            cwyali.style.display = "none";
        }


        
    var etfas = document.getElementById("etfas");
        var enablesuccess = document.getElementById("enablesuccess");
        var closeenablesuccess = document.getElementById("closeenablesuccess");


        enablesuccess.onclick = function() {
            etfas.style.display = "block";
            enableTwoFaModal.style.display = "none";
        }


        closeenablesuccess.onclick = function() {
            etfas.style.display = "none";
        }
    
        var utfas = document.getElementById("utfas");
        var unablesuccess = document.getElementById("unablesuccess");
        var closeunablesuccess = document.getElementById("closeunablesuccess");


        unablesuccess.onclick = function() {
            utfas.style.display = "block";
            disableTwoFaModal.style.display = "none";
        }


        closeunablesuccess.onclick = function() {
            utfas.style.display = "none";
        }
    


        const twoFAToggle = document.getElementById('twoFAToggle');
    const enableTwoFaModal = document.getElementById('enableTwoFaModal');
    const disableTwoFaModal = document.getElementById('disableTwoFaModal');
    const closeModalButtons = document.querySelectorAll('.close-modal');
    const showToggleStateBtn = document.getElementById('showToggleState');
    const codeInputs = document.querySelectorAll('.code-input');
    const verifyButtons = document.querySelectorAll('.verify-btn');
    const countdowns = document.querySelectorAll('.countdown');
    
    // Toggle state (local storage could be used for persistence)
    let isTwoFAEnabled = false;
    
    // Event Listeners
    twoFAToggle.addEventListener('change', handleToggleChange);
    closeModalButtons.forEach(button => {
      button.addEventListener('click', closeAllModals);
    });
    showToggleStateBtn.addEventListener('click', showToggleState);
    verifyButtons.forEach(button => {
      button.addEventListener('click', handleVerification);
    });
    
    // Code input auto-focus functionality
    codeInputs.forEach((input, index) => {
      // Auto-focus to next input after entering a digit
      input.addEventListener('input', function() {
        if (this.value.length === 1) {
          if (index < codeInputs.length - 1) {
            codeInputs[index + 1].focus();
          }
        }
      });
      
      // Handle backspace to go to previous input
      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value.length === 0) {
          if (index > 0) {
            codeInputs[index - 1].focus();
          }
        }
      });
    });
    
    // Functions
    function handleToggleChange(e) {
      isTwoFAEnabled = e.target.checked;
      
      if (isTwoFAEnabled) {
        // Show Enable 2FA Modal
        enableTwoFaModal.style.display = 'block';
        startCountdown(countdowns[0]);
      } else {
        // Show Disable 2FA Modal
        disableTwoFaModal.style.display = 'block';
        startCountdown(countdowns[1]);
      }
    }
    
    function closeAllModals() {
      enableTwoFaModal.style.display = 'none';
      disableTwoFaModal.style.display = 'none';
      
      // Reset toggle if modal is closed without verification
      twoFAToggle.checked = isTwoFAEnabled;
      
      // Reset inputs
      codeInputs.forEach(input => {
        input.value = '';
      });
    }
    
    function showToggleState() {
      alert(`Two-Factor Authentication is currently ${isTwoFAEnabled ? 'enabled' : 'disabled'}`);
    }
    
    function handleVerification() {
      // Check if all code inputs are filled
      let allFilled = true;
      let code = '';
      
      codeInputs.forEach(input => {
        if (input.value.length === 0) {
          allFilled = false;
        }
        code += input.value;
      });
      
      if (allFilled) {
        // In a real app, you would validate the code with the server
        alert(`Verification code submitted: ${code}`);
        
        // Update the 2FA state and close modal
        isTwoFAEnabled = twoFAToggle.checked;
        closeAllModals();
      } else {
        alert('Please enter the complete 4-digit code');
      }
    }
    
    function startCountdown(element) {
      let seconds = 23;
      element.textContent = `${seconds}sec`;
      
      const timer = setInterval(() => {
        seconds--;
        element.textContent = `${seconds}sec`;
        
        if (seconds <= 0) {
          clearInterval(timer);
          element.textContent = 'Resend';
          element.parentElement.innerHTML = 'Code expired. <span class="text-[#1A237E] cursor-pointer">Resend code</span>';
        }
      }, 1000);
    }
    
    // When clicking outside of the modal content, close the modal
    window.addEventListener('click', function(event) {
      if (event.target === enableTwoFaModal || event.target === disableTwoFaModal) {
        closeAllModals();
      }
    });



    </script>

</body>


</html>