       <!-- ========================  The options  starts ======================== -->
       <section class="w-full  py-4 border-b-[1px] border-[#E1E1E1]">
            <div class="w-[90%] mx-auto hidden  md:flex items-center justify-between">
                <div class="flex items-center gap-10">

                    <div class="custom-dropdown">

                        <div class="flex items-center gap-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Duvets</span>
                            <img src="/victosah/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
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
                            <img src="/victosah/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
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
                            <img src="/victosah/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
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
                            <img src="/victosah/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
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
                            <img src="/victosah/assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
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


                <?php if (isAuthenticated()): ?>
                <div class="flex items-center gap-2 cursor-pointer">
                    <img src="/victosah/assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
                    <a href="./user/orders.php" class='text-[13px] md:text-[14px] font-["Open Sans] text-[#1A237E] font-medium underline'>Track your order</a>
                </div>
                <?php else: ?>
                    <?php endif; ?>

            </div>

            <div class="w-[90%] mx-auto flex items-center gap-10  md:hidden">
                <img src="/victosah/assets/global/menu.svg" alt="menu" class="cursor-pointer w-[24px]" onclick="openMobileMenu()" />


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
                        <?php if (isAuthenticated()): ?>
    <div class="flex items-center gap-2 cursor-pointer pt-4">
        <img src="/victosah/assets/home/truck-fast.svg" class='w-[24px] h-[24px]' />
        <strong class='text-[13px] md:text-[14px] font-["Open Sans"] text-[#1A237E] font-medium underline'>Track your order</strong>
    </div>
<?php else: ?>
<?php endif; ?>

                    </div>

                </div>
                <!-- The mobile nav ends -->


                <div class="w-[100%]  flex items-center  items-center gap-0">
                    <div class="w-full flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <img src="/victosah/assets/global/search.svg" alt="Search" class="w-[24px]" />
                        <input type="text" placeholder="What are you shopping for?" class="w-full text-[14px] border-none outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#1A237E] text-[#FBFBFB] text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px]">Search</button>
                </div>
            </div>
            
        </section>
        <!-- ========================  The options  ends ======================== -->

       <script>
        
//    The options starts

document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    toggle.addEventListener("click", function(event) {
        event.stopPropagation();
        document.querySelectorAll(".custom-dropdown").forEach((dd) => {
            if (dd !== dropdown) dd.classList.remove("open");
        });
        dropdown.classList.toggle("open");
    });
});

document.addEventListener("click", function() {
    document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
        dropdown.classList.remove("open");
    });
});

function selectOption(element) {
    let dropdown = element.closest(".custom-dropdown");
    let toggle = dropdown.querySelector(".dropdown-toggle");
    toggle.innerText = element.innerText;
    dropdown.classList.remove("open");
}


// The select options ends 
       </script>

