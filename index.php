<?php
include(__DIR__ . '/config/connect.php');
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . '/includes/auth/auth.php';

// Get current user if logged in
$user = isAuthenticated() ? getCurrentUser() : null;
include(__DIR__ . '/config/products.php');

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}

require_once "./config/servername.php";
require_once "./includes/auth/google.php";


?> 


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include 'includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[#FEFEFE]">
            <!-- Add to Cart Toast Notification -->
<div id="cart-toast" class="hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-50 transition-opacity duration-300">
    Item added to your cart!
</div>
        <?php
      include(__DIR__ . '/includes/header.php');
      include(__DIR__ . '/includes/options.php');
      include(__DIR__ . '/includes/hero.php');
      include(__DIR__ . '/includes/categories.php');
    include(__DIR__ . '/products/product-lists.php');
    include(__DIR__ . '/includes/featured.php');
      include(__DIR__ . '/includes/why.php');
      include(__DIR__ . '/includes/intro.php');
      include(__DIR__ . '/includes/faq.php');
      include(__DIR__ . '/includes/reviews.php');
      include(__DIR__ . '/includes/footer.php');
        ?>
     
    </main>



  
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>

</body>

</html>