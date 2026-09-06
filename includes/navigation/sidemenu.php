<?php
// Check if the current URL contains '/includes/' or is served from within the includes directory
$isIncludesPath = strpos($_SERVER['REQUEST_URI'], '/includes/') !== false;

// Side menu implementation with conditional links
?>
<div id="sidemenu" class="sidemenu-content border-[1px] border-[#E1E1E1] bg-white">
    <div class="relative flex flex-col">
        <?php if (isAuthenticated()): ?>
            <a href="<?php echo DOMAIN; ?>/user/profile.php" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#262626] font-['Open Sans']">
                <i class="fa-solid fa-user text-[16px] text-[#262626] leading-none"></i>
                <span>My Profile</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/user/orders.php" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#262626] font-['Open Sans']">
                <i class="fa-solid fa-receipt text-[16px] text-[#262626] leading-none"></i>
                <span>My Orders</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/user/my-issues.php" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#262626] font-['Open Sans']">
                <i class="fa-solid fa-circle-exclamation text-[16px] text-[#262626] leading-none"></i>
                <span>My Issues</span>
            </a>

            <a href="<?php echo DOMAIN; ?>/user/returns.php" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#262626] font-['Open Sans']">
                <i class="fa-solid fa-rotate-left text-[16px] text-[#262626] leading-none"></i>
                <span>Returned Items</span>
            </a>

            <a id="logooutBtn" class="menulink flex items-center gap-2 text-[16px] font-regular text-[#EE3F3F] font-['Open Sans']">
                <i class="fa-solid fa-right-from-bracket text-[16px] text-[#EE3F3F] leading-none"></i>
                <span>Log Out</span>
            </a>
        <?php else: ?>
            <?php if ($isIncludesPath): ?>
                <!-- Direct links for pages in the /includes/ path -->
                <a href="../login/signin.php" class="menulink text-[16px] font-regular text-[#262626] font-['Open Sans']">Sign In</a>
                <a href="../create-account/sign-up.php" class="menulink text-[16px] font-regular text-[#262626] font-['Open Sans']">Create an Account</a>
            <?php else: ?>
                <!-- Modal triggers for other pages -->
                <p class="menulink text-[16px] font-regular text-[#262626] font-['Open Sans'] cursor-pointer" onclick="openAuthModal('SignIn')">Sign In</p>
                <p class="menulink text-[16px] font-regular text-[#262626] font-['Open Sans'] cursor-pointer" onclick="openAuthModal('SignUp')">Create an Account</p>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isAuthenticated()): ?>
            <div class="w-[1.5rem] h-[1.5rem] border-l-[1px] border-t-[1px] border-[#E1E1E1] bg-white absolute top-[-13px] right-[66px] rotate-[45deg] z-1"></div>
        <?php else: ?>
            <div class="w-[2rem] h-[2rem] border-l-[1px] border-t-[1px] border-[#E1E1E1] bg-white absolute top-[-16px] right-[66px] rotate-[45deg] z-1"></div>
        <?php endif; ?>
    </div>
</div>

<div id="logout" class="modal logout">
    <div class="modal-content overflow-hidden px-5 py-10 flex flex-col">
        <i class="fa-solid fa-xmark text-[26px] md:text-[32px] text-[#262626] cursor-pointer absolute top-10 right-4 leading-none" id="closelogout" alt="close"></i>
        
        <p class="text-[#EE3F3F] font-['Open Sans'] text-[19px] text-[24px] font-medium text-center">
            Log Out
        </p>
        <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-center md:text-[16px] font-['Open Sans'] font-regular text-[#777777] mt-2">
            Come back soon! We'll be here when you're ready to shop again.
        </p>
        <a class="w-full text-center text-[16px] font-regular font-Satoshi py-2 px-6 bg-[#EE3F3F] text-white rounded-[8px] mt-10 cursor-pointer" href="?logout=1">
            Log Out
        </a>
    </div>
</div>

<script>
    var sidemenu = document.getElementById("sidemenu");
    var lobtn = document.getElementById("logooutBtn");
    var logout = document.getElementById("logout");
    var closelogout = document.getElementById("closelogout");
    
    if (lobtn) {
        lobtn.onclick = function() {
            logout.style.display = "block";
            sidemenu.style.display = "none";
        }
    }
    
    if (closelogout) {
        closelogout.onclick = function() {
            logout.style.display = "none";
        }
    }
</script>