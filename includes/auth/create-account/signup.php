<div id="SignUp" class="tabcontent">
    <h3 class="text-[#262626] text-center text-[20x] md:text-[24px] font-['Open Sans'] font-medium">Welcome to Victosah Solution</h3>

    <form action="<?php echo DOMAIN; ?>/includes/auth/create-account/send.php" method="POST" class="flex flex-col gap-4 pt-4" id="userCreationForm">

        <div class="flex flex-col gap-1">
            <label
                for="email"
                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                Email
            </label>
            <input
                type="email"
                name="email"
                id="email"
                placeholder="Enter your email address"
                class="w-full font-['Open Sans'] bg-transparent outline-none border-[1px] border-[#E1E1E1] font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px] rounded-[8px]"
                required
                />
        </div>

        <div class="flex flex-col gap-1">
            <label
                for="password"
                class="font-['Open Sans'] text-[15px] md:text-[16px] font-medium text-[#262626]">
                Password
            </label>

            <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter your password"
                    class="w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]"
                    required
                     />
                <img src="<?php echo DOMAIN; ?>/assets/global/eye.svg" class="w-[24px] cursor-pointer toggle-password" data-target="password" />
            </div>
        </div>


          <input
                    type="hidden"
                    name="otp"
                    id="otp"
                 
                    class="hidden w-full font-['Open Sans'] bg-transparent outline-none font-regular text-[#2C2C2C] placeholder:text-[#D9D9D9] py-[10px] px-2 text-[14px] md:text-[16px]" />
        
                    <input type="hidden" name="send" value="1">
                    <input type="hidden" name="subject" value="Receive OTP">
        <p id="passwordError" class='text-[14px] font-["Open Sans"] text-[#EE3F3F] font-regular underline cursor-pointer' style="display: none;">Password doesn't match</p>
        
       
       
        <button type="submit" class="w-full py-[8px] px-3 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px] text-center">Create an account</button>

   

    </form>

    <p class="text-center font-['Open Sans'] text-[17px] md:text-[18px] font-regular text-[#7A7A7A] py-3">
        Or
    </p>

    <div class="cursor-pointer flex items-center justify-center gap-2 border-[1px] border-[#E1E1E1] rounded-[8px] pr-3">
        <img src="<?php echo DOMAIN; ?>/assets/global/google.svg" class="w-[20px]" />
        <p class="text-center font-['Open Sans'] text-[15px] md:text-[16px] font-regular text-[#262626] py-3">
            Create an account with Google
        </p>
    </div>
</div>



<script>
function generateRandomNumber(){
    let min = 1000;
    let max = 9999;

    let randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;

    let lastGeneratedNumber = localStorage.getItem('lastGeneratedNumber');
    while(randomNumber === parseInt(lastGeneratedNumber)){
        randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
    }

    localStorage.setItem('lastGeneratedNumber', randomNumber);
    return randomNumber;
}

document.getElementById('otp').value = generateRandomNumber();

</script>