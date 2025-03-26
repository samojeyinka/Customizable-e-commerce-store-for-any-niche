<?php
// Include authentication utility
require_once '../includes/auth/auth.php';
require_once __DIR__ . "/../config/config.php";

// Authentication check
requireAuth();

// Get user data
$user = getCurrentUser();
$user_id = $user['id'];

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die(mysqli_error($conn));
}

// Get the order ID from the URL
$order_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$order_id) {
    header('Location: ./orders.php');
    exit;
}

// Get the order details
$sql = "SELECT o.id, o.order_total, o.delivery_method, o.pickup_location, 
               o.payment_reference, o.order_status, o.created_at, o.updated_at,
               o.payment_transaction_id, o.delivery_email, o.order_note,o.status_notes,o.dispatcher_details,
               o.processed_at, o.shipped_at, o.delivered_at, o.cancelled_at, o.returned_at
        FROM orders o
        WHERE o.id = ? AND o.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Order not found or doesn't belong to this user
    header('Location: ./orders.php');
    exit;
}

$order = $result->fetch_assoc();

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

// Get order items
$order_items = getOrderItems($conn, $order_id);

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

// Initialize status timestamps using the status-specific timestamp columns
$status_timestamps = [];

// Direct mapping to timestamp columns with fallbacks
$status_timestamps['Processing'] = !empty($order['processed_at']) ? $order['processed_at'] : $order['created_at'];
$status_timestamps['Shipped'] = !empty($order['shipped_at']) ? $order['shipped_at'] : null;
$status_timestamps['Delivered'] = !empty($order['delivered_at']) ? $order['delivered_at'] : null;
$status_timestamps['Returned'] = !empty($order['returned_at']) ? $order['returned_at'] : null;
$status_timestamps['Cancelled'] = !empty($order['cancelled_at']) ? $order['cancelled_at'] : null;

// Get the current order status
$current_status = $order['order_status'];

// Define status colors
$status_colors = [
    'Processing' => 'bg-[#E8B006]',
    'Shipped' => 'bg-[#1A237E]',
    'Delivered' => 'bg-[#39D959]',
    'Cancelled' => 'bg-red-500',
    'Returned' => 'bg-[#9C27B0]'
];

// Get the color for the current status
$status_color = isset($status_colors[$current_status]) ? $status_colors[$current_status] : 'bg-[#E8B006]';

// Format dates for display
function formatDate($date) {
    if (!$date) return "Not yet";
    return date('M d, Y h:i A', strtotime($date));
}

$processing_date = formatDate($status_timestamps['Processing']);
$shipped_date = formatDate($status_timestamps['Shipped']);
$delivered_date = formatDate($status_timestamps['Delivered']);
$returned_date = formatDate($status_timestamps['Returned']);
$cancelled_date = formatDate($status_timestamps['Cancelled']);

// Define the status sequence and determine current progress
$status_sequence = ['Processing', 'Shipped', 'Delivered'];
$current_status_index = array_search($current_status, $status_sequence);

