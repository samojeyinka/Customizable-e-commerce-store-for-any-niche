<?php
require_once __DIR__ . '/auth/auth.php';



// Get current user if logged in
$user = isAuthenticated() ? getCurrentUser() : null;

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}
?> 

<header class="w-full flex items-center justify-center py-3 md:py-3.5 relative z-30 border-b border-[#262626]/[0.07]" style="background-color: var(--glor-tint, #F5EEF2)">
    <?php echo store_theme_style(); ?>


    <!-- The nav and sidemenu -->
    <?php

    include(__DIR__ . '/navigation/sidemenu.php');
    include(__DIR__ . '/navigation/nav.php');

    ?>


    ​
    <!-- The Modal -->
    <div id="myModal" class="modal reg">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-6 md:p-8 rounded-[20px] md:max-w-[440px] max-h-[88vh] overflow-y-auto">

            <i class="fa-solid fa-xmark text-[22px] md:text-[24px] text-[#262626]/35 hover:text-[#262626] cursor-pointer absolute top-5 right-5 leading-none transition-colors" id="closeauth" alt="close"></i>

            <div class="flex items-center justify-center mx-auto w-fit tab mb-6">
                <button class="tablinks text-[12px] md:text-[13px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold pb-2 px-2 text-[#262626]/40 hover:text-[#262626] transition-colors" onclick="openTab(event, 'SignUp')" id="defaultOpen">Create an account</button>
                <span class="w-px h-4 bg-[#262626]/10 mx-5"></span>
                <button class="tablinks text-[12px] md:text-[13px] tracking-[0.18em] uppercase font-['Montserrat'] font-semibold pb-2 px-2 text-[#262626]/40 hover:text-[#262626] transition-colors" onclick="openTab(event, 'SignIn')" id="signinTab">Sign In</button>
            </div>

            <!-- The signup and signin directory -->
            <?php
            include(__DIR__ . '/auth/create-account/signup.php');
            include(__DIR__ . '/auth/login/signinmain.php');
         
            ?>
            
            ​
        </div>

    </div>


    <!-- The verify email during registration and success directory -->
    <?php
    // include('./includes/auth/create-account/success.php');
    ?>




    <!-- The logout directory -->
    <?php
    // include('./includes/auth/logout.php');
    include(__DIR__ . '/auth/logout.php');

    

    ?>




</header>




   

<script>
    // Get the modal and close button
    var modal = document.getElementById("myModal");
    var close = document.getElementById("closeauth");
    
    // Get all buttons with the class 'modal-open-btn'
    var btns = document.getElementsByClassName("modal-open-btn");
    
    // Add click event to each button
    for (var i = 0; i < btns.length; i++) {
        btns[i].onclick = function() {
            modal.style.display = "block";
        }
    }
    
    // Open the auth modal directly on the requested tab (SignIn / SignUp)
    function openAuthModal(which) {
        var tabBtn = which === 'SignIn' ? document.getElementById('signinTab') : document.getElementById('defaultOpen');
        modal.style.display = "block";
        if (tabBtn) {
            openTab({ currentTarget: tabBtn }, which);
        }
    }
    
    // Close button functionality
    close.onclick = function() {
        modal.style.display = "none";
    }
    
    // Optional: Close when clicking outside the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php include __DIR__ . '/cart-drawer.php'; ?>


