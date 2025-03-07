<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accordion Component</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="accordion-container max-w-2xl mx-auto bg-white rounded-lg shadow-md">
        <!-- Customer Details Section -->
        <div class="accordion-item border-b">
            <div class="accordion-header flex justify-between items-center p-4 cursor-pointer">
                <h2 class="accordion-title text-lg font-medium">Customer Details</h2>
                <span class="accordion-icon">
                    <svg class="w-4 h-4 transform transition-transform duration-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="accordion-content p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-gray-600">Status</div>
                    <div class="text-right text-green-500 font-medium">Active</div>
                    
                    <div class="text-gray-600">Name</div>
                    <div class="text-right">Enyesiobi Golibe</div>
                    
                    <div class="text-gray-600">Email</div>
                    <div class="text-right">golibe.f@gmail.com</div>
                    
                    <div class="text-gray-600">Phone Number</div>
                    <div class="text-right">07089898989</div>
                    
                    <div class="text-gray-600">Order Notes</div>
                    <div class="text-right text-sm">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolorst laborum.
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address Section -->
        <div class="accordion-item">
            <div class="accordion-header flex justify-between items-center p-4 cursor-pointer">
                <h2 class="accordion-title text-lg font-medium">Shipping Address</h2>
                <span class="accordion-icon">
                    <svg class="w-4 h-4 transform transition-transform duration-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="accordion-content p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-gray-600">Address</div>
                    <div class="text-right">70, Shola Martins street, New Oko-Oba, Agege, Lagos</div>
                    
                    <div class="text-gray-600">Phone Number</div>
                    <div class="text-right">07089898989</div>
                    
                    <div class="text-gray-600">State</div>
                    <div class="text-right">Lagos</div>
                    
                    <div class="text-gray-600">City</div>
                    <div class="text-right">Ikeja</div>
                    
                    <div class="text-gray-600">Zip Code</div>
                    <div class="text-right">442210</div>
                </div>
            </div>
        </div>

        <!-- Billing Address Section (added as an example) -->
        <div class="accordion-item border-t">
            <div class="accordion-header flex justify-between items-center p-4 cursor-pointer">
                <h2 class="accordion-title text-lg font-medium">Billing Address</h2>
                <span class="accordion-icon">
                    <svg class="w-4 h-4 transform transition-transform duration-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="accordion-content p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-gray-600">Same as shipping</div>
                    <div class="text-right">Yes</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all accordion headers using class selector
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            // Add click event listener to each accordion header
            accordionHeaders.forEach(header => {
                // Get the content element that follows this header by class
                const content = header.parentElement.querySelector('.accordion-content');
                
                // Get the arrow icon using class
                const arrow = header.querySelector('.accordion-icon svg');
                
                // Click handler for toggling accordion
                header.addEventListener('click', function() {
                    // Toggle content visibility
                    const isVisible = content.style.display !== 'none' && content.style.display !== '';
                    
                    // Toggle content
                    if (isVisible) {
                        content.style.display = 'none';
                        arrow.classList.remove('rotate-180');
                    } else {
                        content.style.display = 'block';
                        arrow.classList.add('rotate-180');
                    }
                });
                
                // Initialize all accordions as open (comment out if you want them closed by default)
                content.style.display = 'block';
                arrow.classList.add('rotate-180');
            });
        });
   
   
   </script>
</body>
</html>