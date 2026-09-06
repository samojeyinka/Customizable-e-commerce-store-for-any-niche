function openOrdermenu(btn) {
    let menu = btn.parentElement.querySelector(".ordermenu-content");
    const isOpen = menu.classList.contains("showom");

    document.querySelectorAll(".ordermenu-content").forEach(item => {
        item.classList.remove("showom");
        item.style.display = "";
    });

    if (isOpen) return;

    const rect = btn.getBoundingClientRect();
    const menuWidth = menu.offsetWidth || 190;
    const menuHeight = menu.offsetHeight || 100;

    let left = rect.right - menuWidth;
    if (left < 8) left = 8;

    let top = rect.bottom + 6;
    if (top + menuHeight > window.innerHeight) {
        top = rect.top - menuHeight - 6;
    }

    menu.style.position = "fixed";
    menu.style.left = left + "px";
    menu.style.top = top + "px";
    menu.style.width = menuWidth + "px";
    menu.style.height = "auto";
    menu.style.minHeight = "auto";
    menu.style.margin = "0";
    menu.classList.add("showom");
    console.log("clicked")
}

function closeOrdermenus() {
    document.querySelectorAll(".ordermenu-content").forEach(item => {
        item.classList.remove("showom");
        item.style.display = "";
    });
}

document.addEventListener("click", function(event) {
    if (!event.target.closest(".ordermenu-content") && !event.target.closest('[onclick="openOrdermenu(this)"]')) {
        closeOrdermenus();
    }
});

window.addEventListener("scroll", closeOrdermenus, true);
window.addEventListener("resize", closeOrdermenus);