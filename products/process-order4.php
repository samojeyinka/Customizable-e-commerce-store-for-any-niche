<?php
// Strict error handling but with logging enabled
ini_set('display_errors', 0);
error_reporting(E_ALL);
error_log("Process order starting");

// Set JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Logging function
function logOrderError($message) {
    error_log('[Order Processing Error] ' . $message);
}

try {
    // Start session and include necessary files
    session_start();
    require_once '../config/connect.php';
    require_once '../includes/auth/auth.php';

    // Authentication and user validation
    requireAuth();
    $user = getCurrentUser();
    $user_id = $user['id'];
    
    logOrderError("User ID: " . $user_id);

    // Validate essential payment information
    if (empty($_POST['payment_reference'])) {
        throw new Exception('Missing payment reference');
    }
    
    // If transaction_id is missing, use payment_reference as a fallback
    if (empty($_POST['transaction_id'])) {
        $_POST['transaction_id'] = $_POST['payment_reference'];
        logOrderError("Using payment reference as transaction ID fallback");
    }

    // Begin database transaction
    mysqli_begin_transaction($con);

    // Prepare order details
    $delivery_method = $_POST['delivery_method'] ?? 'pickup';
    $pickup_location = $delivery_method === 'pickup' 
        ? ($_POST['pickup_location'] ?? 'Lagos Store') 
        : null;
    $delivery_email = $_POST['email'] ?? $user['email'];
    $order_note = $_POST['note'] ?? '';

    // $cart_items_query = "SELECT 
    //                   c.product_id, 
    //                   c.variant_id, 
    //                   c.quantity, 
    //                   p.product_name,
    //                   COALESCE(v.size, 'Default') as size,
    //                   COALESCE(v.discount_price, v.original_price, 0) AS unit_price,
    //                   (c.quantity * COALESCE(v.discount_price, v.original_price, 0)) AS item_total
    //                  FROM cart c
    //                  JOIN products p ON c.product_id = p.product_id
    //                  LEFT JOIN product_variants v ON c.variant_id = v.variant_id
    //                  WHERE c.user_id = $user_id";

    // In your cart items query, modify how prices are retrieved
$cart_items_query = "SELECT 
c.product_id, 
c.variant_id, 
c.quantity, 
p.product_name,
COALESCE(v.size, 'Default') as size,
COALESCE(v.discount_price, v.original_price, 0) AS unit_price,
(c.quantity * COALESCE(v.discount_price, v.original_price, 0)) AS item_total
FROM cart c
JOIN products p ON c.product_id = p.product_id
LEFT JOIN product_variants v ON c.variant_id = v.variant_id
WHERE c.user_id = $user_id";
    
    logOrderError("Cart query: " . $cart_items_query);
    
    $items_result = mysqli_query($con, $cart_items_query);
    if (!$items_result) {
        logOrderError("Cart items query failed: " . mysqli_error($con));
        throw new Exception('Failed to retrieve cart items');
    }
    
    // Calculate subtotal and prepare items for order_items insertion
    $subtotal = 0;
    $order_items = [];
    $item_count = 0;
    
    while ($item = mysqli_fetch_assoc($items_result)) {
        $subtotal += $item['item_total'];
        $order_items[] = $item;
        $item_count++;
        
        logOrderError("Cart item: {$item['product_name']} ({$item['size']}), Quantity: {$item['quantity']}, Unit Price: {$item['unit_price']}, Total: {$item['item_total']}");
    }
    
    logOrderError("Subtotal from cart items: $subtotal, Item count: $item_count");
    
    // ***FALLBACK FOR BUY NOW SCENARIO***
    // If cart is empty but there's a buy_now item in session, use that instead
    if ($item_count == 0 && isset($_SESSION['buy_now_item'])) {
        $buy_now = $_SESSION['buy_now_item'];
        logOrderError("Cart empty, using buy_now item: " . json_encode($buy_now));
        
        $quantity = $buy_now['quantity'] ?? 1;
        $price = $buy_now['price'] ?? 0;
        $item_total = $quantity * $price;
        
        $order_items[] = [
            'product_id' => $buy_now['product_id'],
            'variant_id' => $buy_now['variant_id'],
            'quantity' => $quantity,
            'product_name' => $buy_now['product_name'] ?? 'Product',
            'size' => $buy_now['size'] ?? 'Default',
            'unit_price' => $price,
            'item_total' => $item_total
        ];
        
        $subtotal = $item_total;
        $item_count = 1;
    }
    
    // If still empty, throw exception
    if ($item_count == 0) {
        throw new Exception('Cart is empty');
    }

    // Calculate shipping fee
    $base_shipping_fee = 2000;
    $shipping_fee = $delivery_method === 'express' ? ($base_shipping_fee * $item_count) : 0;
    logOrderError("Shipping fee: $shipping_fee");
    
    // Calculate final total
    $order_total = $subtotal + $shipping_fee;
    logOrderError("Order total: $order_total");
    
    // Create the order
    $order_query = "INSERT INTO orders (
        user_id, order_total, shipping_fee, delivery_method, 
        pickup_location, payment_reference, payment_transaction_id, 
        delivery_email, order_note, order_status
    ) VALUES (
        $user_id, 
        $order_total, 
        $shipping_fee, 
        '$delivery_method', 
        " . ($pickup_location ? "'$pickup_location'" : "NULL") . ", 
        '" . mysqli_real_escape_string($con, $_POST['payment_reference']) . "', 
        '" . mysqli_real_escape_string($con, $_POST['transaction_id']) . "', 
        '" . mysqli_real_escape_string($con, $delivery_email) . "', 
        '" . mysqli_real_escape_string($con, $order_note) . "', 
        'Processing'
    )";
    
    logOrderError("Order query: " . $order_query);
    
    if (!mysqli_query($con, $order_query)) {
        logOrderError("Order insertion failed: " . mysqli_error($con));
        throw new Exception('Order insertion failed: ' . mysqli_error($con));
    }

    $order_id = mysqli_insert_id($con);
    logOrderError("Order created with ID: " . $order_id);

    // Insert each item into order_items individually to handle potential variant_id issues
    foreach ($order_items as $item) {
        // If variant_id is NULL or invalid, find the first variant for this product
        if (empty($item['variant_id'])) {
            logOrderError("Item has no variant_id, searching for first variant: " . $item['product_name']);
            
            $find_variant_query = "SELECT variant_id FROM product_variants 
                                  WHERE product_id = {$item['product_id']} 
                                  ORDER BY variant_id ASC LIMIT 1";
            
            $variant_result = mysqli_query($con, $find_variant_query);
            
            if ($variant_result && mysqli_num_rows($variant_result) > 0) {
                $variant_data = mysqli_fetch_assoc($variant_result);
                $item['variant_id'] = $variant_data['variant_id'];
                logOrderError("Found first variant: " . $item['variant_id']);
            } else {
                logOrderError("No variants found for product: " . $item['product_name']);
                continue; // Skip this item if no variants found
            }
        }
        
        $insert_item_query = "INSERT INTO order_items (
            order_id, product_id, variant_id, quantity, price
        ) VALUES (
            $order_id,
            {$item['product_id']},
            {$item['variant_id']},
            {$item['quantity']},
            {$item['unit_price']}
        )";
        
        logOrderError("Item insert query: " . $insert_item_query);
        
        if (!mysqli_query($con, $insert_item_query)) {
            logOrderError("Failed to insert order item: " . mysqli_error($con));
            throw new Exception('Failed to insert order item: ' . mysqli_error($con));
        }
    }

    // Clear user's cart
    $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
    mysqli_query($con, $clear_cart_query);
    
    // Clear buy_now item from session if it exists
    if (isset($_SESSION['buy_now_item'])) {
        unset($_SESSION['buy_now_item']);
    }

    // Commit transaction
    mysqli_commit($con);

    // Send success response
    echo json_encode([
        'status' => 'success', 
        'order_id' => $order_id, 
        'message' => 'Order processed successfully'
    ]);
    exit;

} catch (Exception $e) {
    // Rollback transaction
    if (isset($con) && mysqli_ping($con)) {
        mysqli_rollback($con);
    }

    // Log and return error
    logOrderError($e->getMessage());
    
    // Send error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
    exit;
} finally {
    // Ensure database connection is closed
    if (isset($con)) {
        mysqli_close($con);
    }
}