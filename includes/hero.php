<!-- ======================== The hero starts ======================== -->
<div class="carousel relative">
    <div class="carousel-inner">
        <div class="carousel-item slide1"></div>
        <div class="carousel-item slide2"></div>
        <div class="carousel-item slide3"></div>
        <div class="carousel-item slide4"></div>
    </div>

    <!-- Static text overlay -->
    <div class="absolute inset-0 flex items-center justify-center flex-col text-center text-white">
        <h1 class="w-[75%] text-[40px] md:text-[65px] font-Onest font-bold">Upgrade Your Home with Style & Comfort</h1>
        <p class="w-[90%] md:w-[80%] lg:w-[60%] text-[16px] md:text-[18px] font-['Open Sans']">
            Shop premium duvets, bedsheets, pillows, foams, and elegant chandeliers to create the perfect living space.
        </p>
        <a href="<?php echo DOMAIN; ?>/products/index.php"
            class="text-center mt-[3rem] mx-auto w-[70%] md:max-w-[377px] py-2 px-4 bg-[#1A237E] text-white text-[18px] md:text-[20px] font-medium font-['Open Sans'] cursor-pointer rounded-[8px]">
            Shop Now
        </a>
    </div>

    <!-- Navigation Controls -->
    <div class="controls">
        <button id="prev">❮</button>
        <button id="next">❯</button>
    </div>
    <div class="indicators">
        <div data-index="0" class="active"></div>
        <div data-index="1"></div>
        <div data-index="2"></div>
        <div data-index="3"></div>
    </div>
</div>
<!-- ======================== The hero ends ======================== -->
