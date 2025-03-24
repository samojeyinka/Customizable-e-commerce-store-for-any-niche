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
  
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">

        <div class="w-full md:w-[70%] bg-white p-6 space-y-4">
            <h1 class="md:hidden  text-[18px] font-Onest font-semibold mb-3 md:mb-0">Settings</h1>
            <!-- Password Change Button -->
            <a href="../../forgotten-password.php" id="myvmBtn" class="cursor-pointer w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Change password</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Notification Settings -->
            <a href="./notifications.php" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150">
                <span class="text-[#2C2C2C]">Manage Notification Settings</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Two-factor Authentication Toggle -->
            <div class="flex items-center justify-between px-4 py-2 hidden">
                <span class="text-[#2C2C2C]">Enable Two-factor Authentication</span>
                <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" id="twoFAToggle">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
      </label>
            </div>

            <!-- Login Status Check -->
            <button id="opencwyali" class="cursor-pointer w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-md transition-colors duration-150 hidden">
                <span class="text-[#2C2C2C]">Check where you are logged in</span>
                <svg class="w-5 h-5 text-[#363636]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
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