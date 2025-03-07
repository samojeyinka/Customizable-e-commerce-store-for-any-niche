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
                <h1 class="hidden md:block  text-[16px] md:text-[20px] font-Onest font-semibold">Profile</h1>
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
  

    <div class="w-[90%] mx-auto md:mx-0 md:w-[70%] pb-10 md:pb-0">
    <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Profile</h1>
    <!-- Profile View (Image 3) -->
    <div id="profileView" class="space-y-6">
      <div class="flex justify-between items-start">
        <div class="flex flex-col items-center">
          <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
            <img id="profileImageDisplay" class="w-full h-full object-cover hidden">
            <svg id="profileImagePlaceholder" class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
        <button id="editProfileBtn" class="px-4 py-2 bg-indigo-800 text-white rounded-md hover:bg-indigo-700 transition-colors">
          Edit Profile
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Full name</label>
          <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
            Golibe Faith
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Email address</label>
          <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
            golibe.f@gmail.com
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Phone number</label>
          <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50 flex">
            <span class="font-medium">+234</span>&nbsp;09090909090
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Role</label>
          <div class="mt-1 p-2 w-full border border-gray-300 rounded-md bg-gray-50">
            Owner
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Profile (Image 2) -->
    <div id="editProfileForm" class="hidden space-y-6">
      <div class="flex flex-col items-center">
        <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden relative group">
          <img id="editProfileImage" class="w-full h-full object-cover hidden">
          <svg id="editProfilePlaceholder" class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
          </svg>
          <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
        </div>
        <input type="file" id="profilePhotoInput" class="hidden" accept="image/*">
        <button id="changeProfilePhotoBtn" class="mt-2 inline-block border border-indigo-700 text-[#262626] px-4 py-1 rounded-md text-sm hover:bg-indigo-50 transition-colors">
          Change Profile Photo
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="fullName" class="block text-sm font-medium text-gray-700">Full name</label>
          <input type="text" id="fullName" class="mt-1 p-2 w-full border border-gray-300 rounded-md" value="Golibe Faith">
        </div>
        
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
          <input type="email" id="email" class="mt-1 p-2 w-full border border-gray-300 rounded-md" value="golibe.f@gmail.com">
        </div>
        
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
          <div class="mt-1 flex">
            <div class="relative">
              <select class="appearance-none bg-white border border-gray-300 rounded-l-md p-2 pr-8 focus:outline-none">
                <option>+234</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <input type="text" id="phone" class="p-2 flex-1 border border-l-0 border-gray-300 rounded-r-md" value="09090909090">
          </div>
        </div>
        
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
          <input type="text" id="role" class="mt-1 p-2 w-full border border-gray-300 rounded-md" value="Owner" readonly>
        </div>
      </div>
      
      <div class="flex space-x-4 pt-4">
        <button id="cancelBtn" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
          Cancel
        </button>
        <button id="saveBtn" class="flex-1 px-4 py-2 bg-indigo-800 text-white rounded-md hover:bg-indigo-700 transition-colors">
          Save Changes
        </button>
      </div>
    </div>

    <!-- Simple Profile (Image 1) -->
    <div id="simpleProfile" class="hidden space-y-6">
      <div>
        <input type="file" id="simpleProfilePhotoInput" class="hidden" accept="image/*">
        <button id="simpleChangePhotoBtn" class="inline-block border border-indigo-700 text-[#262626] px-4 py-1 rounded-md text-sm hover:bg-indigo-50 transition-colors">
          Change Profile Photo
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="fullName2" class="block text-sm font-medium text-gray-700">Full name</label>
          <input type="text" id="fullName2" class="mt-1 p-2 w-full border border-gray-300 rounded-md" value="Golibe Faith">
        </div>
        
        <div>
          <label for="email2" class="block text-sm font-medium text-gray-700">Email address</label>
          <input type="email" id="email2" class="mt-1 p-2 w-full border border-gray-300 rounded-md" value="golibe.f@gmail.com">
        </div>
        
        <div>
          <label for="phone2" class="block text-sm font-medium text-gray-700">Phone number</label>
          <div class="mt-1 flex">
            <div class="relative">
              <select class="appearance-none bg-white border border-gray-300 rounded-l-md p-2 pr-8 focus:outline-none">
                <option>+234</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <input type="text" id="phone2" class="p-2 flex-1 border border-l-0 border-gray-300 rounded-r-md" value="09090909090">
          </div>
        </div>
        
        <div>
          <label for="role2" class="block text-sm font-medium text-gray-700">Role</label>
          <input type="text" id="role2" class="mt-1 p-2 w-full border border-gray-300 rounded-md" value="Owner" readonly>
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

    
  <script>
    // DOM Elements
    const profileView = document.getElementById('profileView');
    const editProfileForm = document.getElementById('editProfileForm');
    const simpleProfile = document.getElementById('simpleProfile');
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    
    // Image elements
    const profileImageDisplay = document.getElementById('profileImageDisplay');
    const profileImagePlaceholder = document.getElementById('profileImagePlaceholder');
    const editProfileImage = document.getElementById('editProfileImage');
    const editProfilePlaceholder = document.getElementById('editProfilePlaceholder');
    
    // File input elements
    const profilePhotoInput = document.getElementById('profilePhotoInput');
    const simpleProfilePhotoInput = document.getElementById('simpleProfilePhotoInput');
    const changeProfilePhotoBtn = document.getElementById('changeProfilePhotoBtn');
    const simpleChangePhotoBtn = document.getElementById('simpleChangePhotoBtn');
    
    // Image upload functionality
    let currentProfileImage = null;
    
    // Handle profile photo button click
    changeProfilePhotoBtn.addEventListener('click', () => {
      profilePhotoInput.click();
    });
    
    // Handle simple profile photo button click
    simpleChangePhotoBtn.addEventListener('click', () => {
      simpleProfilePhotoInput.click();
    });
    
    // Handle file selection
    profilePhotoInput.addEventListener('change', handleImageUpload);
    simpleProfilePhotoInput.addEventListener('change', handleImageUpload);
    
    function handleImageUpload(event) {
      const file = event.target.files[0];
      if (file && file.type.match('image.*')) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          // Store the image data
          currentProfileImage = e.target.result;
          
          // Update all image displays
          updateAllProfileImages(currentProfileImage);
        };
        
        reader.readAsDataURL(file);
      }
    }
    
    function updateAllProfileImages(imageData) {
      // Update main profile view
      if (imageData) {
        profileImageDisplay.src = imageData;
        profileImageDisplay.classList.remove('hidden');
        profileImagePlaceholder.classList.add('hidden');
        
        // Update edit profile view
        editProfileImage.src = imageData;
        editProfileImage.classList.remove('hidden');
        editProfilePlaceholder.classList.add('hidden');
      } else {
        // Reset to placeholder if no image
        profileImageDisplay.classList.add('hidden');
        profileImagePlaceholder.classList.remove('hidden');
        
        editProfileImage.classList.add('hidden');
        editProfilePlaceholder.classList.remove('hidden');
      }
    }
    
    // Show Edit Profile Form
    editProfileBtn.addEventListener('click', () => {
      profileView.classList.add('hidden');
      editProfileForm.classList.remove('hidden');
      simpleProfile.classList.add('hidden');
    });

    // Cancel Edit
    cancelBtn.addEventListener('click', () => {
      profileView.classList.remove('hidden');
      editProfileForm.classList.add('hidden');
      simpleProfile.classList.add('hidden');
    });

    // Save Changes
    saveBtn.addEventListener('click', () => {
      // In a real app, you would send data to server here
      
      // Update displayed name in view mode with edited name
      const nameInput = document.getElementById('fullName');
      const emailInput = document.getElementById('email');
      const phoneInput = document.getElementById('phone');
      
      // Here you would update the displayed values in the view mode
      // with the values from the form fields
      
      profileView.classList.remove('hidden');
      editProfileForm.classList.add('hidden');
      simpleProfile.classList.add('hidden');
      
      // Show success message
      alert('Profile updated successfully!');
    });

    // Function to switch between views (for demo purposes)
    function showView(viewNumber) {
      profileView.classList.add('hidden');
      editProfileForm.classList.add('hidden');
      simpleProfile.classList.add('hidden');
      
      if (viewNumber === 1) {
        simpleProfile.classList.remove('hidden');
      } else if (viewNumber === 2) {
        editProfileForm.classList.remove('hidden');
      } else {
        profileView.classList.remove('hidden');
      }
    }

    // Initialize with view 3 (profile view)
    showView(3);
  </script>

</body>


</html>