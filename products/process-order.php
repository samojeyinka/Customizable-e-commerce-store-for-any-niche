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

    // Use the same cart query from checkout.php to ensure price consistency
    $cart_query = "SELECT DISTINCT c.cart_id, c.quantity, p.product_id, p.product_name, 
        i.image_path AS main_image, v.size, v.variant_id, v.status,
        COALESCE(v.discount_price, v.original_price) AS price,
        (SELECT MIN(COALESCE(pv.discount_price, pv.original_price)) 
         FROM product_variants pv 
         WHERE pv.product_id = p.product_id) AS min_variant_price,
        (SELECT pv_first.size 
         FROM product_variants pv_first 
         WHERE pv_first.product_id = p.product_id 
         ORDER BY pv_first.variant_id ASC 
         LIMIT 1) AS first_variant_size
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
        LEFT JOIN product_variants v ON c.variant_id = v.variant_id
        WHERE c.user_id = ?";

    $stmt = mysqli_prepare($con, $cart_query);
    if (!$stmt) {
        logOrderError("Prepare failed: " . mysqli_error($con));
        throw new Exception("Database error");
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Process cart items exactly like in checkout.php
    $cart_items = [];
    $subtotal = 0;
    $cart_item_ids = [];

    while ($item = mysqli_fetch_assoc($result)) {
        $cart_item_ids[] = $item['cart_id'];
        
        // Use price logic with fallback to minimum variant price
        if (!empty($item['price']) && $item['price'] > 0) {
            $price = floatval($item['price']);
        } else if (!empty($item['min_variant_price']) && $item['min_variant_price'] > 0) {
            $price = floatval($item['min_variant_price']);
        } else {
            $price = 0;
        }
        
        // Calculate item total
        $item_total = $price * $item['quantity'];
        
        // Add calculated values to item
        $item['price'] = $price;
        $item['item_total'] = $item_total;
        
        // Add to cart items array and calculate subtotal
        $cart_items[] = $item;
        $subtotal += $item_total;
    }

    // Deduplicate cart items if needed (same as checkout.php)
    $unique_cart_items = [];
    $unique_cart_ids = [];

    foreach ($cart_items as $item) {
        if (!in_array($item['cart_id'], $unique_cart_ids)) {
            $unique_cart_ids[] = $item['cart_id'];
            $unique_cart_items[] = $item;
        }
    }

    $cart_items = $unique_cart_items;

    // Recalculate subtotal after deduplication
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['item_total'];
    }

    // If using buy now item from session
    if (empty($cart_items) && isset($_SESSION['buy_now_item'])) {
        $buy_now = $_SESSION['buy_now_item'];
        logOrderError("Using buy now item: " . json_encode($buy_now));
        
        $quantity = $buy_now['quantity'] ?? 1;
        $price = $buy_now['price'] ?? 0;
        $item_total = $price * $quantity;
        
        $cart_items[] = $buy_now;
        $subtotal = $item_total;
    }

    // Calculate shipping fee (same as checkout.php)
    $product_count = count($cart_items);
    $base_shipping_fee = 2000;
    $shipping_fee = ($delivery_method === 'express') ? ($base_shipping_fee * $product_count) : 0;
    $total = $subtotal + $shipping_fee;

    // If still empty, throw exception
    if (empty($cart_items)) {
        throw new Exception('Cart is empty');
    }

    logOrderError("Order details: Subtotal=$subtotal, Shipping=$shipping_fee, Total=$total");
    
    // Create the order with correct totals
    $order_query = "INSERT INTO orders (
        user_id, order_total, shipping_fee, delivery_method, 
        pickup_location, payment_reference, payment_transaction_id, 
        delivery_email, order_note, order_status
    ) VALUES (
        $user_id, 
        $total, 
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

    // Insert each item into order_items with correct pricing
    foreach ($cart_items as $item) {
        // If variant_id is missing, try to find the first variant
        if (empty($item['variant_id'])) {
            logOrderError("Finding first variant for product " . $item['product_id']);
            $find_variant_query = "SELECT variant_id FROM product_variants 
                                  WHERE product_id = " . $item['product_id'] . " 
                                  ORDER BY variant_id ASC LIMIT 1";
            
            $variant_result = mysqli_query($con, $find_variant_query);
            if ($variant_result && mysqli_num_rows($variant_result) > 0) {
                $variant_data = mysqli_fetch_assoc($variant_result);
                $item['variant_id'] = $variant_data['variant_id'];
            }
        }
        
        // Make sure we have a valid variant_id
        if (empty($item['variant_id'])) {
            logOrderError("Skipping product " . $item['product_id'] . " - no variant found");
            continue;
        }

        // Get correct item price from the cart item calculation
        $price = $item['price'];
        
        $insert_item_query = "INSERT INTO order_items (
            order_id, product_id, variant_id, quantity, price
        ) VALUES (
            $order_id,
            {$item['product_id']},
            {$item['variant_id']},
            {$item['quantity']},
            {$price}
        )";
        
        logOrderError("Item insert query: " . $insert_item_query);
        
        if (!mysqli_query($con, $insert_item_query)) {
            logOrderError("Failed to insert order item: " . mysqli_error($con));
            throw new Exception('Failed to insert order item: ' . mysqli_error($con));
        }
    }

    // Insert transaction into transaction_history table
    $payment_method = $_POST['payment_method'] ?? 'online';
    $currency = $_POST['currency'] ?? 'NGN';
    $customer_name = $user['first_name'] . ' ' . $user['last_name'];
    if (empty($customer_name) || $customer_name == ' ') {
        $customer_name = $_POST['first_name'] . ' ' . $_POST['last_name'];
    }
    
    $notes = "Order #$order_id - $delivery_method delivery";
    if ($delivery_method === 'pickup') {
        $notes .= " from $pickup_location";
    }
    
    $transaction_insert = "INSERT INTO transaction_history (
        user_id, customer_name, email, transaction_id, payment_reference,
        order_id, amount, status, payment_method, currency, 
        delivery_method, transaction_date, notes
    ) VALUES (
        $user_id,
        '" . mysqli_real_escape_string($con, $customer_name) . "',
        '" . mysqli_real_escape_string($con, $delivery_email) . "',
        '" . mysqli_real_escape_string($con, $_POST['transaction_id']) . "',
        '" . mysqli_real_escape_string($con, $_POST['payment_reference']) . "',
        $order_id,
        $total,
        'completed',
        '" . mysqli_real_escape_string($con, $payment_method) . "',
        '" . mysqli_real_escape_string($con, $currency) . "',
        '" . mysqli_real_escape_string($con, $delivery_method) . "',
        NOW(),
        '" . mysqli_real_escape_string($con, $notes) . "'
    )";
    
    logOrderError("Transaction history query: " . $transaction_insert);
    
    if (!mysqli_query($con, $transaction_insert)) {
        logOrderError("Transaction history insertion failed: " . mysqli_error($con));
        // Continue processing even if transaction history fails
        // This is not critical to order completion
    } else {
        logOrderError("Transaction history created successfully");
    }

    // Clear user's cart
    $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
    mysqli_query($con, $clear_cart_query);
    
    // Clear buy_now item from session if it exists
    if (isset($_SESSION['buy_now_item'])) {
        unset($_SESSION['buy_now_item']);
    }

    // Update user profile if requested
    if (isset($_POST['update_profile']) && $_POST['update_profile'] == '1') {
        // Update the user's profile with the shipping/billing details from this order
        $update_profile_query = "UPDATE profiles SET 
            first_name = ?, last_name = ?, phone = ?, country = ?, 
            address = ?, state = ?, city = ?, zip_code = ?,
            billing_first_name = ?, billing_last_name = ?, billing_country = ?,
            billing_address = ?, billing_state = ?, billing_city = ?, 
            billing_zip_code = ?, billing_phone = ?
            WHERE user_id = ?";
            
        $stmt = mysqli_prepare($con, $update_profile_query);
        mysqli_stmt_bind_param($stmt, "ssssssssssssssssi", 
            $_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['country'],
            $_POST['address'], $_POST['state'], $_POST['city'], $_POST['zip_code'],
            $_POST['billing_first_name'] ?? $_POST['first_name'], 
            $_POST['billing_last_name'] ?? $_POST['last_name'],
            $_POST['billing_country'] ?? $_POST['country'],
            $_POST['billing_address'] ?? $_POST['address'], 
            $_POST['billing_state'] ?? $_POST['state'],
            $_POST['billing_city'] ?? $_POST['city'], 
            $_POST['billing_zip_code'] ?? $_POST['zip_code'],
            $_POST['billing_phone'] ?? $_POST['phone'],
            $user_id
        );
        mysqli_stmt_execute($stmt);
        logOrderError("User profile updated with order details");
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