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