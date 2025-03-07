<?php
// Include authentication utility
require_once './includes/auth/auth.php';

// Get current user if logged in
$user = isAuthenticated() ? getCurrentUser() : null;

// Handle logout
if(isset($_GET['logout'])) {
    logout();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICTOSAH - Home</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="index.php" class="text-2xl font-bold text-[#1A237E]">Victosah</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Home
                        </a>
                        <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Services
                        </a>
                        <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            About
                        </a>
                        <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Contact
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    <?php if (isAuthenticated()): ?>
                        <!-- User is logged in -->
                        <a href="dashboard.php" class="px-3 py-2 rounded-md text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                            Dashboard
                        </a>
                        
                        <div class="relative">
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-indigo-800"><?php echo strtoupper(substr($user['email'], 0, 2)); ?></span>
                                </div>
                                <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                        </div>
                        
                        <a href="?logout=1" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Logout
                        </a>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <a href="includes/auth/create-account/signin.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                            Sign in
                        </a>
                        <a href="includes/auth/create-account/index.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Create account
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Welcome to</span>
                    <span class="block text-indigo-600">Victosah Solution</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Your trusted partner for innovative digital solutions.
                </p>
                
                <?php if (isAuthenticated()): ?>
                    <!-- Logged in user content -->
                    <div class="mt-8 flex justify-center">
                        <div class="rounded-md shadow">
                            <a href="dashboard.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Guest user content -->
                    <div class="mt-8 flex justify-center">
                        <div class="rounded-md shadow">
                            <a href="includes/auth/create-account/index.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                Get Started
                            </a>
                        </div>
                        <div class="ml-3 rounded-md shadow">
                            <a href="includes/auth/create-account/signin.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                                Sign in
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Our Services</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Everything you need for your business
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    We provide comprehensive solutions to help your business grow and succeed.
                </p>
            </div>
            
            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Web Development</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Custom website development tailored to your specific business needs.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Mobile App Development</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Native and cross-platform mobile applications for iOS and Android.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Digital Marketing</h3>
                            <p class="mt-2 text-base text-gray-500">
                                SEO, content marketing, social media management, and PPC advertising.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">IT Consulting</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Strategic technology consulting to optimize your business operations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Testimonials -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-10">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Testimonials</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    What Our Clients Say
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-lg font-medium text-indigo-800">JD</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium">John Doe</h3>
                            <p class="text-gray-500">CEO, TechCorp</p>
                        </div>
                    </div>
                    <p class="text-gray-600">
                        "Victosah Solution delivered an exceptional website that perfectly captures our brand identity. Their team was professional, responsive, and truly understood our business needs."
                    </p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-lg font-medium text-indigo-800">JS</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium">Jane Smith</h3>
                            <p class="text-gray-500">Marketing Director, GrowFast</p>
                        </div>
                    </div>
                    <p class="text-gray-600">
                        "The mobile app developed by Victosah exceeded our expectations. Our customers love the intuitive interface and seamless experience. Highly recommended!"
                    </p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-lg font-medium text-indigo-800">RJ</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium">Robert Johnson</h3>
                            <p class="text-gray-500">Owner, Local Business</p>
                        </div>
                    </div>
                    <p class="text-gray-600">
                        "The digital marketing campaign by Victosah transformed our online presence. Our website traffic increased by 150% and sales grew by 80% within just three months."
                    </p>
                </div>
            </div>
            
            <?php if (!isAuthenticated()): ?>
                <!-- Call to action for guest users only -->
                <div class="mt-10 text-center">
                    <p class="text-xl text-gray-600 mb-6">Ready to transform your business with our solutions?</p>
                    <a href="includes/auth/create-account/index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Get Started Today
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Victosah Solution</h3>
                    <p class="text-gray-400">
                        Your trusted partner for innovative digital solutions. We help businesses grow through technology.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Web Development</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Mobile Apps</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Digital Marketing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">IT Consulting</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Our Team</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Connect</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm0 2c5.32 0 8 1.21 8 6.09 0 1.18-.3 2.09-1.02 2.91-.9 1.09-2.48 1.45-3.98 1.87-1.38.27-1 .48-1 1.09v.59c0 .23-.21.55-.76.55-.43 0-.75-.32-.75-.65v-.14c0-1.85.37-2.38 2.49-3.03 1.33-.41 2.25-.82 2.25-2.09 0-1.2-.85-1.82-2.14-1.82-1.25 0-2.29.61-2.29 1.82 0 .29-.29.55-.71.55-.45 0-.75-.26-.75-.55 0-1.95 1.89-2.73 3.83-2.73 2 0 3.77.73 3.77 2.55 0 2.25-2.16 2.77-4.02 3.23-1.37.36-1.35.42-1.35 1.41v.24c0 .45-.32.65-.71.65-.4 0-.76-.2-.76-.65v-.25c0-1.21.32-1.43 1.43-1.71 1.87-.48 4.19-.87 4.19-2.92 0-1.57-1.34-2.55-3.04-2.55-1.7 0-3.22.98-3.22 2.55 0 .29-.31.55-.75.55-.44 0-.75-.26-.75-.55 0-2.28 2.48-3.45 4.72-3.45 2.32 0 4.54 1.23 4.54 3.45 0 2.79-3.32 3.45-5.06 3.91-.33.09-.33.09-.33.3v.41c0 .46-.31.66-.76.66-.43 0-.75-.2-.75-.65v-.52c0-1.09.21-1.09 1.17-1.3 1.75-.39 3.74-.87 3.74-2.77 0-1.09-.71-1.45-1.70-1.45-1 0-1.54.36-1.54 1.45 0 .23-.28.45-.66.45-.39 0-.74-.22-.74-.45 0-1.45 1.15-2.18 2.94-2.18 1.8 0 3.24.73 3.24 2.18 0 1.45-1.06 2.01-2.16 2.32-.57.16-.57.27-.57.59v.23c0 .29-.15.45-.41.45-.25 0-.4-.16-.4-.45v-.11c0-.29-.01-.36-.25-.41-.89-.16-1.65-.43-1.65-1.71 0-.55.19-.87.62-.87.42 0 .62.32.62.87 0 .37.19.55.45.61.06.01.15.03.28.04v-.85c0-.29.15-.45.4-.45.26 0 .41.16.41.45z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-400 mb-2">Subscribe to our newsletter</h4>
                        <form class="flex">
                            <input type="email" placeholder="Your email" class="px-4 py-2 w-full text-gray-800 rounded-l-md focus:outline-none" />
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-r-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-700">
                <p class="text-gray-400 text-sm text-center">
                    &copy; 2023 Victosah Solution. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Handle logout confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.querySelector('a[href="?logout=1"]');
            
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = '?logout=1';
                    }
                });
            }
        });
    </script>
</body>
</html>