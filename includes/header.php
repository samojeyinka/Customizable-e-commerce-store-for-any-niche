<?php
require_once __DIR__ . '/auth/auth.php';



// Get current user if logged in
$user = isAuthenticated() ? getCurrentUser() : null;

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}
?> 

<header class="w-full bg-[#E8E9F2] flex items-center justify-center p-3">


    <!-- The nav and sidemenu -->
    <?php

    include(__DIR__ . '/navigation/sidemenu.php');
    include(__DIR__ . '/navigation/nav.php');

    ?>


    ​
    <!-- The Modal -->
    <div id="myModal" class="modal reg">
        <!-- Modal content -->
        <div class="modal-content overflow-hidden p-4">

            <img src="<?php echo DOMAIN; ?>/assets/global/close-circle.svg" alt="close" id="closeauth" class="w-[26px] md:w-[32px] cursor-pointer absolute right-4" />

            <div class="w-[fit-content] flex items-center mx-auto gap-10 tab">
                <button class="tablinks text-[16px] font-['Open Sans'] font-medium" onclick="openTab(event, 'SignUp')" id="defaultOpen">Create an account</button>
                <button class="tablinks text-[16px] font-['Open Sans'] font-medium" onclick="openTab(event, 'SignIn')">Sign In</button>
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
</script>


