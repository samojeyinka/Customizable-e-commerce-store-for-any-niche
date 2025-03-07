<?php
require_once __DIR__ . "/config/config.php";

// Include authentication utility
require_once __DIR__ . '/includes/auth/auth.php';

// Get current user if logged in
$user = isAuthenticated() ? getCurrentUser() : null;

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}
?> 


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/inputs.css">

</head>

<body>
    <main class="bg-[#FEFEFE]">
        <?php
      include(__DIR__ . '/includes/header.php');
      include(__DIR__ . '/includes/options.php');
      include(__DIR__ . '/includes/hero.php');
      include(__DIR__ . '/includes/categories.php');
      include(__DIR__ . '/includes/featured.php');
      include(__DIR__ . '/includes/why.php');
      include(__DIR__ . '/includes/intro.php');
      include(__DIR__ . '/includes/faq.php');
      include(__DIR__ . '/includes/reviews.php');
      include(__DIR__ . '/includes/footer.php');
        ?>
        <a href="#" class="fixed top-[55%] md:top-[70%] right-5 md:right-10">
            <img src="<?php echo DOMAIN; ?>/assets/global/whatsapp.svg" class="w-[50px] md:w-[60px] rounded-[50%] shadow-lg" />
        </a>
    </main>



  
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
</body>
</body>

</html>