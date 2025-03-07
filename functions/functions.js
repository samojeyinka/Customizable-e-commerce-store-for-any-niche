document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    toggle.addEventListener("click", function(event) {
        event.stopPropagation();
        document.querySelectorAll(".custom-dropdown").forEach((dd) => {
            if (dd !== dropdown) dd.classList.remove("open");
        });
        dropdown.classList.toggle("open");
    });
});

document.addEventListener("click", function() {
    document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
        dropdown.classList.remove("open");
    });
});

function selectOption(element) {
    let dropdown = element.closest(".custom-dropdown");
    let toggle = dropdown.querySelector(".dropdown-toggle");
    toggle.innerText = element.innerText;
    dropdown.classList.remove("open");
}



function filterMenu() {
    document.getElementById("filteroptions").classList.toggle("showfm");
    console.log("filter Menu clicked")
}


function openSidemenu() {
    document.getElementById("sidemenu").classList.toggle("showdd");
    console.log("Side menu clicked")
}






function openMobileMenu() {
    document.getElementById("menuNav").classList.toggle("showmm");
    console.log("clicked by me")
}


// The select options ends 

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
