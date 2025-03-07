<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | About Us</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/tabs.css">
    <link rel="stylesheet" href="./details.css">
</head>

<body>
    <main class="bg-[#FEFEFE]">
        <header class="w-full bg-[#E8E9F2] flex items-center justify-center p-3">
            <nav class="w-[90%] flex items-center justify-between">
                <div class="flex items-center gap-1 md:gap-2">
                    <img src="../assets/global/logo.svg" alt="VICTOSAH" class="w-[31.35px] md:w-[41.35px]" />
                    <h1 class="text-[20px] md:text-[24px] font-Onest font-semibold">VICTOSAH</h1>
                </div>
                <div class="hidden md:flex items-center gap-0">
                    <div class="flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="../assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="lg:w-[18rem] text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
                </div>
                <div class="flex items-center gap-6">
                    <a href="../products/cart.php">
                        <img src="../assets/global/bag.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>
                    <a href="#">
                        <img src="../assets/global/lovely.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>

                    <a href="#" id="myBtn">
                        <img src="../assets/global/profile.svg" class="w-[22px] md:w-[24px]" alt="bag" />
                    </a>

                </div>
            </nav>


            ​

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
                            <button type="submit" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]" id="rvBtn">Create an account</button>

                        </form>

                        <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
                                    Or
                                </p>

                                <div class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                    <img src="../assets/global/google.svg" class="w-[20px]"/>
                                    <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#262626] py-3">
                                    Create an account with Google
                                </p>
                                    </div>


                    </div>

                    <div id="SignIn" class="tabcontent">
                        <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Welcome Back!</h3>

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

                            <p id="openPassordRq" class='text-[14px] font-["Open Sans] text-[#1A237E] font-regular  cursor-pointer'>Forgot Password?</p>
                            
                            <a href="../user/profile.php" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Sign In</a>

                        </form>

                        <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
                                    Or
                                </p>

                                <div class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                                    <img src="./assets/global/google.svg" class="w-[20px]"/>
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

    

              <form class="w-full mt-[1rem]" >
                

              <div class="w-[fit-content] flex items-center gap-3 mx-auto">
              <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                    
                  />
                  <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                    
                  />
                  <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                    
                  />
                  <input
                    type="password"
                    inputMode="numeric"
                    class="w-[40px] h-[40px] rounded-[4px] border-[1px] text-center bg-[#FFFFFF] border-[#E1E1E1] rounded text-[#262626] py-3 px-1 outline-none"
                    
                  />
              </div>
                 
                 
                <button
                  type="submit"
        id="regsuccessbtn"
                  class="w-full text-[18px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer"
                
                >
                Verify me
                </button>
              </form>

              <p class="text-[#777777] text-[15px] font-['Open Sans'] font-[400] mt-3 text-center">
                Resend code in <span class="text-[#1A237E]">23sec</span>
              </p>
            </div>
            </div>


            <div id="regSuccess" class="modal regsuccess">
          <div class="modal-content overflow-hidden p-4">

          <img src="./assets/global/success.svg" class="mx-auto w-[120px]"/>
         
              <p class="font-['Open Sans']  text-[19px] text-[24px] font-medium text-center">
              Account Creation Successful
              </p>
              <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
              Welcome aboard! Your account has been created successfully. Start exploring and enjoy shopping with us.
              </p>

    
                 
                 
                <button class="w-full text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#1A237E] text-white rounded-[8px] mt-10 cursor-pointer"
                id="closeregsucces"
                >
                Continue Shopping
                </button>
              </form>

        
            </div>
            </div>
           

        </header>

        <div class="details-sec aboutus  flex items-center justify-center">
        <h3 class="text-white text-center text-[30px] md:text-[60px] font-['Open Sans'] font-medium text-center">About Us</h3>
        </div>






<div class="w-[90%] mx-auto flex flex-col-reverse md:flex-row gap-5 py-5">
    <div class="text-[#262626]  text-[15px] md:text-[16px] font-['Open Sans'] font-medium-regular">
    At Victosah Solutions, we believe your home should be a sanctuary—where comfort meets elegance. Specializing in luxury bedding and home interiors, we craft thoughtfully designed products that enhance your living spaces with warmth, style, and relaxation.<br/>

From premium-quality bed linens and plush duvets to elegant home décor pieces, our collection is made from the finest materials to ensure durability, comfort, and timeless appeal. Whether you’re transforming your bedroom into a cozy retreat or elevating your home’s ambiance, our designs seamlessly blend beauty with functionality.<br/>

With a commitment to quality, sustainability, and innovation, we take pride in offering eco-friendly, hypoallergenic, and meticulously crafted home essentials. Our mission is simple: to bring you the best in comfort and design so you can create a home you truly love.
    </div>

    <img src="../assets/global/avs.svg" class="w-[50%]"/>
