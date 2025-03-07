<?php
require_once __DIR__ . "/../config/config.php";
// Include database connection
include(__DIR__ . '/../config/connect.php');
require_once __DIR__ . '/../includes/auth/auth.php';

// Require authentication to access cart
requireAuth();

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Process cart updates (quantity changes or removals)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Handle item removal
        if ($_POST['action'] === 'remove' && isset($_POST['cart_id'])) {
            $cart_id = intval($_POST['cart_id']);
            
            $remove_query = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($con, $remove_query);
            mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Item removed successfully
                header("Location: cart.php?removed=1");
                exit();
            }
        }
        
        // Handle quantity update
        if ($_POST['action'] === 'update' && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
            $cart_id = intval($_POST['cart_id']);
            $quantity = intval($_POST['quantity']);
            
            // Ensure quantity is at least 1
            if ($quantity < 1) {
                $quantity = 1;
            }
            
            $update_query = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Quantity updated successfully
                header("Location: cart.php?updated=1");
                exit();
            }
        }
    }
}

// Ultra-simplified query - should work with almost any database structure
$cart_query = "
    SELECT 
        c.cart_id,
        c.quantity,
        p.product_id,
        p.product_name,
        i.image_path AS main_image,
        v.variant_id,
        v.size,
        v.status,
        v.original_price,
        v.discount_price,
        CASE 
            WHEN v.discount_price IS NOT NULL AND v.discount_price > 0 
            THEN v.discount_price 
            ELSE v.original_price 
        END AS display_price
    FROM 
        cart c
    JOIN 
        products p ON c.product_id = p.product_id
    LEFT JOIN 
        product_images i ON p.product_id = i.product_id AND i.is_main = 1
    LEFT JOIN (
        SELECT 
            pv.* 
        FROM 
            product_variants pv
        INNER JOIN (
            SELECT 
                product_id, 
                MIN(variant_id) as first_variant_id
            FROM 
                product_variants
            GROUP BY 
                product_id
        ) first_variants ON pv.product_id = first_variants.product_id 
            AND pv.variant_id = first_variants.first_variant_id
    ) v ON p.product_id = v.product_id
    WHERE 
        c.user_id = ?
    ORDER BY 
        c.added_at DESC
";

