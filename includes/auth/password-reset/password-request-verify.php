<!-- ========================  The Password reset moal  starts ======================== -->
<div id="passwordRequestverify" class="modal password-request-verify">
    <div class="modal-content overflow-hidden p-4">
        <img src="<?php echo DOMAIN; ?>/assets/global/back.svg" id="backtomail" alt="back" class="w-[26px] md:w-[32px] absolute left-4 cursor-pointer" />

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

