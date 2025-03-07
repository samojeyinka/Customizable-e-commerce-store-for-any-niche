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
    <title>VICTOSAH | Favourites</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/tabs.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>
    <main class="bg-[#FEFEFE]">
        <!-- ========================  The header  starts ======================== -->
        <header class="w-full bg-[#E8E9F2] flex items-center justify-center p-3">


            <nav class="w-[90%] flex items-center justify-between">
                <a href="../index.php" class="flex items-center gap-1 md:gap-2">
                    <img src="../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                    <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </a>
                <div class="hidden md:flex items-center gap-0">
                    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="../assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
                </div>
                <div class="flex items-center gap-6">
                    <a href="./products/cart.php">
                        <img src="../assets/global/bag.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>
                    <a href="./favourites.php">
                        <img src="../assets/global/lovely.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>

                    <a onclick="openSidemenu()" class="flex items-center gap-1 cursor-pointer">
                        <img src="../assets/global/profile.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                        <img src="../assets/products/down2.svg" class="w-[12px]" alt="bag" />
                    </a>

                </div>
            </nav>


            ​
            <!-- The dropdowns -->


            <div id="sidemenu" class="sidemenu-content border-[1px] border-[#E1E1E1] bg-white">
                <div class="relative">
                    <p class="menulink text-[16px] font-regular text-[#262626] font-['Open Sans']" id="myBtn">Sign In</p>
                    <p class="menulink text-[16px] font-regular text-[#262626] font-['Open Sans']" id="myBtn">Create an Account</p>

                    <a href="./profile.php" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#262626] font-['Open Sans']">
                        <img src="../assets/global/user.svg" class="" />
                        <span>My Profile</span>

                    </a>

                    <a href="./orders.php" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#262626] font-['Open Sans']">
                        <img src="../assets/global/invoice.svg" class="" />
                        <span>My Orders</span>

                    </a>
                    <a id="logooutBtn" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#EE3F3F] font-['Open Sans']">
                        <img src="../assets/global/logout.svg" class="" />
                        <span>Log Out</span>

                    </a>

                    <div class="w-[2rem] h-[2rem] border-l-[1px] border-t-[1px] border-[#E1E1E1] bg-white absolute top-[-16px] right-[66px] rotate-[45deg] z-1"></div>
                </div>
            </div>



            ​
            <!-- The Modal -->
            <div id="myModal" class="modal reg">
                <!-- Modal content -->
                <div class="modal-content overflow-hidden p-4">

                    <img src="../assets/global/close-circle.svg" alt="close" id="closeauth" class="w-[26px] md:w-[32px] cursor-pointer absolute right-4" />

                    <div class="w-[fit-content] flex items-center mx-auto gap-10 tab">
                        <button class="tablinks text-[16px] font-['Open Sans'] font-medium" onclick="openTab(event, 'SignUp')" id="defaultOpen">Create an account</button>
                        <button class="tablinks text-[16px] font-['Open Sans'] font-medium" onclick="openTab(event, 'SignIn')">Sign In</button>
                    </div>

                    <div id="SignUp" class="tabcontent">
                        <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Welcome to Victosah Solution</h3>

                        <form class="flex flex-col gap-4 pt-4">
                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    placeholder="Enter your email address"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Password
                                </label>

                                <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                    <input
                                        type="password"
                                        placeholder="Enter your password"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                    <img src="../assets/global/eye.svg" class="w-[24px] cursor-pointer" />
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
                                        placeholder="Confirm your password"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                    <img src="../assets/global/eye-slash.svg" class="w-[24px] cursor-pointer" />
                                </div>
                            </div>
                            <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Password doesn’t match</p>
                            <span class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center" id="rvBtn">Create an account</span>

                        </form>

                        <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
                            Or
                        </p>

                        <div class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                            <img src="../assets/global/google.svg" class="w-[20px]" />
                            <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#262626] py-3">
                                Create an account with Google
                            </p>
                        </div>


                    </div>

                    <div id="SignIn" class="tabcontent">
                        <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Welcome Back!</h3>

                        <section id="dangeralert" class="flex flex-col items-center w-full bg-[#FDECEC] shadow-lg mt-2 py-3 px-4 rounded relative overflow-hidden">
                            <div class="h-[100%] w-[5px] bg-[#EE3F3F] absolute left-0 top-0"></div>
                            <div class="flex items-center gap-2 mr-auto">
                                <img src="../assets/global/canceldanger.svg" id="closedangeralert" alt="Cancel danger alert" class="w-[24px] cursor-pointer" />
                                <p class="text-[16px] md:text-[17px]  text-[#2C2C2C] w-full font-Satoshi font-medium">
                                    Incorrect details
                                </p>
                            </div>
                            <p class="text-[13px] md:text-[14px] text-start w-full font-Satoshi font-regular text-[#7F7F7F] mt-2 ml-[3rem] pr-3 ">
                                Your email or password is incorrect. Try again
                            </p>
                        </section>

                        <form class="flex flex-col gap-4 pt-4">
                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    placeholder="Enter your email address"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <label
                                    htmlFor="firstname"
                                    class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                    Password
                                </label>

                                <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                    <input
                                        type="password"
                                        placeholder="Enter your password"
                                        class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                    <img src="./assets/global/eye.svg" class="w-[24px] cursor-pointer" />
                                </div>
                            </div>

                            <p id="openPassordRqMail" class='text-[14px] font-["Open Sans] text-[#1A237E] font-regular  cursor-pointer'>Forgot Password?</p>

                            <a href="../user/profile.php" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Sign In</a>

                        </form>

                        <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
                            Or
                        </p>

                        <div class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                            <img src="../assets/global/google.svg" class="w-[20px]" />
                            <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#262626] py-3">
                                Create an account with Google
                            </p>
                        </div>


                    </div>


                    ​
                </div>

            </div>


            <div id="regVerify" class="modal verify">
                <div class="modal-content overflow-hidden p-4">
                    <img src="../assets/global/back.svg" alt="back" id="backtoreg" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Let us verify it’s you
                    </p>
                    <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Enter the 4 digit code sent to golibe.f@gmail.com to create your account
                    </p>



                    <form class="w-full mt-[1rem] flex items-center flex-col">


                        <div class="w-[fit-content] flex items-center gap-3 mx-auto">
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

                            id="regsuccessbtn"
                            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                            Verify me
                        </span>
                    </form>

                    <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-center">
                        Resend code in <span class="text-[#1A237E]">23sec</span>
                    </p>
                </div>
            </div>


            <div id="regSuccess" class="modal regsuccess">
                <div class="modal-content overflow-hidden p-4 flex flex-col items-center">

                    <img src="../assets/global/success.svg" class="mx-auto w-[120px]" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Account Creation Successful
                    </p>
                    <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Welcome aboard! Your account has been created successfully. Start exploring and enjoy shopping with us.
                    </p>




                    <span class="text-center w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer"
                        id="closeregsucces">
                        Continue Shopping
                    </span>
                    </form>


                </div>
            </div>


            <!-- The logout modal -->

            <div id="logout" class="modal logout">
                <div class="modal-content overflow-hidden px-5 py-10">
                    <img src="../assets/global/close-circle.svg" alt="close" id="closelogout" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4" />

                    <p class="text-[#EE3F3F] font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Log Out
                    </p>
                    <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Come back soon! We’ll be here when you’re ready to shop again.
                    </p>




                    <button class="w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#EE3F3F] text-white rounded-[8px] mt-10 cursor-pointer"
                        id="closelogout">
                        Log Out
                    </button>
                    </form>


                </div>
            </div>


            <!-- ========================  The Password reset moal  starts ======================== -->
            <div id="passwordRequestMail" class="modal password-request-mail">
                <div class="modal-content overflow-hidden p-4">
                    <img src="../assets/global/back.svg" id="backtologin" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Forgot Password?
                    </p>
                    <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Enter the email you used in creating an account
                    </p>



                    <form class="w-full mt-[1rem] flex flex-col">


                        <div class="flex flex-col gap-1">
                            <label
                                htmlFor="firstname"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Email
                            </label>
                            <input
                                type="email"
                                placeholder="Enter your email address"
                                class="w-full  font-['Open Sans'] bg-transparent outline-none  border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]" />
                        </div>


                        <span
                            id="openPassordRqV"
                            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                            Continue
                        </span>
                    </form>


                </div>
            </div>
            <!-- ========================  The Password reset moal  ends ======================== -->



            <!-- ========================  The Password reset moal  starts ======================== -->
            <div id="passwordRequestverify" class="modal password-request-verify">
                <div class="modal-content overflow-hidden p-4">
                    <img src="../assets/global/back.svg" id="backtomail" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Let us verify it’s you
                    </p>
                    <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Enter the 4 digit code sent to golibe.f@gmail.com to create your account
                    </p>



                    <form class="w-full mt-[1rem] flex flex-col items-center">


                        <div class="w-[fit-content] flex items-center gap-3 mx-auto">
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
                            id="openPasswordRequestNP"
                            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                            Verify me
                        </span>
                    </form>

                    <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-center">
                        Didn't get code? <span class="text-[#1A237E] font-medium cursor-pointer">Resend </span>
                    </p>
                </div>
            </div>
            <!-- ========================  The Password reset moal  ends ======================== -->



            <!-- ========================  The Enter new pasword modal  starts ======================== -->
            <div id="passwordRequestNP" class="modal password-request-np">
                <div class="modal-content overflow-hidden p-4">
                    <img src="./assets/global/back.svg" id="backtoprverify" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Reset Password
                    </p>
                    <p class="w-[75%] md:w-[57%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        Enter your new password
                    </p>



                    <form class="w-full mt-[1rem] flex flex-col gap-4">


                        <div class="flex flex-col gap-1">
                            <label
                                htmlFor="firstname"
                                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                                Password
                            </label>

                            <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                <input
                                    type="password"
                                    placeholder="Enter your password"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                <img src="./assets/global/eye.svg" class="w-[24px] cursor-pointer" />
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
                                    placeholder="Confirm your password"
                                    class="w-full  font-['Open Sans'] bg-transparent outline-none   font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
                                <img src="../assets/global/eye-slash.svg" class="w-[24px] cursor-pointer" />
                            </div>
                        </div>


                        <span
                            id="openpasswordresetsuccess"
                            class="text-center mx-auto w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer">
                            Reset Password
                        </span>
                    </form>


                </div>
            </div>
            <!-- ========================  The The Enter new pasword modal   ends ======================== -->


            <div id="passwordresetsuccess" class="modal passwordresetsuccess">
                <div class="modal-content overflow-hidden p-4 flex flex-col items-center">

                    <img src="../assets/global/success.svg" class="mx-auto w-[120px]" />

                    <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
                        Password Reset Successful
                    </p>
                    <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
                        You have successfully reset your password
                    </p>




                    <span class="text-center w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer"
                        id="closeprsucces">
                        Sign In
                    </span>
                    </form>


                </div>
            </div>

        </header>
        <!-- ========================  The header  ends ======================== -->



        <!-- ========================  The options  starts ======================== -->
        <section class="w-full  py-4 border-b-[1px] border-[#E1E1E1]">
            <div class="w-[90%] mx-auto hidden  md:flex items-center justify-between">
                <div class="flex items-center gap-10">

                    <div class="custom-dropdown">

                        <div class="flex items-center gap-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Duvets</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div onclick="selectOption(this)">Option 1</div>
                                    <div onclick="selectOption(this)">Option 2</div>
                                    <div onclick="selectOption(this)">Option 3</div>
                                </div>
                                <div>
                                    <div onclick="selectOption(this)">Option 4</div>
                                    <div onclick="selectOption(this)">Option 5</div>
                                    <div onclick="selectOption(this)">Option 6</div>
                                </div>
                                <div>
                                    <div onclick="selectOption(this)">Option 7</div>
                                    <div onclick="selectOption(this)">Option 8</div>
                                    <div onclick="selectOption(this)">Option 9</div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="custom-dropdown">

                        <div class="flex items-center gap-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Bedsheets</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div onclick="selectOption(this)">Option 1</div>
                                    <div onclick="selectOption(this)">Option 2</div>
                                    <div onclick="selectOption(this)">Option 3</div>
                                </div>
                                <div>
                                    <div onclick="selectOption(this)">Option 4</div>
                                    <div onclick="selectOption(this)">Option 5</div>
                                    <div onclick="selectOption(this)">Option 6</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom-dropdown">

                        <div class="flex items-center gap-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Foams</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div onclick="selectOption(this)">Option 1</div>
                                    <div onclick="selectOption(this)">Option 2</div>
                                    <div onclick="selectOption(this)">Option 3</div>
                                </div>
                                <div>
                                    <div onclick="selectOption(this)">Option 4</div>
                                    <div onclick="selectOption(this)">Option 5</div>
                                    <div onclick="selectOption(this)">Option 6</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">

                        <div class="flex items-center gap-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Pillows</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div onclick="selectOption(this)">Option 1</div>
                                    <div onclick="selectOption(this)">Option 2</div>
                                    <div onclick="selectOption(this)">Option 3</div>
                                </div>
                                <div>
                                    <div onclick="selectOption(this)">Option 4</div>
                                    <div onclick="selectOption(this)">Option 5</div>
                                    <div onclick="selectOption(this)">Option 6</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="custom-dropdown">

                        <div class="flex items-center gap-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Lightings</span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div onclick="selectOption(this)">Option 1</div>
                                    <div onclick="selectOption(this)">Option 2</div>
                                    <div onclick="selectOption(this)">Option 3</div>
                                </div>
                                <div>
                                    <div onclick="selectOption(this)">Option 4</div>
                                    <div onclick="selectOption(this)">Option 5</div>
                                    <div onclick="selectOption(this)">Option 6</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="flex items-center gap-2 cursor-pointer">
                    <img src="../assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
                    <a href="../user/orders.php" class='text-[13px] md:text-[14px] font-["Open Sans] text-[#1A237E] font-medium underline'>Track your order</a>
                </div>

            </div>

            <div class="w-[90%] mx-auto flex items-center gap-10  md:hidden">
                <img src="../assets/global/menu.svg" alt="menu" class="cursor-pointer w-[24px]" onclick="openMobileMenu()" />


                <!-- The mobile nav starts -->
                <div id="menuNav" class="dropdown-menu border-t-[1px] border-[#E1E1E1] bg-white">

                    <div class="w-[92%] mx-auto">
                        <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626]  font-['Open Sans'] font-medium">Duvets</button>
                        <div class="menufaqext text-[16px] font-regular text-[#262626] flex flex-col gap-3">
                            <p>Duvet type</p>
                            <p>Duvet type</p>
                            <p>Duvet type</p>
                            <p>Duvet type</p>
                        </div>

                        <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626]  font-['Open Sans'] font-medium">Bedsheets</button>
                        <div class="menufaqext text-[16px] font-regular text-[#262626] flex flex-col gap-3">
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                        </div>

                        <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626]  font-['Open Sans'] font-medium">Foams</button>
                        <div class="menufaqext text-[16px] font-regular text-[#262626] flex flex-col gap-3">
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                        </div>

                        <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626]  font-['Open Sans'] font-medium">Pillows</button>
                        <div class="menufaqext text-[16px] font-regular text-[#262626] flex flex-col gap-3">
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                        </div>

                        <button class="menu-accordion cursor-pointer w-full flex items-center justify-between border-b-[1px] border-[#E1E1E1] pb-[1px] text-[15px] md:text-[16px] text-[#262626]  font-['Open Sans'] font-medium">Lightings</button>
                        <div class="menufaqext text-[16px] font-regular text-[#262626] flex flex-col gap-3">
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                            <p>Bedsheets type</p>
                        </div>

                        <div class="flex items-center gap-2 cursor-pointer pt-4">
                            <img src="../assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
                            <strong class='text-[13px] md:text-[14px] font-["Open Sans] text-[#1A237E] font-medium underline'>Track your order</strong>
                        </div>

                    </div>

                </div>
                <!-- The mobile nav ends -->


                <div class="w-[100%]  flex items-center  items-center gap-0">
                    <div class="w-full flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="../assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
                </div>
            </div>
        </section>
        <!-- ========================  The options  ends ======================== -->

        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Favorite</span>
                </div>
            </div>
        </section>


        <div class="w-full bg-[#FFFFFF] py-5">
            <div class="w-[90%] mx-auto hidden md:block">


                <table cols="" class="w-full">
                    <thead class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Add</th>
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
                                <button type="submit" class="py-1 px-4 bg-[#262626] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out of Stock</button>
                            </td>

                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]">Add to Cart</button>
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
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                            </td>

                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]">Add to Cart</button>
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
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                            </td>

                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]">Add to Cart</button>
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
                                <button type="submit" class="py-1 px-4 bg-[#39D959] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                            </td>

                            <td>
                                <button type="submit" class="py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[4px]">Add to Cart</button>
                            </td>


                            <td>
                                <p class='text-[15px] md:text-[16px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from cart</p>
                            </td>

                        </tr>

                    </tbody>
                </table>



            </div>



            <div class="w-[90%] mx-auto  md:hidden">
                <div class="w-full flex flex-col gap-4">

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">


                        <div class="flex items-center justify-between">
                            <button type="submit" class="max-w-[87px] py-[6px] px-3 bg-[#39D959] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
                            <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from favorite</p>
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
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Quantity:</b>1</p>
                                    </div>

                                </div>

                                <button type="submit" class="w-[fit-content] rounded-[4px] py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointerprounded-[8px]">Add to Cart</button>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>



                    </div>

                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2">


                        <div class="flex items-center justify-between">
                            <button type="submit" class="min-w-[87px] py-[6px] px-3 bg-[#262626] text-white text-[14px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out of Stock</button>
                            <p class='text-[14px] font-["Open Sans] text-[#EE3F3F] font-regular underline cursor-pointer'>Remove from favorite</p>
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
                                        <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular"><b>Quantity:</b>1</p>
                                    </div>

                                </div>

                                <button type="submit" class="w-[fit-content] rounded-[4px] py-1 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointerprounded-[8px]">Add to Cart</button>
                            </div>

                            <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>



                    </div>

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
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="../details/about-us.php" class="text-[#777777]">About Us</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="../user/orders.php" class="text-[#777777]">Track Your Order</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="../details//refund-and-return-policy.php" class="text-[#777777]">Return Policy</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="../details/contact-us.php" class="text-[#777777]">Contact Us</a></li>

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


    <script src="../functions/modals.js"></script>
    <script src="../functions/modals2.js"></script>
    <script src="../functions/functions.js"></script>
    <script src="../functions/tabs.js"></script>
    <script type="text/javascript" src="../functions/accordion.js"></script>
    <script type="text/javascript" src="../functions/faq.js"></script>
    <script type="text/javascript" src="../functions/dropdown.js"></script>

</body>

</html