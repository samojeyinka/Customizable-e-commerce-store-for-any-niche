function openNotification() {
    document.getElementById("notification").classList.toggle("shownotification");
    console.log("not clicked")
}




function toggleNav() {
    var sidenav = document.getElementById("mySidenav");
    var main = document.getElementById("main");


    if (sidenav.style.width === "200px" || sidenav.style.width === "") {
        sidenav.style.width = "0px";
        main.style.marginLeft = "0px";
        main.style.width = "100vw"
        sidenav.style.left = "-20px"
    } else {
        sidenav.style.width = "200px";
        main.style.marginLeft = "200px";
        main.style.width = "calc(100vw - 200px)"
        sidenav.style.left = "0px"
    }
}



