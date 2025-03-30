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




// Initialize variables
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$success_message = '';
$error_message = '';
$product = null;
$order = null;
$existing_review = null;

// Validate parameters
if (!$order_id || !$product_id) {
    $error_message = "You can only leave a review for a product you bought.";
} else {
    // Get order details to verify it belongs to the user and is delivered
    $order_sql = "SELECT o.id, o.order_status, o.delivered_at, 
                     p.product_id, p.product_name, p.colors,
                     (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as image_path
                 FROM orders o
                 JOIN order_items oi ON o.id = oi.order_id
                 JOIN products p ON oi.product_id = p.product_id
                 WHERE o.id = ? AND o.user_id = ? AND p.product_id = ?";
                 
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("iii", $order_id, $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $error_message = "Order not found or product not part of this order.";
    } else {
        $product = $result->fetch_assoc();
        
        // Check if order is delivered
        if ($product['order_status'] != 'Delivered') {
            $error_message = "You can only review products from delivered orders.";
        } else {
            // Check if user has already reviewed this product for this order
            $check_sql = "SELECT * FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("iii", $user_id, $product_id, $order_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $existing_review = $check_result->fetch_assoc();
            }
        }
    }
}

// Process form submission - KEEP ONLY ONE VERSION OF THIS BLOCK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = intval($_POST['rating']);
    $review_text = $conn->real_escape_string($_POST['review_text']);
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $error_message = "Rating must be between 1 and 5 stars.";
    } else if (empty($error_message)) { // If no previous errors
        if ($existing_review) {
            // Update existing review
            $sql = "UPDATE reviews 
                    SET rating = ?, review_text = ?, created_at = NOW() 
                    WHERE review_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $rating, $review_text, $existing_review['review_id']);
        } else {
            // Insert new review
            $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, review_text) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiiis", $user_id, $product_id, $order_id, $rating, $review_text);
        }
        
        if ($stmt->execute()) {
            // Get the review ID (either from existing review or newly inserted)
            $review_id = $existing_review ? $existing_review['review_id'] : $stmt->insert_id;
            
            // Include notifications functions
            require_once '../includes/notifications.php';
            
            $product_name = $product['product_name'];
            $action_text = $existing_review ? "updated their review" : "left a new review";
            
            // Create notification for admin
            add_notification(
                $conn,
                'review',
                "New Product Review",
                "A customer has $action_text for $product_name with a rating of $rating/5 stars.",
                $review_id,
                'review',
                null, // null for_user_id means it's for all admins
                1     // 1 means it's for admin
            );
            
            // Create notification for the user too
            add_notification(
                $conn,
                'review_confirmation',
                'Review Submitted',
                "Thank you for reviewing $product_name. Your feedback helps other shoppers make better decisions.",
                $review_id,
                'review',
                $user_id, // specific user
                0         // 0 means it's not for admin
            );
            
            $success_message = "Your review has been successfully submitted!";
            
            // Refresh existing review data
            if (!$existing_review) {
                $check_sql = "SELECT * FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("iii", $user_id, $product_id, $order_id);
                $stmt->execute();
                $check_result = $stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $existing_review = $check_result->fetch_assoc();
                }
            }
        } else {
            $error_message = "Error submitting review: " . $conn->error;
        }
    }
}