$stmt = mysqli_prepare($con, $cart_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Initialize total
$cart_total = 0;
$cart_items = [];

// Fetch all cart items and calculate total
if ($result && mysqli_num_rows($result) > 0) {
    while ($item = mysqli_fetch_assoc($result)) {
        // Calculate the final price (use discount price if available)
        $final_price = (!empty($item['discount_price']) && $item['discount_price'] > 0) ? 
                        $item['discount_price'] : $item['original_price'];
        
        // Calculate item total
        $item_total = $final_price * $item['quantity'];
        
        // Add to cart total
        $cart_total += $item_total;
        
        // Add calculated values to the item array
        $item['final_price'] = $final_price;
        $item['item_total'] = $item_total;
        
        // Add to cart items array
        $cart_items[] = $item;
    }
}

// Get total number of items in cart
$total_items = count($cart_items);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Cart</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />
</head>

<body>
    <main class="bg-[#FEFEFE]">

    <?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
        ?>
        <section class="w-full bg-[#FFFFFFF] py-4">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="<?php echo DOMAIN; ?>/index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="<?php echo DOMAIN; ?>/assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Cart</span>
                </div>
            </div>
        </section>


        <?php if (isset($_GET['removed'])): ?>
            <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-md mb-4">
                Item has been removed from your cart.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-md mb-4">
                Cart has been updated successfully.
            </div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <h2 class="text-xl font-medium text-gray-900 mb-4">Your cart is empty</h2>
                <p class="text-gray-500 mb-6">Looks like you haven't added any products to your cart yet.</p>
                <a href="<?php echo DOMAIN; ?>/products/index.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#1A237E] hover:bg-[#1A237E]/90">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
<!-- <h2 class="text-lg font-medium text-gray-900">Cart Items (<?php echo $total_items; ?>)</h2> -->
        <div class="w-full bg-[#FFFFFF] py-5">
            <div class="w-[90%] mx-auto hidden md:block">


                <table cols="" class="w-full">
                    <thead class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Availability</th>
                        <th>Action</th>
                    </thead>

                    <tbody class="">
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td class="py-3 flex gap-2">

                                <div class="w-[131.64px] h-[88.73px] rounded-[4px] overflow-hidden">
                                <img src="<?php echo !empty($item['main_image']) ? DOMAIN . '/assets/products/' . $item['main_image'] : DOMAIN . '/assets/products/default.svg'; ?>" 
                                                alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                class="h-full w-full object-cover object-center">
                                </div>
                                <div class="flex flex-col gap-[2px]">
                                    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">    <a href="show.php?id=<?php echo $item['product_id']; ?>" class="hover:text-[#1A237E]">
                                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                                        </a></p>
                                                        <?php if (!empty($item['variant_id'])): ?>
    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
        Size: <?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?>
    </p>
<?php endif; ?>
<?php if (!empty($item['variant_id'])): ?>
    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
        Variant: <?php echo htmlspecialchars($item['variant_id'] ?? 'N/A'); ?>
    </p>
<?php endif; ?>

 
                                   
                                </div>

                            </td>
                            <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦<?php echo number_format($item['item_total']); ?></td></td>
                            <td>
                            <form method="POST" action="cart.php" class="flex items-center mb-2">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        
                                                        <div class="flex items-center border rounded-md">
                                                            <button type="button" class="quantity-btn px-2 py-1 text-gray-600 hover:text-gray-900" data-action="decrease">-</button>
                                                            <input type="number" id="quantity" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                                min="1" max="99" class="w-12 text-center border-0 focus:ring-0">
                                                            <button type="button" class="quantity-btn px-2 py-1 text-gray-600 hover:text-gray-900" data-action="increase">+</button>
                                                        </div>
                                                        
                                                        <button type="submit" class="ml-2 text-sm text-[#1A237E] hover:text-[#1A237E]/80">
                                                            Update
                                                        </button>
                                                    </form>
                            </td>


                            <td>
                          
                            <?php if (!empty($item['status'])): ?>
    <?php if ($item['status'] === 'available'): ?>
        <button type="button" class="py-2 px-4 bg-[#D51E5E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
    <?php elseif ($item['status'] === 'outOfStock'): ?>
        <button type="button" class="py-2 px-4 bg-black text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out of Stock</button>
    <?php elseif ($item['status'] === 'discontinued'): ?>
        <button type="button" class="py-2 px-4 bg-red-600 text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">Discontinued</button>
    <?php else: ?>
        <button type="button" class="py-2 px-4 bg-gray-500 text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]"><?php echo htmlspecialchars($item['status']); ?></button>
    <?php endif; ?>
<?php else: ?>
    <button type="button" class="py-2 px-4 bg-gray-500 text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[28px]">N/A</button>
<?php endif; ?>
                            </td>

                            <td>
                            <form method="POST" action="cart.php" class="flex">
                                                        <input type="hidden" name="action" value="remove">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                                            Remove from cart
                                                        </button>
                                                    </form>
                            </td>


                        </tr>
                        <?php endforeach; ?>
                     
                    </tbody>
                </table>

                <a href="./checkout.php" class="flex items-center justify-center gap-2 mt-2 max-w-[377px] py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                    <span>Continue to Checkout</span>
                    <img src="<?php echo DOMAIN; ?>/assets/products/to.svg" class="w-[16px] mt-1" />
</a>

            </div>

            <div class="w-[90%] mx-auto  md:hidden">
                <div class="w-full flex flex-col gap-4">
                <?php foreach ($cart_items as $item): ?>
                    <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex justify-between">
                    <div class="flex flex-col gap-2">
                    <?php if (!empty($item['status'])): ?>
    <?php if ($item['status'] === 'available'): ?>
        <button type="button" class="max-w-[87px] py-2 px-4 bg-[#D51E5E] text-white text-[13px] font-['Open Sans'] cursor-pointer rounded-[28px]">In Stock</button>
    <?php elseif ($item['status'] === 'outOfStock'): ?>
        <button type="button" class="max-w-[87px]  py-2 px-4 bg-black text-white text-[13px] font-['Open Sans'] cursor-pointer rounded-[28px]">Out of Stock</button>
    <?php elseif ($item['status'] === 'discontinued'): ?>
        <button type="button" class="max-w-[87px]  py-2 px-4 bg-red-600 text-white text-[13px] font-['Open Sans'] cursor-pointer rounded-[28px]">Discontinued</button>
    <?php else: ?>
        <button type="button" class="text-nowrap max-w-[87px]  py-2 px-4 bg-gray-500 text-white text-[13px] font-['Open Sans'] cursor-pointer rounded-[28px]"><?php echo htmlspecialchars($item['status']); ?></button>
    <?php endif; ?>
<?php else: ?>
    <button type="button" class="max-w-[87px]  py-2 px-4 bg-gray-500 text-white text-[13px] font-['Open Sans'] cursor-pointer rounded-[28px]">N/A</button>
<?php endif; ?>
                        <div class="flex gap-2">
                        
                            <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                            <img src="<?php echo !empty($item['main_image']) ? DOMAIN . '/assets/products/' . $item['main_image'] : DOMAIN . '/assets/products/default.svg'; ?>" 
                                                alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                class="h-full w-full object-cover object-center">
                            </div>
                            <div class="flex flex-col gap-[2px]">
                                <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">  <a href="show.php?id=<?php echo $item['product_id']; ?>" class="hover:text-[#1A237E]">
                                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                                        </a></p>

                                                        <?php if (!empty($item['variant_id'])): ?>
    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
        Size: <?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?>
    </p>
<?php endif; ?>
<?php if (!empty($item['variant_id'])): ?>
    <p class="text-[#262626] text-[13px] md:text-[14px] font-['Open Sans'] font-regular">
        Variant: <?php echo htmlspecialchars($item['variant_id'] ?? 'N/A'); ?>
    </p>
<?php endif; ?>
                            </div>

                        </div>

                        <form method="POST" action="cart.php" class="flex">
                                                        <input type="hidden" name="action" value="remove">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                                            Remove from cart
                                                        </button>
                                                    </form>
                        </div>


                        <div class="flex flex-col gap-2">
                       
                            <div>
                                <div class="flex items-center gap-3">
                                <form method="POST" action="cart.php" class="flex items-center mb-2">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        
                                                        <div class="flex items-center border rounded-md">
                                                            <button type="button" class="quantity-btn px-2 py-1 text-gray-600 hover:text-gray-900" data-action="decrease">-</button>
                                                            <input type="number" id="quantity" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                                min="1" max="99" class="w-12 text-center border-0 focus:ring-0">
                                                            <button type="button" class="quantity-btn px-2 py-1 text-gray-600 hover:text-gray-900" data-action="increase">+</button>
                                                        </div>
                                                        
                                                        <button type="submit" class="ml-2 text-sm text-[#1A237E] hover:text-[#1A237E]/80">
                                                            Update
                                                        </button>
                                                    </form>
                                </div>
                        </div>
                        <p class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular">₦300,000</p>
                        </div>
                    
                    </div>
                    <?php endforeach; ?>
               

                    <a href="./checkout.php" class="flex items-center justify-center gap-2 mt-2 w-full py-2 px-4 bg-[#1A237E] text-white text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">
                    <span>Continue to Checkout</span>
                    <img src="<?php echo DOMAIN; ?>/assets/products/to.svg" class="w-[16px] mt-1" />
                </a>

                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
    include(__DIR__ . '/../includes/footer.php');
        ?>
    </main>




    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>
        
    <script>
        // Quantity buttons functionality
        document.addEventListener('DOMContentLoaded', function() {
            const quantityBtns = document.querySelectorAll('.quantity-btn');
            
            quantityBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const input = this.parentNode.querySelector('input');
                    let value = parseInt(input.value);
                    
                    if (action === 'decrease' && value > 1) {
                        input.value = value - 1;
                    } else if (action === 'increase') {
                        input.value = value + 1;
                    }
                });
            });
        });

    

   if (proceedtoCart()) {
    proceedtoCart.addEventListener('click', function() {
            if (!validateSelection()) return;
            
            // First add to cart
            const productId = <?php echo $product_id; ?>;
            const variantId = document.getElementById('variantid').value;
            const quantity = document.getElementById('quantity').value;
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('variant_id', variantId);
            formData.append('quantity', quantity);

        window.location.href = './checkout.php';

        }

        )

        
   
        }
    

        
    </script>


</body>

</html