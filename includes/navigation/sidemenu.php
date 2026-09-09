<?php
// Check if the current URL contains '/includes/' or is served from within the includes directory
$isIncludesPath = strpos($_SERVER['REQUEST_URI'], '/includes/') !== false;

// Side menu implementation with conditional links
?>
<div id="sidemenu" class="sidemenu-content border-[1px] border-[#EFE7EC] bg-white rounded-[14px] overflow-hidden shadow-[0_24px_60px_-28px_rgba(0,0,0,0.35)]">
    <div class="relative flex flex-col p-1.5">
        <?php if (isAuthenticated()): ?>
            <a href="<?php echo DOMAIN; ?>/user/profile.php" class="menulink flex items-center gap-2.5 text-[14px] text-[#262626] font-['Open Sans'] font-medium rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                <i class="fa-solid fa-user text-[14px] text-[#262626] leading-none w-4 text-center"></i>
                <span>My Profile</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/user/orders.php" class="menulink flex items-center gap-2.5 text-[14px] text-[#262626] font-['Open Sans'] font-medium rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                <i class="fa-solid fa-receipt text-[14px] text-[#262626] leading-none w-4 text-center"></i>
                <span>My Orders</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/user/my-issues.php" class="menulink flex items-center gap-2.5 text-[14px] text-[#262626] font-['Open Sans'] font-medium rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                <i class="fa-solid fa-circle-exclamation text-[14px] text-[#262626] leading-none w-4 text-center"></i>
                <span>My Issues</span>
            </a>

            <a href="<?php echo DOMAIN; ?>/user/returns.php" class="menulink flex items-center gap-2.5 text-[14px] text-[#262626] font-['Open Sans'] font-medium rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors">
                <i class="fa-solid fa-rotate-left text-[14px] text-[#262626] leading-none w-4 text-center"></i>
                <span>Returned Items</span>
            </a>

            <a id="logooutBtn" class="menulink flex items-center gap-2.5 text-[14px] font-['Open Sans'] font-medium rounded-[9px] text-[#EE3F3F] hover:bg-[#FDECEC] transition-colors">
                <i class="fa-solid fa-right-from-bracket text-[14px] text-[#EE3F3F] leading-none w-4 text-center"></i>
                <span>Log Out</span>
            </a>
        <?php else: ?>
            <?php if ($isIncludesPath): ?>
                <!-- Direct links for pages in the /includes/ path -->
                <a href="../login/signin.php" class="menulink text-[14px] font-['Open Sans'] font-medium text-[#262626] rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors px-3 py-2">Sign In</a>
                <a href="../create-account/sign-up.php" class="menulink text-[14px] font-['Open Sans'] font-medium text-[#262626] rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors px-3 py-2">Create an Account</a>
            <?php else: ?>
                <!-- Modal triggers for other pages -->
                <p class="menulink text-[14px] font-['Open Sans'] font-medium text-[#262626] rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors cursor-pointer px-3 py-2" onclick="openAuthModal('SignIn')">Sign In</p>
                <p class="menulink text-[14px] font-['Open Sans'] font-medium text-[#262626] rounded-[9px] hover:bg-[<?php echo store_color('color_tint'); ?>] hover:text-[<?php echo store_color('color_primary'); ?>] transition-colors cursor-pointer px-3 py-2" onclick="openAuthModal('SignUp')">Create an Account</p>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isAuthenticated()): ?>
            <div class="w-[1.5rem] h-[1.5rem] border-l-[1px] border-t-[1px] border-[#EFE7EC] bg-white absolute top-[-13px] right-[66px] rotate-[45deg] z-1"></div>
        <?php else: ?>
            <div class="w-[2rem] h-[2rem] border-l-[1px] border-t-[1px] border-[#EFE7EC] bg-white absolute top-[-16px] right-[66px] rotate-[45deg] z-1"></div>
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