<!-- ========================  The Enter new pasword modal  starts ======================== -->
<div id="passwordRequestNP" class="modal password-request-np">
    <div class="modal-content overflow-hidden p-4">
        <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" id="backtoprverify" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

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
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye.svg" class="w-[24px] cursor-pointer" />
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
                    <img src="<?php echo DOMAIN; ?>/assets/global/eye-slash.svg" class="w-[24px] cursor-pointer" />
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
