
<!-- ========================  The Password reset moal  starts ======================== -->
<div id="passwordRequestMail" class="modal password-request-mail">
    <div class="modal-content overflow-hidden p-4">
        <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" id="backtologin" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

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