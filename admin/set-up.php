<?php session_start(); // This is essential for accessing session variables ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLOREFY ADMIN | Set Password</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-[#FAFAFA] flex items-center justify-center min-h-screen" style="background-image: url('./assets/global/bg.svg'); background-position: center; background-size: cover;">
    <div class="w-[90%] lg:w-[50%] h-[fit-content] mx-auto bg-white rounded-[24px] p-5">
        <div class="w-[95%] mx-auto max-w-[1440px] flex items-center justify-between">
            <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">SETUP ACCOUNT</h3>

            <div class="flex items-center gap-1 md:gap-2">
                <img src="<?php echo (function_exists('store') ? store_escape(store('logo_url')) : './assets/global/logo.png'); ?>" alt="GLOREFY" class="w-[31.35px] md:w-[41.35px]" />
            </div>
        </div>
        
        <h3 class="text-[#262626] text-center text-[18px] md:text-[22px] font-['Open Sans'] font-medium pt-5">ADMIN PANEL</h3>
        
        <?php if (isset($_SESSION['setup_message'])): ?>
        <section class="flex flex-col items-center w-full bg-[#E0F8E9] shadow-lg mt-4 py-3 px-4 rounded relative overflow-hidden">
            <div class="h-[100%] w-[5px] bg-[#28C76F] absolute left-0 top-0"></div>
            <div class="flex items-center gap-2 mr-auto">
                <i class="fa-solid fa-circle-check text-[#22C55E] text-[24px]"></i>
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-medium">
                    Success
                </p>
            </div>
            <p class="text-[13px] md:text-[14px] text-start w-full text-[#7F7F7F] mt-2 ml-[3rem] pr-3">
                <?php echo $_SESSION['setup_message']; unset($_SESSION['setup_message']); ?>
            </p>
        </section>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['setup_error'])): ?>
        <section class="flex flex-col items-center w-full bg-[#FDECEC] shadow-lg mt-4 py-3 px-4 rounded relative overflow-hidden">
            <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
            <div class="flex items-center gap-2 mr-auto">
                <i class="fa-solid fa-circle-xmark text-[#EE3F3F] text-[24px]"></i>
                <p class="text-[16px] md:text-[17px] text-[#2C2C2C] w-full font-medium">
                    Error
                </p>
            </div>
            <p class="text-[13px] md:text-[14px] text-start w-full text-[#7F7F7F] mt-2 ml-[3rem] pr-3">
                <?php echo $_SESSION['setup_error']; unset($_SESSION['setup_error']); ?>
            </p>
        </section>
        <?php endif; ?>

        <p class="text-[#C2185B] font-['Open Sans'] text-[18px] md:text-[20px] font-medium text-center mt-6">
            Set Your Administrator Password
        </p>
        <p class="w-[90%] md:w-[75%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2 mb-6">
            Enter your email address to receive an OTP code that will help you set up your administrator password.
        </p>

        <form action="./request-password-setup.php" method="POST" class="flex flex-col gap-4 w-[90%] mx-auto max-w-[1440px]">
            <div class="flex flex-col gap-1">
                <label for="email" class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                    Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email address"
                    class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"
                    required
                />
            </div>
            
            <button type="submit" class="w-full py-[8px] px-3 bg-[#C2185B] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center mt-4">
                Request OTP Code
            </button>
        </form>
    </div>
</body>
</html>