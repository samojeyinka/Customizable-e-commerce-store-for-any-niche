<!-- ======================== The hero starts ======================== -->
<div class="carousel relative">
    <div class="carousel-inner">
        <div class="carousel-item slide1"></div>
        <div class="carousel-item slide2"></div>
        <div class="carousel-item slide3"></div>
        <div class="carousel-item slide4"></div>
    </div>

    <!-- Rose gradient overlay for readability -->
    <div class="absolute inset-0 bg-linear-to-r from-[#3D1A2A]/80 via-[#3D1A2A]/40 to-[#C2185B]/30"></div>

    <!-- Static text overlay -->
    <div class="absolute inset-0 flex items-center justify-center flex-col text-center text-white">
        
        <h1 class="w-[90%] md:w-[70%] text-[36px] md:text-[58px] leading-tight font-Onest font-bold drop-shadow-sm">
            Glow Up Your Beauty Routine
        </h1>
        <p class="w-[90%] md:w-[70%] lg:w-[55%] text-[15px] md:text-[18px] font-['Open Sans'] font-light mt-3">
            Discover premium skincare, makeup, and beauty essentials that bring out your natural radiance.
        </p>
        <div class="flex items-center gap-4 mt-8 flex-col sm:flex-row w-[80%] md:w-auto justify-center">
            <a href="<?php echo DOMAIN; ?>/products/index.php"
                class="text-center w-full sm:w-auto py-3 px-8 bg-[#C2185B] text-white text-[16px] md:text-[18px] font-medium font-['Open Sans'] cursor-pointer rounded-full shadow-lg hover:bg-[#A01548] hover:scale-[1.02] transition-all">
                Shop Now
            </a>
            <a href="<?php echo DOMAIN; ?>/details/about-us.php"
                class="text-center w-full sm:w-auto py-3 px-8 bg-white/15 backdrop-blur-sm border border-white/40 text-white text-[16px] md:text-[18px] font-medium font-['Open Sans'] cursor-pointer rounded-full hover:bg-white/25 transition-all">
                Our Story
            </a>
        </div>
    </div>

    <!-- Navigation Controls -->
    <div class="controls">
        <button id="prev">&#10094;</button>
        <button id="next">&#10095;</button>
    </div>
    <div class="indicators">
        <div data-index="0" class="active"></div>
        <div data-index="1"></div>
        <div data-index="2"></div>
        <div data-index="3"></div>
    </div>
</div>
<!-- ======================== The hero ends ======================== -->