</div>

    


        <section class="w-full py-[4rem]">
            <div class="w-[90%] m-auto">

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-[40%]">
                        <img src="../assets/home/Union.svg" />
                    </div>
                    <div class="w-full md:w-[60%]">
                        <h3 class="text-[#0E1345] text-center text-[25px] md:text-[30px] font-['Montserrat'] font-medium">Why Choose Victosah Solution</h3>

                        <div class="w-full md:w-[90%] grid grid-cols-1 md:grid-cols-2 gap-3 mt-10">

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/validation.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Premium Quality Materials</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>We use high-thread-count cotton, moisture-wicking bamboo, and other luxurious fabrics for superior comfort.</p>
                                </div>
                            </div>


                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/24-support.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Durability & Easy Care</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>Our bedding is designed to last, with easy-to-maintain, fade-resistant fabrics.  We prioritize eco-friendly materials and responsible production</p>
                                </div>
                            </div>

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/truck-fast.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Fast & Reliable Delivery</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>We ensure quick and reliable delivery, so your order arrives on time and in perfect condition.</p>
                                </div>
                            </div>

                           

                            <div class="border-[1px] border-[#E7E7E7] px-2 md:px-3 py-4 rounded-[16px] flex gap-3">
                                <img src="../assets/home/validation.svg" class='w-[30px] h-[30px]' />

                                <div class="flex flex-col gap-2">
                                    <strong class='text-[16px] md:text-[18px] font-["Open Sans] text-[#262626] font-medium'>Stylish & Versatile Designs</strong>
                                    <p class='text-[14px] md:text-[14px] text-[#777777] font-regular'>From modern minimalism to classic elegance, our bedding suits every home style.</p>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>

            </div>
        </section>

        <section class="w-full pb-10">
            <div class="w-[90%] m-auto">
                <h3 class="text-[#0E1345] text-center text-[22px] md:text-[30px] font-['Montserrat'] font-medium">Our Bedding Collection</h3>

                <div class="flex flex-col md:flex-row items-center gap-4 mt-8">
                    <div class="w-full md:w-[50%] h-[288.76px] md:h-[488.76px] rounded-[8px] overflow-hidden relative">
                        <img src="../assets/global/c1.svg" alt="Bed" class="w-full h-full object-cover" />
                        <p class='absolute left-20 md:left-10 bottom-10 md:bottom-7 text-[20px] md:text-[24px] text-white font-medium'>Blankets & Throws</p>
                        <p class='md:hidden absolute left-4 md:left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                        Perfect for layering, adding both warmth and style to your bedroom
                                </p>
                    </div>

                    <div class="flex flex-col gap-2 md:gap-4 w-full  md:w-[50%]">
                        <div class="w-full flex items-center gap-4">
                        <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/cl2.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px]  md:text-[18px] lg:text-nowrap text-white font-medium'>Bed Sheets & Pillowcases</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Made from soft, breathable fabrics like cotton, linen, and bamboo for a smooth, comfortable feel.
                                </p>
                            </div>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/c3.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute left-4 bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Pillows & Mattress Toppers</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Ergonomically designed for support and relaxation, catering to different sleep preferences.
                                </p>
                            
                            </div>
                        </div>

                        <div class="w-full flex items-center gap-4">
                        <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/c4.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute left-[25%] bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Lightnings</p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Made from soft, breathable fabrics like cotton, linen, and bamboo for a smooth, comfortable feel.
                                </p>
                            </div>
                            <div class="h-[180px] md:h-[235px] w-[50%] rounded-[8px] overflow-hidden relative">
                                <img src="../assets/global/c5.svg" alt="Bed" class="w-full object-cover h-full" />
                                <p class='absolute  left-3 bottom-17 md:bottom-4 text-[15px] md:text-[18px] lg:text-nowrap text-white font-medium'>Duvet Covers & Comforters </p>
                                <p class='md:hidden absolute left-1 bottom-3 text-[12px] md:text-[12px] text-center text-white font-regular'>
                                Designed for warmth and elegance, available in various textures and patterns to complement any décor.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

     

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
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="./about-us.php" class="text-[#777777]">About Us</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="./user/orders.php" class="text-[#777777]">Track Your Order</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="./refund-and-return-policy.php" class="text-[#777777]">Return Policy</a></li>
                        <li class="text-[15px] md:text-[16px] font-['Open Sans'] font-medium"><a href="./contact-us.php" class="text-[#777777]">Contact Us</a></li>

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
    <script src="./functions/modals.js"></script>
    <script src="./functions/modals2.js"></script>
    <script src="./functions/tabs.js"></script>

</body>

</html>