// If the order is cancelled, we need special handling
$is_cancelled = ($current_status == 'Cancelled');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Track Order</title>
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
        .status-icon {
            transition: background-color 0.3s ease;
        }
        
        .status-line {
            position: absolute;
            left: 20px;
            top: 40px;
            width: 2px;
            height: calc(100% - 40px);
            background-color: #E1E1E1;
            z-index: 0;
        }
        
        /* Active line style */
        .status-line-active {
            background-color: #1A237E;
        }
        
        /* Cancelled state styles */
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
                    <a href="./orders.php" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">My Orders</a>
                    <img src="../assets/products/right.svg" class="w-[7px]" />
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Track Order</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto bg-[#FFFFFF] py-5">
            <div class="w-full md:w-[80%] lg:w-[70%] mx-auto">
                <h1 class="text-[24px] md:text-[28px] text-[#2C2C2C] font-['Open Sans'] font-medium mb-6 text-center">Track Your Order</h1>
                
                <!-- Order Details Card -->
                <div class="order-details bg-[#FBFBFB] p-4 md:p-6 rounded-[8px] mb-6 border-[1px] border-[#F3F3F3]">
                    <!-- <div class="flex flex-col md:flex-row justify-between mb-4">
                        <div>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']">Order ID: <span class="font-medium">#<?php echo $order['id']; ?></span></p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']">Order Date: <span class="font-medium"><?php echo formatDate($order['created_at']); ?></span></p>
                        </div>
                        <div class="mt-3 md:mt-0">
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']">Payment Ref: <span class="font-medium"><?php echo $order['payment_reference']; ?></span></p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']">Delivery Method: <span class="font-medium"><?php echo ucfirst($order['delivery_method']); ?></span></p>
                        </div>
                    </div> -->

                    <div class="flex justify-between mb-4">
                        <div>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#777777]">Status</p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#777777]">Order ID</p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#777777]">Delivery Time</p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#777777]">Order Type</p>
                                                        <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#777777]">Order Type</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#262626] font-medium"> <div class="py-1 px-4 <?php echo $status_color; ?> text-white text-[14px] md:text-[16px] font-['Open Sans'] rounded-[28px]"><?php echo $current_status; ?></div></p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#262626] font-medium">#<?php echo $order['id']; ?></p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#262626] font-medium">2-4 days</p>
                            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] text-[#262626] font-medium"><?php echo ucfirst($order['delivery_method']); ?></p>
                        </div>
                    </div>


                    
                    <?php if ($order['pickup_location']): ?>
                    <div class="mb-4">
                        <p class="text-[14px] md:text-[16px] text-[#777777] font-['Open Sans']">Pickup Location: <span class="font-medium text-[#262626]"><?php echo $order['pickup_location']; ?></span></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($order['order_note']) && !empty($order['status_notes'])): ?>
    <div class="mb-4 bg-blue-100 border-l-4 border-blue-700 p-3 rounded-lg flex items-start gap-2">
        <img src="../assets/user/info.svg" alt="Note Icon" class="w-6 h-6 mt-1"> 
        <div>
            <p class="text-[14px] md:text-[16px] text-[#1A237E] font-['Open Sans'] font-semibold">Order Notes:</p>
            <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']">
                <?php echo $order['status_notes']; ?>
            </p>
        </div>
    </div>
<?php endif; ?>

                </div>
                
                <!-- Status Tracker -->
                <div class="status-tracker p-4 md:p-6 border-[1px] border-[#E1E1E1] rounded-[8px] mb-6">
                  
                    
                  <div class="tracker-timeline mt-8">
                      <div class="relative">
                          <?php if (!$is_cancelled && $order['order_status'] !== 'Returned'): ?>
                          <!-- Status Progress Line -->
                          <div class="status-line"></div>
                          
                          <!-- Processing Status -->
                          <div class="status-item relative flex mb-12">
                              <div class="status-icon w-[40px] h-[40px] rounded-full <?php echo ($current_status_index >= 0) ? 'bg-[#1A237E]' : 'bg-[#E1E1E1]'; ?> flex items-center justify-center z-10">
                                  <!-- <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M9 16.2L4.8 12L3.4 13.4L9 19L21 7L19.6 5.6L9 16.2Z" fill="white"/>
                                  </svg> -->
                                  <img src="../assets/user/processed.svg" alt="processing"/>
                              </div>
                              <div class="status-content ml-4">
                                  <h4 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold">Order Processing</h4>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']"><?php echo $processing_date; ?></p>
                              </div>
                          </div>
                          
                          <!-- Shipped Status -->
                          <div class="status-item relative flex mb-12">
                          <div class="status-icon w-[40px] h-[40px] rounded-full 
  <?php echo ($current_status_index >= 1) ? 'bg-[#1A237E]' : 'bg-[#E1E1E1]'; ?> 
  flex items-center justify-center z-10">
  
  <img src="../assets/user/<?php echo ($current_status_index >= 1) ? 'shipped.svg' : 'not-shipped.svg'; ?>" 
       alt="Shipping Status" 
       class="min-w-[50px] h-[20px]" />
