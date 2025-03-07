var modal = document.getElementById("myModal");
var btn = document.getElementById("myBtn");
var close = document.getElementById("closeauth");
var logout = document.getElementById("logout");
var logmeout = document.getElementById("logmeout");
var closelo = document.getElementById("closelo");





var createCategory = document.getElementById("createCategory");
var openCategory = document.getElementById("openCategory");
var closeCC = document.getElementById("closeCC");




openCategory.onclick = function () {
  createCategory.style.display = "block";
}


closeCC.onclick = function () {
  createCategory.style.display = "none";
}








btn.onclick = function () {
  modal.style.display = "block";
}


close.onclick = function () {
  modal.style.display = "none !important";
  console.log("click jus now")
}




logmeout.onclick = function () {
  logout.style.display = "block";
  console.log("log out now")
}


closelo.onclick = function () {
  logout.style.display = "none";
}


