<?php
// Include authentication utility
require_once '../includes/auth/auth.php';
require_once __DIR__ . "/../config/config.php";

// Authentication check
requireAuth();

// Get user data
$user = getCurrentUser();
$user_id = $user['id']; // Assuming this returns an array with the user ID

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}



// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die(mysqli_error($conn));
}

// Get the orders for the current user
// Let's debug the column names first by showing all columns
$debug_sql = "SHOW COLUMNS FROM orders";
$debug_result = $conn->query($debug_sql);
$available_columns = [];
if ($debug_result) {
    while ($row = $debug_result->fetch_assoc()) {
        $available_columns[] = $row['Field'];
    }
}

// Now construct the SQL with the correct column names
// Check if 'created_at' exists instead of 'created'
$date_column = in_array('created_at', $available_columns) ? 'created_at' : 
              (in_array('date_added', $available_columns) ? 'date_added' : 
              (in_array('created', $available_columns) ? 'created' : 'id'));

$sql = "SELECT o.id, o.order_total, o.delivery_method, o.pickup_location, 
               o.payment_reference, o.order_status, o.$date_column as order_date
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.$date_column DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Function to get order items for a specific order
function getOrderItems($conn, $order_id) {
    $sql = "SELECT oi.id, oi.product_id, oi.variant_id, oi.quantity, oi.price,
                  p.product_name, pv.size, pv.texture,
                  (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
           FROM order_items oi
           JOIN products p ON oi.product_id = p.product_id
           JOIN product_variants pv ON oi.variant_id = pv.variant_id
           WHERE oi.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get variant details
function getVariantDetails($conn, $variant_id) {
    $sql = "SELECT size, texture
            FROM product_variants
            WHERE variant_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $variant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get product color
function getProductColor($conn, $product_id) {
    $sql = "SELECT colors
            FROM products
            WHERE product_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    return $product ? $product['colors'] : 'N/A';
}

// Get filter status from query parameter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Handle search
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | My Orders</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/style.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/modal.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/tabs.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/styles.css">
    <link rel="stylesheet" href="<?php echo DOMAIN; ?>/styles/faq.css" />

    <style>
   
        .adminordersMenu{
            position: absolute;
            left: -10rem;
         min-width: 10rem;
         min-height: 10rem;
           height: 100%;
           z-index: 2;
        }

  
        

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
        position: relative;
        margin: 10% auto;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .close {
        color: #aaa;
        font-weight: bold;
    }
    
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    
    /* Active Status Styles */
    .status-active {
        background-color: #1A237E !important;
    }
    
    .status-completed {
        background-color: #39D959 !important;
    }
    
    /* For cancelled orders */
    .status-cancelled .status-icon {
        background-color: #E1E1E1 !important;
    }
    
    .status-cancelled .status-content h4,
    .status-cancelled .status-content p {
        color: #8F8F8F;
    }

    </style>
</head>

<body>
    <main class="bg-[#FEFEFE]">

    <?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
        ?>


        <section class="w-full bg-[#FFFFFFF] py-1">
            <div class="w-[90%] mx-auto">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">My Orders</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] gap-3 mx-auto bg-[#FFFFFF] py-5 flex items-center flex-col-reverse md:flex-row justify-between">
            <div class="w-full flex items-center gap-4 overflow-x-auto">
                <a href="?status=all" class="py-1 px-4 bg-[<?php echo $status_filter == 'all' ? '#1A237E' : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'all' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">All</a>
                <a href="?status=Processing" class="py-1 px-4 bg-[<?php echo $status_filter == 'Processing' ? '#1A237E' : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Processing' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Processing</a>
                <a href="?status=Shipped" class="py-1 px-4 bg-[<?php echo $status_filter == 'Shipped' ? '#1A237E' : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Shipped' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Shipped</a>
                <a href="?status=Delivered" class="py-1 px-4 bg-[<?php echo $status_filter == 'Delivered' ? '#1A237E' : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Delivered' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Delivered</a>
                <a href="?status=Returned" class="py-1 px-4 bg-[<?php echo $status_filter == 'Returned' ? '#1A237E' : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Returned' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Returned</a>
                <a href="?status=Cancelled" class="py-1 px-4 bg-[<?php echo $status_filter == 'Cancelled' ? '#1A237E' : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Cancelled' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Cancelled</a>
            </div>

            <div class="w-full md:w-[80%] lg:w-[70%] flex items-center justify-between">
                <form action="" method="GET" class="w-full flex">
                    <div class="w-full flex items-center gap-2 border-y-[1px] border-l-[1px] border-[#B8BBD7] rounded-l-[4px] p-2">
                        <input type="text" name="search" placeholder="Search order ID" value="<?php echo htmlspecialchars($search_query); ?>" class="w-full lg:w-[18rem] text-[14px] border-[#B8BBD7] outline-none placeholder:text-[#B8BBD7]" />
                    </div>
                    <button type="submit" class="py-2 px-4 bg-[#E8E9F2] text-black text-[16px] font-['Open Sans'] cursor-pointer rounded-r-[4px] border-[1px] border-[]">Search</button>
                </form>
            </div>
        </div>

        <div class="w-full bg-[#FFFFFF] py-5">
            <!-- Desktop View -->
            <div class="w-[90%] mx-auto hidden md:block">
                <table cols="" class="w-full">
                    <thead class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <th>Product</th>
                        <th>Amount/Quantity</th>
                        <th>Order ID</th>
                        <th>Order Type</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </thead>

                    <tbody class="">
                        <?php
                        // Filter orders based on status if needed
                        $filtered_orders = $orders;
                        if ($status_filter != 'all') {
                            $filtered_orders = array_filter($orders, function($order) use ($status_filter) {
                                return $order['order_status'] == $status_filter;
                            });
                        }

                        // Filter orders based on search query if provided
                        if (!empty($search_query)) {
                            $filtered_orders = array_filter($filtered_orders, function($order) use ($search_query) {
                                return strpos($order['id'], $search_query) !== false;
                            });
                        }

                        if (empty($filtered_orders)) {
                            echo "<tr><td colspan='7' class='py-4 text-center'>No orders found</td></tr>";
                        } else {
                            foreach ($filtered_orders as $order) {
                                $order_items = getOrderItems($conn, $order['id']);
                                
                                // Display first item of each order
                                foreach ($order_items as $index => $item) {
                                    $variant = getVariantDetails($conn, $item['variant_id']);
                                    $color = getProductColor($conn, $item['product_id']);
                                    
                                    // Determine status color
                                    $status_color = '';
                                    $status_text = $order['order_status'];
                                    
                                    if ($status_text == 'Processing') {
                                        $status_color = 'bg-[#E8B006]';
                                    } elseif ($status_text == 'Shipped') {
                                        $status_color = 'bg-[#1A237E]';
                                    } elseif ($status_text == 'Delivered') {
                                        $status_color = 'bg-[#39D959]';
                                    } elseif ($status_text == 'Returned') {
                                        $status_color = 'bg-[#9C27B0]';
                                    } else {
                                        $status_color = 'bg-red-500';
                                    }
                                    
                                    // Format date (assuming date is in a standard format)
                                    $order_date = date('M d, Y', strtotime($order['order_date']));
                                    
                                    // Get image path or use placeholder
                                    $image_path = isset($item['image_path']) ? "../assets/products/" . $item['image_path'] : "../assets/products/img1.svg";
                                    
                                  echo "
<tr>
    <td class='py-3 flex gap-2'>
        <div class='w-[131.64px] h-[88.73px] rounded-[4px] overflow-hidden'>
            <img src='{$image_path}' class='w-full h-full object-cover' />
        </div>
        <div class='flex flex-col gap-[2px]'>
            <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'>Name: {$item['product_name']}</p>
            <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'>Color: {$color}</p>
            <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'>Size: {$variant['size']}</p>
        </div>
    </td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>₦" . number_format($item['price']) . "/{$item['quantity']}</td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>#{$order['id']}</td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>{$order['delivery_method']}</td>
    <td>
        <button type='button' class='py-1 px-4 {$status_color} text-white text-[16px] font-[\"Open Sans\"] cursor-pointer rounded-[28px]'>{$status_text}</button>
    </td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>{$order_date}</td>
    <td class='relative'>
        <img src='../assets/user/action.svg' class='w-[20px] cursor-pointer openAdminOrderMenu' />

        <!-- The menu for each order starts -->
        <div class='adminordersMenu h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]'>
            <div class='flex flex-col gap-3'>
                <a href='../products/show.php?id={$item['product_id']}' class='text-[16px] font-medium text-[#262626]'>Re-Order</a>
                <a href='./track-order.php?id={$order['id']}' class='text-[16px] font-medium text-[#262626]'>Track Order</a>";
                
if ($status_text == 'Delivered') {
    echo "<a href='./write-review.php?order_id={$order['id']}&product_id={$item['product_id']}' class='text-[16px] font-medium text-[#262626]'>Leave a review</a>";
} else {
    echo "<span class='text-[16px] font-medium text-gray-400 cursor-not-allowed' title='You can review this product after delivery'>Leave a review</span>";
}

echo "
                <a href='./report-issue.php?id={$order['id']}' class='text-[16px] font-medium text-[#E8B006]'>Report an issue</a>
                <a href='./r.php?id={$order['id']}' class='text-[16px] font-medium text-[#E8B006]'>Report an issue</a>
            </div>
        </div>
    </td>
</tr>";
                                    
                                    // Only show the first item for each order in desktop view
                                    break;
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="w-[90%] mx-auto md:hidden">
                <div class="w-full flex flex-col gap-4">
                    <?php
                    // Same filtering as above
                    $filtered_orders = $orders;
                    if ($status_filter != 'all') {
                        $filtered_orders = array_filter($orders, function($order) use ($status_filter) {
                            return $order['order_status'] == $status_filter;
                        });
                    }

                    if (!empty($search_query)) {
                        $filtered_orders = array_filter($filtered_orders, function($order) use ($search_query) {
                            return strpos($order['id'], $search_query) !== false;
                        });
                    }

                    if (empty($filtered_orders)) {
                        echo "<p class='text-center py-4'>No orders found</p>";
                    } else {
                        foreach ($filtered_orders as $order) {
                            $order_items = getOrderItems($conn, $order['id']);
                            
                            // Get first item for the order
                            if (!empty($order_items)) {
                                $item = $order_items[0];
                                $variant = getVariantDetails($conn, $item['variant_id']);
                                $color = getProductColor($conn, $item['product_id']);
                                
                                // Determine status color
                                $status_color = '';
                                $status_text = $order['order_status'];
                                
                                if ($status_text == 'Processing') {
                                    $status_color = 'bg-[#E8B006]';
                                } elseif($status_text == 'Shipped'){
                                    $status_color = 'bg-[#1A237E]';
                                }
                                
                                elseif ($status_text == 'Delivered') {
                                    $status_color = 'bg-[#39D959]';
                                } else {
                                    $status_color = 'bg-red-500';
                                }
                                
                                
                                // Format date
                                $order_date = date('M d, Y', strtotime($order['order_date']));
                                
                                // Get image path or use placeholder
                                $image_path = isset($item['image_path']) ? "../assets/products/" . $item['image_path'] : "../assets/products/img1.svg";
                                
                                echo "
                                <div class='border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2'>
                                    <div class='flex items-center justify-between relative'>
                                        <p class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>{$order_date}</p>
                                   
                                        <!--  The order menu starts -->

 <img src='../assets/user/action.svg' class='w-[20px] cursor-pointer openAdminOrderMenuForMobile' />

        <!-- The menu for each order starts -->
       <div class='adminordersMenuformobile hidden h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px] absolute right-0 top-[2rem]   min-h-[10rem] min-h-[10rem]'>
            <div class='flex flex-col gap-3'>
                <a href='../products/show.php?id={$item['product_id']}' class='text-[16px] font-medium text-[#262626]'>Re-Order</a>
                <a href='./track-order.php?id={$order['id']}' class='text-[16px] font-medium text-[#262626]'>Track Order</a>";
                
if ($status_text == 'Delivered') {
    echo "<a href='./write-review.php?order_id={$order['id']}&product_id={$item['product_id']}' class='text-[16px] font-medium text-[#262626]'>Leave a review</a>";
} else {
    echo "<span class='text-[16px] font-medium text-gray-400 cursor-not-allowed' title='You can review this product after delivery'>Leave a review</span>";
}

echo "
                <a href='./report-issue.php?id={$order['id']}' class='text-[16px] font-medium text-[#E8B006]'>Report an issue</a>
            </div>
        </div>
                                <!-- The order menu ends -->


                                     
                                    </div>
                                    
                                    <div class='w-full h-[1px] bg-[#E1E1E1]'></div>
                                    
                                    <div class='flex items-center justify-between'>
                                        <button type='button' class='max-w-[87px] py-[6px] px-3 {$status_color} text-white text-[14px] font-[\"Open Sans\"] cursor-pointer rounded-[28px]'>{$status_text}</button>
                                        <div class='flex flex-col gap-[2px] text-right'>
                                            <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'>" . ucfirst($order['delivery_method']) . "</p>
                                            <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'><b>Order ID:</b> #{$order['id']}</p>
                                        </div>
                                    </div>
                                    
                                    <div class='w-full h-[1px] bg-[#E1E1E1]'></div>
                                    
                                    <div class='flex justify-between'>
                                        <div class='flex flex-col gap-2'>
                                            <div class='flex gap-2'>
                                                <div class='w-[80px] h-[80px] rounded-[4px] overflow-hidden'>
                                                    <img src='{$image_path}' class='w-full h-full object-cover' />
                                                </div>
                                                <div class='flex flex-col gap-[2px]'>
                                                    <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'><b>Name:</b> {$item['product_name']}</p>
                                                    <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'><b>Color:</b> {$color}</p>
                                                    <p class='text-[#262626] text-[13px] md:text-[14px] font-[\"Open Sans\"] font-regular'><b>Size:</b> {$variant['size']}</p>
                                                </div>
                                            </div>
                                            <a href='./track-order.php?id={$order['id']}' class='text-[14px] font-[\"Open Sans\"] text-[#1A237E] font-regular underline cursor-pointer'>Track your order</a>
                                        </div>
                                        <p class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>₦" . number_format($item['price']) . "</p>
                                    </div>
                                </div>";
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        include(__DIR__ . '/../includes/footer.php');
        ?>













<!-- The order tracking modal starts here -->
 <!-- Order Tracking Modal -->
<div id="trackOrderModal" class="modal">
    <div class="modal-content w-[90%] md:w-[80%] lg:w-[60%] mx-auto bg-white rounded-[8px] p-5">
        <div class="modal-header flex justify-between items-center mb-4">
            <h2 class="text-[18px] md:text-[20px] font-['Open Sans'] font-semibold">Track Order</h2>
            <span class="close cursor-pointer text-[24px]">&times;</span>
        </div>
        
        <div class="modal-body">
            <!-- Order Details -->
            <div class="order-details bg-[#F8F9FB] p-4 rounded-[8px] mb-4">
                <div class="flex flex-col md:flex-row justify-between mb-4">
                    <div>
                        <p class="text-[14px] text-[#262626] font-['Open Sans']">Order ID: <span id="modal-order-id" class="font-medium"></span></p>
                        <p class="text-[14px] text-[#262626] font-['Open Sans']">Order Date: <span id="modal-order-date" class="font-medium"></span></p>
                    </div>
                    <div class="mt-2 md:mt-0">
                        <p class="text-[14px] text-[#262626] font-['Open Sans']">Payment Ref: <span id="modal-payment-ref" class="font-medium"></span></p>
                        <p class="text-[14px] text-[#262626] font-['Open Sans']">Delivery Method: <span id="modal-delivery-method" class="font-medium"></span></p>
                    </div>
                </div>
                
                <div id="modal-pickup-location-container" class="mb-4 hidden">
                    <p class="text-[14px] text-[#262626] font-['Open Sans']">Pickup Location: <span id="modal-pickup-location" class="font-medium"></span></p>
                </div>
                
                <div id="modal-status-notes-container" class="mb-4 hidden">
                    <p class="text-[14px] text-[#262626] font-['Open Sans'] font-medium">Status Notes:</p>
                    <p id="modal-status-notes" class="text-[14px] text-[#262626] font-['Open Sans'] bg-white p-2 rounded-[4px] mt-1"></p>
                </div>
            </div>
            
            <!-- Status Tracker -->
            <div class="status-tracker p-4">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-[16px] font-['Open Sans'] font-semibold">Order Status</h3>
                    <div id="modal-status-badge" class="py-1 px-4 bg-[#E8B006] text-white text-[14px] font-['Open Sans'] rounded-[28px]">Processing</div>
                </div>
                
                <div class="tracker-timeline mt-6">
                    <div class="relative">
                        <!-- Status Line -->
                        <div class="absolute left-6 top-0 w-[2px] h-full bg-[#E1E1E1]"></div>
                        
                        <!-- Processing Status -->
                        <div class="status-item relative flex mb-8">
                            <div id="processing-icon" class="status-icon w-[40px] h-[40px] rounded-full bg-[#1A237E] flex items-center justify-center z-10">
                                <img src="../assets/user/clipboard-check.svg" alt="Processing" class="w-[20px] h-[20px]" />
                            </div>
                            <div class="status-content ml-4">
                                <h4 class="text-[16px] font-['Open Sans'] font-semibold">Order Processing</h4>
                                <p id="processing-date" class="text-[14px] text-[#262626] font-['Open Sans']">Not processed yet</p>
                            </div>
                        </div>
                        
                        <!-- Shipped Status -->
                        <div class="status-item relative flex mb-8">
                            <div id="shipped-icon" class="status-icon w-[40px] h-[40px] rounded-full bg-[#E1E1E1] flex items-center justify-center z-10">
                                <img src="../assets/user/shipping.svg" alt="Shipped" class="w-[20px] h-[20px]" />
                            </div>
                            <div class="status-content ml-4">
                                <h4 class="text-[16px] font-['Open Sans'] font-semibold">Order Dispatched</h4>
                                <p id="shipped-date" class="text-[14px] text-[#262626] font-['Open Sans']">Not shipped yet</p>
                                <p id="shipped-notes" class="text-[14px] text-[#262626] font-['Open Sans'] hidden"></p>
                            </div>
                        </div>
                        
                        <!-- Delivered Status -->
                        <div class="status-item relative flex">
                            <div id="delivered-icon" class="status-icon w-[40px] h-[40px] rounded-full bg-[#E1E1E1] flex items-center justify-center z-10">
                                <img src="../assets/user/box.svg" alt="Delivered" class="w-[20px] h-[20px]" />
                            </div>
                            <div class="status-content ml-4">
                                <h4 class="text-[16px] font-['Open Sans'] font-semibold">Order Delivered</h4>
                                <p id="delivered-date" class="text-[14px] text-[#262626] font-['Open Sans']">Not delivered yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items Preview -->
            <div class="order-items mt-6">
                <h3 class="text-[16px] font-['Open Sans'] font-semibold mb-3">Order Items</h3>
                <div id="modal-order-items" class="flex flex-col gap-4">
                    <!-- Order items will be populated here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- The order tracking modal ends here -->

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
    // Select all admin order menu triggers
const adminOrderMenuButtons = document.querySelectorAll(".openAdminOrderMenu");

// Add click event listeners to each button
adminOrderMenuButtons.forEach(button => {
    button.addEventListener('click', function() {
        // Find the closest parent td and then find the menu inside it
        const menuContainer = this.closest('td').querySelector('.adminordersMenu');
        
        // Toggle the display of the menu
        if (menuContainer.style.display === "block") {
            menuContainer.style.display = "none";
        } else {
            // First, close all other open menus
            document.querySelectorAll('.adminordersMenu').forEach(menu => {
                menu.style.display = "none";
            });
            
            // Then open the clicked menu
            menuContainer.style.display = "block";
        }
    });
});

// Handle mobile order menus
function openOrdermenu(element) {
    const menuContent = element.nextElementSibling;
    
    // Toggle show class
    if (menuContent.classList.contains('showom')) {
        menuContent.classList.remove('showom');
    } else {
        // Close all other menus first
        document.querySelectorAll('.ordermenu-content').forEach(menu => {
            menu.classList.remove('showom');
        });
        
        // Show this menu
        menuContent.classList.add('showom');
        console.log("Clickeddddddddddddd")
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    // Close admin order menus if clicking outside
    if (!event.target.closest('.adminordersMenu') && !event.target.closest('.openAdminOrderMenu')) {
        document.querySelectorAll('.adminordersMenu').forEach(menu => {
            menu.style.display = "none";
        });
    }
    
    // Close mobile order menus if clicking outside
    if (!event.target.closest('.ordermenu-content') && !event.target.matches('img[onclick="openOrdermenu(this)"]')) {
        document.querySelectorAll('.ordermenu-content').forEach(menu => {
            menu.classList.remove('showom');
        });
    }
});

// Make sure menus are hidden initially
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.adminordersMenu').forEach(menu => {
        menu.style.display = "none";
    });
});



document.addEventListener('DOMContentLoaded', function() {
    // Select all action icons and their corresponding menus
    const actionIcons = document.querySelectorAll('.openAdminOrderMenuForMobile');

    actionIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            // Find the closest menu to this icon
            const menu = this.nextElementSibling;

            // Toggle menu visibility
            if (menu.classList.contains('hidden')) {
                // Close any other open menus
                document.querySelectorAll('.adminordersMenuformobile').forEach(openMenu => {
                    if (openMenu !== menu) {
                        openMenu.classList.add('hidden');
                    }
                });

                // Show this menu
                menu.classList.remove('hidden');
            } else {
                // Hide this menu
                menu.classList.add('hidden');
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menus = document.querySelectorAll('.adminordersMenuformobile');
        
        menus.forEach(menu => {
            // Check if the click is outside the menu and its trigger icon
            if (!menu.contains(event.target) && 
                !menu.previousElementSibling.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    });
});


  </script>
</body>

</html>