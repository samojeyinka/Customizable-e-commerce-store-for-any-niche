
var modal = document.getElementById("myModal");
var regVerify = document.getElementById("regVerify");
var passwordRequestverify = document.getElementById("passwordRequestverify");
var passwordRequestMail = document.getElementById("passwordRequestMail");


var openps = document.getElementById("openPs");
var btn = document.getElementById("myBtn");
var openpsrv = document.getElementById("openPassordRqV");
var backtologin = document.getElementById("backtologin");
var openpsrmail = document.getElementById("openPassordRqMail");
var passwordRequestNP = document.getElementById("passwordRequestNP");
var openPasswordRequestNP = document.getElementById("openPasswordRequestNP");
var closeprsucces = document.getElementById("closeprsucces");

var passwordresetsuccess = document.getElementById("passwordresetsuccess");
var openpasswordresetsuccess = document.getElementById("openpasswordresetsuccess");


var rvbtn = document.getElementById("rvBtn");
var backtoreg = document.getElementById("backtoreg");
var backtomail = document.getElementById("backtomail");
var backtoprverify = document.getElementById("backtoprverify");
var closeregsucces = document.getElementById("closeregsucces");




var dangeralert = document.getElementById("dangeralert");
var closedangeralert = document.getElementById("closedangeralert");


if (closedangeralert) {
  closedangeralert.onclick = function () {
    dangeralert.style.display = "none";
  }
}

var regSuccess = document.getElementById("regSuccess");
var regsuccessbtn = document.getElementById("regsuccessbtn");




var closeauth = document.getElementById("closeauth");



if (btn) {
  btn.onclick = function () {
    modal.style.display = "block";
    regVerify.style.display = "none";
  }
}

if (rvbtn) {
  rvbtn.onclick = function () {
    modal.style.display = "none";
    regVerify.style.display = "block";
  }
}





if (openpsrmail) {
  openpsrmail.onclick = function () {
    modal.style.display = "none";
    passwordRequestMail.style.display = "block";
  }
}




if (openpsrv) {
  openpsrv.onclick = function () {
    passwordRequestMail.style.display = "none";
    passwordRequestverify.style.display = "block";
  }
}

if (backtologin) {
  backtologin.onclick = function () {
    passwordRequestMail.style.display = "none";
    modal.style.display = "block";
  }
}

if (backtomail) {
  backtomail.onclick = function () {
    passwordRequestverify.style.display = "none";
    passwordRequestMail.style.display = "block";
  }
}


if (openPasswordRequestNP) {
  openPasswordRequestNP.onclick = function () {
    passwordRequestverify.style.display = "none";
    passwordRequestNP.style.display = "block";
  }
}

if (openpasswordresetsuccess) {
  openpasswordresetsuccess.onclick = function () {
    passwordRequestNP.style.display = "none";
    passwordresetsuccess.style.display = "block";
  }
}

if (backtoprverify) {
  backtoprverify.onclick = function () {
    passwordRequestNP.style.display = "none";
    passwordRequestverify.style.display = "block";
  }
}

if (regsuccessbtn) {
  regsuccessbtn.onclick = function () {
    regVerify.style.display = "none";
    regSuccess.style.display = "block";
  }
}

if (closeauth) {
  closeauth.onclick = function () {
    modal.style.display = "none";
  }
}

if (backtoreg) {
  backtoreg.onclick = function () {
    modal.style.display = "block";
    regVerify.style.display = "none";
  }
}




if (closeregsucces) {
  closeregsucces.onclick = function () {
    regSuccess.style.display = "none";
    window.location.href = './products/index.php';
  }
}

if (closeprsucces) {
  closeprsucces.onclick = function () {
    passwordresetsuccess.style.display = "none";
    window.location.href = '../user/profile.php';
  }
}



window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }

  if (event.target == regVerify) {
    regVerify.style.display = "none";
  }
}