</div>

                              <div class="status-content ml-4">
                                  <h4 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold">Order Shipped</h4>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']"><?php echo $shipped_date; ?></p>
                                  <?php if (isset($order['dispatcher_details']) && !empty($order['dispatcher_details'])): ?>
                  <div class="mb-4">
    <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] bg-white p-2 rounded-[4px] mt-1"><?php echo $order['dispatcher_details']; ?></p>
                  </div>
                  <?php endif; ?>
                              </div>

                           
                  
                          </div>
                          
                          <!-- Delivered Status -->
                          <div class="status-item relative flex">
                          <div class="status-icon w-[40px] h-[40px] rounded-full 
  <?php echo ($current_status_index >= 2) ? 'bg-[#1A237E]' : 'bg-[#E1E1E1]'; ?> 
  flex items-center justify-center z-10">

  <img src="../assets/user/<?php echo ($current_status_index >= 2) ? 'shipped.svg' : 'not-shipped.svg'; ?>" 
       alt="Delivery Status" 
       class="w-[20px] h-[20px]" />
</div>

                              <div class="status-content ml-4">
                                  <h4 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold">Order Delivered</h4>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']"><?php echo $delivered_date; ?></p>
                              </div>
                          </div>
                          
                          <?php elseif ($order['order_status'] === 'Returned'): ?>
                          <!-- Returned Order Status -->
                          <div class="status-returned relative flex">
                              <div class="status-icon w-[40px] h-[40px] rounded-full bg-[#9C27B0] flex items-center justify-center z-10">
                                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="white"/>
                                  </svg>
                              </div>
                              <div class="status-content ml-4">
                                  <h4 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold">Order Returned</h4>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']"><?php echo formatDate($status_timestamps['Returned'] ?? $order['updated_at']); ?></p>
                                  <?php if (isset($order['order_note']) && !empty($order['order_note'])): ?>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mt-2 max-w-[300px] md:max-w-[500px]"><?php echo $order['order_note']; ?></p>
                                  <?php endif; ?>
                              </div>
                          </div>
                          <?php else: ?>
                          <!-- Cancelled Order Status -->
                          <div class="status-cancelled relative flex">
                              <div class="status-icon w-[40px] h-[40px] rounded-full bg-red-500 flex items-center justify-center z-10">
                                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="white"/>
                                  </svg>
                              </div>
                              <div class="status-content ml-4">
                                  <h4 class="text-[16px] md:text-[18px] font-['Open Sans'] font-semibold">Order Cancelled</h4>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans']"><?php echo formatDate($status_timestamps['Cancelled'] ?? $order['updated_at']); ?></p>
                                  <?php if (isset($order['order_note']) && !empty($order['order_note'])): ?>
                                  <p class="text-[14px] md:text-[16px] text-[#262626] font-['Open Sans'] mt-2 max-w-[300px] md:max-w-[500px]"><?php echo $order['order_note']; ?></p>
                                  <?php endif; ?>
                              </div>
                          </div>
                          <?php endif; ?>
                      </div>
                  </div>
              </div>
                <!-- Order Items -->
                <!-- <div class="order-items border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6">
                    <h3 class="text-[18px] md:text-[20px] font-['Open Sans'] font-semibold mb-4">Order Items</h3>
                    
                    <div class="flex flex-col gap-4">
                        <?php foreach ($order_items as $item): 
                            $color = getProductColor($conn, $item['product_id']);
                            $image_path = isset($item['image_path']) ? "../assets/products/" . $item['image_path'] : "../assets/products/img1.svg";
                        ?>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-3 border-[1px] border-[#E1E1E1] rounded-[4px]">
                            <div class="flex items-center gap-3">
                                <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                                    <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" alt="<?php echo $item['product_name']; ?>" />
                                </div>
                                <div>
                                    <h4 class="text-[16px] font-['Open Sans'] font-medium"><?php echo $item['product_name']; ?></h4>
                                    <p class="text-[14px] text-[#262626] font-['Open Sans']">Size: <?php echo $item['size']; ?> • Color: <?php echo $color; ?></p>
                                    <p class="text-[14px] text-[#262626] font-['Open Sans']">Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                            </div>
                            <div class="mt-3 md:mt-0 ml-0 md:ml-auto">
                                <p class="text-[16px] font-['Open Sans'] font-medium">₦<?php echo number_format($item['price']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-6 flex justify-between">
                        <p class="text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Total Amount:</p>
                        <p class="text-[16px] md:text-[18px] font-['Open Sans'] font-bold">₦<?php echo number_format($order['order_total']); ?></p>
                    </div>
                </div> -->
                
              <!-- Modification for the Order Items section in track-order.php -->
<!-- Replace the existing Order Items section with this code -->

<!-- Order Items -->
<div class="order-items border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6">
    <h3 class="text-[18px] md:text-[20px] font-['Open Sans'] font-semibold mb-4">Order Items</h3>
    
    <div class="flex flex-col gap-4">
        <?php foreach ($order_items as $item): 
            $color = getProductColor($conn, $item['product_id']);
            $image_path = isset($item['image_path']) ? "../assets/products/" . $item['image_path'] : "../assets/products/img1.svg";
            
            // Check if return is possible for this order and item
            $can_request_return = ($current_status == 'Delivered' || $current_status == 'Shipped');
            
            // Check if this item already has a return request
            $return_check_sql = "SELECT id FROM return_requests WHERE order_id = ? AND order_item_id = ? LIMIT 1";
            $return_check_stmt = $conn->prepare($return_check_sql);
            $return_check_stmt->bind_param("ii", $order_id, $item['id']);
            $return_check_stmt->execute();
            $return_result = $return_check_stmt->get_result();
            $has_return_request = ($return_result->num_rows > 0);
        ?>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-3 border-[1px] border-[#E1E1E1] rounded-[4px]">
            <div class="flex items-center gap-3">
                <div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden">
                    <img src="<?php echo $image_path; ?>" class="w-full h-full object-cover" alt="<?php echo $item['product_name']; ?>" />
                </div>
                <div>
                    <h4 class="text-[16px] font-['Open Sans'] font-medium"><?php echo $item['product_name']; ?></h4>
                    <p class="text-[14px] text-[#262626] font-['Open Sans']">Size: <?php echo $item['size']; ?> • Color: <?php echo $color; ?></p>
                    <p class="text-[14px] text-[#262626] font-['Open Sans']">Qty: <?php echo $item['quantity']; ?></p>
                    
                    <?php if ($can_request_return): ?>
                    <div class="mt-2">
                        <?php if ($has_return_request): ?>
                        <span class="text-[13px] text-[#1A237E] font-['Open Sans'] italic">Return request pending</span>
                        <?php else: ?>
                        <a href="./return-request.php?order_id=<?php echo $order_id; ?>&item_id=<?php echo $item['id']; ?>" class="inline-block py-1 px-3 bg-[#1A237E] text-white text-[13px] font-['Open Sans'] rounded-[4px]">Request Return</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-3 md:mt-0 ml-0 md:ml-auto">
                <p class="text-[16px] font-['Open Sans'] font-medium">₦<?php echo number_format($item['price']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-6 flex justify-between">
        <p class="text-[16px] md:text-[18px] font-['Open Sans'] font-medium">Total Amount:</p>
        <p class="text-[16px] md:text-[18px] font-['Open Sans'] font-bold">₦<?php echo number_format($order['order_total']); ?></p>
    </div>
</div>
              
                <!-- Actions -->
                <div class="actions mt-6 flex flex-col md:flex-row gap-3 justify-end">
                    <a href="./orders.php" class="py-2 px-4 bg-[#F3F3F3] text-[#262626] text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
                    
                    <div class="flex items-center gap-3">
                    <?php if ($current_status != 'Cancelled' && $current_status != 'Delivered'): ?>
                    <a href="./report-issue.php?id=<?php echo $order_id; ?>" class="w-full py-2 px-4 bg-[#E8B006] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px] text-nowrap">Report an Issue</a>
                    <?php endif; ?>
                    
                    <a href="../products/show.php?id=<?php echo $order_items[0]['product_id']; ?>" class="w-full py-2 px-4 bg-[#1A237E] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Re-Order</a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        include(__DIR__ . '/../includes/footer.php');
        ?>
    </main>

    <script src="<?php echo DOMAIN; ?>/functions/modals.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/modals2.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/functions.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/tabs.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/accordion.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/faq.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/dropdown.js"></script>
    <script src="<?php echo DOMAIN; ?>/functions/openoptions.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we need to adjust the status line colors based on current status
        const statusLine = document.querySelector('.status-line');
        const currentStatus = '<?php echo $current_status; ?>';
        const currentStatusIndex = <?php echo $current_status_index !== false ? $current_status_index : '-1'; ?>;
        
        if (currentStatus !== 'Cancelled' && statusLine) {
            // Add active class to the line if we're past the first step
            if (currentStatusIndex > 0) {
                statusLine.classList.add('status-line-active');
            }
        }
    });
    </script>
</body>

</html>