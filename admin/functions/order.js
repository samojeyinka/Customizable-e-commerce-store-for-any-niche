function openNotimenu(btn) {

    let menu = btn.parentElement.querySelector(".not-content");
    document.querySelectorAll(".not-content").forEach(item => {
        if (item !== menu) {
            item.classList.remove("showom");
        }
    });
    menu.classList.toggle("showom");
}

