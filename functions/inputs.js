let index = 0;
const slides = document.querySelectorAll(".carousel-item");
const totalSlides = slides.length;
const indicators = document.querySelectorAll(".indicators div");
const carouselInner = document.querySelector(".carousel-inner");

function updateCarousel() {
    carouselInner.style.transform = `translateX(-${index * 100}vw)`;
    indicators.forEach((dot, i) => dot.classList.toggle("active", i === index));
}

function nextSlide() {
    index = (index + 1) % totalSlides;
    updateCarousel();
}

function prevSlide() {
    index = (index - 1 + totalSlides) % totalSlides;
    updateCarousel();
}

document.getElementById("next").addEventListener("click", nextSlide);
document.getElementById("prev").addEventListener("click", prevSlide);
indicators.forEach(dot => dot.addEventListener("click", (e) => {
    index = parseInt(e.target.dataset.index);
    updateCarousel();
}));

setInterval(nextSlide, 3000);


function openSidemenu() {
    document.getElementById("sidemenu").classList.toggle("showdd");
    console.log("Side menu clicked")
}


function openMobileMenu() {
    document.getElementById("menuNav").classList.toggle("showmm");
    console.log("clicked by me")
}


const sliders = document.querySelectorAll(".reviews-container");

sliders.forEach((slider) => {
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener("mousedown", (e) => {
        isDown = true;
        slider.classList.add("active");
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener("mouseleave", () => {
        isDown = false;
        slider.classList.remove("active");
    });

    slider.addEventListener("mouseup", () => {
        isDown = false;
        slider.classList.remove("active");
    });

    slider.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2;
        slider.scrollLeft = scrollLeft - walk;
    });


    let touchStartX = 0;
    let touchScrollLeft = 0;

    slider.addEventListener("touchstart", (e) => {
        touchStartX = e.touches[0].pageX;
        touchScrollLeft = slider.scrollLeft;
    });

    slider.addEventListener("touchmove", (e) => {
        const touchMoveX = e.touches[0].pageX;
        const touchWalk = (touchMoveX - touchStartX) * 2;
        slider.scrollLeft = touchScrollLeft - touchWalk;
    });
});

