<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include authentication utility
require_once '../../includes/auth/auth.php';
require_once "../../config/config.php";

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'victosah');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verify if product_id is provided
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Get product details
$product = null;
if ($product_id > 0) {
    $product_query = "SELECT p.*, c.category_title, b.brand_title 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.category_id 
                     LEFT JOIN brands b ON p.brand_id = b.brand_id 
                     WHERE p.product_id = ?";
    $stmt = mysqli_prepare($conn, $product_query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    }
}

// Get reviews with filter options
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query for reviews
$reviews_sql = "SELECT r.review_id, r.user_id, r.product_id, r.rating, r.review_text, r.created_at, r.status,
               CONCAT(p.first_name, ' ', p.last_name) as reviewer_name,
               pr.product_name
               FROM reviews r
               JOIN profiles p ON r.user_id = p.user_id
               JOIN products pr ON r.product_id = pr.product_id
               WHERE 1=1";

// Add product filter if product_id is valid
if ($product_id > 0) {
    $reviews_sql .= " AND r.product_id = " . $product_id;
}

// Add status filter if not 'all'
if ($filter_status !== 'all') {
    $reviews_sql .= " AND r.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

// Add search filter if provided
if (!empty($search_query)) {
    $search_term = mysqli_real_escape_string($conn, $search_query);
    $reviews_sql .= " AND (p.first_name LIKE '%$search_term%' OR 
                          p.last_name LIKE '%$search_term%' OR 
                          r.review_text LIKE '%$search_term%' OR 
                          pr.product_name LIKE '%$search_term%')";
}

$reviews_sql .= " ORDER BY r.created_at DESC";

// Execute the query
$reviews_result = mysqli_query($conn, $reviews_sql);
if (!$reviews_result) {
    die("Error in query: " . mysqli_error($conn));
}

// Count reviews for this product
$count_sql = "SELECT COUNT(*) as count FROM reviews";
if ($product_id > 0) {
    $count_sql .= " WHERE product_id = " . $product_id;
}
$count_result = mysqli_query($conn, $count_sql);
$count_data = mysqli_fetch_assoc($count_result);
$total_reviews = $count_data['count'];

// Function to generate star rating HTML
function generateStarRating($rating) {
    $html = '<div class="flex items-center gap-1">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<img src="../assets/products/star.svg" class="w-[16px]" />';
        } else {
            $html .= '<img src="../assets/products/lstar.svg" class="w-[16px]" />';
        }
    }
    $html .= '</div>';
    return $html;
}

