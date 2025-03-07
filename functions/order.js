function openOrdermenu(btn) {

    let menu = btn.parentElement.querySelector(".ordermenu-content");
    document.querySelectorAll(".ordermenu-content").forEach(item => {
        if (item !== menu) {
            item.classList.remove("showom");
        }
    });
    menu.classList.toggle("showom");
}