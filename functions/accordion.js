var acc = document.getElementsByClassName("menu-accordion");
var i;
for (i = 0; i < acc.length; i++) {
	acc[i].addEventListener("click", function(){
		this.classList.toggle("active");
		var menufaqext = this.nextElementSibling;
		if (menufaqext.style.maxHeight){
			menufaqext.style.maxHeight = null;
		}
		else{
            menufaqext.style.maxHeight = menufaqext.scrollHeight +"px";
	
		}
	});

}