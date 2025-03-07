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
            <span class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"> <img src="../../assets/dash/logout.svg" class="nonactiveicon w-[20px] h-[20px]" /><span class="text-[#D93939]">Logout</span></span>
        </div>
    </div>


    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
  
    <div class="w-full md:w-[70%] p-6 bg-white">
    <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Orders</h1>
  <h2 class="text-[16px] font-medium mb-6">Select your notification preference within the dashboard</h2>
  
  <!-- Dashboard Notifications -->
  <div class="space-y-4 mb-8">
    <div class="flex items-center justify-between">
      <span class="text-gray-700">New Orders</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <span class="text-gray-700">Transactions</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" checked class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <span class="text-gray-700">Cancellation & Refund Alerts</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" checked class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <span class="text-gray-700">Security Alerts</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" checked class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <span class="text-gray-700">Stock & Quantity Alert</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" checked class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <span class="text-gray-700">Chat</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" checked class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>
  </div>

  <!-- Email Notifications -->
  <div class="space-y-4 mb-8">
    <div class="flex items-center justify-between">
      <div>
        <span class="text-gray-700 block">Get notifications on your email</span>
        <span class="text-gray-500 text-sm">miraclegift@gmail.com</span>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
    </div>

    <div class="pl-6 space-y-4 opacity-50">
      <div class="flex items-center justify-between">
        <span class="text-gray-700">New Orders</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" disabled class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-gray-700">Transactions</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" disabled class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-gray-700">Cancellation & Refund Alerts</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" disabled class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-gray-700">Security Alerts</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" disabled class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-gray-700">Stock & Quantity Alert</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" disabled class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-gray-700">Chat</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" disabled class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
        </label>
      </div>
    </div>
  </div>
</div>

    </div>
    </div>









    <script type="text/javascript" src="../../functions/drop-select.js"></script>
    <script type="text/javascript" src="../../functions/order.js"></script>
    <script type="text/javascript" src="../../functions/dash.js"></script>
    <script type="text/javascript" src="../../functions/tab.js"></script>
    <script type="text/javascript" src="../../functions/overlay.js"></script>
    <script type="text/javascript" src="../../functions/ordermenu.js"></script>
    <script type="text/javascript" src="../../functions/nav.js"></script>

  
</body>


</html>