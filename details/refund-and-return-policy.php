<?php
require_once __DIR__ . "/../config/config.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | Refund & Return Policy</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[#FEFEFE]">
    <?php
      include('../includes/header.php');
      include('../includes/options.php');
      ?>

        <div class="details-sec refund flex items-center justify-center">
            <h3 class="text-white text-center text-[30px] md:text-[60px] font-['Open Sans'] font-medium text-center">Refund & Return Policy</h3>
        </div>






        <div class="w-[90%] mx-auto max-w-[1440px] py-5">

            <div class="flex flex-col gap-4">
                <div class="">
                    <h2 class="text-[#262626]  text-[25px] md:text-[30px] font-['Open Sans'] font-medium">Refund & Return Policy</h2>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        At Glorefy, we are committed to providing high-quality beauty and skincare products. If you are not completely satisfied with your purchase, we offer a hassle-free return and refund process.
                    </p>
                </div>

                <div class="">
                    <h2 class="text-[#262626]  text-[22px] md:text-[30px] font-['Open Sans'] font-medium">Return Policy</h2>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • Returns are accepted within 7 days of the delivery date.
                    </p>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • Items must be unused, unwashed, and in their original packaging with all tags attached.
                    </p>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • Proof of purchase (receipt or order confirmation) is required for all returns.
                    </p>
                </div>

                <div class="">
                    <h2 class="text-[#262626]  text-[22px] md:text-[30px] font-['Open Sans'] font-medium">Non-Returnable Items</h2>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                    For hygiene and safety reasons, the following items cannot be returned or refunded:
                    </p>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • Opened or used cosmetics, skincare, and beauty products, unless the product is faulty or defective.
                    </p>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • Custom-made or personalized products.
                    </p>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • Items marked as final sale or clearance.
                    </p>
                </div>

                <div class="">
                    <h2 class="text-[#262626]  text-[22px] md:text-[30px] font-['Open Sans'] font-medium">Return process</h2>
                    <div class="flex flex-col md:flex-row md:items-center gap-3 py-4">
                        <div class="flex items-start  md:flex-col gap-2">
                        <i class="fa-solid fa-1 text-[35px] text-[#C2185B] leading-none"></i>    
                        <div class="flex flex-col gap-1">
                        <h2 class="text-[#262626]  text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Initiate a Return</h2>
                        <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        Contact our customer support team at <b class="text-[#C2185B]">support@glorefy.com</b> or WhatsApp <b class="text-[#C2185B]">+1 (212) 555-0147</b> to request a return.
                    </p>
                            </div>
                        </div>

                        <div class="flex items-start  md:flex-col gap-2">
                        <i class="fa-solid fa-2 text-[35px] text-[#C2185B] leading-none"></i>    
                        <div class="flex flex-col gap-1">
                        <h2 class="text-[#262626]  text-[16px] md:text-[18px] font-['Open Sans'] font-medium"> Prepare Your Return</h2>
                        <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                       
Securely pack the item in its original packaging and include proof of purchase.
                    </p>
                            </div>
                        </div>

                        <div class="flex items-start  md:flex-col gap-2">
                        <i class="fa-solid fa-3 text-[35px] text-[#C2185B] leading-none"></i>    
                        <div class="flex flex-col gap-1">
                        <h2 class="text-[#262626]  text-[16px] md:text-[18px] font-['Open Sans'] font-medium"> Ship the Item</h2>
                        <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                       
                        • Our support team will provide the return address.<br/>
                        • Customers are responsible for return shipping costs unless the product is defective or incorrect.
                            </div>
                        </div>

                    </div>
                   
                </div>

                <div class="">
                    <h2 class="text-[#262626]  text-[22px] md:text-[30px] font-['Open Sans'] font-medium">Refund process</h2>
                    <div class="flex flex-col md:flex-row md:items-center gap-3 py-4">
                        <div class="flex items-start  md:flex-col gap-2">
                        <i class="fa-solid fa-1 text-[35px] text-[#C2185B] leading-none"></i>    
                        <div class="flex flex-col gap-1">
                        <h2 class="text-[#262626]  text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Approval</h2>
                        <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                         
Once we receive and inspect the returned item, we will notify you about the approval or rejection of your refund.
                    </p>
                            </div>
                        </div>

                        <div class="flex items-start  md:flex-col gap-2">
                        <i class="fa-solid fa-2 text-[35px] text-[#C2185B] leading-none"></i>    
                        <div class="flex flex-col gap-1">
                        <h2 class="text-[#262626]  text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Processing Time</h2>
                        <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                       
                       
Approved refunds will be processed within 7-10 business days and credited back to the original payment method.
                    </p>
                            </div>
                        </div>

                        <div class="flex items-start  md:flex-col gap-2">
  
                        <div class="flex flex-col gap-1">
                        <h2 class="text-[#262626]  text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Note:</h2>
                        <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                    
                        Shipping fees are non-refundable, except in cases where the return is due to an error on our part.
                            </div>
                        </div>

                    </div>
                   
                </div>
                
                <div class="">
                    <h2 class="text-[#262626]  text-[22px] md:text-[30px] font-['Open Sans'] font-medium">Damaged or Defective Items</h2>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                    If you receive a damaged or defective product, please contact us within 48 hours of delivery with photos of the issue. We will arrange for a replacement
                    </p>
                   
                </div>

                <div class="">
                    <h2 class="text-[#262626]  text-[22px] md:text-[30px] font-['Open Sans'] font-medium">Exchanges</h2>
                   
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • We only offer exchanges for defective or incorrect items.
                    </p>
                    <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                        • If you need an exchange, please contact our customer support team for assistance.
                    </p>
                   
                </div>

                <p class="text-[#5B5B5B]  text-[15px] md:text-[16px] font-['Open Sans'] font-regular">
                For any further inquiries, please reach out to our support team at <b>support@glorefy.com</b>. We appreciate your trust in Glorefy and are dedicated to ensuring your satisfaction.
                        </p>


            </div>

        </div>







        <?php
      include('../includes/footer.php');
      ?>
   
    </main>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/inputs.js"></script>
    <script src="./functions/tabs.js"></script>

</body>

</html>