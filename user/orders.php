<?php
require_once '../includes/auth/auth.php';
require_once __DIR__ . "/../config/config.php";

requireAuth();

$user = getCurrentUser();
$user_id = $user['id'];

if (isset($_GET['logout'])) {
    logout();
}

$conn = db();

$all_orders_sql = "
    SELECT o.id, o.order_total, o.delivery_method, o.pickup_location,
           o.payment_reference, o.order_status, o.created_at as order_date,
           o.delivery_confirmed_at,
           oi.id as item_id, oi.product_id, oi.variant_id, oi.quantity, oi.price,
           p.product_name, p.product_slug, p.colors,
           pv.size, pv.texture,
           pi.image_path
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_main = 1
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC, o.id DESC, oi.id ASC
";

$stmt = $conn->prepare($all_orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$all_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$orders = [];
$order_items_map = [];
foreach ($all_rows as $row) {
    $oid = $row['id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'id' => $oid,
            'order_total' => $row['order_total'],
            'delivery_method' => $row['delivery_method'],
            'pickup_location' => $row['pickup_location'],
            'payment_reference' => $row['payment_reference'],
            'order_status' => $row['order_status'],
            'order_date' => $row['order_date'],
            'delivery_confirmed_at' => $row['delivery_confirmed_at'],
        ];
        $order_items_map[$oid] = [];
    }
    if (!empty($row['item_id'])) {
        $order_items_map[$oid][] = [
            'id' => $row['item_id'],
            'product_id' => $row['product_id'],
            'variant_id' => $row['variant_id'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'product_name' => $row['product_name'],
            'colors' => $row['colors'],
            'size' => $row['size'],
            'texture' => $row['texture'],
            'image_path' => $row['image_path'],
        ];
    }
}
$orders = array_values($orders);

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

require_once "../includes/auth/google.php";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo DOMAIN; ?>/assets/global/logo.png">
    <title>GLOREFY | My Orders</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<?php include '../includes/tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="bg-[<?php echo store_color('color_bg'); ?>]">

    <?php
    include(__DIR__ . '/../includes/header.php');
    include(__DIR__ . '/../includes/options.php');
        ?>


        <section class="w-full bg-[<?php echo store_color('color_bg'); ?>] py-1">
            <div class="w-[90%] mx-auto max-w-[1440px]">
                <div class="flex items-center gap-1 cursor-pointer">
                    <a href="../index.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-[#C5C5C5] leading-none"></i>
                    <span class="text-[<?php echo store_color('color_primary'); ?>] text-[13px] md:text-[14px] font-Onest font-medium">My Orders</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] gap-3 mx-auto bg-[<?php echo store_color('color_bg'); ?>] py-5 flex items-center flex-col-reverse md:flex-row justify-between">
            <div class="w-full flex items-center gap-4 overflow-x-auto">
                <a href="?status=all" class="py-1 px-4 bg-[<?php echo $status_filter == 'all' ? store_color('color_primary') : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'all' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">All</a>
                <a href="?status=Processing" class="py-1 px-4 bg-[<?php echo $status_filter == 'Processing' ? store_color('color_primary') : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Processing' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Processing</a>
                <a href="?status=Shipped" class="py-1 px-4 bg-[<?php echo $status_filter == 'Shipped' ? store_color('color_primary') : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Shipped' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Shipped</a>
                <a href="?status=Delivered" class="py-1 px-4 bg-[<?php echo $status_filter == 'Delivered' ? store_color('color_primary') : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Delivered' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Delivered</a>
                <a href="?status=Returned" class="py-1 px-4 bg-[<?php echo $status_filter == 'Returned' ? store_color('color_primary') : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Returned' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Returned</a>
                <a href="?status=Cancelled" class="py-1 px-4 bg-[<?php echo $status_filter == 'Cancelled' ? store_color('color_primary') : '#F3F3F3'; ?>] text-[<?php echo $status_filter == 'Cancelled' ? 'white' : '#262626'; ?>] text-[16px] font-['Open Sans'] cursor-pointer rounded-[8px]">Cancelled</a>
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

        <div class="w-full bg-[<?php echo store_color('color_bg'); ?>] py-5">
            <!-- Desktop View -->
            <div class="w-[90%] mx-auto max-w-[1440px] hidden md:block">
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
                            echo "<tr><td colspan='7' class='py-4 text-center text-[#262626] font-medium font-[Open Sans]'>
                            <div>
                            <i class='fa-regular fa-heart text-[80px] text-[<?php echo store_color('color_tint'); ?>] mx-auto leading-none'></i>
                           You have not made any orders yet
                            </div>
                            </td></tr>";
                        } else {
                            foreach ($filtered_orders as $order) {
                                $order_items = $order_items_map[$order['id']] ?? [];
                                
                                foreach ($order_items as $index => $item) {
                                    $variant = ['size' => $item['size'], 'texture' => $item['texture']];
                                    $color = $item['colors'] ?? 'N/A';
                                    
                                    // Determine status color
                                    $status_color = '';
                                    $status_text = $order['order_status'];
                                    
                                    if ($status_text == 'Processing') {
                                        $status_color = 'bg-[#E8B006]';
                                    } elseif ($status_text == 'Shipped') {
                                        $status_color = 'bg-[' . store_color('color_primary') . ']';
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
                                    $reorder_url = product_url($item);
                                    
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
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>₦" . number_format((float)$item['price']) . "/{$item['quantity']}</td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>#{$order['id']}</td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>{$order['delivery_method']}</td>
    <td>
        <button type='button' class='py-1 px-4 {$status_color} text-white text-[16px] font-[\"Open Sans\"] cursor-pointer rounded-[28px]'>{$status_text}</button>
    </td>
    <td class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>{$order_date}</td>
    <td class='relative'>
        <i class='fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer openAdminOrderMenu leading-none'></i>

        <!-- The menu for each order starts -->
        <div class='adminordersMenu h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]'>
            <div class='flex flex-col gap-3'>
                <a href='{$reorder_url}' class='text-[16px] font-medium text-[#262626]'>Re-Order</a>
                <a href='./track-order.php?id={$order['id']}' class='text-[16px] font-medium text-[#262626]'>Track Order</a>";
                
if ($status_text == 'Delivered') {
    $delivery_confirmed = !empty($order['delivery_confirmed_at']);
    if ($delivery_confirmed) {
        echo "<a href='./write-review.php?order_id={$order['id']}&product_id={$item['product_id']}' class='text-[16px] font-medium text-[#262626]'>Leave a review</a>";
    } else {
        echo "<span class='text-[16px] font-medium text-gray-400 cursor-not-allowed' title='Confirm you received this order to unlock reviews'>Leave a review</span>";
        echo "<a href='./track-order.php?id={$order['id']}' class='text-[16px] font-medium text-[#2FA05A]'>Confirm delivery received</a>";
    }
    echo "<a href='./return-request.php?item_id={$item['id']}&order_id={$order['id']}' class='text-[16px] font-medium text-[#262626]'>Return Item</a>";
} else {
    echo "<span class='text-[16px] font-medium text-gray-400 cursor-not-allowed' title='You can review this product after delivery'>Leave a review</span>";
}

echo "
                <a href='./report-issue.php?id={$order['id']}' class='text-[16px] font-medium text-[#E8B006]'>Report an issue</a>
          
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
            <div class="w-[90%] mx-auto max-w-[1440px] md:hidden">
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
                        echo "
                        <div class='py-4 text-center text-[#262626] font-medium font-[Open Sans]'>
                            <div>
                            <i class='fa-regular fa-heart text-[80px] text-[<?php echo store_color('color_tint'); ?>] mx-auto leading-none'></i>
                           You have not made any orders yet
                            </div>
                            </div>
                        ";
                    } else {
                        foreach ($filtered_orders as $order) {
                            $order_items = $order_items_map[$order['id']] ?? [];
                            
                            if (!empty($order_items)) {
                                $item = $order_items[0];
                                $variant = ['size' => $item['size'], 'texture' => $item['texture']];
                                $color = $item['colors'] ?? 'N/A';
                                
                                // Determine status color
                                $status_color = '';
                                $status_text = $order['order_status'];
                                
                                if ($status_text == 'Processing') {
                                    $status_color = 'bg-[#E8B006]';
                                } elseif($status_text == 'Shipped'){
                                    $status_color = 'bg-[' . store_color('color_primary') . ']';
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
                                $reorder_url = product_url($item);
                                
                                echo "
                                <div class='border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex flex-col gap-2'>
                                    <div class='flex items-center justify-between relative'>
                                        <p class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>{$order_date}</p>
                                   
                                        <!--  The order menu starts -->

 <i class='fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer openAdminOrderMenuForMobile leading-none'></i>

        <!-- The menu for each order starts -->
       <div class='adminordersMenuformobile hidden h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px] absolute right-0 top-[2rem]   min-h-[10rem] min-h-[10rem]'>
            <div class='flex flex-col gap-3'>
                <a href='{$reorder_url}' class='text-[16px] font-medium text-[#262626]'>Re-Order</a>
                <a href='./track-order.php?id={$order['id']}' class='text-[16px] font-medium text-[#262626]'>Track Order</a>";
                
if ($status_text == 'Delivered') {
    $delivery_confirmed = !empty($order['delivery_confirmed_at']);
    if ($delivery_confirmed) {
        echo "<a href='./write-review.php?order_id={$order['id']}&product_id={$item['product_id']}' class='text-[16px] font-medium text-[#262626]'>Leave a review</a>";
    } else {
        echo "<span class='text-[16px] font-medium text-gray-400 cursor-not-allowed' title='Confirm you received this order to unlock reviews'>Leave a review</span>";
        echo "<a href='./track-order.php?id={$order['id']}' class='text-[16px] font-medium text-[#2FA05A]'>Confirm delivery received</a>";
    }
    echo "<a href='./return-request.php?item_id={$item['id']}&order_id={$order['id']}' class='text-[16px] font-medium text-[#262626]'>Return Item</a>";
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
                                            <a href='./track-order.php?id={$order['id']}' class='text-[14px] font-[\"Open Sans\"] text-[" . store_color('color_primary') . "] font-regular underline cursor-pointer'>Track your order</a>
                                        </div>
                                        <p class='text-[#262626] text-[15px] md:text-[16px] font-[\"Open Sans\"] font-regular'>₦" . number_format((float)$item['price']) . "</p>
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
                            <div id="processing-icon" class="status-icon w-[40px] h-[40px] rounded-full bg-[<?php echo store_color('color_primary'); ?>] flex items-center justify-center z-10">
                                <i class="fa-solid fa-clipboard-check text-[20px] text-[<?php echo store_color('color_primary'); ?>] leading-none" alt="Processing"></i>
                            </div>
                            <div class="status-content ml-4">
                                <h4 class="text-[16px] font-['Open Sans'] font-semibold">Order Processing</h4>
                                <p id="processing-date" class="text-[14px] text-[#262626] font-['Open Sans']">Not processed yet</p>
                            </div>
                        </div>
                        
                        <!-- Shipped Status -->
                        <div class="status-item relative flex mb-8">
                            <div id="shipped-icon" class="status-icon w-[40px] h-[40px] rounded-full bg-[#E1E1E1] flex items-center justify-center z-10">
                                <i class="fa-solid fa-truck-fast text-[20px] text-[<?php echo store_color('color_primary'); ?>] leading-none" alt="Shipped"></i>
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
                                <i class="fa-solid fa-box text-[20px] text-[<?php echo store_color('color_primary'); ?>] leading-none" alt="Delivered"></i>
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