// Get image path
$image_path = isset($product['image_path']) ? "../assets/products/" . $product['image_path'] : "../assets/products/img1.svg";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH | Write a Review</title>
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
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            cursor: pointer;
            width: 36px;
            height: 36px;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>');
            background-size: 36px;
            background-position: center;
            background-repeat: no-repeat;
            filter: grayscale(100%);
            opacity: 0.5;
            transition: all 0.2s ease;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            filter: grayscale(0);
            opacity: 1;
            color: #FFD700;
            fill: #FFD700;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="%23FFD700" stroke="%23FFD700" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>');
        }
        
        .rating-display {
            display: flex;
            align-items: center;
        }
        
        .rating-display .star {
            width: 20px;
            height: 20px;
            margin-right: 2px;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="%23FFD700" stroke="%23FFD700" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .rating-display .star.empty {
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>');
            filter: grayscale(100%);
            opacity: 0.5;
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
                    <span class="text-[#18237E] text-[13px] md:text-[14px] font-Onest font-medium">Write a Review</span>
                </div>
            </div>
        </section>

        <div class="w-[90%] mx-auto bg-[#FFFFFF] py-5">
            <div class="w-full md:w-[80%] lg:w-[60%] mx-auto">
                <h1 class="text-[24px] md:text-[28px] font-['Open Sans'] font-bold mb-6">Write a Review</h1>
                
                <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error_message; ?>
                </div>
                <div class="flex justify-center mt-6">
                    <a href="./orders.php" class="py-2 px-4 bg-[#1A237E] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">Back to Orders</a>
                </div>
                <?php elseif (!empty($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($product) && empty($error_message)): ?>
                <!-- Product Details -->
                <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-4 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-[120px] h-[120px] rounded-[4px] overflow-hidden">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1">
                            <h2 class="text-[18px] font-medium mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                            <p class="text-[14px] text-[#262626]">Color: <?php echo htmlspecialchars($product['colors']); ?></p>
                            <p class="text-[14px] text-[#262626]">Order #<?php echo $order_id; ?></p>
                            <p class="text-[14px] text-[#262626]">Delivered on: <?php echo date('M d, Y', strtotime($product['delivered_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Review Form -->
                <div class="border-[1px] border-[#E1E1E1] rounded-[8px] p-4 md:p-6">
                    <?php if ($existing_review): ?>
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                        You've already reviewed this product. You can update your review below.
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="?order_id=<?php echo $order_id; ?>&product_id=<?php echo $product_id; ?>">
                        <div class="mb-6">
                            <label class="block text-[16px] font-medium mb-2">Your Rating</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" <?php echo ($existing_review && $existing_review['rating'] == 5) ? 'checked' : ''; ?> />
                                <label for="star5" title="5 stars"></label>
                                
                                <input type="radio" id="star4" name="rating" value="4" <?php echo ($existing_review && $existing_review['rating'] == 4) ? 'checked' : ''; ?> />
                                <label for="star4" title="4 stars"></label>
                                
                                <input type="radio" id="star3" name="rating" value="3" <?php echo ($existing_review && $existing_review['rating'] == 3) ? 'checked' : ''; ?> />
                                <label for="star3" title="3 stars"></label>
                                
                                <input type="radio" id="star2" name="rating" value="2" <?php echo ($existing_review && $existing_review['rating'] == 2) ? 'checked' : ''; ?> />
                                <label for="star2" title="2 stars"></label>
                                
                                <input type="radio" id="star1" name="rating" value="1" <?php echo ($existing_review && $existing_review['rating'] == 1) ? 'checked' : ''; ?> />
                                <label for="star1" title="1 star"></label>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="review_text" class="block text-[16px] font-medium mb-2">Your Review</label>
                            <textarea 
                                id="review_text" 
                                name="review_text" 
                                rows="6" 
                                class="w-full p-3 border border-[#E1E1E1] rounded-[4px] focus:outline-none focus:ring-2 focus:ring-[#1A237E]"
                                placeholder="Share your experience with this product..."
                            ><?php echo $existing_review ? htmlspecialchars($existing_review['review_text']) : ''; ?></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <a href="./orders.php" class="py-2 px-4 bg-[#F3F3F3] text-[#262626] text-center text-[16px] font-['Open Sans'] rounded-[4px]">Cancel</a>
                            <button type="submit" name="submit_review" class="py-2 px-4 bg-[#1A237E] text-white text-center text-[16px] font-['Open Sans'] rounded-[4px]">
                                <?php echo $existing_review ? 'Update Review' : 'Submit Review'; ?>
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
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
</body>

</html>