// Function to get status color class
function getStatusColorClass($status) {
    switch ($status) {
        case 'approved':
            return 'text-green-500';
        case 'pending':
            return 'text-yellow-500';
        case 'rejected':
            return 'text-red-500';
        default:
            return 'text-gray-500';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <title>Product Reviews</title>
</head>

<body class="relative">

<?php
include "./header.php";
include "./sidebar.php"
?>

    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <h1 class="md:hidden text-[18px] font-Onest font-semibold mb-3 md:mb-0">Reviews</h1>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <div class="flex flex-col">
                    <?php if ($product): ?>
                        <h1 class="text-[18px] md:text-[22px] font-Onest font-semibold text-[#262626]">
                            Reviews for: <?php echo htmlspecialchars($product['product_name']); ?>
                        </h1>
                        <div class="flex items-center gap-2 mt-1">
                            <a href="./products.php" class="text-[#1A237E] text-[14px] hover:underline">Back to Products</a>
                        </div>
                    <?php else: ?>
                        <h1 class="text-[18px] md:text-[22px] font-Onest font-semibold text-[#262626]">
                            All Product Reviews
                        </h1>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center gap-4 mt-3 md:mt-0">
                    <form action="" method="GET" class="flex items-center gap-2">
                        <?php if ($product_id > 0): ?>
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <?php endif; ?>
                        
                        <div class="flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                            <img src="../assets/dash/search-normal (1).svg" alt="Search" class="w-[18px]" />
                            <input type="text" name="search" placeholder="Search reviews..." value="<?php echo htmlspecialchars($search_query); ?>" class="w-full md:w-[200px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                        </div>
                        
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none text-[14px]" onchange="this.form.submit()">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="approved" <?php echo $filter_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="rejected" <?php echo $filter_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                        
                        <button type="submit" class="bg-[#1A237E] text-white py-1 px-3 rounded-md text-[14px]">Filter</button>
                        
                        <a href="?<?php echo $product_id > 0 ? 'product_id='.$product_id : ''; ?>" class="text-[#777777] text-[14px] hover:underline">Clear</a>
                    </form>
                </div>
            </div>

            <div class="w-full border-t-[1.5px] border-[#E1E1E1]">
                <div class="flex flex-col gap-3 mt-3">
                    <?php if (mysqli_num_rows($reviews_result) === 0): ?>
                        <div class="text-center py-4">
                            <p class="text-[#5B5B5B] text-[14px] font-['Montserrat']">
                                <?php if ($product_id > 0): ?>
                                    No reviews found for this product.
                                <?php else: ?>
                                    No reviews found.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead class="bg-[#E7E7E7]">
                                    <tr>
                                        <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Reviewer</th>
                                        <?php if (!$product_id): ?>
                                            <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Product</th>
                                        <?php endif; ?>
                                        <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Rating</th>
                                        <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Review</th>
                                        <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Date</th>
                                        <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Status</th>
                                        <th class="text-left p-3 text-[13px] md:text-[14px] font-['Open Sans'] font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                                        <tr class="border-b border-[#E1E1E1]">
                                            <td class="p-3 text-[13px] md:text-[14px] font-['Open Sans']">
                                                <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                            </td>
                                            <?php if (!$product_id): ?>
                                                <td class="p-3 text-[13px] md:text-[14px] font-['Open Sans']">
                                                    <a href="?product_id=<?php echo $review['product_id']; ?>" class="text-[#1A237E] hover:underline">
                                                        <?php echo htmlspecialchars($review['product_name']); ?>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                            <td class="p-3">
                                                <?php echo generateStarRating($review['rating']); ?>
                                            </td>
                                            <td class="p-3 text-[13px] md:text-[14px] font-['Open Sans'] max-w-xs truncate">
                                                <?php echo htmlspecialchars($review['review_text']); ?>
                                            </td>
                                            <td class="p-3 text-[13px] md:text-[14px] font-['Open Sans'] whitespace-nowrap">
                                                <?php echo date('m/d/Y', strtotime($review['created_at'])); ?>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 rounded-full text-[12px] font-medium <?php echo getStatusColorClass($review['status']); ?>">
                                                    <?php echo ucfirst($review['status']); ?>
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <div class="flex items-center gap-2">
                                               
                                                    
                                                    <?php if ($review['status'] !== 'approved'): ?>
                                                        <button 
                                                            onclick="updateReviewStatus(<?php echo $review['review_id']; ?>, 'approved')" 
                                                            class="text-green-600 hover:underline text-[13px]"
                                                        >
                                                            Approve
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($review['status'] !== 'rejected'): ?>
                                                        <button 
                                                            onclick="updateReviewStatus(<?php echo $review['review_id']; ?>, 'rejected')" 
                                                            class="text-red-600 hover:underline text-[13px]"
                                                        >
                                                            Reject
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <button 
                                                        onclick="deleteReview(<?php echo $review['review_id']; ?>)" 
                                                        class="text-red-800 hover:underline text-[13px]"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Detail Modal -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal-content p-4 max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold" id="modalTitle">Review Details</h2>
                <span class="close cursor-pointer text-2xl">&times;</span>
            </div>
            <div id="reviewDetails" class="mb-4">
                <!-- Review details will be loaded here -->
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button id="closeModal" class="px-4 py-2 bg-gray-300 rounded-md text-gray-800">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Function to view review details
        function viewReview(reviewId) {
            // Fetch review data
            fetch(`review-details.php?id=${reviewId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').innerText = 'Review Details';
                        
                        // Format the review display
                        let reviewHtml = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Reviewer</p>
                                    <p class="font-medium">${data.review.reviewer_name}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Product</p>
                                    <p class="font-medium">${data.review.product_name}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Rating</p>
                                    <div class="flex items-center gap-1">
                                        ${generateStarRating(data.review.rating)}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Date</p>
                                    <p class="font-medium">${data.review.created_at}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-gray-600 text-sm">Status</p>
                                    <p class="font-medium ${getStatusClass(data.review.status)}">${capitalizeFirstLetter(data.review.status)}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-gray-600 text-sm">Review Text</p>
                                    <p class="mt-1">${data.review.review_text}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 justify-end mt-4">
                                ${data.review.status !== 'approved' ? 
                                    `<button onclick="updateReviewStatus(${reviewId}, 'approved')" class="px-3 py-1 bg-green-600 text-white rounded">Approve</button>` : ''}
                                ${data.review.status !== 'rejected' ? 
                                    `<button onclick="updateReviewStatus(${reviewId}, 'rejected')" class="px-3 py-1 bg-red-600 text-white rounded">Reject</button>` : ''}
                                <button onclick="deleteReview(${reviewId})" class="px-3 py-1 bg-red-800 text-white rounded">Delete</button>
                            </div>
                        `;
                        
                        document.getElementById('reviewDetails').innerHTML = reviewHtml;
                        
                        // Show the modal
                        document.getElementById('reviewModal').style.display = 'block';
                    } else {
                        alert('Failed to load review details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading review details');
                });
        }
        
        // Function to update review status
        function updateReviewStatus(reviewId, status) {
            if (confirm(`Are you sure you want to ${status} this review?`)) {
                fetch('update-review-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_id=${reviewId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Review has been ${status}`);
                        // Close modal if open
                        document.getElementById('reviewModal').style.display = 'none';
                        // Reload page to show updated data
                        location.reload();
                    } else {
                        alert('Failed to update review status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating status');
                });
            }
        }
        
        // Function to delete review
        function deleteReview(reviewId) {
            if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                fetch('delete-review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_id=${reviewId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Review has been deleted');
                        // Close modal if open
                        document.getElementById('reviewModal').style.display = 'none';
                        // Reload page to show updated data
                        location.reload();
                    } else {
                        alert('Failed to delete review');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting review');
                });
            }
        }
        
        // Helper functions
        function generateStarRating(rating) {
            let html = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    html += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                } else {
                    html += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                }
            }
            return html;
        }
        
        function getStatusClass(status) {
            switch (status) {
                case 'approved': return 'text-green-600';
                case 'pending': return 'text-yellow-600';
                case 'rejected': return 'text-red-600';
                default: return 'text-gray-600';
            }
        }
        
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // Modal close handlers
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('reviewModal').style.display = 'none';
        });
        
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('reviewModal').style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('reviewModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>