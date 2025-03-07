<?php
// Include database connection
include('../config/connect.php');

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if product ID is valid
if ($product_id <= 0) {
    echo "<script>alert('Invalid product ID'); window.location.href='../index.php';</script>";
    exit;
}

// Fetch product details
$product_query = "SELECT p.*, c.category_title, b.brand_title 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 LEFT JOIN brands b ON p.brand_id = b.brand_id 
                 WHERE p.product_id = ?";
$stmt = mysqli_prepare($con, $product_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Product not found'); window.location.href='../index.php';</script>";
    exit;
}

$product = mysqli_fetch_assoc($result);

// Fetch product variants
$variants_query = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY original_price";
$stmt = mysqli_prepare($con, $variants_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$variants_result = mysqli_stmt_get_result($stmt);
$variants = [];
while ($row = mysqli_fetch_assoc($variants_result)) {
    $variants[] = $row;
}

// Fetch product images
$images_query = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC, display_order ASC";
$stmt = mysqli_prepare($con, $images_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$images_result = mysqli_stmt_get_result($stmt);
$images = [];
while ($row = mysqli_fetch_assoc($images_result)) {
    $images[] = $row;
}

// Get main image
$main_image = "../assets/products/default.jpg"; // Default image if none found
foreach ($images as $image) {
    if ($image['is_main'] == 1) {
        $main_image = "../assets/products/" . $image['image_path'];
        break;
    }
}

// Get available colors
$colors = explode(',', $product['colors']);
// Trim whitespace from each color
$colors = array_map('trim', $colors);

// Get lowest price from variants
$lowest_price = 0;
if (!empty($variants)) {
    $lowest_price = $variants[0]['original_price'];
    $has_discount = false;
    foreach ($variants as $variant) {
        if ($variant['discount_price'] && $variant['discount_price'] > 0) {
            $has_discount = true;
            if ($variant['discount_price'] < $lowest_price) {
                $lowest_price = $variant['discount_price'];
            }
        } elseif ($variant['original_price'] < $lowest_price) {
            $lowest_price = $variant['original_price'];
        }
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
    <title><?php echo $product['product_name']; ?> - VICTOSAH</title>
</head>

<body>
  

    <!-- Product Details Content -->
    <div class="pt-[80px] pb-[40px]">
        <div class="w-[90%] mx-auto flex flex-col md:flex-row gap-5">
            <!-- Product Images Section -->
            <div class="flex flex-col gap-3">
                <!-- Main Image -->
                <div class="flex flex-col gap-2">
                    <div class="w-full rounded-[4px] overflow-hidden">
                        <img src="<?php echo $main_image; ?>" class="w-full h-full object-cover" alt="<?php echo $product['product_name']; ?>" />
                    </div>
                    
                    <!-- Thumbnail Images -->
                    <div class="flex items-center gap-2 overflow-x-auto">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="w-[127.4px] h-[80px] rounded-[4px] overflow-hidden flex-shrink-0 cursor-pointer thumbnail-image" 
                                 data-img="../assets/products/<?php echo $image['image_path']; ?>">
                                <img src="../assets/products/<?php echo $image['image_path']; ?>" class="w-full h-full object-cover" alt="Product image <?php echo $index + 1; ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2"> 
                        <h1 class="text-[#262626] text-[24px] md:text-[28px] font-['Montserrat'] font-medium"><?php echo $product['product_name']; ?></h1> 
                        <?php if ($product['is_featured']): ?>
                            <span class="w-[fit-content] h-[fit-content] bg-[#D51E5E] rounded-[28px] text-white text-[12px] md:text-[13px] font-Onest font-regular py-[1.5px] px-2">Featured</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Price Display -->
                    <div class="text-[#262626] text-[18px] md:text-[20px] font-['Montserrat'] font-medium">
                        ₦<?php echo number_format($lowest_price, 2); ?>
                    </div>
                    
                    <!-- Category and Brand -->
                    <div class="text-[#5B5B5B] text-[14px] font-['Montserrat']">
                        Category: <?php echo $product['category_title']; ?>
                        <?php if (!empty($product['brand_title'])): ?>
                            | Brand: <?php echo $product['brand_title']; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Selection Options -->
                <div class="flex flex-col gap-3">
                    <!-- Color Selection -->
                    <?php if (!empty($colors) && $colors[0] != ""): ?>
                    <div class="flex flex-col gap-1">
                        <label class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Select Color</label>
                        <div class="custom-dropdown">
                            <div class="w-[152px] md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-Onest font-regular color-select">Select</span>
                                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                            </div>
                            <div class="dropdown-content">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                        <?php foreach ($colors as $color): ?>
                                            <span onclick="selectOption(this, 'color')" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $color; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Size Selection -->
                    <?php if (!empty($variants)): ?>
                    <div class="flex flex-col gap-1">
                        <label class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Select Size</label>
                        <div class="custom-dropdown">
                            <div class="w-[152px] md:min-w-[5rem] lg:min-w-[7rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-Onest font-regular size-select">Select</span>
                                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                            </div>
                            <div class="dropdown-content">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                        <?php foreach ($variants as $variant): ?>
                                            <span onclick="selectSize(this, '<?php echo $variant['variant_id']; ?>', <?php echo $variant['original_price']; ?>, <?php echo ($variant['discount_price'] ? $variant['discount_price'] : 'null'); ?>)" 
                                                  class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"
                                                  data-texture="<?php echo $variant['texture']; ?>"
                                                  data-quantity="<?php echo $variant['quantity']; ?>">
                                                <?php echo $variant['size']; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Texture Display (if applicable) -->
                    <div id="texture-display" class="text-[#5B5B5B] text-[14px] font-['Montserrat'] hidden">
                        Texture: <span id="selected-texture"></span>
                    </div>

                    <!-- Quantity Selection -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium">Select Quantity</label>
                        <div class="custom-dropdown">
                            <div class="w-[175px] md:min-w-[5rem] lg:min-w-[9rem] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                <span class="text-[#777777] text-[13px] md:text-[14px] font-Onest font-regular quantity-select">1</span>
                                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                            </div>
                            <div class="dropdown-content">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer" id="quantity-options">
                                        <span onclick="selectOption(this, 'quantity')" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">1</span>
                                        <span onclick="selectOption(this, 'quantity')" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">2</span>
                                        <span onclick="selectOption(this, 'quantity')" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">3</span>
                                        <span onclick="selectOption(this, 'quantity')" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">4</span>
                                        <span onclick="selectOption(this, 'quantity')" class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="text-[#262626] text-[16px] md:text-[17px] font-['Montserrat'] font-medium">
                        Total: ₦<span id="total-price"><?php echo number_format($lowest_price, 2); ?></span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4">
                        <button type="button" id="add-to-cart" class="w-[180px] bg-[#E8E9F2] border-[1px] border-[#969AC4] rounded-[8px] text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2 text-center">Add to cart</button>
                        <button type="button" id="buy-now" class="text-center w-[180px] bg-[#1A237E] rounded-[8px] text-white text-[15px] md:text-[16px] font-Onest font-medium cursor-pointer py-[5px] px-2">Buy now</button>
                        <img src="../assets/products/fav.svg" class="w-[24px] cursor-pointer" />
                    </div>
                </div>

                <!-- Product Information Accordion -->
                <div class="w-full flex flex-col gap-2 rounded-[8px] border-[#E1E1E1] border-[1px] p-3">
                    <!-- Details Section -->
                    <div class="flex flex-col gap-0 border-b-[1.2px] py-0 border-[#E1E1E1]">
                        <div class="accordion w-full flex items-center justify-between cursor-pointer">
                            <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Details</span>
                        </div> 
                        <div class="faqext text-[16px] font-regular text-[#777777]">
                            <?php echo $product['details']; ?>
                        </div>
                    </div>
                    
                    <!-- Warranty Section -->
                    <div class="flex flex-col gap-0 border-b-[1.2px] py-0 border-[#E1E1E1]">
                        <div class="accordion w-full flex items-center justify-between cursor-pointer">
                            <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Warranty</span>
                        </div> 
                        <div class="faqext text-[16px] font-regular text-[#777777]">
                            <?php echo $product['warranty']; ?>
                        </div>
                    </div>
                    
                    <!-- Care Section -->
                    <div class="flex flex-col gap-0">
                        <div class="accordion w-full flex items-center justify-between cursor-pointer">
                            <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium">Care</span>
                        </div> 
                        <div class="faqext text-[16px] font-regular text-[#777777]">
                            <?php echo $product['care']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables to store selected values
        let selectedVariantId = null;
        let selectedColor = null;
        let selectedSize = null;
        let selectedQuantity = 1;
        let currentPrice = <?php echo $lowest_price; ?>;
        let maxQuantity = 5; // Default max quantity

        // Function to handle general option selection (color and quantity)
        function selectOption(element, type) {
            const value = element.innerText;
            
            if (type === 'color') {
                selectedColor = value;
                document.querySelector('.color-select').innerText = value;
            } else if (type === 'quantity') {
                selectedQuantity = parseInt(value);
                document.querySelector('.quantity-select').innerText = value;
                updateTotalPrice();
            }
            
            // Close dropdown
            const dropdown = element.closest('.dropdown-content');
            dropdown.style.display = 'none';
        }

        // Function to handle size selection (also updates price)
        function selectSize(element, variantId, originalPrice, discountPrice) {
            selectedVariantId = variantId;
            selectedSize = element.innerText;
            document.querySelector('.size-select').innerText = selectedSize;
            
            // Update price based on selection
            currentPrice = discountPrice !== null ? discountPrice : originalPrice;
            updateTotalPrice();
            
            // Display texture if available
            const texture = element.getAttribute('data-texture');
            if (texture) {
                document.getElementById('selected-texture').innerText = texture;
                document.getElementById('texture-display').classList.remove('hidden');
            } else {
                document.getElementById('texture-display').classList.add('hidden');
            }
            
            // Update max quantity based on available stock
            maxQuantity = parseInt(element.getAttribute('data-quantity')) || 5;
            updateQuantityOptions();
            
            // Close dropdown
            const dropdown = element.closest('.dropdown-content');
            dropdown.style.display = 'none';
        }

        // Update total price based on quantity and selected variant
        function updateTotalPrice() {
            const total = currentPrice * selectedQuantity;
            document.getElementById('total-price').innerText = total.toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Update quantity options based on available stock
        function updateQuantityOptions() {
            const quantityOptions = document.getElementById('quantity-options');
            quantityOptions.innerHTML = '';
            
            for (let i = 1; i <= Math.min(maxQuantity, 10); i++) {
                const option = document.createElement('span');
                option.className = 'text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular';
                option.onclick = function() { selectOption(this, 'quantity'); };
                option.innerText = i;
                quantityOptions.appendChild(option);
            }
            
            // Reset selected quantity if it exceeds max
            if (selectedQuantity > maxQuantity) {
                selectedQuantity = 1;
                document.querySelector('.quantity-select').innerText = '1';
                updateTotalPrice();
            }
        }

        // Thumbnail image clicks
        document.querySelectorAll('.thumbnail-image').forEach(thumb => {
            thumb.addEventListener('click', function() {
                const mainImg = document.querySelector('.w-full.rounded-[4px].overflow-hidden img');
                mainImg.src = this.getAttribute('data-img');
            });
        });

        // Add to cart button click
        document.getElementById('add-to-cart').addEventListener('click', function() {
            if (!validateSelection()) return;
            
            // Add to cart logic would go here
            alert('Product added to cart!');
        });

        // Buy now button click
        document.getElementById('buy-now').addEventListener('click', function() {
            if (!validateSelection()) return;
            
            // Redirect to checkout
            window.location.href = './checkout.php';
        });

        // Validate that necessary options are selected
        function validateSelection() {
            if (!selectedVariantId) {
                alert('Please select a size');
                return false;
            }
            
            <?php if (!empty($colors) && $colors[0] != ""): ?>
            if (!selectedColor) {
                alert('Please select a color');
                return false;
            }
            <?php endif; ?>
            
            return true;
        }

        // Toggle accordion sections
        document.querySelectorAll('.accordion').forEach(accordion => {
            accordion.addEventListener('click', function() {
                const content = this.nextElementSibling;
                content.style.display = content.style.display === 'none' ? 'block' : 'none';
            });
        });

        // Initialize all accordions as open by default
        document.querySelectorAll('.faqext').forEach(content => {
            content.style.display = 'block';
        });

        // Toggle dropdowns
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const content = this.nextElementSibling;
                document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                    if (dropdown !== content) dropdown.style.display = 'none';
                });
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            });
        });

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>